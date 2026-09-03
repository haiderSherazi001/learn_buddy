<?php

namespace App\Events;

use App\Models\Room;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;

    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    public function broadcastOn(): array
    {
        // Broadcast this to a global Lobby channel for all authenticated users
        return [
            new PrivateChannel('lobby'),
        ];
    }

    public function broadcastWith(): array
    {
        // Force refresh the relationships so the counts are perfectly accurate
        $this->room->load('users'); 

        return [
            'roomId' => $this->room->id,
            // ⚡ THE MAGIC: Render the Blade component into a raw HTML string!
            'html' => view('components.room.card', ['room' => $this->room])->render()
        ];
    }
}