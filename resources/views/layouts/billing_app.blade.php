<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <title>Billing Panel</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


  <script src="https://cdn.tailwindcss.com/3.4.17"></script>

<body class="bg-gray-100 font-sans">

<div class="flex">
    <!-- Sidebar -->
<!-- Sidebar -->
<aside class="fixed top-0 left-0 h-screen w-64 bg-white border-r flex flex-col justify-between">
    <div>
        <!-- Header -->
        <div class="flex items-center gap-3 px-4 py-5 border-b">
            <img src="{{ asset('img/control-panel.png') }}" alt="avatar" class="w-10 h-10 rounded-full">
            <div>
                <p class="text-sm font-semibold">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">{{ Auth::user()->role ?? 'Billing CLerk' }}</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="mt-4 px-2 space-y-1">
            <a href="#"
               class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg bg-indigo-50 text-indigo-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 12l2-2m0 0l7-7 7 7M13 5v14m-4-4h8"></path>
                </svg>
                Dashboard
            </a>

            <a href="#" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 4.354l6 3.462V20H6V7.816l6-3.462z"></path>
                </svg>
                Clients
            </a>

            <a href="#" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 17v-2a4 4 0 014-4h4"></path>
                    <path d="M13 7h4a4 4 0 010 8h-4"></path>
                </svg>
                Billing
            </a>

            <a href="#" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M9 14h6v7H9zM9 3h6v7H9z"></path>
                </svg>
                Receivables
            </a>

            <!-- Manage Users -->
            <div class="mt-6 px-3 text-xs font-semibold text-gray-500 uppercase">Manage Users</div>

            <a href="#" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                <span class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-200 text-gray-600 text-xs">P</span>
                Profile Settings
            </a>

            <a href="#" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-600">New</span>
                Register New User
            </a>
        </nav>
    </div>

    <!-- Logout + Footer -->
    <div class="px-4 py-4 ">
        <!-- Logout -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium rounded-lg
                       text-red-600 hover:bg-red-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                </svg>
                Logout
            </button>
        </form>

        <!-- Footer -->
        <div class="mt-4 text-center border-t">
            <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10 mx-auto">
            <p class="text-xs font-semibold mt-2">FARB SYSTEM</p>
            <p class="text-[11px] text-gray-500">Multi Purpose Cooperative</p>
        </div>
    </div>
</aside>


    <!-- Main Content -->
    <main class="ml-64 flex-1 p-6 overflow-y-auto">
        @yield('content')
    </main>
</div>

</body>
</html>


