<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('The Study Lobby') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('info'))
                <div class="mb-4 p-4 bg-blue-100 text-blue-700 rounded-lg">
                    {{ session('info') }}
                </div>
            @endif
            
            <!-- Welcome Message -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Find your study buddy.</h1>
                <p class="text-gray-600 mt-2">Join a queue for popular topics or create a custom room.</p>
            </div>

            <!-- CREATE CUSTOM ROOM SECTION -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8 border border-gray-200">
                <div class="p-6 bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">✨ Create a Custom Room</h3>
                    <p class="text-sm text-gray-600 mb-4">Studying with friends? Create a private room and share the invite link.</p>
                    
                    <form action="{{ route('rooms.store') }}" method="POST" class="flex flex-col md:flex-row gap-4 items-end">
                        @csrf
                        <div class="flex-1 w-full">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Room Name</label>
                            <input type="text" name="title" placeholder="e.g., University Finals Prep" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div class="w-full md:w-32">
                            <label class="block text-xs font-bold text-gray-700 mb-1">Max Users</label>
                            <input type="number" name="max_capacity" value="4" min="2" max="10" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-md hover:bg-indigo-700 transition shadow-sm">
                            Create Room
                        </button>
                    </form>
                </div>
            </div>

            <!-- MY ACTIVE ROOMS SECTION -->
            @if($myRooms->count() > 0)
                <div class="mb-10">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 border-b pb-2">My Active Cohorts</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($myRooms as $room)
                            <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-bold text-lg text-gray-900">{{ $room->title }}</h3>
                                    
                                    <!-- Badge showing if it's a template match or custom -->
                                    @if($room->type === 'template')
                                        <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded">Auto-Matched</span>
                                    @else
                                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded">Custom</span>
                                    @endif
                                </div>
                                
                                <p class="text-sm text-gray-500 mb-4">
                                    Status: <span class="capitalize font-medium {{ $room->status === 'active' ? 'text-green-600' : 'text-gray-600' }}">{{ $room->status }}</span>
                                </p>
                                
                                <a href="{{ route('rooms.show', $room->id) }}" class="block w-full text-center px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
                                    Enter Room &rarr;
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        
            <!-- Active Queue Alert -->
            @if($activeQueue)
                <div class="mb-8 p-6 bg-yellow-50 border border-yellow-200 rounded-lg flex flex-col sm:flex-row justify-between items-center shadow-sm">
                    <div class="mb-4 sm:mb-0">
                        <h4 class="text-lg font-bold text-yellow-800 flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-yellow-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Searching for a {{ $activeQueue->topic }} Buddy...
                        </h4>
                        <p class="text-sm text-yellow-700 mt-1">We will create a room automatically when someone else joins this queue.</p>
                    </div>
                    
                    <form action="{{ route('queue.leave') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-5 py-2 bg-white border border-red-300 text-red-600 rounded-lg font-bold hover:bg-red-50 transition shadow-sm">
                            Leave Queue
                        </button>
                    </form>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- LEFT SIDE: The Matchmaking Queue (System A) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Fast Track: Join a Queue
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">Click a topic below. Once another user clicks it, we'll auto-generate a room for you both!</p>
                    
                    <div class="flex flex-wrap gap-3">
                        @foreach($popularTopics as $topic)
                        <form action="{{ route('queue.join') }}" method="POST" class="flex flex-col shadow-sm hover:shadow-md transition rounded-lg">
                            @csrf
                            <input type="hidden" name="topic" value="{{ $topic }}">
                            
                            <!-- The Join Button -->
                            <button type="submit" class="px-4 py-3 bg-indigo-50 text-indigo-700 rounded-t-lg font-bold hover:bg-indigo-100 transition border-b border-indigo-100">
                                {{ $topic }}
                            </button>
                            
                            <!-- The Preference Dropdown -->
                            <select name="size_preference" class="text-xs bg-white border border-gray-200 rounded-b-lg text-gray-600 focus:ring-indigo-500 py-2 text-center cursor-pointer outline-none">
                                <option value="duo">1-on-1 Buddy</option>
                                <option value="group">Group (Max 4)</option>
                            </select>
                        </form>
                        @endforeach
                    </div>
                </div>

                <!-- RIGHT SIDE: Custom Room (System B) -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-2 border-dashed border-gray-300">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        Custom Track: Create a Room
                    </h3>
                    <p class="text-sm text-gray-500 mb-6">Learning something niche? Create your own room, set your own goals, and invite a friend.</p>
                    
                    <a href="#" class="inline-block w-full text-center px-4 py-3 bg-gray-900 text-white rounded-lg font-medium hover:bg-gray-800 transition">
                        + Create Custom Room
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>