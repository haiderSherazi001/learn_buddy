<?php

namespace App\Events;

use App\Models\Commitment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommitmentUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $commitment;

    public function __construct(Commitment $commitment)
    {
        $this->commitment = $commitment;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('room.' . $this->commitment->room_id),
        ];
    }
}