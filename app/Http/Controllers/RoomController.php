<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomEvent;

class RoomController extends Controller
{
    public function show(Room $room)
    {
        $user = auth()->user();

        if (!$room->users->contains($user->id)) {
            abort(403, 'You do not have permission to access this room.');
        }

        // We load the users, plus the commitments/standups (newest first) and the user who wrote them
        $room->load([
            'users', 
            'commitments' => fn($query) => $query->orderBy('is_completed', 'asc')->latest(),
            'commitments.user',
            'standups' => fn($query) => $query->latest(),
            'standups.user',
            'standups.comments.user',
            'resources' => fn($query) => $query->latest(),
            'resources.user',
            'events' => fn($query) => $query->latest(),
            'messages' => fn($query) => $query->oldest(), 
            'messages.user'
        ]);

        return view('rooms.show', compact('room'));
    }

    public function leave(Room $room)
    {
        $user = auth()->user();

        // 1. Remove the user from the room
        $room->users()->detach($user->id);

        // 2. Generate the System Notification
        RoomEvent::create([
            'room_id' => $room->id,
            'message' => $user->name . ' has left the cohort.',
            'type' => 'leave'
        ]);

        // 3. (Optional but good) If the room is now completely empty, we can just delete it
        if ($room->users()->count() === 0) {
            $room->delete();
        }

        return redirect()->route('lobby')->with('success', 'You have left the room.');
    }

    // Store a new custom room
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:50',
            'max_capacity' => 'required|integer|min:2|max:10'
        ]);

        $room = Room::create([
            'title' => $request->title,
            'type' => 'custom',
            'status' => 'active',
            'creator_id' => auth()->id(),
            'max_capacity' => $request->max_capacity,
            'invite_code' => Str::random(8), // Generates something like 'aB3dE9xQ'
        ]);

        // Attach the creator to the room
        $room->users()->attach(auth()->id());

        return redirect()->route('rooms.show', $room->id)->with('success', 'Custom room created! Share your invite link.');
    }

    // Process the invite link
    public function joinViaInvite($invite_code)
    {
        $room = Room::where('invite_code', $invite_code)->firstOrFail();

        // If they are already in the room, just redirect them there
        if ($room->users->contains(auth()->id())) {
            return redirect()->route('rooms.show', $room->id);
        }

        // Check if the room is full
        if ($room->users()->count() >= $room->max_capacity) {
            return redirect()->route('lobby')->with('error', 'Sorry, this custom room is already full!');
        }

        // Welcome to the room!
        $room->users()->attach(auth()->id());

        return redirect()->route('rooms.show', $room->id)->with('success', 'You successfully joined the custom room!');
    }
}
