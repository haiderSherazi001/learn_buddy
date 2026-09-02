<?php

namespace App\Events;

use App\Models\Standup;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StandupCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $standup;

    public function __construct(Standup $standup)
    {
        // Load the user so JS can display their name and avatar
        $this->standup = $standup->load('user', 'comments.user'); 
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('room.' . $this->standup->room_id),
        ];
    }
}