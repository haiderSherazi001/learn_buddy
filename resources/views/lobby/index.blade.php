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
                            <x-room.card :room="$room" />
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

        Echo.private('lobby')
        .listen('RoomUpdated', (event) => {
            const oldCard = document.getElementById(`room-card-${event.roomId}`);
            if (oldCard) {
                oldCard.outerHTML = event.html;
            }
        });
    </script>
</x-app-layout>