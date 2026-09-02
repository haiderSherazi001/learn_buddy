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

        // 2. Save it to the database AND capture it in a variable
        $commitment = Commitment::create([
            'user_id' => auth()->id(),
            'room_id' => $room->id,
            'goal' => $request->goal,
            'is_completed' => false,
        ]);

        RoomEvent::create([
            'room_id' => $room->id,
            'message' => auth()->user()->name . ' set a new weekly goal.',
            'type' => 'info'
        ]);

        // 3. ⚡ NEW: Broadcast the goal to everyone else in the room
        broadcast(new \App\Events\CommitmentCreated($commitment))->toOthers();

        // 4. ⚡ NEW: Return JSON instead of redirecting so the page doesn't reload!
        return response()->json([
            'commitment' => $commitment->load('user')
        ]);
    }

    public function toggle(Commitment $commitment)
    {
        // 1. Your excellent security check!
        if (auth()->id() !== $commitment->user_id) {
            abort(403, 'You can only update your own commitments.');
        }

        // 2. Update the database
        $commitment->update([
            'is_completed' => !$commitment->is_completed
        ]);

        // 3. Log the activity
        if ($commitment->is_completed) {
            RoomEvent::create([
                'room_id' => $commitment->room_id,
                'message' => '🎯 ' . auth()->user()->name . ' completed a weekly commitment!',
                'type' => 'success'
            ]);
        }

        broadcast(new \App\Events\CommitmentUpdated($commitment))->toOthers();

        return response()->json([
            'commitment' => $commitment
        ]);
    }
}
