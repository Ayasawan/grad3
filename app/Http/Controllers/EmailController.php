<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Mail\SendMail;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponseTrait;
use Laravel\Passport\HasApiTokens;



class EmailController extends Controller
{
    use  ApiResponseTrait;

    // public function sendWelcomeEmail()
    // {
    //     $title = 'Welcome to the laracoding.com example email';
    //     $body = 'Thank you for participating!';

    //     Mail::to('abeerasidah@gmail.com')->send(new WelcomeMail($title, $body));

    //     return "Email sent successfully!";
    // }


    public function sendWelcomeEmail()
    {
        $title = 'Welcome to the laracoding.com example email';
        $body = 'Thank you for participating!';

        Mail::to('abeerasidah@gmail.com')->send(new WelcomeMail($title, $body));

        return "Email sent successfully!";
    }
         
    public function requestOtp(Request $request)
    {

            $otp = rand(1000,9999);
            Log::info("otp = ".$otp);
            $user = User::where('email','=',$request->email)->update(['otp' => $otp]);
            if($user){
            // send otp in the email
                $title ='Testing Application OTP';
                $body ='Your OTP is : '. $otp;
        
            Mail::to($request->email)->send(new WelcomeMail($title, $body));
        
            return response(["status" => 200, "message" => "OTP sent successfully"]);
            }
            else{
                return response(["status" => 401, 'message' => 'Invalid']);
            }
        }



        public function verifyOtp(Request $request)
        {
            $user = User::where([['email', '=', $request->email], ['otp', '=', $request->otp]])->first();
        
            if ($user) {
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
}