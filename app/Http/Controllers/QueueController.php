<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaitingQueue;
use App\Models\Room;

class QueueController extends Controller
{
    public function join(Request $request)
    {
        $request->validate([
            'topic' => 'required|string',
            'size_preference' => 'required|in:duo,group' // Ensure it's valid
        ]);

        $user = auth()->user();
        $topic = $request->topic;
        $pref = $request->size_preference;

        // 1. Are they already in a room for this exact topic?
        $alreadyInRoom = $user->rooms()
            ->where('title', $topic . ' Cohort')
            ->where('status', 'active')
            ->exists();

        if ($alreadyInRoom) {
            return redirect()->route('lobby')->with('info', "You are already in an active {$topic} room!");
        }

        // 2. GROUP FEATURE: Is there an existing open Group room they can instantly join?
        if ($pref === 'group') {
            // Find active group rooms for this topic
            $activeGroupRooms = Room::where('title', $topic . ' Cohort')
                ->where('status', 'active')
                ->where('max_capacity', 4)
                ->withCount('users') 
                ->get();

            // Find the first one that has less than 4 people
            $openRoom = $activeGroupRooms->where('users_count', '<', 4)->first();

            if ($openRoom) {
                // Instantly add them to the room!
                $openRoom->users()->attach($user->id);
                
                // Remove them from any queues just in case
                WaitingQueue::where('user_id', $user->id)->delete();
                
                return redirect()->route('rooms.show', $openRoom->id)
                    ->with('success', "You instantly joined an active {$topic} group!");
            }
        }

        // 3. Handle existing queue entries
        $existingQueue = WaitingQueue::where('user_id', $user->id)->first();
        
        if ($existingQueue) {
            $existingQueue->update([
                'topic' => $topic,
                'size_preference' => $pref
            ]);
        } else {
            WaitingQueue::create([
                'user_id' => $user->id,
                'topic' => $topic,
                'size_preference' => $pref
            ]);
        }

        // 4. Look for a buddy waiting for the EXACT same topic AND preference
        $buddy = WaitingQueue::where('topic', $topic)
                    ->where('size_preference', $pref)
                    ->where('user_id', '!=', $user->id)
                    ->oldest() 
                    ->first();

        if ($buddy) {
            // MATCH FOUND! Create the new Room
            $room = Room::create([
                'title' => $topic . ' Cohort',
                'type' => 'template',
                'status' => 'active',
                'max_capacity' => $pref === 'group' ? 4 : 2
            ]);

            // Attach BOTH users to the room 
            $room->users()->attach([$user->id, $buddy->user_id]);

            // Remove both users from the queue
            WaitingQueue::whereIn('user_id', [$user->id, $buddy->user_id])->delete();

            return redirect()->route('lobby')->with('success', 'Match found! Your ' . $topic . ' room was created.');
        }

        return redirect()->route('lobby')->with('info', "Joined the {$topic} queue. Waiting for a {$pref} buddy...");
    }

    public function leave()
    {
        $user = auth()->user();
        
        WaitingQueue::where('user_id', $user->id)->delete();

        return redirect()->route('lobby')->with('info', 'You have left the queue.');
    }
}