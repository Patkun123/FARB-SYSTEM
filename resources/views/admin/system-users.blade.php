<x-admin-layout>
    <!-- Header -->
    <div class="bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-12 h-12">
                    <span class="ml-2 text-xl font-semibold text-gray-800">FARB SYSTEM <BR><p class="text-[11px] text-gray-500">Multi Purpose Cooperative</p></span>
                </a>

                <!-- Navigation Links -->
                <div class="hidden sm:flex sm:space-x-6">
                    <x-nav-link :href="route('admin.system-users')" :active="request()->routeIs('billing-summary')">
                        {{ __('System Users') }}
                    </x-nav-link>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-200">
                <!-- Page Title -->
                <h1 class="text-1xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                     <img class="w-10 h-10" src="{{ asset('img/billing_summaries.png') }}" alt="Billing">
                    System Users
                </h1>
            </div>
        </div>
    </main>


</x-admin-layout>
