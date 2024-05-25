<?php


namespace App\Traits;

use Illuminate\Http\Request;
use App\Models\User;
trait ApiResponseTrait
{
    public function  apiResponse($data= null ,$message=null ,$status= null)
    {
        $array=[
            'data'=> $data,
            'message' =>$message,
            'status' => $status,
        ];
        return response($array,$status);
    }
    // public function saveImage($photo,$folder){
    //     $file_extension =$photo->getClientOriginalExtension();
    //     $file_name =time().'.'.$file_extension;
    //     $path = $folder;
    //     $photo->move($path,$file_name);
    //     return $file_name;

    // }



    public function saveImage($photo,$folder){
        $file_extension =$photo->getClientOriginalExtension();
        $file_name =time().'.'.$file_extension;
        $path = $folder;
        $photo->move($path,$file_name);
        $fullImagePath = $file_name ? $folder . '/' . $file_name : null;
        $host = $_SERVER['HTTP_HOST'];
        $fullPath = 'http://' . $host . '/' . $fullImagePath;
        return $fullPath;
    }


  public function sendPushNotification($title, $body,$token)
{
    $SERVER_API_KEY = 'AAAAbhqSv94:APA91bGW09iiW-lKztGKaIvQ2_MLkMir7U6GIoKgUgDNj9sxFvrsW2zMqMQ50y0lDYGRCU_QdhOKhw2rR2408pueH8RI5sW3cPIBQR_Y-m44RW3uSI46qVX9i6qQm_tzHccnxGic7jPa';

    $data = [
        "registration_ids" => [$token],
        "notification" => [
            "title" => $title,
            "body" => $body,
            "sound" => "default"
        ]
    ];

    $dataString = json_encode($data);

    $headers = [
        'Authorization: key=' . $SERVER_API_KEY,
        'Content-Type: application/json',
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
    $response = curl_exec($ch);
    curl_close($ch);

    return response()->json(['success' => true, 'response' => $response]);
}




public function storeNotification(Request $request)
{
   
    $user_type = $request->notifiable_type;

    if ($user_type === "investor") {
        $investor = Investor::findOrFail($request->notifiable_id);
        $investor->notifications()->create([
            'notifiable_id' => $request->notifiable_id,
            'notifiable_type' => "investor",
            'title' => $request->title,
            'body' => $request->body,
        ]);
    } elseif ($user_type === "user") {
        $user = User::findOrFail($request->notifiable_id);
        $user->notifications()->create([
            'notifiable_id' => $request->notifiable_id,
            'notifiable_type' => "user",
            'title' => $request->title,
            'body' => $request->body,
        ]);
    }

    return response()->json(null, 204);
}
    

 

// public function saveToken(Request $request)
// {
//     $user = auth()->user();
//     $user_id = $user->id;
//     $user1 =User::find($user_id);
    
//     $user1->update(['device_token'=>$request->token]);
//     return response()->json(['token saved successfully.']);
// }


// public function sendPushNotification($title, $body,$token)
// {
//     $firebaseToken = User::whereNotNull('device_token')->pluck('device_token')->all();

//     $SERVER_API_KEY = 'AM_C-e--QDjrLzchGFByvSx8DVvC2dQXftV_7nYLeifDW1b4Ng8fvWfI3DRhHjIK-9QNRFbo8Sr6ILLxGorziYOUCnr4X_mRV238DBZ_vlaSErnFDCJPPnola';

//     $data = [
//         "registration_ids" => $firebaseToken,
//         "notification" => [
//             "title" => $title,
//             "body" => $body,
//             "content_available" => true,
//             "priority" => "high",
//             "sound" => "default"
//         ]
//     ];

//     $dataString = json_encode($data);

//     $headers = [
//         'Authorization: key=' . $SERVER_API_KEY,
//         'Content-Type: application/json',
//     ];

//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
//     curl_setopt($ch, CURLOPT_POST, true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
//     $response = curl_exec($ch);
//     curl_close($ch);

//     return response()->json(['success' => true, 'response' => $response]);
// }



}
