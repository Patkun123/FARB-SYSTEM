<x-admin-layout>
  <title>Register User</title>
    <!-- Header -->
    <header class="sticky top-0 left-0 right-0 bg-white border-b border-gray-200 shadow-sm z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Left Section: Sidebar Toggle + Logo -->
                <div class="flex items-center gap-3">
                    <!-- Sidebar Toggle -->
                    <button
                        @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none transition"
                    >
                        <!-- Hamburger Icon -->
                        <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <!-- Close Icon -->
                        <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Logo -->
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10 object-contain">
                        <div class="ml-2 leading-tight">
                            <span class="text-lg font-semibold text-gray-800">FARB SYSTEM</span>
                            <p class="text-[11px] text-gray-500">Multi Purpose Cooperative</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden sm:flex sm:space-x-6">
                    <x-nav-link :href="route('admin.register-user')" :active="request()->routeIs('register-user')">
                        {{ __('Register') }}
                    </x-nav-link>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-10 space-y-10 bg-gray-50 min-h-screen sm:px-6 lg:px-6">
        <div class="max-w-4xl mx-auto mt-6">
            <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-200">
                <!-- Page Title -->
                <div class="flex items-center gap-3 mb-8 border-b pb-4">
                    <img class="w-10 h-10" src="{{ asset('img/teamwork.png') }}" alt="Billing">
                    <h1 class="text-2xl font-bold text-gray-800">Register New User</h1>
                </div>

                <form method="POST" action="{{ route('admin.register-user.store') }}" class="space-y-6">
                    @csrf

                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Full Name')" />
                        <x-text-input id="name" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-input-label for="email" :value="__('Email Address')" />
                        <x-text-input id="email" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200" type="email" name="email" :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div>
                        <x-input-label for="role" :value="__('User Role')" />
                        <select id="role" name="role" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-gray-700">
                           <option value="" disabled selected>Select a role</option>
                            <option value="admin">Admin</option>
                            <option value="billing_clerk">Billing Clerk</option>
                            <option value="receivable_clerk">Receivable Clerk</option>

                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation" class="block mt-1 w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring focus:ring-indigo-200"
                            type="password"
                            name="password_confirmation"
                            required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-end pt-4 border-t mt-6">
                        <x-primary-button class="ms-4 px-6 py-2.5 text-sm rounded-lg">
                            {{ __('Register') }}
                        </x-primary-button>
                    </div>
                </form>






            </div>
        </div>
    </main>
</x-admin-layout>
