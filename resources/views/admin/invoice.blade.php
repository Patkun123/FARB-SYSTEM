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
                            {{ __('Invoice') }}
                        </x-nav-link>
                    </div>
                </div>

                <!-- Right Section -->
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10">
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="py-6" x-data="invoiceApp()" x-init="loadData">
        <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-md hover:shadow-lg transition rounded-2xl p-8 border border-gray-200">
                <!-- Page Title -->
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <img class="w-10 h-10" src="{{ asset('img/invoice.png') }}" alt="Billing">
                        Invoice Manager
                    </h1>
                </div>

                <!-- Controls -->
                <div class="flex items-center gap-2 mb-5 flex-wrap">
                    <div class="flex items-center gap-2">
                        <input type="number" min="1" x-model.number="rowsToAdd"
                               class="w-20 border-gray-300 rounded-lg p-1.5 text-right focus:ring focus:ring-indigo-200">
                        <button @click="addRow(rowsToAdd)"
                                class="px-3 py-1.5 bg-green-500 hover:bg-green-600 text-white rounded-lg shadow transition">
                            ➕ Add Row(s)
                        </button>
                    </div>

                    <div class="flex items-center gap-2">
                        <button @click="undo"
                                class="px-3 py-1.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg shadow transition">
                            ↩️ Undo
                        </button>
                        <button @click="redo"
                                class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow transition">
                            ↪️ Redo
                        </button>
                    </div>

                    <span class="ml-auto text-gray-700 font-medium">
                        Total Rows: <span class="text-indigo-600 font-bold" x-text="rows.length"></span>
                    </span>
                </div>

                <!-- Invoice Table -->
                <div class="overflow-x-auto rounded-lg border border-gray-300">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-100 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="border p-2">Invoice #</th>
                                <th class="border p-2">Date</th>
                                <th class="border p-2">Charge To</th>
                                <th class="border p-2">Department</th>
                                <th class="border p-2">QTY</th>
                                <th class="border p-2">Unit</th>
                                <th class="border p-2">Description</th>
                                <th class="border p-2">Unit Price</th>
                                <th class="border p-2">Amount</th>
                                <th class="border p-2">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <template x-for="(row, index) in rows" :key="index">
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="border p-2 text-center font-medium text-gray-700" x-text="row.invoice"></td>
                                    <td class="border p-2">
                                        <input type="date" x-model="row.date" @input="recordChange"
                                               class="w-36 border-gray-300 rounded-lg p-1 focus:ring focus:ring-indigo-200">
                                    </td>
                                    <td class="border p-2">
                                        <input type="text" x-model="row.chargeTo" @input="recordChange"
                                               class="w-36 border-gray-300 rounded-lg p-1 focus:ring focus:ring-indigo-200">
                                    </td>
                                    <td class="border p-2">
                                        <input type="text" x-model="row.department" @input="recordChange"
                                               class="w-36 border-gray-300 rounded-lg p-1 focus:ring focus:ring-indigo-200">
                                    </td>
                                    <td class="border p-2">
                                        <input type="number" min="0" x-model.number="row.qty" @input="updateAmount(row, true)"
                                               class="w-20 border-gray-300 rounded-lg p-1 text-right focus:ring focus:ring-indigo-200">
                                    </td>
                                    <td class="border p-2">
                                        <input type="text" x-model="row.unit" @input="recordChange"
                                               class="w-20 border-gray-300 rounded-lg p-1 focus:ring focus:ring-indigo-200">
                                    </td>
                                    <td class="border p-2">
                                        <input type="text" x-model="row.description" @input="recordChange"
                                               class="w-48 border-gray-300 rounded-lg p-1 focus:ring focus:ring-indigo-200">
                                    </td>
                                    <td class="border p-2">
                                        <input type="number" min="0" step="0.01" x-model.number="row.unitPrice" @input="updateAmount(row, true)"
                                               class="w-28 border-gray-300 rounded-lg p-1 text-right focus:ring focus:ring-indigo-200">
                                    </td>
                                    <td class="border p-2 text-right font-medium text-gray-700">
                                        <span x-text="row.amount.toFixed(2)"></span>
                                    </td>
                                    <td class="border p-2 text-center">
                                        <button @click="deleteRow(index)"
                                                class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded-lg shadow transition">
                                            🗑️ Delete
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 font-bold text-gray-800">
                                <!-- Span all columns before 'Amount' -->
                                <td colspan="8" class="border p-2 text-right">Total Amount:</td>
                                <!-- Amount column -->
                                <td class="border p-2 text-right" x-text="totalAmount.toFixed(2)"></td>
                                <!-- Empty cell for Action column -->
                                <td class="border p-2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end mt-6">
                    <button @click="manualSave"
                            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:bg-indigo-700 active:scale-95 transition transform">
                        💾 Save Invoice
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Alpine.js Script -->
    <script>
        function invoiceApp() {
            return {
                rows: [],
                history: [],
                future: [],
                rowsToAdd: 1,
                lastInvoiceNumber: 700,
                addRow(count = 1) {
                    this.saveState();
                    for (let i = 0; i < count; i++) {
                        this.lastInvoiceNumber++;
                        this.rows.push({
                            invoice: 'SI ' + this.lastInvoiceNumber,
                            date: '',
                            chargeTo: '',
                            department: '',
                            qty: 0,
                            unit: '',
                            description: '',
                            unitPrice: 0,
                            amount: 0
                        });
                    }
                    this.saveData();
                },
                deleteRow(index) {
                    this.saveState();
                    this.rows.splice(index, 1);
                    this.saveData();
                },
                updateAmount(row, track = false) {
                    if (track) this.saveState();
                    row.amount = (row.qty || 0) * (row.unitPrice || 0);
                    this.saveData();
                },
                recordChange() {
                    this.saveState();
                    this.saveData();
                },
                get totalAmount() {
                    return this.rows.reduce((sum, r) => sum + (r.amount || 0), 0);
                },
                saveData() {
                    localStorage.setItem('invoiceRows', JSON.stringify(this.rows));
                    localStorage.setItem('lastInvoiceNumber', this.lastInvoiceNumber);
                },
                manualSave() {
                    this.saveData();
                    alert("✅ Invoice data has been saved successfully!");
                },
                loadData() {
                    const saved = localStorage.getItem('invoiceRows');
                    const lastNum = localStorage.getItem('lastInvoiceNumber');
                    if (saved) {
                        this.rows = JSON.parse(saved);
                    } else {
                        this.rows = [];
                    }
                    if (lastNum) {
                        this.lastInvoiceNumber = parseInt(lastNum);
                    }
                },
                saveState() {
                    this.history.push(JSON.stringify(this.rows));
                    this.future = [];
                },
                undo() {
                    if (this.history.length > 0) {
                        this.future.push(JSON.stringify(this.rows));
                        this.rows = JSON.parse(this.history.pop());
                        this.saveData();
                    }
                },
                redo() {
                    if (this.future.length > 0) {
                        this.history.push(JSON.stringify(this.rows));
                        this.rows = JSON.parse(this.future.pop());
                        this.saveData();
                    }
                }
            }
        }
    </script>
</x-admin-layout>
