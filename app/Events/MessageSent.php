<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $receiver;
    public Message $message;

    public function __construct($receiver, Message $message)
    {
        $this->receiver = $receiver;
        $this->message = $message;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->receiver->id),
        ];
    }

    public function broadcastAs()
    {
        return 'chatMessage';
    }
}
