<x-admin-layout>
    <title>Admin Dashboard</title>

    <!-- Header -->
    <header class="sticky top-0 left-0 right-0 bg-white/90 backdrop-blur border-b border-gray-200 shadow-sm z-30 transition">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Left Section: Sidebar Toggle + Logo -->
                <div class="flex items-center gap-3">
                    <button
                        @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-xl text-gray-600 hover:bg-gray-100 focus:ring-2 focus:ring-blue-200 transition-all"
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
                    <a href="{{ route('dashboard') }}" class="flex items-center group">
                        <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10 object-contain transition-transform group-hover:scale-110">
                        <div class="ml-2 leading-tight">
                            <span class="text-lg font-semibold text-gray-800 tracking-tight">FARB SYSTEM</span>
                            <p class="text-[11px] text-gray-500">Multi Purpose Cooperative</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden sm:flex sm:space-x-8">
                    <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-10 bg-gradient-to-br from-gray-50 via-white to-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

            <!-- Title -->
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Admin Dashboard</h1>
                <div class="text-sm text-gray-500">Updated {{ now()->format('M d, Y') }}</div>
            </div>

            <!--  Cover Photo & Profile Section -->

            <section class="relative rounded-2xl overflow-hidden shadow-xl border border-gray-200 bg-white/80 backdrop-blur-lg">
                <!-- Cover Photo with Centered Overlay -->
                <div class="relative h-56 sm:h-64 bg-cover bg-center transition-all duration-700 hover:scale-[1.02]"
                    style="background-image: url('{{ asset('img/logo.jpg') }}');">
                    <!-- Dark overlay for better contrast -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/40 to-transparent"></div>

                    <!-- Centered Welcome Text -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white px-4">
                        <h3 class="text-2xl sm:text-3xl font-bold drop-shadow-md">
                            Welcome back, {{ Auth::user()->name ?? 'Admin' }}!
                        </h3>
                        <p class="text-sm sm:text-base opacity-90 mt-2 drop-shadow-md">
                            Manage and monitor your cooperative system efficiently
                        </p>
                    </div>
                </div>

                <!-- Profile Info Section -->
                <div class="relative px-6 sm:px-8 pb-8 -mt-16 sm:-mt-20">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                        <div class="flex items-center gap-5">
                            <!-- Profile Image with Gradient Glow -->
                            <div class="relative">
                                <div class="absolute inset-0 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 blur-md opacity-60"></div>
                                <img src="{{ Auth::user()->profile_photo_url ?? asset('img/control-panel.png') }}"
                                    class="relative w-32 h-32 rounded-full border-[5px] border-white shadow-lg object-cover hover:scale-105 transition-transform duration-300"
                                    alt="User Photo">
                            </div>

                            <!-- User Details with Background Blur -->
                            <div class="bg-white/80 backdrop-blur-md px-4 py-2 rounded-xl shadow-sm border border-gray-100">
                                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">
                                    {{ Auth::user()->name ?? 'Admin User' }}
                                </h2>
                                <p class="text-sm text-gray-600 mt-1">
                                    {{ Auth::user()->email ?? 'admin@example.com' }}
                                </p>
                                <span
                                    class="inline-flex items-center gap-1 mt-2 px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 border border-blue-200">
                                    <i class="fa-solid fa-shield-alt text-[11px]"></i> Administrator
                                </span>
                            </div>
                        </div>

                        <!-- Profile Actions -->
                        <div class="flex items-center gap-3">
                            <a href="{{ route('admin.profile-settings') }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl shadow-md hover:bg-blue-700 hover:shadow-lg active:scale-95 transition-all duration-300">
                                <i class="fa-solid fa-pen"></i>
                                Profile Settings
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
                @php
                    $cards = [
                        ['label' => 'Total Users', 'value' => $userCount ?? '120', 'color' => 'blue', 'icon' => 'fa-users'],
                        ['label' => 'Total Clients', 'value' => $clientCount ?? '45', 'color' => 'green', 'icon' => 'fa-user-tie'],
                    ];
                @endphp

                @foreach ($cards as $card)
                    <div class="bg-white p-6 rounded-2xl shadow-md border border-gray-100 hover:shadow-lg transition-all duration-300 flex items-center gap-5 group">
                        <div class="p-4 bg-{{ $card['color'] }}-100 text-{{ $card['color'] }}-600 rounded-2xl group-hover:scale-110 transition-transform">
                            <i class="fa-solid {{ $card['icon'] }} text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">{{ $card['label'] }}</p>
                            <h2 class="text-3xl font-semibold text-gray-800">{{ $card['value'] }}</h2>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Billing & Receivable Summary -->
            <section class="mb-10">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Billing & Receivables Overview</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Cards -->
                    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-50 to-indigo-100 p-6 rounded-2xl shadow-md border border-indigo-100 flex items-center gap-4 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                        <div class="p-4 bg-indigo-600 text-white rounded-2xl group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-file-invoice text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Total Invoices</p>
                            <h2 class="text-3xl font-bold text-indigo-700">{{ $invoiceTotal ?? '150' }}</h2>
                            <p class="text-xs text-gray-500">All generated invoices</p>
                        </div>
                    </div>

                    <div class="relative overflow-hidden bg-gradient-to-br from-emerald-50 to-emerald-100 p-6 rounded-2xl shadow-md border border-emerald-100 flex items-center gap-4 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                        <div class="p-4 bg-emerald-600 text-white rounded-2xl group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-circle-check text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Paid Bills</p>
                            <h2 class="text-3xl font-bold text-emerald-700">{{ $paidBills ?? '90' }}</h2>
                            <p class="text-xs text-gray-500">Fully settled payments</p>
                        </div>
                    </div>

                    <div class="relative overflow-hidden bg-gradient-to-br from-red-50 to-red-100 p-6 rounded-2xl shadow-md border border-red-100 flex items-center gap-4 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                        <div class="p-4 bg-red-600 text-white rounded-2xl group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-exclamation-circle text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Unpaid Bills</p>
                            <h2 class="text-3xl font-bold text-red-700">{{ $unpaidBills ?? '45' }}</h2>
                            <p class="text-xs text-gray-500">Pending client payments</p>
                        </div>
                    </div>

                    <div class="relative overflow-hidden bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-2xl shadow-md border border-gray-200 flex items-center gap-4 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                        <div class="p-4 bg-gray-600 text-white rounded-2xl group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-ban text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Void Bills (Past Due)</p>
                            <h2 class="text-3xl font-bold text-gray-800">{{ $voidBills ?? '12' }}</h2>
                            <p class="text-xs text-gray-500">Overdue and cancelled</p>
                        </div>
                    </div>

                    <div class="relative overflow-hidden bg-gradient-to-br from-yellow-50 to-yellow-100 p-6 rounded-2xl shadow-md border border-yellow-100 flex items-center gap-4 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                        <div class="p-4 bg-yellow-600 text-white rounded-2xl group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-file-invoice-dollar text-2xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium">Billing Summary</p>
                            <h2 class="text-3xl font-bold text-yellow-700">{{ $billingCount ?? '23' }}</h2>
                            <p class="text-xs text-gray-500">Summary of all billing activity</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- System Overview -->
            <div class="bg-white p-8 rounded-2xl shadow-md border border-gray-100">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">System Overview</h3>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Monitor your organization’s performance at a glance. You can integrate charts, graphs, and recent logs here for a live overview of user, client, and billing activities.
                </p>
                <div class="mt-6 border-t border-gray-100 pt-6 text-center text-gray-400 text-sm">
                    Charts and analytics integration coming soon.
                </div>
            </div>
        </div>
    </main>
</x-admin-layout>
