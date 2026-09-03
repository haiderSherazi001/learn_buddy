<?php

namespace App\Events;

use App\Models\Room;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomHeaderUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $room;

    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('room.' . $this->room->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            // Instantly render the new header HTML
            'html' => view('components.room.header', ['room' => $this->room])->render()
        ];
    }
}