<?php

namespace App\Events;

use App\Models\Resource;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ResourceAdded implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $resource;

    public function __construct(Resource $resource)
    {
        // Load the user so JS can display their name
        $this->resource = $resource->load('user');
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('room.' . $this->resource->room_id),
        ];
    }
}