<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $room->title }}
            </h2>
            
            <!-- THE LEAVE ROOM BUTTON -->
            <form action="{{ route('rooms.leave', $room->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to leave this cohort?');">
                @csrf
                <button type="submit" class="text-sm px-4 py-2 bg-red-100 text-red-600 font-bold rounded-lg hover:bg-red-200 transition">
                    Leave Room
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- MAIN CONTENT AREA -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-t-4 border-indigo-500">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Weekly Commitments</h3>
                        <p class="text-gray-500 text-sm mb-4">What is everyone focusing on this week?</p>
                        <div class="p-4 bg-gray-50 border border-dashed border-gray-300 rounded text-center text-gray-400">
                            Forms coming in Task 3...
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Daily Standups</h3>
                        <p class="text-gray-500 text-sm mb-4">Log your daily progress and blockers.</p>
                        <div class="p-4 bg-gray-50 border border-dashed border-gray-300 rounded text-center text-gray-400">
                            Forms coming in Task 3...
                        </div>
                    </div>
                </div>

                <!-- RIGHT SIDEBAR: STUDY BUDDIES -->
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

            </div>
        </div>
    </div>
</x-app-layout>