<x-app-layout>
    @vite(['resources/js/room.js'])
    <x-slot name="header">
        <div id="room-data" data-room-id="{{ $room->id }}" data-user-id="{{ auth()->id() }}" class="hidden"></div>
        <x-room.header :room="$room" />
    </x-slot>

    <!-- ⚡ Sleek, Mac-style scrollbars -->
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
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg shadow-sm font-medium animate-fade-in">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="flex flex-col lg:flex-row gap-8 items-start">
                
                <!-- ⚡ LEFT MAIN AREA (65%) -->
                <div class="w-full lg:w-2/3 space-y-8">
                    <!-- WEEKLY COMMITMENTS -->
                    <x-room.commitments :room="$room" />

                    <!-- DAILY STANDUPS -->
                    <x-room.standups :room="$room" />
                </div>

                <!-- ⚡ RIGHT SIDEBAR (35%) - Now with 4 Tabs! -->
                <div class="w-full lg:w-1/3 sticky top-6">

                    <!-- Sidebar Navigation Tabs -->
                    <div class="flex space-x-1 bg-gray-200/60 p-1 rounded-xl mb-4 shadow-inner">
                        <button onclick="switchTab('chat')" id="btn-chat" class="flex-1 py-1.5 px-2 rounded-lg text-xs sm:text-sm font-bold bg-white text-indigo-600 shadow-sm transition">
                            💬 Chat
                        </button>
                        <button onclick="switchTab('members')" id="btn-members" class="flex-1 py-1.5 px-2 rounded-lg text-xs sm:text-sm font-semibold text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition">
                            👥 Members
                        </button>
                        <button onclick="switchTab('resources')" id="btn-resources" class="flex-1 py-1.5 px-2 rounded-lg text-xs sm:text-sm font-semibold text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition">
                            📚 Stash
                        </button>
                        <button onclick="switchTab('activity')" id="btn-activity" class="flex-1 py-1.5 px-2 rounded-lg text-xs sm:text-sm font-semibold text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition">
                            🔔 Log
                            <span id="activity-badge" class="hidden absolute top-1 right-2 w-2.5 h-2.5 bg-red-500 border-2 border-white rounded-full animate-bounce"></span>
                        </button>
                    </div>

                    <!-- Tab 1: CHAT (Default) - Now it gets all the space! -->
                    <div id="tab-chat" class="space-y-6 block animate-fade-in">
                        <x-room.chat-box :room="$room" />
                    </div>

                    <!-- Tab 2: MEMBERS (Hidden by default) -->
                    <div id="tab-members" class="space-y-6 hidden animate-fade-in">
                        <x-room.members :room="$room" />
                    </div>

                    <!-- Tab 3: RESOURCES (Hidden by default) -->
                    <div id="tab-resources" class="space-y-6 hidden animate-fade-in">
                        <x-room.resources :room="$room" />
                    </div>

                    <!-- ⚡ NEW Tab 4: ACTIVITY LOG (Hidden by default) -->
                    <div id="tab-activity" class="space-y-6 hidden animate-fade-in">
                        <x-room.activity-log :room="$room" />
                    </div>

                </div>
            </div> 

        </div>
    </div>

    <!-- ⚡ Tiny Script to handle the 4 Tabs -->
    <script>
        function switchTab(tabName) {
            // 1. Hide all tab contents
            document.getElementById('tab-chat').classList.add('hidden');
            document.getElementById('tab-members').classList.add('hidden');
            document.getElementById('tab-resources').classList.add('hidden');
            document.getElementById('tab-activity').classList.add('hidden'); // ⚡ NEW

            // 2. Reset all buttons to gray/inactive style
            const btns = ['chat', 'members', 'resources', 'activity']; // ⚡ NEW
            btns.forEach(btn => {
                const el = document.getElementById('btn-' + btn);
                el.className = "flex-1 py-1.5 px-2 rounded-lg text-xs sm:text-sm font-semibold text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition";
            });

            // 3. Show the selected tab content
            document.getElementById('tab-' + tabName).classList.remove('hidden');

            // 4. Highlight the active button
            const activeBtn = document.getElementById('btn-' + tabName);
            activeBtn.className = "flex-1 py-1.5 px-2 rounded-lg text-xs sm:text-sm font-bold bg-white text-indigo-600 shadow-sm transition";
            
            if (tabName === 'activity') {
                document.getElementById('activity-badge').classList.add('hidden');
            }
        }
    </script>
</x-app-layout>