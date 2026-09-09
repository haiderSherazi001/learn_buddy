<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(\App\Http\Requests\ProfileUpdateRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user(); 
        $oldAvatar = $user->avatar; 
        
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // 1. Handle Avatar Removal
        if ($request->boolean('remove_avatar')) {
            if ($oldAvatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldAvatar);
            }
            $user->avatar = null; // Clear from database
        } 
        // 2. Handle New Avatar Upload
        elseif ($request->hasFile('avatar')) {
            if ($oldAvatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldAvatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->save();

        // ⚡ 3. REAL-TIME MAGIC: Tell all rooms this user is in to update their UIs!
        foreach ($user->rooms as $room) {
            broadcast(new \App\Events\CohortMembersUpdated($room));
            broadcast(new \App\Events\RoomUpdated($room)); // Updates the Lobby Cards too!
        }

        return \Illuminate\Support\Facades\Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
