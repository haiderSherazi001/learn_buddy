@props(['room'])

<!-- AUDIO PLAYER CSS (Hides volume, 3-dots, and makes it sleek) -->
<style>
    audio::-webkit-media-controls-volume-slider,
    audio::-webkit-media-controls-mute-button {
        display: none !important;
    }
    audio::-webkit-media-controls-overflow-button {
        display: none !important;
    }
    audio::-webkit-media-controls-enclosure {
        background: rgba(255, 255, 255, 0.8);
    }
</style>

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg flex flex-col h-[600px] relative">
    
    <!-- Header -->
    <div class="p-4 border-b border-gray-200 bg-gray-50 z-10">
        <h3 class="text-md font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            Live Chat
        </h3>
    </div>
    
    <!-- Messages Container -->
    <div id="chat-messages" class="flex-1 p-4 space-y-4 overflow-y-auto scrollable-panel bg-white">
        @forelse($room->messages as $message)
            @php
                $isMe = $message->user_id === auth()->id();
                $alignClass = $isMe ? 'items-end' : 'items-start';
                $bubbleClass = $isMe ? 'bg-indigo-600 text-white rounded-tr-sm' : 'bg-gray-100 text-gray-800 rounded-tl-sm';
            @endphp
            <div class="flex flex-col {{ $alignClass }}">
                <span class="text-[10px] text-gray-400 mb-0.5 mx-1">{{ $message->user->name }}</span>
                
                <div class="px-3 py-2 text-sm shadow-sm max-w-[85%] break-words rounded-2xl {{ $bubbleClass }} overflow-hidden">
                    @if($message->type === 'image')
                        <img src="{{ asset('storage/' . $message->body) }}" class="rounded-lg w-full h-auto max-h-64 object-contain cursor-pointer border border-black/10" onclick="window.open(this.src, '_blank')">
                    @elseif($message->type === 'audio')
                        <div class="flex items-center gap-2 py-1">
                            <audio controls controlsList="nodownload noplaybackrate" src="{{ asset('storage/' . $message->body) }}" class="h-10 w-48 rounded-full shadow-sm"></audio>
                        </div>
                    @elseif($message->type === 'document')
                        <a href="{{ asset('storage/' . $message->body) }}" target="_blank" class="flex items-center gap-3 p-2 bg-black/5 rounded-lg hover:bg-black/10 transition">
                            <div class="p-2 bg-indigo-100 text-indigo-600 rounded shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            </div>
                            <span class="font-bold underline truncate">View Document</span>
                        </a>
                    @else
                        {{ $message->body }}
                    @endif
                </div>
            </div>
        @empty
            <p class="text-gray-500 italic text-sm text-center mt-4">Say hello to your buddies!</p>
        @endforelse
    </div>

    <div id="media-preview-container" class="hidden absolute bottom-16 left-0 right-0 bg-white border-t border-gray-200 p-3 shadow-lg-up z-20">
        <div class="flex items-center justify-between bg-gray-50 p-2 rounded-lg border border-gray-200">
            <div class="flex items-center gap-3 overflow-hidden">
                <div id="preview-icon" class="text-indigo-500 shrink-0"></div>
                <span id="preview-filename" class="text-sm font-bold text-gray-700 truncate"></span>
            </div>
            <button type="button" id="cancel-media-btn" class="text-red-500 hover:bg-red-50 p-1.5 rounded-full shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
    </div>

    <!-- Input Area -->
    <div class="p-3 bg-gray-50 border-t border-gray-200 relative z-30">
        <form action="{{ route('messages.store', $room->id) }}" method="POST" id="chat-form" class="flex items-center gap-2">
            @csrf
            
            <input type="file" id="media-upload" class="hidden" accept="*/*">

            <button type="button" id="attach-btn" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-full transition shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
            </button>

            <button type="button" id="record-btn" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-full transition shrink-0">
                <!-- Mic Icon -->
                <svg id="mic-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                <!-- Trash Icon -->
                <svg id="trash-icon" class="w-5 h-5 text-red-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>

            <div id="input-container" class="flex-1 relative">
                <input type="text" id="chat-input" placeholder="Type a message..." autocomplete="off" class="w-full rounded-full border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm px-4 py-2">
                
                <div id="recording-ui" class="hidden absolute inset-0 bg-red-50 rounded-full flex items-center justify-between px-4 border border-red-200">
                    <div class="flex items-center gap-2">
                        <span class="animate-pulse h-2.5 w-2.5 bg-red-500 rounded-full"></span>
                        <span class="text-sm font-bold text-red-600">Recording...</span>
                    </div>
                    <span id="record-time" class="text-xs font-mono text-red-500">0:00</span>
                </div>
            </div>
            
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white rounded-full p-2 h-10 w-10 flex items-center justify-center transition shadow-sm shrink-0">
                <svg class="w-4 h-4 transform rotate-90 translate-x-[1px]" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path></svg>
            </button>
        </form>
    </div>
</div>