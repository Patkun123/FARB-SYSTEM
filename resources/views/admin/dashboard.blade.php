
<!-- resources/views/admin/dashboard.blade.php -->
 <!-- Using the admin-layout component for consistent layout -->
<x-admin-layout>
    <!-- Header -->
    <div class="bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                <!-- Left Section: Toggle + Logo -->
                <div class="flex items-center gap-4">
                    <!-- Toggle Sidebar Button -->
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="p-2 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none transition">
                        <!-- Hamburger Icon -->
                        <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg"
                             class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <!-- Close Icon -->
                        <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg"
                             class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <!-- Logo -->
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10">
                        <div class="ml-2 leading-tight">
                            <span class="block text-lg font-semibold text-gray-800">FARB SYSTEM</span>
                            <span class="block text-xs text-gray-500">Multi Purpose Cooperative</span>
                        </div>
                    </a>
                </div>

                <!-- Right Section: Navigation Links -->
                <div class="hidden sm:flex sm:space-x-6">
                    <x-nav-link :href="route('admin.dashboard')"
                                :active="request()->routeIs('dashboard')">
                        {{ __('DASHBOARD') }}
                    </x-nav-link>
                </div>
            </div>
        </div>
    </div>

<div class="grid grid-cols-5 grid-rows-5 gap-4">
    <div class="col-start-3 row-start-3"><h1 class="text-2xl font-bold">Welcome, Admin!</h1>
    <p class="mt-2 text-gray-600">This is your dashboard.</p></div>
</div>
</x-admin-layout>

