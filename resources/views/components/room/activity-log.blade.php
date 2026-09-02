@props(['room'])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500">
    <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center gap-2">
        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
        <h3 class="text-md font-bold text-gray-900">Activity Log</h3>
    </div>
    
    <ul id="activity-log" class="p-4 space-y-3 h-[250px] overflow-y-auto scrollable-panel">
        
        <!-- Assuming you grab the latest 50 events -->
        @forelse($room->events()->latest()->take(50)->get() as $event)
            @php
                $colorClass = $event->type === 'leave' || $event->type === 'error'
                    ? 'border-red-400 text-red-700 bg-red-50' 
                    : ($event->type === 'success' ? 'border-green-400 text-green-700 bg-green-50' : 'border-blue-400 text-blue-700 bg-blue-50');
            @endphp
            <li class="text-sm border-l-2 pl-3 py-1 {{ $colorClass }} rounded-r">
                <span class="block font-medium">{{ $event->message }}</span>
                <span class="text-xs opacity-75">{{ $event->created_at->diffForHumans() }}</span>
            </li>
        @empty
            <li class="text-sm text-gray-500 italic empty-log">No activity yet.</li>
        @endforelse
    </ul>
</div>