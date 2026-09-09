@props(['room'])

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

        <div id="members-list" class="flex-1 p-3 overflow-y-auto scrollable-panel bg-white space-y-3">
            @foreach($room->users as $user)
                <div onclick="openProfileModal('{{ $user->id }}')" class="bg-gray-50 border border-gray-100 rounded-xl p-3 hover:border-emerald-300 hover:shadow-md transition cursor-pointer group flex items-center justify-between">
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
                            @if($user->id === $room->creator_id)
                                <p class="text-[10px] text-gray-400 uppercase tracking-wide font-semibold mt-0.5">Creator</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-gray-300 group-hover:text-emerald-500 transition-colors bg-white rounded-full p-1 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script>
        window.openProfileModal = async function(userId) {
            if (document.getElementById('modal-' + userId)) {
                document.getElementById('modal-' + userId).classList.remove('hidden');
                return;
            }

            try {
                const response = await fetch(`/users/${userId}/modal`);
                if (response.ok) {
                    const html = await response.text();
                    document.body.insertAdjacentHTML('beforeend', html);
                }
            } catch (error) {
                console.error("Failed to load profile:", error);
            }
        };

        window.closeProfileModal = function(userId) {
            const modal = document.getElementById('modal-' + userId);
            if (modal) {
                modal.classList.add('hidden');
            }
        };
    </script>
</div>