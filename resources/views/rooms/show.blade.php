<x-app-layout>
    @vite(['resources/js/room.js'])
    <x-slot name="header">
        <div id="room-data" data-room-id="{{ $room->id }}" data-user-id="{{ auth()->id() }}" class="hidden"></div>
        <x-room.header :room="$room" />
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
                    <x-room.commitments :room="$room" />

                    <!-- DAILY STANDUPS -->
                    <x-room.standups :room="$room" />

                </div>

                <!-- RIGHT SIDEBAR: STUDY BUDDIES, ACTIVITY, RESOURCES -->
                <div class="md:col-span-1 space-y-6">

                    <!-- Cohort Members -->
                    <x-room.members :room="$room" />

                    <!-- 💬 LIVE CHAT BOX -->
                    <x-room.chat-box :room="$room" />

                    <!-- 🔔 ACTIVITY LOG -->
                    <x-room.activity-log :room="$room" />

                    <!-- 📚 RESOURCE STASH -->
                    <x-room.resources :room="$room" />

                </div> <!-- Ends right sidebar wrapper -->
            </div> <!-- Ends grid -->

        </div>
    </div>
</x-app-layout>