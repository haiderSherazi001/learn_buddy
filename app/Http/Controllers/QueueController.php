<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WaitingQueue;
use App\Models\Room;
use App\Models\RoomEvent;
use App\Events\MatchFound;
use App\Events\RoomUpdated;
use App\Events\CohortMembersUpdated;

class QueueController extends Controller
{
    public function join(Request $request)
    {
        $request->validate([
            'topic' => 'required|string',
            'size_preference' => 'required|in:duo,group' 
        ]);

        $user = auth()->user();
        $topic = $request->topic;
        $pref = $request->size_preference;

        $alreadyInRoom = $user->rooms()
            ->where('title', $topic . ' Cohort')
            ->where('status', 'active')
            ->exists();

        if ($alreadyInRoom) {
            return redirect()->route('lobby')->with('info', "You are already in an active {$topic} room!");
        }

        $targetCapacity = $pref === 'group' ? 4 : 2;

        $activeRooms = Room::where('title', $topic . ' Cohort')
            ->where('status', 'active')
            ->where('type', 'template')
            ->where('max_capacity', $targetCapacity)
            ->withCount('users') 
            ->get();

        $openRoom = $activeRooms->where('users_count', '<', $targetCapacity)->first();

        if ($openRoom) {
            $openRoom->users()->attach($user->id);
            $openRoom->touch();
            
            RoomEvent::create([
                'room_id' => $openRoom->id,
                'message' => $user->name . ' joined the cohort!',
                'type' => 'join'
            ]);
            
            WaitingQueue::where('user_id', $user->id)->delete();
            
            broadcast(new RoomUpdated($openRoom));
            broadcast(new CohortMembersUpdated($openRoom));

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

            // ⚡ NEW: Broadcast the new room to the Lobby!
            broadcast(new RoomUpdated($room));

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