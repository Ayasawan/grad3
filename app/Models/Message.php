<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $appends = ['time_ago'];


    protected $fillable = ['sender_id', 'sender_type', 'receiver_id', 'receiver_type', 'content'];

    
  
    public function sender()
    {
        return $this->morphTo();
    }

    public function receiver()
    {
        return $this->morphTo();
    }

    public static function getMessagesQueryBetweenTwoUsers($request, $sender_id, $sender_type, $receiver_id, $receiver_type)
{
    $query = self::with(['sender', 'receiver'])->where(function($q) use($sender_id, $sender_type, $receiver_id, $receiver_type) {
        $q->where(function($sub) use ($sender_id, $sender_type, $receiver_id, $receiver_type) {
            $sub->where('sender_id', $sender_id)
                ->where('sender_type', $sender_type)
                ->where('receiver_id', $receiver_id)
                ->where('receiver_type', $receiver_type);
        })
        ->orWhere(function($sub) use ($sender_id, $sender_type, $receiver_id, $receiver_type) {
            $sub->where('receiver_id', $sender_id)
                ->where('receiver_type', $sender_type)
                ->where('sender_id', $receiver_id)
                ->where('sender_type', $receiver_type);
        });
    });

    return $query;
}

public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

}


  

