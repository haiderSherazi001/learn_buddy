@props(['room'])

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