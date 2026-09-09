<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- PROFILE DATA -->
        <div class="mt-8 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-bold text-gray-900">Profile Setup </h3>

            <!-- Avatar Upload -->
            <div class="mt-4">
                <x-input-label for="avatar" :value="__('Profile Picture')" />
                <input id="avatar" type="file" name="avatar" accept="image/*" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" />
                <x-input-error :messages="$errors->get('avatar')" class="mt-2" />
            </div>

            <!-- Phone -->
            <div class="mt-4">
                <x-input-label for="phone" :value="__('Phone Number')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" placeholder="e.g., +1 234 567 8900" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- Education -->
            <div class="mt-4">
                <x-input-label for="education" :value="__('Education & Qualifications')" />
                <x-text-input id="education" class="block mt-1 w-full" type="text" name="education" :value="old('education')" placeholder="e.g., BS Computer Science" />
                <x-input-error :messages="$errors->get('education')" class="mt-2" />
            </div>

            <!-- Skills -->
            <div class="mt-4">
                <x-input-label for="skills" :value="__('Core Skills')" />
                <x-text-input id="skills" class="block mt-1 w-full" type="text" name="skills" :value="old('skills')" placeholder="e.g., Laravel, Vue, Tailwind" />
                <x-input-error :messages="$errors->get('skills')" class="mt-2" />
            </div>

            <!-- Bio -->
            <div class="mt-4">
                <x-input-label for="bio" :value="__('Short Bio')" />
                <textarea id="bio" name="bio" rows="3" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" placeholder="Tell your future study buddies a bit about yourself...">{{ old('bio') }}</textarea>
                <x-input-error :messages="$errors->get('bio')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-8">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>