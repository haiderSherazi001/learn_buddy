<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Current Avatar Preview & Upload -->
        <div class="flex items-start gap-4">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar" class="w-16 h-16 rounded-full object-cover border-2 border-emerald-400 shadow-sm">
            @else
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-xl border-2 border-emerald-400 shadow-sm">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            
            <div class="flex-1">
                <x-input-label for="avatar" :value="__('Change Profile Picture')" />
                <input id="avatar" type="file" name="avatar" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer" />
                <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
                
                @if($user->avatar)
                    <div class="mt-3">
                        <label class="inline-flex items-center cursor-pointer group">
                            <input type="checkbox" name="remove_avatar" value="1" class="rounded border-gray-300 text-red-600 shadow-sm focus:ring-red-500 cursor-pointer">
                            <span class="ml-2 text-sm text-red-600 font-medium group-hover:text-red-700 transition">Remove current picture</span>
                        </label>
                    </div>
                @endif
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <!-- Phone -->
        <div>
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <!-- Education -->
        <div>
            <x-input-label for="education" :value="__('Education & Qualifications')" />
            <x-text-input id="education" name="education" type="text" class="mt-1 block w-full" :value="old('education', $user->education)" required />
            <x-input-error class="mt-2" :messages="$errors->get('education')" />
        </div>

        <!-- Skills -->
        <div>
            <x-input-label for="skills" :value="__('Core Skills (Comma separated)')" />
            <x-text-input id="skills" name="skills" type="text" class="mt-1 block w-full" :value="old('skills', $user->skills)" required />
            <x-input-error class="mt-2" :messages="$errors->get('skills')" />
        </div>

        <!-- Bio -->
        <div>
            <x-input-label for="bio" :value="__('Short Bio (Optional)')" />
            <textarea id="bio" name="bio" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">{{ old('bio', $user->bio) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('bio')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>