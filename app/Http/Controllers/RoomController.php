<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

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
            'standups.comments.user'
        ]);

        return view('rooms.show', compact('room'));
    }

    public function leave(Room $room)
    {
        $user = auth()->user();

        $room->users()->detach($user->id);

        if ($room->users()->count() === 0) {
            $room->delete();
        }

        return redirect()->route('lobby')->with('success', 'You have successfully left the room.');
    }
}
