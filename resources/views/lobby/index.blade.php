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
                            <form action="{{ route('queue.join') }}" method="POST">
                                @csrf
                                <input type="hidden" name="topic" value="{{ $topic }}">
                                <button type="submit" class="px-4 py-2 bg-blue-50 text-blue-700 rounded-full font-medium hover:bg-blue-100 transition">
                                    {{ $topic }}
                                </button>
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