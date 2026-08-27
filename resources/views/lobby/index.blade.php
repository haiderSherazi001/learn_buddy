<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('The Study Lobby') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Welcome Message -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Find your study buddy.</h1>
                <p class="text-gray-600 mt-2">Join a queue for popular topics or create a custom room.</p>
            </div>

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
                            <form action="#" method="POST">
                                @csrf
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