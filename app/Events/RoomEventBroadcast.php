<?php

namespace App\Events;

use App\Models\RoomEvent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RoomEventBroadcast implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $event; // We name this $event so it's easy to read in JS

    public function __construct(RoomEvent $event)
    {
        $this->event = $event;
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('room.' . $this->event->room_id),
        ];
    }
}