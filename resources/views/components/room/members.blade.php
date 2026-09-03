@props(['room'])

<!-- ⚡ NEW: Added this wrapper ID so JS can instantly replace it -->
<div id="cohort-members-wrapper">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-purple-500">
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-md font-bold text-gray-900">Cohort Members</h3>
            <span class="text-xs font-bold text-purple-700 bg-purple-100 px-2 py-1 rounded-full">
                {{ $room->users->count() }}/{{ $room->max_capacity ?? '∞' }}
            </span>
        </div>
        
        <div class="p-4 space-y-3 max-h-[250px] overflow-y-auto scrollable-panel">
            @foreach($room->users as $member)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-100 to-indigo-100 border-2 border-white flex items-center justify-center text-purple-700 text-xs font-bold shadow-sm">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 leading-tight">{{ $member->name }}</p>
                        @if($member->id === $room->creator_id)
                            <p class="text-[10px] text-gray-400 uppercase tracking-wide font-semibold">Creator</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>