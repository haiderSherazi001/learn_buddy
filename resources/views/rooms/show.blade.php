<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            
            <!-- Left Side: Title & Streak Badge -->
            <div class="flex items-center space-x-4">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $room->title }}
                </h2>
                
                @if($room->streak_count > 0)
                    <div class="px-3 py-1 bg-orange-100 border border-orange-300 text-orange-700 font-bold rounded-full text-sm flex items-center shadow-sm">
                        🔥 {{ $room->streak_count }} Day Streak
                    </div>
                @else
                    <div class="px-3 py-1 bg-gray-100 text-gray-500 font-medium rounded-full text-sm flex items-center">
                        🧊 No active streak
                    </div>
                @endif
            </div>
            
            <!-- THE LEAVE ROOM BUTTON -->
            <form action="{{ route('rooms.leave', $room->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to leave this cohort?');">
                @csrf
                <button type="submit" class="text-sm px-4 py-2 bg-red-100 text-red-600 font-bold rounded-lg hover:bg-red-200 transition shadow-sm">
                    Leave Room
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg font-medium">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- MAIN CONTENT AREA -->
                <div class="md:col-span-2 space-y-6">
                    
                    <!-- WEEKLY COMMITMENTS -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500">
                        <div class="p-6 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-bold text-gray-900">Set Weekly Commitment</h3>
                            <form action="{{ route('commitments.store', $room->id) }}" method="POST" class="mt-4 flex gap-3">
                                @csrf
                                <input type="text" name="goal" placeholder="e.g., Finish Chapter 4 of the course" required class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700">Post Goal</button>
                            </form>
                            @error('goal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="p-6 space-y-4">
                            @forelse($room->commitments as $commitment)
                            <div class="p-4 border rounded-lg shadow-sm transition {{ $commitment->is_completed ? 'opacity-60 bg-gray-50 border-gray-200' : 'bg-white border-gray-300' }}">
                                <div class="flex justify-between items-start">
                                    
                                    <!-- The Goal Text -->
                                    <div>
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="font-bold text-gray-900">{{ $commitment->user->name }}</span>
                                            <span class="text-xs text-gray-400">{{ $commitment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="{{ $commitment->is_completed ? 'text-gray-500 line-through' : 'text-gray-700' }}">
                                            🎯 {{ $commitment->goal }}
                                        </p>
                                    </div>

                                    <!-- The Actions (Securely Hidden from Buddies) -->
                                    @if(auth()->id() === $commitment->user_id)
                                        <form action="{{ route('commitments.toggle', $commitment->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs px-3 py-1 rounded font-bold border transition {{ $commitment->is_completed ? 'bg-gray-200 text-gray-700 border-gray-300 hover:bg-gray-300' : 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100' }}">
                                                {{ $commitment->is_completed ? 'Undo' : 'Mark Done ✔' }}
                                            </button>
                                        </form>
                                    @else
                                        <!-- If it belongs to their buddy, just show a badge if completed -->
                                        @if($commitment->is_completed)
                                            <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded">Completed</span>
                                        @endif
                                    @endif
                                    
                                </div>
                            </div>
                            @empty
                                <p class="text-gray-500 italic text-sm">No commitments set yet. Be the first!</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- DAILY STANDUPS -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Log Daily Standup</h3>
                            <form action="{{ route('standups.store', $room->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">What did you learn/do today?</label>
                                    <textarea name="what_i_did" rows="2" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    @error('what_i_did') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Any blockers? (Optional)</label>
                                    <textarea name="blockers" rows="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-gray-900 text-white rounded-md font-bold hover:bg-gray-800">Submit Standup</button>
                            </form>
                        </div>

                        <div class="p-6 space-y-4">
                            @forelse($room->standups as $standup)
                            <div class="p-4 bg-white border rounded-lg shadow-sm">
                                <!-- Original Standup Content -->
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="font-bold text-gray-900">{{ $standup->user->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $standup->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-700"><strong>Progress:</strong> {{ $standup->what_i_did }}</p>
                                @if($standup->blockers)
                                    <p class="text-red-600 mt-2 text-sm bg-red-50 p-2 rounded border border-red-100"><strong>Blocker:</strong> {{ $standup->blockers }}</p>
                                @endif

                                <!-- The Comments Section -->
                                <div class="mt-4 pt-4 border-t border-gray-100 pl-4 border-l-2 border-indigo-100">
                                    
                                    <!-- List existing comments -->
                                    @foreach($standup->comments as $comment)
                                        <div class="mb-3">
                                            <p class="text-xs text-gray-500 font-bold mb-1">{{ $comment->user->name }} <span class="font-normal text-gray-400">&bull; {{ $comment->created_at->diffForHumans() }}</span></p>
                                            <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded-lg inline-block">{{ $comment->body }}</p>
                                        </div>
                                    @endforeach

                                    <!-- Reply Form -->
                                    <form action="{{ route('comments.store', $standup->id) }}" method="POST" class="mt-3 flex gap-2">
                                        @csrf
                                        <input type="text" name="body" placeholder="Reply or help unblock..." required class="text-sm flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1 px-3">
                                        <button type="submit" class="text-sm px-3 py-1 bg-indigo-50 text-indigo-700 rounded-md font-bold border border-indigo-200 hover:bg-indigo-100 transition">Reply</button>
                                    </form>
                                </div>
                            </div>
                            @empty
                                <p class="text-gray-500 italic text-sm">No standups logged yet. Start working!</p>
                            @endforelse
                        </div>
                    </div>

                </div>

                <!-- RIGHT SIDEBAR: STUDY BUDDIES -->
                <div class="md:col-span-1 space-y-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 self-start">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Cohort Members</h3>
                    <ul class="space-y-3">
                        @foreach($room->users as $member)
                            <li class="flex items-center space-x-3">
                                <!-- Dummy Avatar -->
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                                    {{ strtoupper(substr($member->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $member->name }}
                                        @if($member->id === auth()->id())
                                            <span class="text-xs text-gray-400 font-normal">(You)</span>
                                        @endif
                                    </p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-yellow-400">
                    <div class="p-6 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            📚 Resource Stash
                        </h3>
                        
                        <!-- Add Resource Form -->
                        <form action="{{ route('resources.store', $room->id) }}" method="POST" class="mt-4 space-y-3">
                            @csrf
                            <div>
                                <input type="text" name="title" placeholder="Title (e.g., Laravel Docs)" required class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <input type="url" name="url" placeholder="https://..." required class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('url') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <button type="submit" class="w-full px-4 py-2 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-md font-bold hover:bg-yellow-100 transition text-sm">
                                Save Link
                            </button>
                        </form>
                    </div>

                    <!-- Resource List -->
                    <div class="p-6 space-y-3 max-h-96 overflow-y-auto">
                        @forelse($room->resources as $resource)
                            <div class="p-3 bg-gray-50 border border-gray-100 rounded-lg shadow-sm hover:shadow-md transition">
                                <!-- target="_blank" ensures it opens in a new tab so they don't lose the room -->
                                <a href="{{ $resource->url }}" target="_blank" rel="noopener noreferrer" class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline text-sm block truncate">
                                    🔗 {{ $resource->title }}
                                </a>
                                <div class="text-xs text-gray-500 mt-2 flex justify-between items-center">
                                    <span>By {{ $resource->user->name }}</span>
                                    <span>{{ $resource->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <p class="text-gray-500 italic text-sm">No resources shared yet.</p>
                                <p class="text-xs text-gray-400 mt-1">Drop a helpful YouTube link or tutorial here!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            </div>
        </div>
    </div>
</x-app-layout>