<x-admin-layout>
    <title>Receivable Records</title>

    <main class="pb-6 px-4 sm:px-6 lg:px-8" x-data="paymentRecords({{ $payments->toJson() }})">
        <div class="max-w-7xl mx-auto mt-6">
            <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-200">
                <!-- Page Title -->
                <h1 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <img class="w-10 h-10" src="{{ asset('img/billing_summaries.png') }}" alt="Billing">
                    Receivable Records
                </h1>

                <!-- Search Bar -->
                <div class="mb-4">
                    <input type="text" placeholder="Search Invoice or Client..." x-model="search"
                        class="w-full sm:w-1/3 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none" />
                </div>

                <!-- Payment Records Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Charge to (Client)</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">O.R.</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Paid</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="invoice in paginatedRecords()" :key="invoice.id">
                                <tr :class="invoice.status === 'Void' ? 'bg-red-200' : 'hover:bg-gray-50'">
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.invoice_number"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.invoice_date"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.client_name"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.internal_dept || '-'"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.total_amount"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.status"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.or_number || '-'"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.amount_paid || '-'"></td>
                                    <td class="px-4 py-2 text-sm text-gray-700" x-text="invoice.date_paid || '-'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Controls -->
                <div class="mt-4 flex justify-end items-center gap-2">
                    <button @click="prevPage" :disabled="currentPage === 1"
                        class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50">Prev</button>
                    <span x-text="currentPage + ' / ' + totalPages()"></span>
                    <button @click="nextPage" :disabled="currentPage === totalPages()"
                        class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50">Next</button>
                </div>
            </div>
        </div>
    </main>

    <script>
        function paymentRecords(initialRecords) {
            return {
                search: '',
                currentPage: 1,
                perPage: 5,
                records: initialRecords,
                paginatedRecords() {
                    const filtered = this.records.filter(r =>
                        r.invoice_number?.toLowerCase().includes(this.search.toLowerCase()) ||
                        r.client_name?.toLowerCase().includes(this.search.toLowerCase())
                    );
                    const start = (this.currentPage - 1) * this.perPage;
                    return filtered.slice(start, start + this.perPage);
                },
                totalPages() {
                    return Math.ceil(this.records.filter(r =>
                        r.invoice_number?.toLowerCase().includes(this.search.toLowerCase()) ||
                        r.client_name?.toLowerCase().includes(this.search.toLowerCase())
                    ).length / this.perPage) || 1;
                },
                nextPage() { if (this.currentPage < this.totalPages()) this.currentPage++ },
                prevPage() { if (this.currentPage > 1) this.currentPage-- }
            }
        }
    </script>
</x-admin-layout>
