<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Investor;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
// use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\HasApiTokens;
use Laravel\Passport\RefreshToken;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\Rule;
use App\Traits\ApiResponseTrait;

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
        if($validator->fails()){
            $errors = $validator->errors()->all();
            return $this->apiResponse($errors, 'Validation Error', 422);
            // return response()->json(['error' => $validator->errors()->all()]);
        }
        if(auth()->guard('admin')->attempt(['email' => request('email'), 'password' => request('password')])){
            config(['auth.guards.api.provider' => 'admin']);
            $admin = Admin::select('admins.*')->find(auth()->guard('admin')->user()->id);
            $success =  $admin;
            $success['token'] =  $admin->createToken('MyApp',['admin'])->accessToken;

            return $this->apiResponse($success, 'success', 200);
        }else{
            return $this->apiResponse(null, ['error' => ['Email and Password are Wrong.']], 200);
        }
    }

    public function adminlogout(Request $request)
    {
        $token=$request->user()->token();
        $token->revoke();
        return response()->json([  'message' => 'Successfully logged out' ]);
    }





    //user_auth

    public function register(Request $request){

        $validator = Validator::make($request->all(),[
            'first_name' => [ 'required' , 'string','min:3'],
            'last_name' => [ 'required' , 'string','min:3'],
            'email' => ['required', 'string', 'email', 'max:255' ,'unique:users',],
            'password' => ['required', 'string', 'min:8'],

                
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 401);
        }

        $request['password'] = Hash::make($request['password']);

        $user = User::create([
            'first_name'=> $request->first_name,
            'last_name'=> $request->last_name,
            'user_type' => 'user',
            'email' => $request->email,
            'password' => $request->password,
            'phone' => null,
            'location' => null,
            'iD_card' => null,
            'personal_photo' => null,
            'property_deed' => null,
            'clean_record' => null,
           
        ]);

     

        if ($tokenResult = $user->createToken('Personal Access Token')) {
            $data["message"] = 'User successfully registered';
            $data["user_type"] = 'user';
            $data["user"] = $user;
            $data["token_type"] = 'Bearer';
            $data["access_token"] = $tokenResult->accessToken;

            return response()->json($data, Response::HTTP_OK);
        }

        return response()->json(['error' => ['Email and Password are wrong.']], 401);

    }



    
    public function userLogin(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }

        if(auth()->guard('user')->attempt(['email' => request('email'), 'password' => request('password')])){

            config(['auth.guards.api.provider' => 'user']);

            $user = User::select('users.*')->find(auth()->guard('user')->user()->id);
            $success =  $user;
            $success["user_type"] = 'user ';
            $success['token'] =  $user->createToken('MyApp',['user'])->accessToken;

            return response()->json($success, 200);
        }else{
            return response()->json(['error' => ['Email and Password are Wrong.']], 401);
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
        $validator = Validator::make($request->all(),[
            'first_name' => [ 'required' , 'string','min:3'],
            'last_name' => [ 'required' , 'string','min:3'],
            'email' => ['required', 'string', 'email', 'max:255' ,'unique:users',],
            'password' => ['required', 'string', 'min:8'],

                
        ]);

        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 401);
        }

        $request['password'] = Hash::make($request['password']);

        $investor = Investor::create([
            'first_name'=> $request->first_name,
            'last_name'=> $request->last_name,
            'user_type' => 'investor',
            'email' => $request->email,
            'password' => $request->password,
            'phone' => null,
            'location' => null,
            'iD_card' => null,
            'personal_photo' => null,
           
        ]);

     

        if ($tokenResult = $investor->createToken('Personal Access Token')) {
            $data["message"] = 'User successfully registered';
            $data["user_type"] = 'investor';
            $data["investor"] = $investor;
            $data["token_type"] = 'Bearer';
            $data["access_token"] = $tokenResult->accessToken;

            return response()->json($data, Response::HTTP_OK);
        }

        return response()->json(['error' => ['Email and Password are wrong.']], 401);
    }





    public function LoginInvestor(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(['error' => $validator->errors()->all()]);
        }
        if(auth()->guard('investor')->attempt(['email' => request('email'), 'password' => request('password')])){

            config(['auth.guards.api.provider' => 'investor']);
            $investor = Investor::select('investors.*')->find(auth()->guard('investor')->user()->id);
            $success =  $investor;
            $success["user_type"] = 'investor ';
            $success['token'] =  $investor->createToken('MyApp',['investor'])->accessToken;

            return response()->json($success, 200);
        }else{
            return response()->json(['error' => ['Email and Password are Wrong.']], 401);
        }
    }




    public function LogoutInvestor(Request $request)
    {
        $token=$request->user()->token();
        $token->revoke();
        return response()->json([  'message' => 'Successfully logged out' ]);
    }


}
