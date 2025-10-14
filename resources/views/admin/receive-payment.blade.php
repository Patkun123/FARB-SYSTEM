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
                    <x-nav-link :href="route('admin.receive-payment')" :active="request()->routeIs('admin.receive-payment')">
                        {{ __('Receive Payment') }}
                    </x-nav-link>
                </nav>
            </div>
        </div>
    </header>

    <title>Receive Payment</title>

    <main class="pb-6 px-4 sm:px-6 lg:px-8" x-data="invoiceTable()" x-init="init()">
        <div class="max-w-7xl mx-auto mt-6">
            <!-- Receive Payment Section -->
            <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-200">
                <h1 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <img class="w-10 h-10" src="{{ asset('img/billing_summaries.png') }}" alt="Billing">
                    Receive Payment
                </h1>

                <!-- Loading -->
                <div x-show="loading" class="mb-4 p-4 bg-blue-50 rounded-lg">
                    <p class="text-blue-700 flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading invoices...
                    </p>
                </div>

                <!-- Error -->
                <div x-show="error" class="mb-4 p-4 bg-red-50 rounded-lg">
                    <p class="text-red-700" x-text="error"></p>
                </div>

                <!-- Search -->
                <div class="mb-4" x-show="!loading && !error">
                    <input type="text" placeholder="Search Invoice or Client..." x-model="search"
                        class="w-full sm:w-1/3 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none" />
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Invoice #</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Charge To</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="invoice in paginatedInvoices" :key="invoice.id">
                                <tr :class="invoice.status.toLowerCase() === 'void' ? 'bg-red-200' : 'hover:bg-gray-50'">
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.invoice_number"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.date"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.chargeTo"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.department"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700 text-right" x-text="invoice.qty"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.unit"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.description"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700 text-right" x-text="invoice.unitPrice"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.status"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700 text-right" x-text="invoice.total"></td>
                                    <td class="px-4 py-2 text-sm text-indigo-600 hover:underline text-center cursor-pointer"
                                        @click="selectInvoice(invoice)">Select</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>


                <!-- Pagination -->
                <div class="mt-4 flex justify-end items-center gap-2" x-show="!loading && invoices.length > 0">
                    <button @click="prevPage" :disabled="currentPage === 1"
                        class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50">Prev</button>
                    <span x-text="currentPage + ' / ' + totalPages"></span>
                    <button @click="nextPage" :disabled="currentPage === totalPages"
                        class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50">Next</button>
                </div>

                <!-- Payment Form -->
                <div x-show="showForm" class="mt-8 p-6 border border-gray-300 rounded-xl bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Input Payment for Invoice #<span x-text="selectedInvoice?.invoice_number"></span></h2>
                    
                    <!-- Invoice Items -->
                    <div class="mb-6 p-4 bg-white rounded-lg border border-gray-200" x-show="selectedInvoice && selectedInvoice.items?.length">
                        <h3 class="text-md font-medium text-gray-700 mb-3">Invoice Items</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="item in selectedInvoice.items" :key="item.id">
                                        <tr class="border-b border-gray-200">
                                            <td class="px-3 py-2 text-sm text-gray-700" x-text="item.qty"></td>
                                            <td class="px-3 py-2 text-sm text-gray-700" x-text="item.unit"></td>
                                            <td class="px-3 py-2 text-sm text-gray-700" x-text="item.description"></td>
                                            <td class="px-3 py-2 text-sm text-gray-700" x-text="item.unitPrice"></td>
                                            <td class="px-3 py-2 text-sm text-gray-700" x-text="item.amount"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Official Receipt (O.R.)</label>
                            <input type="text" x-model="selectedInvoice.or" placeholder="Enter OR Number"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Amount Paid</label>
                            <input type="number" x-model="selectedInvoice.amountPaid" placeholder="Enter Amount"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date Paid</label>
                            <input type="date" x-model="selectedInvoice.datePaid"
                                class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none" />
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button @click="cancelForm"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                        <button @click="savePayment"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function invoiceTable() {
            return {
                search: '',
                currentPage: 1,
                perPage: 5,
                invoices: [],
                selectedInvoice: null,
                showForm: false,
                loading: true,
                error: null,

                async init() { await this.fetchInvoices(); },

                async fetchInvoices() {
                    try {
                        this.loading = true;
                        this.error = null;
                        const res = await fetch("{{ route('admin.receive-payment.api.invoices') }}");
                        if (!res.ok) throw new Error('Failed to fetch invoices');
                        this.invoices = await res.json();
                    } catch (err) {
                        console.error(err);
                        this.error = 'Failed to load invoices. Please try again.';
                    } finally { this.loading = false; }
                },

                get filteredInvoices() {
                    return this.invoices.filter(inv =>
                        String(inv.invoice_number).toLowerCase().includes(this.search.toLowerCase()) ||
                        inv.chargeTo?.toLowerCase().includes(this.search.toLowerCase()) ||
                        inv.description?.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                get paginatedInvoices() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredInvoices.slice(start, start + this.perPage);
                },

                get totalPages() {
                    return Math.max(1, Math.ceil(this.filteredInvoices.length / this.perPage));
                },

                nextPage() { if (this.currentPage < this.totalPages) this.currentPage++; },
                prevPage() { if (this.currentPage > 1) this.currentPage--; },

                selectInvoice(invoice) {
                    this.selectedInvoice = { ...invoice, or: '', amountPaid: '', datePaid: '' };
                    this.showForm = true;
                },

                cancelForm() { this.showForm = false; this.selectedInvoice = null; },

                async savePayment() {
                    if (!this.selectedInvoice?.or || !this.selectedInvoice?.amountPaid || !this.selectedInvoice?.datePaid) {
                        alert('Please fill all payment fields'); return;
                    }
                    try {
                        const res = await fetch("{{ route('admin.receive-payment.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                invoice_id: this.selectedInvoice.id,
                                or_number: this.selectedInvoice.or,
                                amount_paid: this.selectedInvoice.amountPaid,
                                date_paid: this.selectedInvoice.datePaid
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            alert('Payment saved for Invoice #' + this.selectedInvoice.invoice_number);
                            this.cancelForm();
                            await this.fetchInvoices();
                        } else alert('Failed to save payment: ' + data.message);
                    } catch (err) {
                        console.error(err);
                        alert('Error saving payment. Please try again.');
                    }
                }
            }
        }
    </script>
</x-admin-layout>
