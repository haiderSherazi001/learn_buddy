@props(['room'])

<div class="flex justify-between items-center">
            
            <!-- Left Side: Title & Streak Badge -->
            <div class="flex items-center space-x-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $room->title }}
                </h2>
                
                <!-- STREAK LOGIC -->
                @if($room->streak_count > 0)
                    <div class="px-3 py-1 bg-orange-100 border border-orange-300 text-orange-700 font-bold rounded-full text-sm flex items-center shadow-sm">
                        🔥 {{ $room->streak_count }} Day Streak
                    </div>
                @else
                    <div class="px-3 py-1 bg-gray-100 text-gray-500 font-medium rounded-full text-sm flex items-center">
                        🧊 No active streak
                    </div>
                @endif
            
                <!-- THE INVITE LINK (Completely independent of the streak) -->
                @if($room->type === 'custom' && $room->invite_code)
                    <div class="px-3 py-1 bg-blue-50 border border-blue-200 text-blue-700 rounded text-sm flex items-center space-x-2 shadow-sm">
                        <span class="font-bold">Invite Link:</span>
                        <code class="bg-white px-2 py-0.5 rounded text-xs select-all">{{ url('/join/' . $room->invite_code) }}</code>
                    </div>
                @endif 
            </div>
            
            <!-- THE LEAVE ROOM BUTTON -->
            <form action="{{ route('rooms.leave', $room->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to leave this cohort?');">
                @csrf
                <button type="submit" class="text-sm px-4 py-2 bg-red-100 text-red-600 font-bold rounded-lg hover:bg-red-200 transition shadow-sm">
                    Leave Room
                </button>
            </form>
        </div>