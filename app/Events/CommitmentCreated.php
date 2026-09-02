<?php

namespace App\Events;

use App\Models\Commitment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommitmentCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $commitment;

    public function __construct(Commitment $commitment)
    {
        // Load the user relationship so JS has the user's name!
        $this->commitment = $commitment->load('user');
    }

    public function broadcastOn(): array
    {
        // Broadcast to this specific room's channel
        return [
            new PrivateChannel('room.' . $this->commitment->room_id),
        ];
    }
}