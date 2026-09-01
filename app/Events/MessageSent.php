<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    // 1. Define WHERE to broadcast (A private channel specific to this room)
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('room.' . $this->message->room_id),
        ];
    }

    // 2. Define WHAT data is sent over the WebSocket
    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
            'user' => $this->message->user,
        ];
    }
}