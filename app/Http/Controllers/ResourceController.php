<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Resource;
use App\Models\RoomEvent;

class ResourceController extends Controller
{
    public function store(Request $request, Room $room)
    {
        // 1. Validate input (ensure the URL is actually a link)
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url|max:255',
        ]);

        // 2. Security Check: Is the user actually in this room?
        if (!$room->users->contains(auth()->id())) {
            abort(403, 'You cannot post resources to this room.');
        }

        // 3. Save it
        Resource::create([
            'user_id' => auth()->id(),
            'room_id' => $room->id,
            'title' => $request->title,
            'url' => $request->url,
        ]);

        RoomEvent::create([
            'room_id' => $room->id,
            'message' => '📚 ' . auth()->user()->name . ' shared a new resource: ' . $request->title,
            'type' => 'info'
        ]);

        return back()->with('success', 'Resource added to the stash!');
    }
}
