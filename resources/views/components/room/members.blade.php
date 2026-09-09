@props(['room'])

<!-- ⚡ The essential wrapper ID for real-time JavaScript updates -->
<div id="cohort-members-wrapper">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-emerald-400 flex flex-col h-[600px]">
        
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="text-md font-bold text-gray-900 flex items-center gap-2">
                👥 Cohort Members
            </h3>
            <span class="bg-emerald-100 text-emerald-700 py-0.5 px-2 rounded-full text-xs font-bold shadow-sm">
                {{ $room->users->count() }}/{{ $room->max_capacity ?? '∞' }}
            </span>
        </div>

        <!-- The Member List -->
        <div id="members-list" class="flex-1 p-3 overflow-y-auto scrollable-panel bg-white space-y-3">
            @foreach($room->users as $user)
                <div onclick="toggleProfile('{{ $user->id }}')" class="bg-gray-50 border border-gray-100 rounded-xl p-3 hover:border-emerald-300 hover:shadow-md transition cursor-pointer group">
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                            @else
                                <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center border-2 border-white shadow-sm">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            
                            <div>
                                <p class="text-sm font-bold text-gray-900 group-hover:text-emerald-700 transition leading-tight">{{ $user->name }}</p>
                                <!-- Brought back your Creator Badge! -->
                                @if($user->id === $room->creator_id)
                                    <p class="text-[10px] text-gray-400 uppercase tracking-wide font-semibold mt-0.5">Creator</p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Animated Arrow Icon -->
                        <div class="text-gray-400 group-hover:text-emerald-500 transition-colors">
                            <svg id="chevron-{{ $user->id }}" class="w-5 h-5 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>

                    <!-- Hidden Accordion Details -->
                    <div id="profile-{{ $user->id }}" class="hidden mt-3 pt-3 border-t border-gray-200 text-xs text-gray-600 space-y-2 animate-fade-in">
                        @if($user->education)
                            <p><span class="font-bold text-gray-800">Education:</span> {{ $user->education }}</p>
                        @endif
                        @if($user->skills)
                            <p><span class="font-bold text-gray-800">Skills:</span> {{ $user->skills }}</p>
                        @endif
                        @if($user->email)
                            <p><span class="font-bold text-gray-800">Email:</span> {{ $user->email }}</p>
                        @endif
                        @if($user->bio)
                            <p><span class="font-bold text-gray-800">Bio:</span> {{ $user->bio }}</p>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        function toggleProfile(userId) {
            const detailsDiv = document.getElementById('profile-' + userId);
            const chevronIcon = document.getElementById('chevron-' + userId); 

            if (detailsDiv && chevronIcon) {
                if (detailsDiv.classList.contains('hidden')) {
                    detailsDiv.classList.remove('hidden');
                    chevronIcon.classList.add('rotate-180'); 
                } else {
                    detailsDiv.classList.add('hidden');
                    chevronIcon.classList.remove('rotate-180'); 
                }
            }
        }
    </script>
</div>