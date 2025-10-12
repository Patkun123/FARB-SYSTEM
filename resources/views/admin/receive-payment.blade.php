<x-admin-layout>
    <title>Receive Payment</title>
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
                    <x-nav-link :href="route('admin.records')" :active="request()->routeIs('records')">
                        {{ __('Records') }}
                    </x-nav-link>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="pb-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto mt-6">
            <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-200">
                <!-- Page Title -->
                <h1 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <img class="w-10 h-10" src="{{ asset('img/billing_summaries.png') }}" alt="Payment">
                    Payment Received
                </h1>
            </div>
        </div>
    </main>
</x-admin-layout>
