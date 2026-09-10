<section>
    <header class="border-b border-gray-100 pb-4 mb-6">
        <h2 class="text-xl font-bold text-gray-900">
            {{ __('Profile Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __("Update your account's profile information, email address, and professional details.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-8">
        @csrf
        @method('patch')

        <!-- 1. Basic Details (2-Column Grid) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="sm:col-span-2">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-2">Basic Info</h3>
            </div>

            <div>
                <x-input-label for="name" :value="__('Full Name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-gray-50 focus:bg-white transition-colors" :value="old('name', $user->name)" required autocomplete="name" />
                <x-input-error class="mt-2" :messages="$errors->get('name')" />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email Address')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-gray-50 focus:bg-white transition-colors" :value="old('email', $user->email)" required autocomplete="username" />
                <x-input-error class="mt-2" :messages="$errors->get('email')" />
            </div>

            <div>
                <x-input-label for="phone" :value="__('Phone Number')" />
                <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full bg-gray-50 focus:bg-white transition-colors" :value="old('phone', $user->phone)" placeholder="+1 (555) 000-0000" />
                <x-input-error class="mt-2" :messages="$errors->get('phone')" />
            </div>
        </div>

        <!-- 2. Professional Details (2-Column Grid) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 border-t border-gray-100">
            <div class="sm:col-span-2">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-2">Professional Info</h3>
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="education" :value="__('Education & Qualifications')" />
                <x-text-input id="education" name="education" type="text" class="mt-1 block w-full bg-gray-50 focus:bg-white transition-colors" :value="old('education', $user->education)" required placeholder="e.g. BS Computer Science" />
                <x-input-error class="mt-2" :messages="$errors->get('education')" />
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="skills" :value="__('Core Skills (Comma separated)')" />
                <x-text-input id="skills" name="skills" type="text" class="mt-1 block w-full bg-gray-50 focus:bg-white transition-colors" :value="old('skills', $user->skills)" required placeholder="e.g. Laravel, Vue, Tailwind" />
                <x-input-error class="mt-2" :messages="$errors->get('skills')" />
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="bio" :value="__('Short Bio (Optional)')" />
                <textarea id="bio" name="bio" rows="3" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm bg-gray-50 focus:bg-white transition-colors resize-none" placeholder="Tell us a little about yourself...">{{ old('bio', $user->bio) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('bio')" />
            </div>
        </div>

        <!-- 3. Save Actions -->
        <div class="flex items-center gap-4 pt-6 border-t border-gray-100">
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-sm">
                {{ __('Save Changes') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-medium text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-100">
                    <span class="mr-1">✓</span> {{ __('Saved successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>