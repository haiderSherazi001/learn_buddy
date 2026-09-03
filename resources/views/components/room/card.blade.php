@props(['room'])

<div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col group relative overflow-hidden" id="room-card-{{ $room->id }}">
    
    <!-- Decorative Top Border -->
    <div class="absolute top-0 left-0 w-full h-1 {{ $room->type === 'template' ? 'bg-blue-400' : 'bg-green-400' }}"></div>

    <!-- Top Row: Title & Badge -->
    <div class="flex justify-between items-start mb-4 pt-2">
        <h3 class="font-bold text-lg text-gray-900 leading-tight pr-2 group-hover:text-indigo-600 transition-colors">{{ $room->title }}</h3>
        @if($room->type === 'template')
            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-[10px] uppercase font-extrabold rounded-full whitespace-nowrap border border-blue-100">Auto</span>
        @else
            <span class="px-2.5 py-1 bg-green-50 text-green-700 text-[10px] uppercase font-extrabold rounded-full whitespace-nowrap border border-green-100">Custom</span>
        @endif
    </div>
    
    <!-- Middle Details Section -->
    <div class="flex-1 space-y-4 mb-6">
        
        <!-- 1. Streak Indicator -->
        <div class="flex items-center">
            @if($room->streak_count > 0)
                <span class="text-xs font-bold text-orange-700 bg-orange-50 px-2.5 py-1.5 rounded-lg border border-orange-100 flex items-center gap-1.5">
                    🔥 {{ $room->streak_count }} Day Streak
                </span>
            @else
                <span class="text-xs font-medium text-gray-500 bg-gray-50 px-2.5 py-1.5 rounded-lg border border-gray-100">
                    🧊 No active streak
                </span>
            @endif
        </div>

        <!-- 2. Member Avatars & Capacity -->
        <div class="flex items-center justify-between border-t border-gray-50 pt-4">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Buddies ({{ $room->users->count() }}/{{ $room->max_capacity ?? '∞' }})
            </div>
            <div class="flex -space-x-2">
                @foreach($room->users->take(4) as $member)
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 border-2 border-white flex items-center justify-center text-indigo-700 text-xs font-bold shadow-sm" title="{{ $member->name }}">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 3. Last Active Time -->
        <div class="text-[11px] text-gray-400 font-medium flex items-center gap-1">
            ⏱ Active {{ $room->updated_at->diffForHumans() }}
        </div>
    </div>
    
    <!-- Bottom Row: Buttons -->
    <div class="flex gap-2 mt-auto">
        <a href="{{ route('rooms.show', $room->id) }}" class="flex-1 text-center px-4 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-bold hover:bg-gray-800 transition shadow-sm">
            Enter Room &rarr;
        </a>
        
        <form action="{{ route('rooms.leave', $room->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to leave {{ $room->title }}?');">
            @csrf
            <button type="submit" class="px-4 py-2.5 bg-white text-gray-400 font-bold rounded-xl hover:bg-red-50 hover:text-red-600 border border-gray-200 hover:border-red-100 transition shadow-sm h-full flex items-center justify-center" title="Leave Cohort">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    </div>
</div>