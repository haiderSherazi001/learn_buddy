@props(['room'])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-blue-400 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Activity Log</h3>
                        <ul id="activity-log" class="space-y-3 max-h-48 overflow-y-auto">
                            @forelse($room->events as $event)
                                <li class="text-sm border-l-2 pl-3 py-1 {{ $event->type === 'leave' ? 'border-red-400 text-red-700 bg-red-50' : 'border-blue-400 text-blue-700 bg-blue-50' }} rounded-r">
                                    <span class="block font-medium">{{ $event->message }}</span>
                                    <span class="text-xs opacity-75">{{ $event->created_at->diffForHumans() }}</span>
                                </li>
                            @empty
                                <p class="text-xs text-gray-500 italic">No recent activity.</p>
                            @endforelse
                        </ul>
                    </div>