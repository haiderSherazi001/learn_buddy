@props(['room'])

<!-- ⚡ NEW: Fixed height (400px) and flex layout so the input sticks to the bottom -->
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col h-[600px]">
    
    <div class="p-4 border-b border-gray-200 bg-gray-50">
        <h3 class="text-md font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            Live Chat
        </h3>
    </div>
    
    <!-- ⚡ NEW: flex-1 makes this take up all available space, scrollable-panel applies the custom CSS -->
    <div id="chat-messages" class="flex-1 p-4 space-y-4 overflow-y-auto scrollable-panel bg-white">
        @forelse($room->messages as $message)
            @php
                $isMe = $message->user_id === auth()->id();
                $alignClass = $isMe ? 'items-end' : 'items-start';
                $bubbleClass = $isMe ? 'bg-green-600 text-white rounded-tr-sm' : 'bg-gray-100 text-gray-800 rounded-tl-sm';
            @endphp
            <div class="flex flex-col {{ $alignClass }}">
                <span class="text-[10px] text-gray-400 mb-0.5 mx-1">{{ $message->user->name }}</span>
                <div class="px-3 py-2 text-sm shadow-sm max-w-[85%] break-words rounded-2xl {{ $bubbleClass }}">
                    {{ $message->body }}
                </div>
            </div>
        @empty
            <p class="text-gray-500 italic text-sm text-center mt-4">Say hello to your buddies!</p>
        @endforelse
    </div>

    <!-- Pinned Chat Form -->
    <div class="p-3 bg-gray-50 border-t border-gray-200">
        <form action="{{ route('messages.store', $room->id) }}" method="POST" id="chat-form" class="flex gap-2">
            @csrf
            <input type="text" id="chat-input" name="body" placeholder="Type a message..." required autocomplete="off" class="flex-1 rounded-full border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-full p-2 h-10 w-10 flex items-center justify-center transition shadow-sm">
                <!-- Send Icon -->
                <svg class="w-4 h-4 transform rotate-90" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
            </button>
        </form>
    </div>
    
</div>