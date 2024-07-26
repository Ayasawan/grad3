<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\Admin;
use App\Models\User;
use App\Models\Investor;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Traits\ApiResponseTrait;




class ChatController extends Controller
{
    use  ApiResponseTrait;



    public function index(Request $request)
    {
        $senderId = auth()->user()->id;
        $senderType = "admin";
        $receiverId = $request->receiver_id;
        $receiverType = $request->receiver_type;
    
        // بناء استعلام الأساس للحصول على الرسائل بين المرسل والمستقبل
        $messagesQuery = Message::query()
            ->where(function ($query) use ($senderId, $senderType, $receiverId, $receiverType) {
                // شروط للرسائل المرسلة من المرسل إلى المستقبل
                $query->where('sender_id', $senderId)
                    ->where('sender_type', $senderType)
                    ->where('receiver_id', $receiverId)
                    ->where('receiver_type', $receiverType);
            })
            ->orWhere(function ($query) use ($senderId, $senderType, $receiverId, $receiverType) {
                // شروط للرسائل المستقبلة من المستقبل إلى المرسل
                $query->where('receiver_id', $senderId)
                    ->where('receiver_type', $senderType)
                    ->where('sender_id', $receiverId)
                    ->where('sender_type', $receiverType);
            });
    
        $messages = $messagesQuery
            ->orderBy('created_at', 'DESC')
            ->limit($request->input('limit', 10))
            ->get();
    
        if ($request->has('last_message_time')) {
            $last_message_time = $request->input('last_message_time');
            $messages = $messages->filter(function ($message) use ($last_message_time) {
                return $message->created_at < $last_message_time;
            });
        }
    
        // تحديث حالة الرسائل المقروءة للمستقبلين
        foreach ($messages as $msg) {
            if ((int) $msg->receiver_id === $senderId && $msg->receiver_type === $senderType) {
                $msg->seen = 1; 
                $msg->save();
            }
        }
    
        return response()->json(['status' => true, 'messages' => $messages]);
    }
    



    public function indexInvestor(Request $request)
    {
        $receiverId = 1;
        $receiverType = "admin";
        $senderId = auth()->user()->id;
        // $user=Auth::user();
        
        $senderType = "investor"; 
    
        // بناء الاستعلام للحصول على الرسائل بين المستخدم والمشرف
        $messagesQuery = Message::query()
            ->where(function($query) use ($senderId, $senderType, $receiverId, $receiverType) {
                $query->where('sender_id', $senderId)
                    ->where('sender_type', $senderType)
                    ->where('receiver_id', $receiverId)
                    ->where('receiver_type', $receiverType);
            })
            ->orWhere(function($query) use ($senderId, $senderType, $receiverId, $receiverType) {
                $query->where('receiver_id', $senderId)
                    ->where('receiver_type', $senderType)
                    ->where('sender_id', $receiverId)
                    ->where('sender_type', $receiverType);
            });
    
        
        $messages = $messagesQuery
            ->orderBy('created_at', 'DESC')
            ->limit($request->input('limit', 10))
            ->get();
    
        if ($request->has('last_message_time')) {
            $last_message_time = $request->input('last_message_time');
            $messages = $messages->filter(function ($message) use ($last_message_time) {
                return $message->created_at < $last_message_time;
            });
        }
    
        // تحديث حالة الرسائل المقروءة للمستقبلين
        foreach ($messages as $msg) {
            if ((int) $msg->receiver_id === $senderId && $msg->receiver_type === $senderType) {
                $msg->seen = 1; 
                $msg->save(); 
            }
        }
    
        return response()->json(['status' => true, 'messages' => $messages]);
    }



    

    public function indexUser(Request $request)
    {
        $receiverId = 1; 
        $receiverType = "admin";
        $senderId = auth()->user()->id;
        // $user=Auth::user();
        
        $senderType = "user"; 
    
        // بناء الاستعلام للحصول على الرسائل بين المستخدم والمشرف
        $messagesQuery = Message::query()
            ->where(function($query) use ($senderId, $senderType, $receiverId, $receiverType) {
                $query->where('sender_id', $senderId)
                    ->where('sender_type', $senderType)
                    ->where('receiver_id', $receiverId)
                    ->where('receiver_type', $receiverType);
            })
            ->orWhere(function($query) use ($senderId, $senderType, $receiverId, $receiverType) {
                $query->where('receiver_id', $senderId)
                    ->where('receiver_type', $senderType)
                    ->where('sender_id', $receiverId)
                    ->where('sender_type', $receiverType);
            });
    
        
            $messages = $messagesQuery
            ->orderBy('created_at', 'DESC')
            ->limit($request->input('limit', 10))
            ->get();

        if ($request->has('last_message_time')) {
            $last_message_time = $request->input('last_message_time');
            $messages = $messages->filter(function ($message) use ($last_message_time) {
                return $message->created_at < $last_message_time;
            });
        }

        // تحديث حالة الرسائل المقروءة للمستقبلين
        foreach ($messages as $msg) {
            if ((int) $msg->receiver_id === $senderId && $msg->receiver_type === $senderType) {
                $msg->seen = 1; 
                $msg->save();   
            }
        }

        return response()->json(['status' => true, 'messages' => $messages]);
    }





    public function sendMessage(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'User is not authenticated'], 401);
            }
             ////works
            // $user = auth()->guard()->user();
            $user = Auth::user();

            $request->validate([
                'receiver_id' => 'required',
                'receiver_type' => 'required',
                'content' => 'required'
            ]);

            $message = Message::create([
                'sender_id' => $user->id,
                'sender_type' => 'admin',
                'receiver_id' => $request->receiver_id,
                'receiver_type' => $request->receiver_type,
                'content' => $request->content
            ]);


                // Broadcast the message
                $receiverClass = '\\App\\Models\\' . $request->receiver_type; 
                $receiver = $receiverClass::find($request->receiver_id);
                event(new MessageSent($receiver, $message));

                // Send Firebase notification
                $title = 'رسالة جديدة';
                $body = "لديك رسالة جديدة من مسؤول النظام تفقد دردشتك الخاصة";
                $this->sendNotificationAndStore($receiver->id, 'admin', $title, $body);


            // // Broadcast the message
            // $receiver = $request->receiver_type::find($request->receiver_id);
            // event(new MessageSent($receiver, $message));

            return response()->json(['status' => 'Message Sent!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send message', 'details' => $e->getMessage()], 500);
        }
    }





    public function sendMessageUser(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'User is not authenticated'], 401);
            }
             ////works
            // $user = auth()->guard()->user();
            $user = Auth::user();


            $request->validate([
                'content' => 'required'
            ]);

            $message = Message::create([
                'sender_id' => $user->id,
                'sender_type' => 'user',
                'receiver_id' => 1,
                'receiver_type' => 'admin',
                'content' => $request->content
            ]);

                // $id=1;
                // Broadcast the message
                $receiverClass = '\\App\\Models\\Admin'; 
                $receiver = $receiverClass::find(1);
                if ($receiver) {
                    event(new MessageSent($receiver, $message));


                    // Send Firebase notification
                    $title = 'رسالة جديدة';
                    $body = "لديك رسالة جديدة من {$user->name}: {$message->content}";
                    $this->sendNotificationAndStore($receiver->id, 'admin', $title, $body);
                } else {
                    return response()->json(['error' => 'Receiver not found'], 404);
                }
            // // Broadcast the message
            // $receiver = $request->receiver_type::find($request->receiver_id);
            // event(new MessageSent($receiver, $message));

            return response()->json(['status' => 'Message Sent!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send message', 'details' => $e->getMessage()], 500);
        }
    }






    public function sendMessageInvestor(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'User is not authenticated'], 401);
            }
             ////works
            // $user = auth()->guard()->user();
            $user = Auth::user();

            $request->validate([
                'content' => 'required'
            ]);

            $message = Message::create([
                'sender_id' => $user->id,
                'sender_type' => 'investor',
                'receiver_id' => 1,
                'receiver_type' => 'admin',
                'content' => $request->content
            ]);


                // Broadcast the message
                $receiverClass = '\\App\\Models\\Admin';
                $receiver = $receiverClass::find(1);
                if ($receiver) {
                    event(new MessageSent($receiver, $message));


                    // Send Firebase notification
                    $title = 'رسالة جديدة';
                    $body = "لديك رسالة جديدة من {$user->name}: {$message->content}";
                    $this->sendNotificationAndStore($receiver->id, 'admin', $title, $body);
                } else {
                    return response()->json(['error' => 'Receiver not found'], 404);
                }

            // // Broadcast the message
            // $receiver = $request->receiver_type::find($request->receiver_id);
            // event(new MessageSent($receiver, $message));

            return response()->json(['status' => 'Message Sent!']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to send message', 'details' => $e->getMessage()], 500);
        }
    }




    public function update($id)
    {
        $message = Message::find($id);

        $message->seen = 1;

        $message->save();

        return response()->json(data: ['status' => true, 'message' => $message]);
    }




    //un_seen
        public function usersWithUnseenMessages()
    {
        $users = User::where('verified', 1)
                    ->get()
                    ->map(function ($user) {
                        $unseenMessagesCount = Message::where('sender_id', $user->id)
                                                    ->where('sender_type', 'user')
                                                    ->where('seen', 0)
                                                    ->count();
                        $user->unseen_messages_count = $unseenMessagesCount;
                        return $user;
                    });

        return response()->json([
            'status' => true,
            'users' => $users
        ]);
    }


    public function investorsWithUnseenMessages()
    {
        $investors = Investor::where('verified', 1)
                         ->get()
                         ->map(function ($investor) {
                             $unseenMessagesCount = Message::where('sender_id', $investor->id)
                                                           ->where('sender_type', 'investor')
                                                           ->where('seen', 0)
                                                           ->count();
                             $investor->unseen_messages_count = $unseenMessagesCount;
                             return $investor;
                         });
    
    return response()->json([
        'status' => true,
        'investors' => $investors 
    ]);
    }
    
    


    ///for_unseen message from admin
    public function adminWithUnseenMessages()
    {
        $user = Auth::user();
        $user_id = $user->id;
        $user_type = $user->user_type;
    
        $admins = Admin::where('id', 1)
                        ->get()
                        ->map(function ($admin) use ($user_id, $user_type) {
                            $unseenMessagesCount = Message::where('sender_id', $admin->id)
                                                          ->where('sender_type', 'admin')
                                                          ->where('receiver_id', $user_id)
                                                          ->where('receiver_type', $user_type)
                                                          ->where('seen', 0)
                                                          ->count();
                            $admin->unseen_messages_count = $unseenMessagesCount;
                            return $admin;
                        });
    
        return response()->json([
            'status' => true,
            'admins' => $admins 
        ]);
    }
    



}
