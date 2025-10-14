<x-admin-layout>
    <!-- Header -->
    <header class="sticky top-0 left-0 right-0 bg-white border-b border-gray-200 shadow-sm z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none transition">
                        <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10 object-contain">
                        <div class="ml-2 leading-tight">
                            <span class="text-lg font-semibold text-gray-800">FARB SYSTEM</span>
                            <p class="text-[11px] text-gray-500">Multi Purpose Cooperative</p>
                        </div>
                    </a>
                </div>

                <nav class="hidden sm:flex sm:space-x-6">
                    <x-nav-link :href="route('admin.records')" :active="request()->routeIs('admin.records')">
                        {{ __('Records') }}
                    </x-nav-link>
                </nav>
            </div>
        </div>
    </header>

    <title>Billing & Invoice Records</title>

    <main class="pb-6 px-4 sm:px-6 lg:px-8"
        x-data="invoiceRecords()"
        x-init="fetchInvoices()">

        <div class="max-w-7xl mx-auto mt-6">
            <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-200">

                <!-- Page Title -->
                <h1 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <img class="w-10 h-10" src="{{ asset('img/invoice_history.png') }}" alt="Billing">
                    Invoice Records
                </h1>

                <!-- Search Bar -->
                <div class="mb-4">
                    <input 
                        type="text" 
                        placeholder="Search..." 
                        x-model.debounce.500ms="search"
                        @input="fetchInvoices"
                        class="w-full sm:w-1/3 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none"
                    />
                </div>

                <!-- Invoice Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <template x-for="header in headers" :key="header">
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" x-text="header"></th>
                                </template>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="invoice in invoices" :key="invoice.id">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.invoice_number"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.invoice_date"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.charge_to"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.internal_department ?? 'N/A'"></td>

                                    <!-- Display first item quantity/unit -->
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.items?.[0]?.qty ?? '-'"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.items?.[0]?.unit ?? '-'"></td>

                                    <!-- Description -->
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.description"></td>

                                    <!-- Unit Price and Amount -->
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.items?.[0]?.unit_price ?? '-'"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.items?.[0]?.amount ?? '-'"></td>

                                    <!-- Total -->
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.total_amount"></td>

                                    <!-- Status -->
                                    <td class="px-4 py-2 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs text-white"
                                            :class="{
                                                'bg-yellow-500': invoice.status === 'pending',
                                                'bg-green-500': invoice.status === 'paid',
                                                'bg-red-500': invoice.status === 'void'
                                            }"
                                            x-text="invoice.status.toUpperCase()">
                                        </span>
                                    </td>

                                    <!-- Placeholder OR / Date Paid -->
                                    <td class="px-4 py-2 text-sm text-gray-700">-</td>
                                    <td class="px-4 py-2 text-sm text-gray-700">-</td>

                                    <!-- Action -->
                                    <td class="px-4 py-2 text-sm text-indigo-600 hover:underline cursor-pointer">
                                        <button @click="viewInvoice(invoice)">View</button>
                                    </td>
                                </tr>
                            </template>

                            <!-- Empty State -->
                            <tr x-show="invoices.length === 0">
                                <td colspan="14" class="px-4 py-4 text-center text-gray-500">No invoices found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination (static placeholder for now) -->
                <div class="mt-4 flex justify-end">
                    <nav class="inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                        <a href="#" class="px-3 py-1 border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">Previous</a>
                        <a href="#" class="px-3 py-1 border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">1</a>
                        <a href="#" class="px-3 py-1 border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">Next</a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div 
            x-show="showModal"
            class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50"
            x-transition
        >
            <div class="bg-white rounded-xl shadow-lg max-w-lg w-full p-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-800 flex justify-between items-center">
                    Invoice Details
                    <button @click="showModal = false" class="text-gray-500 hover:text-gray-700 text-xl">&times;</button>
                </h2>

                <template x-if="selectedInvoice">
                    <div>
                        <div class="space-y-2 text-sm text-gray-700 border-b pb-4 mb-4">
                            <p><strong>Invoice #:</strong> <span x-text="selectedInvoice.invoice_number"></span></p>
                            <p><strong>Date:</strong> <span x-text="selectedInvoice.invoice_date"></span></p>
                            <p><strong>Charge To:</strong> <span x-text="selectedInvoice.client?.name ?? 'N/A'"></span></p>
                            <p><strong>Department:</strong> <span x-text="selectedInvoice.internal_department ?? 'N/A'"></span></p>
                            <p><strong>Description:</strong> <span x-text="selectedInvoice.description"></span></p>
                            <p><strong>Total:</strong> ₱<span x-text="selectedInvoice.total_amount"></span></p>
                            <p><strong>Status:</strong> <span x-text="selectedInvoice.status"></span></p>
                        </div>

                        <!-- Options -->
                        <div class="flex flex-col gap-2">
                            <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Edit Invoice</button>
                            <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm">Download PDF</button>
                            <button class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Delete Invoice</button>
                        </div>

                        <div class="mt-6 text-right">
                            <button 
                                @click="showModal = false"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </main>

    <script>
        function invoiceRecords() {
            return {
                invoices: [],
                search: '',
                showModal: false,
                selectedInvoice: null,
                headers: [
                    'Invoice #', 'Date', 'Charge To', 'Internal Dept.',
                    'Qty', 'Unit', 'Description', 'Unit Price',
                    'Amount', 'Total', 'Status', 'O.R.', 'Date Paid', 'Action'
                ],

                async fetchInvoices() {
                    try {
                        const res = await fetch(`/admin/invoices?search=${this.search}`);
                        const data = await res.json();
                        if (data.success) {
                            this.invoices = data.data;
                        }
                    } catch (e) {
                        console.error('Error fetching invoices:', e);
                    }
                },

                viewInvoice(invoice) {
                    this.selectedInvoice = invoice;
                    this.showModal = true;
                }
            }
        }
    </script>

</x-admin-layout>
