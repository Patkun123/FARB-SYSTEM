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
                    <x-nav-link :href="route('admin.billing')"
                                :active="request()->routeIs('billing')">
                        {{ __('Billing') }}
                    </x-nav-link>
                </div>

                </div>

                <!-- Right Section -->
                    <!-- Logo -->
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10">

                    </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="py-10 space-y-10 bg-gray-50 min-h-screen">
        <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-md hover:shadow-lg transition rounded-2xl p-8 border border-gray-200">

                <!-- Page Title -->
                <h1 class="text-2xl font-bold text-gray-800 mb-8 flex items-center gap-3">
                    <img class="w-10 h-10" src="{{ asset('img/billing.png') }}" alt="Billing">
                    Billing – Statement of Account
                </h1>

                <!-- SOA Details -->
                <form class="space-y-8">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- SOA Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">SOA Title</label>
                            <input type="text" placeholder="Enter SOA title"
                                   class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <!-- Client Company -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Client Company</label>
                            <select class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option>Select Company</option>
                                <option>ABC Corp</option>
                                <option>XYZ Solutions</option>
                            </select>
                        </div>

                        <!-- Department -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Department</label>
                            <select class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option>Select Department</option>
                                <option>Finance</option>
                                <option>Operations</option>
                            </select>
                        </div>



                    </div>


                    <!-- Covered Date -->
                    <div class="space-y-4 border-t pt-6">
                        <h2 class="text-lg font-semibold text-gray-700">Covered Date</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="date"
                                       class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="date"
                                       class="mt-1 w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        </div>
                    </div>


                    <!-- SOA Format (Employee Info + Statement) -->
                    <div class="space-y-4 border-t pt-6">
                        <h2 class="text-lg font-semibold text-gray-700">SOA Format</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <input type="text" placeholder="Name"
                                   class="rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <input type="text" placeholder="Position"
                                   class="rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <input type="text" placeholder="Department"
                                   class="rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <input type="text" placeholder="Company Name"
                                   class="rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <textarea rows="3" placeholder="Statement text..."
                                  class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>

                    <!-- Department Payroll Totals -->
                    <div class="space-y-4 border-t pt-6">
                        <h2 class="text-lg font-semibold text-gray-700">Payroll Breakdown</h2>
                        <div class="space-y-3">
                            <div class="flex gap-3">
                                <input type="text" placeholder="Department (ex. MRF)"
                                       class="flex-1 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <input type="text" placeholder="Total Payroll (₱)"
                                       class="flex-1 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <!-- Add another row button -->
                            <button type="button" class="text-indigo-600 text-sm hover:underline">+ Add Department</button>
                        </div>

                        <!-- Auto Total -->
                        <div class="mt-4 text-right">
                            <span class="text-gray-700 font-semibold">Total Amount Due: </span>
                            <span class="text-xl text-indigo-600 font-bold">₱ 54,735.27</span>
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="border-t pt-6">
                        <label class="block text-sm font-medium text-gray-700">Billing Date</label>
                        <input type="date"
                               class="mt-1 w-1/3 rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>

                    <!-- Items Table -->
                    <div class="space-y-4 border-t pt-6">
                        <h2 class="text-lg font-semibold text-gray-700">Items / Services</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200 text-sm text-left">
                                <thead class="bg-gray-50 text-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 border">Description</th>
                                        <th class="px-4 py-2 border">Quantity</th>
                                        <th class="px-4 py-2 border">Unit</th>
                                        <th class="px-4 py-2 border">Unit Price</th>
                                        <th class="px-4 py-2 border">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="px-4 py-2 border">
                                            <input type="text" placeholder="Ex. Soap"
                                                   class="w-full border-gray-300 rounded-lg">
                                        </td>
                                        <td class="px-4 py-2 border">
                                            <input type="number" placeholder="0"
                                                   class="w-full border-gray-300 rounded-lg">
                                        </td>
                                        <td class="px-4 py-2 border">
                                            <input type="text" placeholder="pcs"
                                                   class="w-full border-gray-300 rounded-lg">
                                        </td>
                                        <td class="px-4 py-2 border">
                                            <input type="number" placeholder="0.00"
                                                   class="w-full border-gray-300 rounded-lg">
                                        </td>
                                        <td class="px-4 py-2 border text-right">₱ 0.00</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="mt-2 text-indigo-600 text-sm hover:underline">+ Add Row</button>
                    </div>

                    <!-- Submit -->
                    <div class="pt-6 text-right">
                        <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                            Save Billing
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</x-admin-layout>
