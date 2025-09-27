

<x-guest-layout>
    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

    <!-- Password Reset Token -->
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <!-- Email Address -->
    <div>
        <x-input-label for="email" :value="__('Email')" class="text-white font-medium" />
        <x-text-input
            id="email"
            class="block mt-1 w-full rounded-lg bg-white/10 text-gray-100 border border-white/30
                   focus:border-indigo-400 focus:ring focus:ring-indigo-300 focus:ring-opacity-40
                   placeholder-gray-400"
            type="email"
            name="email"
            :value="old('email', $request->email)"
            required autofocus
            autocomplete="username"
            placeholder="Enter your email"
        />
        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
    </div>

    <!-- Password -->
    <div>
        <x-input-label for="password" :value="__('Password')" class="text-white font-medium" />
        <x-text-input
            id="password"
            class="block mt-1 w-full rounded-lg bg-white/10 text-gray-100 border border-white/30
                   focus:border-indigo-400 focus:ring focus:ring-indigo-300 focus:ring-opacity-40
                   placeholder-gray-400"
            type="password"
            name="password"
            required
            autocomplete="new-password"
            placeholder="Enter your new password"
        />
        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
    </div>

    <!-- Confirm Password -->
    <div>
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-white font-medium" />
        <x-text-input
            id="password_confirmation"
            class="block mt-1 w-full rounded-lg bg-white/10 text-gray-100 border border-white/30
                   focus:border-indigo-400 focus:ring focus:ring-indigo-300 focus:ring-opacity-40
                   placeholder-gray-400"
            type="password"
            name="password_confirmation"
            required
            autocomplete="new-password"
            placeholder="Confirm your new password"
        />
        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-400" />
    </div>

    <!-- Actions -->
    <div class="flex items-center justify-end mt-6">
        <x-primary-button class="metallic-btn px-6 py-2 rounded-lg !text-black text-sm font-semibold">
            {{ __('Reset Password') }}
        </x-primary-button>
    </div>
</form>


</x-guest-layout>



