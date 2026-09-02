<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- ALERTS & MESSAGES -->
            @if(session('success'))
                <div class="p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm font-medium animate-fade-in">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700 rounded-r-lg shadow-sm font-medium animate-fade-in">
                    {{ session('info') }}
                </div>
            @endif
            
            <!-- ACTIVE QUEUE ALERT (Floating Banner) -->
            @if($activeQueue)
                <div class="p-5 bg-indigo-50 border border-indigo-100 rounded-xl flex flex-col sm:flex-row justify-between items-center shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500 animate-pulse"></div>
                    <div class="mb-4 sm:mb-0 pl-3">
                        <h4 class="text-lg font-extrabold text-indigo-900 flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Searching for a {{ $activeQueue->topic }} Buddy...
                        </h4>
                        <p class="text-sm text-indigo-700 mt-1">Leave this page open. We will teleport you instantly when a match is found.</p>
                    </div>
                    
                    <form action="{{ route('queue.leave') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-lg font-bold hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition shadow-sm">
                            Leave Queue
                        </button>
                    </form>
                </div>
            @endif

            <!-- MY ACTIVE ROOMS SECTION (Using your upgraded cards!) -->
            @if($myRooms->count() > 0)
                <div>
                    <h2 class="text-xl font-extrabold text-gray-900 mb-5">My Active Cohorts</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($myRooms as $room)
                            <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col group relative overflow-hidden">
                                
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
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- ACTION SECTION: Start a New Session -->
            <div class="mt-12 pt-10 border-t border-gray-200">
                <h2 class="text-xl font-extrabold text-gray-900 mb-6">Start a New Session</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- LEFT: Matchmaking Queue -->
                    <div class="bg-white rounded-2xl border border-gray-200 p-6 sm:p-8 shadow-sm">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Auto-Matchmaking</h3>
                        </div>
                        <p class="text-sm text-gray-500 mb-6 ml-13">Pick a topic to instantly join a queue. We'll generate a room the moment we find a partner.</p>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($popularTopics as $topic)
                            <form action="{{ route('queue.join') }}" method="POST" class="flex flex-col bg-gray-50 rounded-xl border border-gray-100 hover:border-indigo-300 hover:shadow-md transition overflow-hidden group">
                                @csrf
                                <input type="hidden" name="topic" value="{{ $topic }}">
                                
                                <button type="submit" class="px-4 py-3 bg-white text-gray-800 font-bold text-left group-hover:text-indigo-700 transition border-b border-gray-100 flex justify-between items-center">
                                    {{ $topic }}
                                    <span class="text-indigo-400 opacity-0 group-hover:opacity-100 transition">&rarr;</span>
                                </button>
                                
                                <select name="size_preference" class="text-xs bg-transparent border-none text-gray-500 focus:ring-0 py-2 cursor-pointer outline-none w-full font-medium">
                                    <option value="duo">1-on-1 Buddy</option>
                                    <option value="group">Group (Max 4)</option>
                                </select>
                            </form>
                            @endforeach
                        </div>
                    </div>

                    <!-- RIGHT: Custom Room -->
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl border border-indigo-100 p-6 sm:p-8 shadow-sm flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-10 h-10 bg-indigo-100 text-indigo-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">Create Custom Room</h3>
                            </div>
                            <p class="text-sm text-gray-600 mb-6">Studying for a specific exam? Create a private room and generate an invite link for your friends.</p>
                        </div>
                        
                        <form action="{{ route('rooms.store') }}" method="POST" class="bg-white p-5 rounded-xl border border-white/50 shadow-sm space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Room Name</label>
                                <input type="text" name="title" placeholder="e.g., University Finals Prep" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            </div>
                            
                            <div class="flex gap-4">
                                <div class="w-1/3">
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5 uppercase tracking-wide">Size</label>
                                    <input type="number" name="max_capacity" value="4" min="2" max="10" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                </div>
                                <div class="w-2/3 flex items-end">
                                    <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition shadow-sm h-[42px]">
                                        Create Room
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- Real-Time Queue Teleportation -->
    <script type="module">
        const currentUserId = {{ auth()->id() }};

        Echo.private(`user.${currentUserId}`)
            .listen('MatchFound', (event) => {
                window.location.href = `/rooms/${event.roomId}`;
            });
    </script>
</x-app-layout>