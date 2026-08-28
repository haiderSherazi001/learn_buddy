<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Commitment;
use App\Models\RoomEvent;

class CommitmentController extends Controller
{
    public function store(Request $request, Room $room)
    {
        // 1. Validate the form data
        $request->validate([
            'goal' => 'required|string|max:255',
        ]);

        // 2. Save it to the database
        Commitment::create([
            'user_id' => auth()->id(),
            'room_id' => $room->id,
            'goal' => $request->goal,
        ]);

        // 3. Send them back to the room with a success message
        return back()->with('success', 'Weekly commitment set!');
    }

    public function toggle(Commitment $commitment)
    {
        if (auth()->id() !== $commitment->user_id) {
            abort(403, 'You can only update your own commitments.');
        }

        $commitment->update([
            'is_completed' => !$commitment->is_completed
        ]);

        if ($commitment->is_completed) {
            RoomEvent::create([
                'room_id' => $commitment->room_id,
                'message' => '🎯 ' . auth()->user()->name . ' completed a weekly commitment!',
                'type' => 'success'
            ]);
        }

        return back()->with('success', 'Commitment status updated!');
    }
}
