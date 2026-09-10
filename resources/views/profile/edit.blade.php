<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight tracking-tight">
            {{ __('Account Settings') }}
        </h2>
    </x-slot>

    <div class="py-10 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                
                <!-- ⚡ LEFT COLUMN: Profile Summary & Actions -->
                <div class="md:col-span-4 lg:col-span-3 space-y-6">
                    <div class="bg-white shadow-sm rounded-2xl border border-gray-100 p-6 flex flex-col items-center text-center relative overflow-hidden">
                        
                        <!-- Decorative Background -->
                        <div class="absolute top-0 left-0 w-full h-24 bg-gradient-to-r from-indigo-500 to-purple-600"></div>

                        <!-- Avatar Display -->
                        <div class="relative mt-8 mb-3">
                            @if(Auth::user()->avatar)
                                <img class="h-24 w-24 rounded-full object-cover ring-4 ring-white shadow-md" src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}">
                            @else
                                <div class="h-24 w-24 rounded-full bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-3xl border-4 border-white shadow-md">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <!-- ⚡ NEW: Instant Avatar Controls -->
                        <div class="flex items-center gap-2 mb-4">
                            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" x-data>
                                @csrf
                                @method('patch')
                                
                                <input type="hidden" name="name" value="{{ Auth::user()->name }}">
                                <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                                <input type="hidden" name="education" value="{{ Auth::user()->education }}">
                                <input type="hidden" name="skills" value="{{ Auth::user()->skills }}">
                                <input type="hidden" name="phone" value="{{ Auth::user()->phone }}">
                                <input type="hidden" name="bio" value="{{ Auth::user()->bio }}">
                                
                                <input type="file" name="avatar" id="avatar_upload" class="hidden" accept="image/*" x-on:change="$el.closest('form').submit()">
                                
                                <label for="avatar_upload" class="cursor-pointer text-xs font-bold text-indigo-700 bg-indigo-100 hover:bg-indigo-200 px-3 py-1.5 rounded-full transition shadow-sm">
                                    {{ Auth::user()->avatar ? 'Change' : 'Upload' }}
                                </label>
                            </form>

                            @if(Auth::user()->avatar)
                                <form method="POST" action="{{ route('profile.update') }}">
                                    @csrf
                                    @method('patch')
                                    
                                    <input type="hidden" name="name" value="{{ Auth::user()->name }}">
                                    <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                                    <input type="hidden" name="education" value="{{ Auth::user()->education }}">
                                    <input type="hidden" name="skills" value="{{ Auth::user()->skills }}">
                                    <input type="hidden" name="phone" value="{{ Auth::user()->phone }}">
                                    <input type="hidden" name="bio" value="{{ Auth::user()->bio }}">
                                    <input type="hidden" name="remove_avatar" value="1">
                                    
                                    <button type="submit" class="text-xs font-bold text-red-700 bg-red-100 hover:bg-red-200 px-3 py-1.5 rounded-full transition shadow-sm">
                                        Remove
                                    </button>
                                </form>
                            @endif
                        </div>

                        <!-- User Info -->
                        <h3 class="text-lg font-bold text-gray-900">{{ Auth::user()->name }}</h3>
                        <p class="text-sm text-gray-500 mb-6">{{ Auth::user()->email }}</p>

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <button type="submit" class="w-full flex justify-center items-center gap-2 px-4 py-2 bg-gray-50 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-100 hover:text-gray-900 transition border border-gray-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- ⚡ RIGHT COLUMN: Settings Forms -->
                <div class="md:col-span-8 lg:col-span-9 space-y-6">
                    
                    <!-- Profile Information -->
                    <div class="p-6 sm:p-8 bg-white shadow-sm rounded-2xl border border-gray-100">
                        <div class="max-w-2xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="p-6 sm:p-8 bg-white shadow-sm rounded-2xl border border-gray-100">
                        <div class="max-w-2xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>

                    <!-- Danger Zone (Delete Account) -->
                    <div class="p-6 sm:p-8 bg-red-50/50 shadow-sm rounded-2xl border border-red-100">
                        <div class="max-w-2xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>