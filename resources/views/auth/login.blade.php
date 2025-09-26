<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address or Username -->
        <div>
            <x-input-label for="login" :value="__('Email or Username')" class="text-white font-medium" />
            <x-text-input
                id="login"
                class="block mt-1 w-full rounded-lg bg-white/10 text-gray-100 border border-white/30 focus:border-indigo-400 focus:ring focus:ring-indigo-300 focus:ring-opacity-40 placeholder-gray-400"
                type="text"
                name="login"
                :value="old('login')"
                required autofocus
                autocomplete="username"
                placeholder="Enter your email or username"
            />
            <x-input-error :messages="$errors->get('login')" class="mt-2 text-red-400" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="text-white font-medium" />
            <x-text-input
                id="password"
                class="block mt-1 w-full rounded-lg bg-white/10 text-gray-100 border border-white/30 focus:border-indigo-400 focus:ring focus:ring-indigo-300 focus:ring-opacity-40 placeholder-gray-400"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Enter your password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-400 bg-white/10 text-indigo-400 focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-300">{{ __('Remember me') }}</span>
            </label>
        </div>

        <!-- Actions -->
        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-400 hover:text-indigo-300 transition ease-in-out duration-150" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="metallic-btn px-6 py-2 rounded-lg !text-black text-sm font-semibold">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
