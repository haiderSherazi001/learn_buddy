@props(['room'])
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-green-500 flex flex-col h-[400px]">
                        <div class="p-4 border-b border-gray-200 bg-gray-50 flex-shrink-0">
                            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                💬 Live Cohort Chat
                            </h3>
                        </div>

                        <!-- Chat Messages Area -->
                        <div class="p-4 flex-1 overflow-y-auto flex flex-col space-y-3" id="chat-messages">
                            @forelse($room->messages as $message)
                                <!-- Align Right if Auth User, Align Left if Buddy -->
                                <div class="flex flex-col {{ $message->user_id === auth()->id() ? 'items-end' : 'items-start' }}">
                                    <span class="text-[10px] text-gray-400 mb-0.5 mx-1">{{ $message->user->name }}</span>
                                    
                                    <div class="px-3 py-2 text-sm shadow-sm max-w-[85%] break-words {{ $message->user_id === auth()->id() ? 'bg-green-600 text-white rounded-2xl rounded-tr-sm' : 'bg-gray-100 text-gray-800 rounded-2xl rounded-tl-sm' }}">
                                        {{ $message->body }}
                                    </div>
                                </div>
                            @empty
                                <div class="flex-1 flex items-center justify-center">
                                    <p class="text-xs text-gray-400 italic">No messages yet. Say hi! 👋</p>
                                </div>
                            @endforelse
                        </div>

                        <!-- Chat Input Form -->
                        <div class="p-3 bg-white border-t border-gray-100 flex-shrink-0">
                            <form id="chat-form" action="{{ route('messages.store', $room->id) }}" method="POST" class="flex gap-2">
                                @csrf
                                <input type="text" name="body" id="chat-input" placeholder="Type a message..." required autocomplete="off" class="flex-1 text-sm rounded-full border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 px-4">
                                <button type="submit" class="bg-green-600 text-white rounded-full h-9 w-9 flex items-center justify-center hover:bg-green-700 transition shadow-sm shrink-0">
                                    🚀
                                </button>
                            </form>
                        </div>
                    </div>