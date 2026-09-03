<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Standup;
use App\Models\RoomEvent;
use Carbon\Carbon;

class StandupController extends Controller
{
    public function store(Request $request, Room $room)
    {
        $request->validate([
            'what_i_did' => 'required|string',
            'blockers' => 'nullable|string',
        ]);

        $standup = Standup::create([
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

                broadcast(new \App\Events\RoomUpdated($room));

                $milestones = [3, 7, 14, 30, 50, 100];
                if (in_array($room->streak_count, $milestones)) {
                    RoomEvent::create([
                        'room_id' => $room->id,
                        'message' => '🔥 Amazing! The cohort just hit a ' . $room->streak_count . '-Day Streak!',
                        'type' => 'success'
                    ]);
                }
            } else {
                RoomEvent::create([
                    'room_id' => $room->id,
                    'message' => auth()->user()->name . ' logged their daily standup.',
                    'type' => 'info'
                ]);
            }
        } else {
            RoomEvent::create([
                'room_id' => $room->id,
                'message' => auth()->user()->name . ' logged their daily standup.',
                'type' => 'info'
            ]);
        }

        $standup->load('user'); 

        broadcast(new \App\Events\StandupCreated($standup))->toOthers();

        return response()->json([
            'standup' => $standup,
            'message' => 'Daily standup logged!'
        ]);
    }
}