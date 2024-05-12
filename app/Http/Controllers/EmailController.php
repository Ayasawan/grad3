<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Mail\SendMail;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class EmailController extends Controller
{
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
            // $user = User::where('email', $request->email)->first();
            // dd($user);
            // dd("Email sent successfully!");
            if($user){
            // send otp in the email
            // $mail_details = [
            //     'subject' => 'Testing Application OTP',
            //     'body' => 'Your OTP is : '. $otp
            // ];
                $title ='Testing Application OTP';
                $body ='Your OTP is : '. $otp;
        
            Mail::to($request->email)->send(new WelcomeMail($title, $body));
        
            return response(["status" => 200, "message" => "OTP sent successfully"]);
            }
            else{
                return response(["status" => 401, 'message' => 'Invalid']);
            }
        }


        public function verifyOtp(Request $request){
        
            $user  = User::where([['email','=',$request->email],['otp','=',$request->otp]])->first();
            if($user){
                auth()->login($user, true);
                User::where('email','=',$request->email)->update(['otp' => null]);
                $accessToken = $user->createToken('MyApp',['user'])->accessToken;
                // $success['token'] =  $user->createToken('MyApp',['user'])->accessToken;

                return response(["status" => 200, "message" => "Success", 'user' => auth()->user(), 'access_token' => $accessToken]);
            }
            else{
                return response(["status" => 401, 'message' => 'Invalid']);
            }
        }
}