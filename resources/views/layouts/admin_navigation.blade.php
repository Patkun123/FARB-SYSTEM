<aside class="fixed top-0 left-0 h-screen w-64 bg-white border-r shadow-lg flex flex-col">
    <!-- User Header -->
    <div class="flex items-center gap-3 px-4 py-5 border-b bg-gray-50">
        <img src="{{ asset('img/control-panel.png') }}" alt="avatar" class="w-12 h-12 rounded-full border">
        <div>
            <p class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</p>
            <p class="text-xs text-gray-500">{{ Auth::user()->role ?? 'Admin' }}</p>
        </div>
    </div>

    <!-- Navigation -->
    <div class="flex-1 overflow-y-auto px-2 py-4 " x-data="{ openBilling: {{ request()->routeIs('admin.billing*') ? 'true' : 'false' }}, openReceivables: false, openUsers: false }">
        <nav class="space-y-1">

            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition
               {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                <img class="w-5 h-5" src="{{ asset('img/dashboard.png') }}" alt="Dashboard">
                Dashboard
            </a>

            <!-- Clients -->
            <a href="{{ route('admin.clients.index')}}"
               class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition
               {{ request()->routeIs('admin.clients.index') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                <img class="w-5 h-5" src="{{ asset('img/client.png') }}" alt="Clients">
                Clients
            </a>

            <!-- Manage Billing Dropdown -->
            <div class="mt-4">
                <button @click="openBilling = !openBilling"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg transition
                        {{ request()->routeIs('admin.billing*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span class="flex items-center gap-2">
                        <span class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-200 text-gray-600 text-xs">M</span>
                        Manage Billing
                    </span>
                    <svg :class="{ 'rotate-180': openBilling }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openBilling" x-cloak class="mt-2 space-y-1 pl-6">
                    <a href="{{ route('admin.billing')}}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition
                       {{ request()->routeIs('admin.billing') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                        <img class="w-5 h-5" src="{{ asset('img/billing.png') }}" alt="Billing"> Billing
                    </a>
                    <a href="{{ route('admin.billing-summary')}}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition
                       {{ request()->routeIs('admin.billing-summary') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                        <img class="w-5 h-5" src="{{ asset('img/billing_summaries.png') }}" alt="Billing Summary"> Billing Summaries
                    </a>
                    <a href="{{ route('admin.invoice')}}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition
                       {{ request()->routeIs('admin.invoice') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                        <img class="w-5 h-5" src="{{ asset('img/invoice.png') }}" alt="Invoice"> Invoice
                    </a>
                    <a href="{{ route('admin.invoice-records')}}" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition
                       {{ request()->routeIs('admin.invoice-records') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                        <img class="w-5 h-5" src="{{ asset('img/invoice_history.png') }}" alt="Invoice Records"> Invoice Records
                    </a>
                </div>
            </div>

            <!-- Receivables Dropdown -->
            <div class="mt-4">
                <button @click="openReceivables = !openReceivables"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg transition
                        {{ request()->routeIs('admin.receivables*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span class="flex items-center gap-2">
                        <span class="w-6 h-6 flex items-center justify-center rounded-full bg-gray-200 text-gray-600 text-xs">R</span>
                        Receivables
                    </span>
                    <svg :class="{ 'rotate-180': openReceivables }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openReceivables" x-cloak class="mt-2 space-y-1 pl-6">
                    <a href="#" class="block px-3 py-2 text-sm rounded-lg transition">Receivables</a>
                    <a href="#" class="block px-3 py-2 text-sm rounded-lg transition">Receivable Reports</a>
                </div>
            </div>

            <!-- Manage Users Dropdown -->
            <div class="mt-4">
                <button @click="openUsers = !openUsers"
                        class="w-full flex items-center justify-between px-3 py-2 text-sm rounded-lg transition
                        {{ request()->routeIs('admin.users*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }}">
                    <span class="flex items-center gap-2">
                        <img class="w-5 h-5" src="{{ asset('img/settings.png') }}" alt="Settings"> Settings
                    </span>
                    <svg :class="{ 'rotate-180': openUsers }" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="openUsers" x-cloak class="mt-2 space-y-1 pl-6">
                    <a href="#" class="block px-3 py-2 text-sm rounded-lg transition">Profile Settings</a>
                    <a href="#" class="block px-3 py-2 text-sm rounded-lg transition">System Users</a>
                    <a href="#" class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition">
                        <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 text-indigo-600">New</span> Register New User
                    </a>
                </div>
            </div>

        </nav>
    </div>

    <!-- Logout Button -->
    <div class="px-4 py-3 ">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-sm font-medium rounded-lg text-red-600 hover:bg-red-50 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"/>
                </svg>
                Logout
            </button>
        </form>
    </div>

<!-- Footer -->
<div class="px-4 py-4 border-t bg-gray-50 flex flex-col gap-2">
    <div class="flex items-center gap-3">
        <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10">
        <div class="flex flex-col">
            <p class="text-xs font-semibold text-gray-800">FARB SYSTEM</p>
            <p class="text-[11px] text-gray-500">Multi Purpose Cooperative</p>
        </div>
    </div>
    <p class="text-[10px] text-gray-400 mt-2">© {{ date('Y') }} FARB SYSTEM. All rights reserved.</p>
</div>

</aside>
