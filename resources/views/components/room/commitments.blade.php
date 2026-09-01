@props(['room'])

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-t-4 border-indigo-500">
                        <div class="p-6 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-bold text-gray-900">Set Weekly Commitment</h3>
                            <form action="{{ route('commitments.store', $room->id) }}" method="POST" class="mt-4 flex gap-3">
                                @csrf
                                <input type="text" name="goal" placeholder="e.g., Finish Chapter 4 of the course" required class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md font-bold hover:bg-indigo-700">Post Goal</button>
                            </form>
                            @error('goal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="p-6 space-y-4">
                            @forelse($room->commitments as $commitment)
                            <div class="p-4 border rounded-lg shadow-sm transition {{ $commitment->is_completed ? 'opacity-60 bg-gray-50 border-gray-200' : 'bg-white border-gray-300' }}">
                                <div class="flex justify-between items-start">
                                    
                                    <!-- The Goal Text -->
                                    <div>
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="font-bold text-gray-900">
                                                {{ $commitment->user->name }}
                                                @if(!$room->users->contains($commitment->user_id))
                                                    <span class="text-red-400 text-xs italic font-normal ml-1">(Left Cohort)</span>
                                                @endif
                                            </span>
                                            <span class="text-xs text-gray-400">{{ $commitment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="{{ $commitment->is_completed ? 'text-gray-500 line-through' : 'text-gray-700' }}">
                                            🎯 {{ $commitment->goal }}
                                        </p>
                                    </div>

                                    <!-- The Actions (Securely Hidden from Buddies) -->
                                    @if(auth()->id() === $commitment->user_id)
                                        <form action="{{ route('commitments.toggle', $commitment->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-xs px-3 py-1 rounded font-bold border transition {{ $commitment->is_completed ? 'bg-gray-200 text-gray-700 border-gray-300 hover:bg-gray-300' : 'bg-green-50 text-green-700 border-green-200 hover:bg-green-100' }}">
                                                {{ $commitment->is_completed ? 'Undo' : 'Mark Done ✔' }}
                                            </button>
                                        </form>
                                    @else
                                        <!-- If it belongs to their buddy, just show a badge if completed -->
                                        @if($commitment->is_completed)
                                            <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded">Completed</span>
                                        @endif
                                    @endif
                                    
                                </div>
                            </div>
                            @empty
                                <p class="text-gray-500 italic text-sm">No commitments set yet. Be the first!</p>
                            @endforelse
                        </div>
                    </div>