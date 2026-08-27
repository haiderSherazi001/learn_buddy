<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Standup;

class StandupController extends Controller
{
    public function store(Request $request, Room $room)
    {
        $request->validate([
            'what_i_did' => 'required|string',
            'blockers' => 'nullable|string',
        ]);

        Standup::create([
            'user_id' => auth()->id(),
            'room_id' => $room->id,
            'what_i_did' => $request->what_i_did,
            'blockers' => $request->blockers,
        ]);

        return back()->with('success', 'Daily standup logged!');
    }
}
