<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class NotificationController extends Controller
{

    
  
    public function getUserNotifications()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'User not authenticated'], 401);
            }

            $notifications = $user->notifications()
                                  ->orderBy('created_at', 'desc')
                                  ->get();

            return response()->json(['notifications' => $notifications], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching user notifications: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to fetch user notifications'], 500);
        }
    }


    public function ShowUserNotification($id)
{
    try {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        $notification = $user->notifications()->find($id);

        if (!$notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        return response()->json(['notification' => $notification], 200);
    } catch (\Exception $e) {
        \Log::error('Error fetching user notification: ' . $e->getMessage());
        return response()->json(['message' => 'Failed to fetch user notification'], 500);
    }
}
    

//work_in_trait
        public function sendNotificationToAll($title, $body)
        {
            // الحصول على جميع المستخدمين والمستثمرين
            $users = User::all();
            $investors = Investor::all();

            // إرسال الإشعار لكل المستخدمين
            foreach ($users as $user) {
                $userBody = "عزيزي/عزيزتي {$user->first_name}، {$body}";
                $this->sendNotificationAndStore($user->id, 'user', $title, $userBody);
            }

            // إرسال الإشعار لكل المستثمرين
            foreach ($investors as $investor) {
                $investorBody = "عزيزي/عزيزتي {$investor->first_name}، {$body}";
                $this->sendNotificationAndStore($investor->id, 'investor', $title, $investorBody);
            }

            return response()->json(['message' => 'تم إرسال الإشعارات بنجاح إلى جميع المستخدمين والمستثمرين.']);
        }


//work_in_trait
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







    ///////it works
    public function notifyUser(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'required',
            'type' => 'required|string|in:user,investor',
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        // Determine the type of user (either 'user' or 'investor')
        if ($request->type === 'user') {
            $notifiable = \App\Models\User::find($request->id);
        } elseif ($request->type === 'investor') {
            $notifiable = \App\Models\Investor::find($request->id);
        } else {
            return response()->json(['message' => 'Invalid notifiable type'], 400);
        }

        // Check if the user or investor exists
        if (!$notifiable) {
            return response()->json(['message' => 'Notifiable entity not found'], 404);
        }

        // Prepare notification data
        $notification_id = $notifiable->device_token;
        $title = $request->title;
        $body = $request->body;

        // Send push notification and capture response
        $response = $this->sendPushNotification($title, $body, $notification_id);

        // Check if sending notification was successful
        if ($response->getStatusCode() === 200) {
            // Store the notification in the database
            $notification = $notifiable->notifications()->create([
                'notifiable_id' => $notifiable->id,
                'notifiable_type' => $request->type,
                'title' => $title,
                'body' => $body,
            ]);

            return response()->json(['message' => 'Notification sent and stored successfully', 'notification' => $notification], 200);
        } else {

            return response()->json(['message' => 'Failed to send notification, and failed  to store in database', 'notification' => $notification], 200);
        }
    }

//work_in_trait
    public function sendNotificationAndStore($id, string $user_type, string $title, string $body)
    {
        try {
            // Send push notification based on user type
            if ($user_type === "user") {
                $user = User::find($id);
            } elseif ($user_type === "investor") {
                $user = Investor::find($id);
            } else {
                return response()->json(['message' => 'Invalid user type'], 400);
            }

            if (!$user) {
                return response()->json(['message' => 'User not found'], 404);
            }

            $notification_id = $user->device_token;
            $response = $this->sendPushNotification($title, $body, $notification_id);

            // Check if notification was successfully sent
            if ($response && $response['success']) {
                // Store the notification in the database
                $notification = $user->notifications()->create([
                    'notifiable_id' => $user->id,
                    'notifiable_type' => $user_type,
                    'title' => $title,
                    'body' => $body,
                ]);

                return response()->json(['message' => 'Notification sent and stored successfully', 'notification' => $notification], 200);
            } else {
                return response()->json(['message' => 'Failed to send notification and store in database'], 500);
            }
        } catch (\Exception $e) {
            // Handle any exceptions
            \Log::error('Error sending and storing notification: ' . $e->getMessage());
            return response()->json(['message' => 'Unexpected error'], 500);
        }
    }



 
}
