<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements \Illuminate\Contracts\Broadcasting\ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
   public $message;
    public $receiver_id;

    public function __construct(Message $message, $receiver_id)
    {
        $this->message = $message;
        $this->receiver_id = $receiver_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
public function broadcastOn()
    {
return new \Illuminate\Broadcasting\PrivateChannel('chat.' . $this->message->conversation_id);    }




public function broadcastAs()
    {
        return 'message.sent';
    }
}
