<div id="modal-{{ $user->id }}" class="fixed inset-0 z-[100] flex items-center justify-center px-4 animate-fade-in">
    <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" onclick="closeProfileModal('{{ $user->id }}')"></div>
    
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        
        <button onclick="closeProfileModal('{{ $user->id }}')" class="absolute top-3 right-3 z-10 p-1.5 bg-black/20 hover:bg-black/40 text-white rounded-full transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="h-28 bg-gradient-to-r from-emerald-400 to-teal-500"></div>
        
        <div class="absolute top-12 left-1/2 transform -translate-x-1/2">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md bg-white">
            @else
                <div class="w-24 h-24 rounded-full bg-emerald-100 text-emerald-700 text-3xl font-bold flex items-center justify-center border-4 border-white shadow-md">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
        </div>

        <!-- Profile Content -->
        <div class="pt-12 pb-8 px-6 text-center">
            <h2 class="text-2xl font-extrabold text-gray-900">{{ $user->name }}</h2>

            @if($user->bio)
                <p class="mt-4 text-sm text-gray-600 italic">"{{ $user->bio }}"</p>
            @endif

            <div class="mt-6 text-left space-y-4">
                @if($user->education)
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Education</p>
                        <p class="text-sm font-medium text-gray-800 bg-gray-50 p-2 rounded-lg border border-gray-100">{{ $user->education }}</p>
                    </div>
                @endif
                
                @if($user->skills)
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Core Skills</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(explode(',', $user->skills) as $skill)
                                <span class="bg-gray-100 text-gray-700 text-xs font-medium px-2.5 py-1 rounded-md">{{ trim($skill) }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($user->email)
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Contact</p>
                        <a href="mailto:{{ $user->email }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            {{ $user->email }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>