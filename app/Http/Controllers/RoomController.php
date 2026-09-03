<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomEvent;
use App\Events\RoomUpdated;
use App\Events\CohortMembersUpdated;

class RoomController extends Controller
{
    public function show(Room $room)
    {
        $user = auth()->user();

        if (!$room->users->contains($user->id)) {
            abort(403, 'You do not have permission to access this room.');
        }

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

        $room->users()->detach($user->id);
        $room->touch(); 

        RoomEvent::create([
            'room_id' => $room->id,
            'message' => $user->name . ' has left the cohort.',
            'type' => 'leave'
        ]);

        if ($room->users()->count() === 0) {
            $room->delete();
        } else {
            broadcast(new RoomUpdated($room));
            broadcast(new CohortMembersUpdated($room));
        }

        return redirect()->route('lobby')->with('success', 'You have left the room.');
    }

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
            'invite_code' => Str::random(8),
        ]);

        $room->users()->attach(auth()->id());

        RoomEvent::create([
            'room_id' => $room->id,
            'message' => auth()->user()->name . ' joined the cohort!',
            'type' => 'info'
        ]);

        broadcast(new RoomUpdated($room));

        return redirect()->route('rooms.show', $room->id)->with('success', 'Custom room created! Share your invite link.');
    }

    public function joinViaInvite($invite_code)
    {
        $room = Room::where('invite_code', $invite_code)->firstOrFail();

        if ($room->users->contains(auth()->id())) {
            return redirect()->route('rooms.show', $room->id);
        }

        if ($room->users()->count() >= $room->max_capacity) {
            return redirect()->route('lobby')->with('error', 'Sorry, this custom room is already full!');
        }

        $room->users()->attach(auth()->id());
        $room->touch();

        RoomEvent::create([
            'room_id' => $room->id,
            'message' => auth()->user()->name . ' joined the cohort!',
            'type' => 'info'
        ]);

        broadcast(new RoomUpdated($room));
        broadcast(new CohortMembersUpdated($room));

        return redirect()->route('rooms.show', $room->id)->with('success', 'You successfully joined the custom room!');
    }
}