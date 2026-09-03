@props(['room'])
<!-- Added h-[250px] and flex-col for nice fixed layout -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-yellow-400 flex flex-col h-[350px]">
    <div class="p-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            📚 Resource Stash
        </h3>
        
        <!-- ⚡ ADDED ID: resource-form -->
        <form id="resource-form" action="{{ route('resources.store', $room->id) }}" method="POST" class="mt-4 space-y-3">
            @csrf
            <div>
                <!-- ⚡ ADDED ID: resource-title -->
                <input type="text" id="resource-title" name="title" placeholder="Title (e.g., Laravel Docs)" required class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <!-- ⚡ ADDED ID: resource-url -->
                <input type="url" id="resource-url" name="url" placeholder="https://..." required class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit" class="w-full px-4 py-2 bg-yellow-50 text-yellow-700 border border-yellow-200 rounded-md font-bold hover:bg-yellow-100 transition text-sm">
                Save Link
            </button>
        </form>
    </div>

    <!-- ⚡ ADDED ID: resources-list AND class: scrollable-panel -->
    <div id="resources-list" class="flex-1 p-4 space-y-3 overflow-y-auto scrollable-panel bg-white">
        @forelse($room->resources as $resource)
            <div class="p-3 bg-gray-50 border border-gray-100 rounded-lg shadow-sm hover:shadow-md transition">
                <a href="{{ $resource->url }}" target="_blank" rel="noopener noreferrer" class="font-bold text-indigo-600 hover:text-indigo-800 hover:underline text-sm block truncate">
                    🔗 {{ $resource->title }}
                </a>
                <div class="text-xs text-gray-500 mt-2 flex justify-between items-center">
                    <span>
                        By {{ $resource->user->name }}
                        @if(!$room->users->contains($resource->user_id))
                            <span class="text-red-400 italic">(Left)</span>
                        @endif
                    </span>
                    <span>{{ $resource->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <!-- ⚡ ADDED CLASS: empty-resources-text -->
            <div class="text-center py-4 empty-resources-text">
                <p class="text-gray-500 italic text-sm">No resources shared yet.</p>
                <p class="text-xs text-gray-400 mt-1">Drop a helpful YouTube link or tutorial here!</p>
            </div>
        @endforelse
    </div>
</div>