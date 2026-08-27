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
            'topic' => 'required|string'
        ]);

        $user = auth()->user();
        $topic = $request->topic;

        // 1. Are they already in a room for this exact topic?
        $alreadyInRoom = $user->rooms()
            ->where('title', $topic . ' Cohort')
            ->where('status', 'active')
            ->exists();

        if ($alreadyInRoom) {
            return redirect()->route('lobby')->with('info', "You are already in an active {$topic} room!");
        }

        // 2. Handle existing queue entries
        $existingQueue = WaitingQueue::where('user_id', $user->id)->first();
        
        if ($existingQueue) {
            if ($existingQueue->topic === $topic) {
                // Same topic? Do nothing.
                return redirect()->route('lobby')->with('info', "You are already waiting in the {$topic} queue.");
            } else {
                // BUG FIX: Switch the topic, but DO NOT return. Let the code continue!
                $existingQueue->update(['topic' => $topic]);
            }
        } else {
            // 3. Put user in the queue if they weren't in one at all
            WaitingQueue::create([
                'user_id' => $user->id,
                'topic' => $topic
            ]);
        }

        // 4. Look for a buddy (Someone else waiting for the exact same topic)
        $buddy = WaitingQueue::where('topic', $topic)
                    ->where('user_id', '!=', $user->id)
                    ->oldest() 
                    ->first();

        if ($buddy) {
            // MATCH FOUND! Create the new Room
            $room = Room::create([
                'title' => $topic . ' Cohort',
                'type' => 'template',
                'status' => 'active'
            ]);

            // Attach BOTH users to the room 
            $room->users()->attach([$user->id, $buddy->user_id]);

            // Remove both users from the waiting queue 
            WaitingQueue::whereIn('user_id', [$user->id, $buddy->user_id])->delete();

            return redirect()->route('lobby')->with('success', 'Match found! Your ' . $topic . ' room was created.');
        }

        // 5. Still no match? Now we return the waiting message.
        return redirect()->route('lobby')->with('info', 'You joined the ' . $topic . ' queue. Waiting for a buddy...');
    }

    public function leave()
    {
        $user = auth()->user();
        
        WaitingQueue::where('user_id', $user->id)->delete();

        return redirect()->route('lobby')->with('info', 'You have left the queue.');
    }
}