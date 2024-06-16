<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class NotificationController extends Controller
{
    // $user = User::where('id', $request->id)->first();
    // $notification_id = $user->device_token;
    // $title="Greeting Notification";
    // $body = "Have good day!";
    // $id = $user->id;
    // $type= "basic";
    // $res = sendPushNotification ($title, $body, $notification_id);
    // if ($res == 1){
    // // success code
    // }else{
    // // fail code
    // }


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




public function saveToken(Request $request)
{
    $user = auth()->user();
    $user_id = $user->id;
    $user1 =User::find($user_id);

    $user1->update(['device_token'=>$request->token]);
    return response()->json(['token saved successfully.']);
}


public function notifyUser(Request $request)
{
    $validatedData = $request->validate([
        'id' => 'required',
        'type' => 'required|string|in:user,investor',
        'title' => 'required|string',
        'body' => 'required|string',
    ]);

    // تحديد نوع المستخدم والبحث عنه
    if ($request->type === 'user') {
        $notifiable = \App\Models\User::find($request->id);
    } elseif ($request->type === 'investor') {
        $notifiable = \App\Models\Investor::find($request->id);
    } else {
        return response()->json(['message' => 'Invalid notifiable type'], 400);
    }

    if (!$notifiable) {
        return response()->json(['message' => 'Notifiable entity not found'], 404);
    }

    $notification_id = $notifiable->device_token;
    $title = $request->title;
    $body = $request->body;

    $res = $this->sendPushNotification($title, $body, $notification_id);

    // تخزين الإشعار في قاعدة البيانات باستخدام العلاقات
    $notifiable->notifications()->create([
        'notifiable_id' => $notifiable->id,
        'notifiable_type' => $request->type,
        'title' => $title,
        'body' => $body,
    ]);

    if ($res == 1) {
        return response()->json(['message' => 'Notification sent and stored successfully'], 200);
    } else {
        return response()->json(['message' => 'Failed to send notification, but stored in database'], 500);
    }
}





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
