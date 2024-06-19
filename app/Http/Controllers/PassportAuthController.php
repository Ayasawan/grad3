<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Investor;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
// use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Http\Controllers\EmailController;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\RefreshToken;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;
use App\Traits\ApiResponseTrait;
use App\Notifications\EmailVerificationNotification;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;

class PassportAuthController extends Controller
{
    use  ApiResponseTrait;


    //Admin_auth

    public function adminLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return $this->apiResponse($errors, 'Validation Error', 422);
        }

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $admin = Auth::guard('admin')->user();
            $admin->bank_account_number = $request->input('bank_account_number');
            $admin->save();

            $token = $admin->createToken('MyApp', ['admin'])->accessToken;

            $data = [
                'id' => $admin->id,
                'first_name' => $admin->first_name,
                'last_name' => $admin->last_name,
                'user_type' => $admin->user_type,
                'email' => $admin->email,
                'created_at' => $admin->created_at,
                'updated_at' => $admin->updated_at,
                'token' => $token,
            ];

            return $this->apiResponse($data, 'success', 200);
        } else {
            return $this->apiResponse(null, ['error' => ['Email and Password are Wrong.']], 200);
        }
    }


    
    public function updateAdminBankAccountNumber(Request $request)
    {
        $admin = Auth::user();

        $validator = Validator::make($request->all(), [
            'bank_account_number' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return $this->apiResponse($errors, 'Validation Error', 422);
        }

        if (!$admin) {
            return $this->apiResponse(null, ['error' => ['Admin not found.']], 404);
        }

        $admin->bank_account_number = $request->input('bank_account_number');
        $admin->save();

        return $this->apiResponse($admin, 'Bank account number updated successfully', 200);
    }
    public function adminlogout(Request $request)
    {
        $token=$request->user()->token();
        $token->revoke();
        return response()->json([  'message' => 'Successfully logged out' ]);
    }





    //user_auth

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'min:3'],
            'last_name' => ['required', 'string', 'min:3'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'device_token'=> ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 401);
        }

        $request['password'] = Hash::make($request['password']);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_type' => 'user',
            'email' => $request->email,
            'otp' => null,
            'device_token'=>$request->device_token,
            'password' => $request->password,
            'phone' => $request->phone,
            'location' => $request->location,
            'iD_card' => null,
            'personal_photo' => null,
            'property_deed' => null,
            'clean_record' => null,
        ]);

        // if ($tokenResult = $user->createToken('Personal Access Token')) {
        //     $data["message"] = 'User successfully registered';
        //     $data["user_type"] = 'user';
        //     $data["user"] = $user;
        //     $data["token_type"] = 'Bearer';
        //     $data["access_token"] = $tokenResult->accessToken;
        if ($user) {
            $data["message"] = 'Thank you for registering..Please check your email.. We are waiting for you to verify your account';
            $data["user_type"] = 'user';
            $data["user"] = $user;
            // $data["token_type"] = 'Bearer';
            // $data["access_token"] = $tokenResult->accessToken;
            $data["OTP"]=$this->requestOtp($request,'User');

            // Send notification
            $title = 'مرحبًا بك في تطبيق Bloom';
            $body = 'شكرًا لتسجيلك في تطبيق Bloom. نرجو منك التحقق من بريدك الإلكتروني لإتمام عملية التفعيل. نحن سعيدون بانضمامك إلينا!';
            $this->sendNotificationAndStore($user->id, 'user', $title, $body);
            

            return response()->json($data, Response::HTTP_OK);
        }

        return response()->json(['error' => ['Email and Password are wrong.']], 401);
    }




    public function userLogin(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'device_token'=>['required', 'string'],
        ]);
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }

        if(auth()->guard('user')->attempt(['email' => request('email'), 'password' => request('password')])){

            $user = User::where('email', request('email'))->first();

            if ($user->verified) {
                config(['auth.guards.api.provider' => 'user']);
                $user = User::select('users.*')->find(auth()->guard('user')->user()->id);

                // // تحديث device_token
                $user->device_token = $request->device_token;
                $user->save(); // تخزين النتائج في قاعدة البيانات
     
                $success =  $user;
                $success["user_type"] = 'user ';
                $success['token'] =  $user->createToken('MyApp',['user'])->accessToken;

              
                 // Send notification
                $title = 'مرحبًا بك في تطبيق Bloom';
                $body = "مرحبًا، {$user->first_name}! يسعدنا أن نرحب بك مرة أخرى في تطبيق Bloom. نحن سعداء بانضمامك إلينا. إذا كان لديك أي استفسارات أو تحتاج إلى مساعدة، فلا تتردد في التواصل مع فريق الدعم الخاص بنا. شكرًا لانضمامك إلينا!";
                $this->sendNotificationAndStore($user->id, 'user', $title, $body);

                return response()->json($success, 200);
            } else {
                return response()->json(['error' => ['Email and Password are correct, but the account is not verified.']], 401);
            }

        } else {
            return response()->json(['error' => ['Email and Password are wrong.']], 401);
        }
    }


    public function logout(Request $request)
    {
        $token=$request->user()->token();
        $token->revoke();
        return response()->json([  'message' => 'Successfully logged out' ]);
    }



    public function destroy($id)
    {
        $res= User::find($id);
        if(!$res)
        {
            return $this->apiResponse(null ,'the user not found ',404);
        }
        $res->delete($id);
        if($res)
            return $this->apiResponse(null ,'the user delete ',200);

    }





    ///Investor
    public function registerInvestor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'min:3'],
            'last_name' => ['required', 'string', 'min:3'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'device_token'=> ['required', 'string'],

        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 401);
        }

        $request['password'] = Hash::make($request['password']);

        $investor = Investor::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'user_type' => 'investor',
            'email' => $request->email,
            'device_token'=>$request->device_token,
            'password' => $request->password,
            'phone' => $request->phone,
            'location' => $request->location,
            'iD_card' => null,
            'personal_photo' => null,
        ]);

        // if ($tokenResult = $investor->createToken('Personal Access Token')) {
        //     $data["message"] = 'Investor successfully registered';
        //     $data["user_type"] = 'investor';
        //     $data["investor"] = $investor;
        //     $data["token_type"] = 'Bearer';
        //     $data["access_token"] = $tokenResult->accessToken;
        if ($investor) {
            $data["message"] = 'Thank you for registering..Please check your email.. We are waiting for you to verify your account';
            $data["user_type"] = 'investor';
            $data["user"] = $investor;
            // $data["token_type"] = 'Bearer';
            // $data["access_token"] = $tokenResult->accessToken;
            $data["OTP"]=$this->requestOtp($request,'Investor');



              // Send notification
              $title = 'مرحبًا بك في تطبيق Bloom';
              $body = 'شكرًا لتسجيلك في تطبيق Bloom. نرجو منك التحقق من بريدك الإلكتروني لإتمام عملية التفعيل. نحن سعيدون بانضمامك إلينا!';
              $this->sendNotificationAndStore($user->id, 'user', $title, $body);

            if ($notificationResponse->getStatusCode() !== 200) {
                return response()->json(['message' => 'Failed to send notification. Registration aborted.'], 500);
            }

            return response()->json($data, Response::HTTP_OK);
        }

        return response()->json(['error' => ['Email and Password are wrong.']], 401);
    }




    public function LoginInvestor(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
            'device_token'=>['required', 'string'],

        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }
        if(auth()->guard('investor')->attempt(['email' => request('email'), 'password' => request('password')])){

            $investor = Investor::where('email', request('email'))->first();

            if ($investor->verified) {
                config(['auth.guards.api.provider' => 'investor']);
                $investor = Investor::select('investors.*')->find(auth()->guard('investor')->user()->id);

                 // // تحديث device_token
                $investor->device_token = $request->device_token;
                $investor->save(); // تخزين النتائج في قاعدة البيانات

                $success =  $investor;
                $success["user_type"] = 'investor ';
                $success['token'] =  $investor->createToken('MyApp',['investor'])->accessToken;

              
               // Send notification
               $title = 'مرحبًا بك في تطبيق Bloom';
               $body = "مرحبًا، {$investor->first_name}! يسعدنا أن نرحب بك مرة أخرى في تطبيق Bloom. نحن سعداء بانضمامك إلينا. إذا كان لديك أي استفسارات أو تحتاج إلى مساعدة، فلا تتردد في التواصل مع فريق الدعم الخاص بنا. شكرًا لانضمامك إلينا!";
               $this->sendNotificationAndStore($investor->id, 'investor', $title, $body);

                return response()->json($success, 200);
            } else {
                return response()->json(['error' => ['Email and Password are correct, but the account is not verified.']], 401);
            }

        } else {
            return response()->json(['error' => ['Email and Password are wrong.']], 401);
        }
    }
            
       





    public function LogoutInvestor(Request $request)
    {
        $token=$request->user()->token();
        $token->revoke();
        return response()->json([  'message' => 'Successfully logged out' ]);
    }



//////

public function sendWelcomeEmail()
{
    $title = 'Welcome to the laracoding.com example email';
    $body = 'Thank you for participating!';

    Mail::to('abeerasidah@gmail.com')->send(new WelcomeMail($title, $body));

    return "Email sent successfully!";
}

public function requestOtp(Request $request, $modelName)
{
    $otp = rand(1000, 9999);
    Log::info("otp = " . $otp);

    $model = app('App\\Models\\' . $modelName); // استبدل 'App\\Models\\' بالمسار الصحيح لمجلد النماذج الخاص بك

    $user = $model->where('email', '=', $request->email)->update(['otp' => $otp]);

    if ($user) {
        // send otp in the email
        $title = 'Testing Application OTP';
        $body = 'Your OTP is: ' . $otp;

        Mail::to($request->email)->send(new WelcomeMail($title, $body));

        return response(["status" => 200, "message" => "OTP sent successfully"]);
    } else {
        return response(["status" => 401, 'message' => 'Invalid']);
    }
}


    public function verifyOtp(Request $request)
    {
        $user = User::where([['email', '=', $request->email], ['otp', '=', $request->otp]])->first();

        if ($user) {
            $user->verified = true;
            $user->save();
            auth()->login($user);

            $tokenResult = $user->createToken('Personal Access Token');

            $data["message"] = 'User successfully registered';
            $data["user_type"] = 'user';
            $data["user"] = $user;
            $data["token_type"] = 'Bearer';
            $data["access_token"] = $tokenResult->accessToken;

            return response()->json($data, 200);
        } else {
            return response()->json(['message' => 'Invalid'], 401);
        }
    }

    public function verifyOtpInv(Request $request)
    {
        $investor = Investor::where([['email', '=', $request->email], ['otp', '=', $request->otp]])->first();

        if ($investor) {
            $investor->verified = true;
            $investor->save();
            auth()->login($investor);

            $tokenResult = $investor->createToken('Personal Access Token');

            $data["message"] = 'User successfully registered';
            $data["user_type"] = 'user';
            $data["user"] = $investor;
            $data["token_type"] = 'Bearer';
            $data["access_token"] = $tokenResult->accessToken;

            return response()->json($data, 200);
        } else {
            return response()->json(['message' => 'Invalid'], 401);
        }
    }

}
