<x-guest-layout>
    <div class="mb-6 text-sm text-gray-300 text-center">
        {{ __('Forgot your password? No problem. Just enter your email and we’ll send you a reset link.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-green-400" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-white" />
            <x-text-input id="email" class="block mt-1 w-full bg-gray-800/50 text-gray-100 border-gray-600 focus:border-gray-400 focus:ring-gray-400 rounded-md"
                          type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400" />
        </div>

        <div class="flex items-center justify-end">
            <button type="submit" class="px-6 py-2 rounded-lg metallic-btn text-sm font-semibold">
                {{ __('Email Password Reset Link') }}
            </button>
        </div>
    </form>
</x-guest-layout>
