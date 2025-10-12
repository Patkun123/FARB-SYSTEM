<x-admin-layout>
    <title>Profile Settings</title>
    <!-- Header -->
    <div class="bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                <!-- Left Section: Toggle + Logo -->
                <div class="flex items-center gap-4">
                    <!-- Toggle Sidebar Button -->
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="p-2 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none transition">
                        <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg"
                             class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg"
                             class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                    <div class="hidden sm:flex sm:space-x-6">
                        <a href="#" class="text-gray-700 hover:text-indigo-600 font-medium">Invoice</a>
                    </div>
                </div>

                <!-- Right Section -->
                <a href="#" class="flex items-center">
                    <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10">
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="py-6 bg-gray-50 min-h-screen">
        <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class=" shadow-md hover:shadow-lg transition rounded-2xl p-8 border border-gray-200">

                <!-- Page Title -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <img class="w-10 h-10" src="{{ asset('img/profile-settings.png') }}" alt="Billing">
                        {{ __('Profile Settings') }}
                    </h1>
                </div>

                      <div class="p-8">

        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Update Profile Info -->
            <div class="p-6 bg-white shadow rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Update Password -->
            <div class="p-6 bg-white shadow rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="p-6 bg-white shadow rounded-xl border border-red-200">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>

            </div>
        </div>
    </main>
</x-admin-layout>
