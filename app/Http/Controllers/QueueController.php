<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaitingQueue;
use App\Models\Room;
use App\Models\RoomEvent;
use App\Events\MatchFound;

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

        // 2. REFILL FEATURE: Is there an existing open room they can instantly join?
        $targetCapacity = $pref === 'group' ? 4 : 2;

        $activeRooms = Room::where('title', $topic . ' Cohort')
            ->where('status', 'active')
            ->where('type', 'template')
            ->where('max_capacity', $targetCapacity)
            ->withCount('users') 
            ->get();

        // Find the first one that has an empty slot
        $openRoom = $activeRooms->where('users_count', '<', $targetCapacity)->first();

        if ($openRoom) {
            // Instantly add them to the room!
            $openRoom->users()->attach($user->id);
            
            // Generate a join notification!
            RoomEvent::create([
                'room_id' => $openRoom->id,
                'message' => $user->name . ' joined the cohort!',
                'type' => 'join'
            ]);
            
            // Remove them from any queues just in case
            WaitingQueue::where('user_id', $user->id)->delete();
            
            return redirect()->route('rooms.show', $openRoom->id)
                ->with('success', "You instantly joined an active {$topic} room!");
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
            $room = Room::create([
                'title' => $topic . ' Cohort',
                'type' => 'template',
                'status' => 'active',
                'max_capacity' => $pref === 'group' ? 4 : 2
            ]);

            // Attach BOTH users to the room 
            $room->users()->attach([$user->id, $buddy->user_id]);
            
            // Generate a welcome notification!
            RoomEvent::create([
                'room_id' => $room->id,
                'message' => 'Cohort created! Welcome to the group.',
                'type' => 'info'
            ]);

            // Remove both users from the queue
            WaitingQueue::whereIn('user_id', [$user->id, $buddy->user_id])->delete();

            MatchFound::dispatch($buddy->user_id, $room->id);

            return redirect()->route('rooms.show', $room->id)->with('success', 'Match found! Your ' . $topic . ' room was created.');
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