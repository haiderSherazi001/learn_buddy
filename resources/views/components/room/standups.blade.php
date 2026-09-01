@props(['room'])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Log Daily Standup</h3>
                            <form action="{{ route('standups.store', $room->id) }}" method="POST" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">What did you learn/do today?</label>
                                    <textarea name="what_i_did" rows="2" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    @error('what_i_did') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Any blockers? (Optional)</label>
                                    <textarea name="blockers" rows="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-gray-900 text-white rounded-md font-bold hover:bg-gray-800">Submit Standup</button>
                            </form>
                        </div>

                        <div class="p-6 space-y-4">
                            @forelse($room->standups as $standup)
                            <div class="p-4 bg-white border rounded-lg shadow-sm">
                                <!-- Original Standup Content -->
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="font-bold text-gray-900">
                                        {{ $standup->user->name }}
                                        @if(!$room->users->contains($standup->user_id))
                                            <span class="text-red-400 text-xs italic font-normal ml-1">(Left Cohort)</span>
                                        @endif
                                    </span>
                                    <span class="text-xs text-gray-400">{{ $standup->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-700"><strong>Progress:</strong> {{ $standup->what_i_did }}</p>
                                @if($standup->blockers)
                                    <p class="text-red-600 mt-2 text-sm bg-red-50 p-2 rounded border border-red-100"><strong>Blocker:</strong> {{ $standup->blockers }}</p>
                                @endif

                                <!-- The Comments Section -->
                                <div class="mt-4 pt-4 border-t border-gray-100 pl-4 border-l-2 border-indigo-100">
                                    
                                    <!-- List existing comments -->
                                    @foreach($standup->comments as $comment)
                                        <div class="mb-3">
                                            <p class="text-xs text-gray-500 font-bold mb-1">
                                                {{ $comment->user->name }} 
                                                @if(!$room->users->contains($comment->user_id))
                                                    <span class="text-red-400 font-normal italic mr-1">(Left Cohort)</span>
                                                @endif
                                                <span class="font-normal text-gray-400">&bull; {{ $comment->created_at->diffForHumans() }}</span>
                                            </p>
                                            <p class="text-sm text-gray-700 bg-gray-50 p-2 rounded-lg inline-block">{{ $comment->body }}</p>
                                        </div>
                                    @endforeach

                                    <!-- Reply Form -->
                                    <form action="{{ route('comments.store', $standup->id) }}" method="POST" class="mt-3 flex gap-2">
                                        @csrf
                                        <input type="text" name="body" placeholder="Reply or help unblock..." required class="text-sm flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1 px-3">
                                        <button type="submit" class="text-sm px-3 py-1 bg-indigo-50 text-indigo-700 rounded-md font-bold border border-indigo-200 hover:bg-indigo-100 transition">Reply</button>
                                    </form>
                                </div>
                            </div>
                            @empty
                                <p class="text-gray-500 italic text-sm">No standups logged yet. Start working!</p>
                            @endforelse
                        </div>
                    </div>