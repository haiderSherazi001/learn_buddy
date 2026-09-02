<x-app-layout>
    @vite(['resources/js/room.js'])
    <x-slot name="header">
        <div id="room-data" data-room-id="{{ $room->id }}" data-user-id="{{ auth()->id() }}" class="hidden"></div>
        <x-room.header :room="$room" />
    </x-slot>

    <!-- ⚡ NEW: Sleek, Mac-style scrollbars for internal panels -->
    <style>
        .scrollable-panel::-webkit-scrollbar {
            width: 6px;
        }
        .scrollable-panel::-webkit-scrollbar-track {
            background: transparent;
        }
        .scrollable-panel::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 20px;
        }
        .scrollable-panel:hover::-webkit-scrollbar-thumb {
            background-color: #94a3b8;
        }
    </style>

    <div class="py-8">
        <!-- ⚡ NEW: Wider container (max-w-[1400px]) for a true app feel -->
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm font-medium animate-fade-in">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="flex flex-col lg:flex-row gap-8 items-start">
                
                <!-- MAIN CONTENT AREA (Left Side - 65%) -->
                <div class="w-full lg:w-2/3 space-y-8">
                    
                    <!-- WEEKLY COMMITMENTS -->
                    <x-room.commitments :room="$room" />

                    <!-- DAILY STANDUPS -->
                    <x-room.standups :room="$room" />

                </div>

                <!-- RIGHT SIDEBAR (35% - ⚡ NEW: Sticky to viewport!) -->
                <div class="w-full lg:w-1/3 space-y-6 sticky top-6">

                    <!-- Cohort Members -->
                    <x-room.members :room="$room" />

                    <!-- 💬 LIVE CHAT BOX -->
                    <x-room.chat-box :room="$room" />

                    <!-- 🔔 ACTIVITY LOG -->
                    <x-room.activity-log :room="$room" />

                    <!-- 📚 RESOURCE STASH -->
                    <x-room.resources :room="$room" />

                </div> 
            </div> 

        </div>
    </div>
</x-app-layout>