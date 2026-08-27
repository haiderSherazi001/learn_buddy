<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Standup;
use Carbon\Carbon;

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

        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        if ($room->last_streak_date !== $today) {
            
            $usersWhoSubmittedToday = Standup::where('room_id', $room->id)
                ->whereDate('created_at', Carbon::today())
                ->distinct('user_id')
                ->count('user_id');

            if ($usersWhoSubmittedToday === $room->users()->count()) {
                
                if ($room->last_streak_date === $yesterday) {
                    $room->streak_count += 1;
                } else {
                    $room->streak_count = 1;
                }

                $room->last_streak_date = $today;
                $room->save();

                return back()->with('success', 'Daily standup logged! 🔥 Cohort Streak Increased!');
            }
        }

        return back()->with('success', 'Daily standup logged!');
    }
}