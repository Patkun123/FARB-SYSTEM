<x-billing-app>

    <title>Invoice</title>

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
                    <x-nav-link :href="route('billing.invoice')" :active="request()->routeIs('billing.invoice')">
                        {{ __('Invoice') }}
                    </x-nav-link>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main -->
    <main class="py-6 bg-gray-50 min-h-screen" x-data="invoiceApp()" x-init="init()">
        <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-2xl p-8 border border-gray-200">

                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <img class="w-10 h-10" src="{{ asset('img/invoice.png') }}" alt="Billing">
                        Invoice
                    </h1>
                    <span class="text-gray-700 font-medium">Invoice #:
                        <span class="text-indigo-600" x-text="invoiceNumber"></span>
                    </span>
                </div>

                <!-- Invoice Info -->
                <div class="border-b pb-4 mb-6">
                    <h2 class="text-gray-700 font-semibold mb-2">Invoice Information</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Invoice Date</label>
                            <input type="date" x-model="invoiceDate"
                                   class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-indigo-200">
                        </div>
                    </div>
                </div>

                <!-- Charge To -->
                <div class="border-b pb-4 mb-6">
                    <h2 class="text-gray-700 font-semibold mb-2">Charge To (Client)</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Client Company</label>
                            <select x-model="selectedCompany" @change="fetchDepartments"
                                    class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-indigo-200">
                                <option value="">-- Select Company --</option>
                                <template x-for="client in clients" :key="client.id">
                                    <option :value="client.id" x-text="client.company"></option>
                                </template>
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 font-medium mb-1">Client Department</label>
                            <select x-model="selectedDepartment"
                                    class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-indigo-200"
                                    :disabled="!selectedCompany">
                                <option value="">-- Select Department --</option>
                                <template x-for="dept in departments" :key="dept">
                                    <option :value="dept" x-text="dept"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Handled By -->
                <div class="border-b pb-4 mb-6">
                    <h2 class="text-gray-700 font-semibold mb-2">Department</h2>
                    <div>
                        <input type="text" x-model="internalDepartment"
                               placeholder="Enter department name"
                               class="w-full border-gray-300 rounded-lg p-2 focus:ring focus:ring-indigo-200">
                    </div>
                </div>

                <!-- Add Rows Control -->
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <label class="font-medium text-gray-700">How many rows to add:</label>
                    <input type="number" min="1" x-model.number="rowsToAdd"
                           class="w-24 border-gray-300 rounded-lg p-1.5 focus:ring-indigo-200 text-center">
                    <button @click="addRow(rowsToAdd)"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        ➕ Add Rows
                    </button>
                    <span class="text-gray-500 text-sm ml-2">
                        Total Rows: <strong x-text="rows.length"></strong>
                    </span>
                </div>

                <!-- Invoice Table -->
                <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm mt-4 bg-white">
                    <table class="min-w-full border-collapse text-sm">
                        <thead class="bg-indigo-50 text-gray-700 uppercase text-xs font-semibold">
                            <tr>
                                <th class="p-3 text-center border-b border-gray-200">Qty</th>
                                <th class="p-3 text-center border-b border-gray-200">Unit</th>
                                <th class="p-3 text-left border-b border-gray-200">Description</th>
                                <th class="p-3 text-right border-b border-gray-200">Unit Price</th>
                                <th class="p-3 text-right border-b border-gray-200">Amount</th>
                                <th class="p-3 text-center border-b border-gray-200">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index) in rows" :key="index">
                                <tr class="odd:bg-white even:bg-gray-50 hover:bg-indigo-50 transition">
                                    <td class="p-2 text-center">
                                        <input type="number" x-model.number="row.qty" @input="updateAmount(row)"
                                               class="w-20 border-gray-300 rounded-lg text-center p-1.5 focus:ring-indigo-300">
                                    </td>
                                    <td class="p-2 text-center">
                                        <input type="text" x-model="row.unit"
                                               class="w-20 border-gray-300 rounded-lg text-center p-1.5 focus:ring-indigo-300">
                                    </td>
                                    <td class="p-2">
                                        <input type="text" x-model="row.description"
                                               class="w-full border-gray-300 rounded-lg p-1.5 focus:ring-indigo-300">
                                    </td>
                                    <td class="p-2 text-right">
                                        <input type="number" x-model.number="row.unitPrice" step="0.01"
                                               @input="updateAmount(row)"
                                               class="w-28 border-gray-300 rounded-lg text-right p-1.5 focus:ring-indigo-300">
                                    </td>
                                    <td class="p-2 text-right font-medium">
                                        <span x-text="row.amount.toFixed(2)"></span>
                                    </td>
                                    <td class="p-2 text-center">
                                        <button @click="deleteRow(index)"
                                                class="px-2 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
                                            🗑️
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-indigo-50 font-semibold text-gray-800">
                            <tr>
                                <td colspan="4" class="p-3 text-right border-t">Total:</td>
                                <td class="p-3 text-right border-t" x-text="totalAmount.toFixed(2)"></td>
                                <td class="border-t"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Save -->
                <div class="flex justify-end mt-6">
                    <button @click="manualSave"
                            class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 transition">
                        💾 Save Invoice
                    </button>
                </div>
            </div>
        </div>
    </main>

    <!-- Alpine + Axios -->

    <script>
        function invoiceApp() {
            return {
                invoiceNumber: '',
                invoiceDate: '',
                internalDepartment: '',
                clients: [],
                departments: [],
                selectedCompany: '',
                selectedDepartment: '',
                rows: [],
                rowsToAdd: 1,

                get totalAmount() {
                    return this.rows.reduce((sum, r) => sum + (r.amount || 0), 0);
                },

                async init() {
                    const res = await axios.get('{{ route('billing.invoice.clients') }}');
                    this.clients = res.data;

                    // ✅ Fetch next invoice number indicator
                    const nextNum = await axios.get('{{ route('billing.invoice.nextInvoiceNumber') }}');
                    this.invoiceNumber = nextNum.data.next_invoice_number;
                },

                async fetchDepartments() {
                    if (!this.selectedCompany) {
                        this.departments = [];
                        return;
                    }
                    const res = await axios.get(`/admin/invoice/departments/${this.selectedCompany}`);
                    this.departments = res.data;
                    this.selectedDepartment = '';
                },

                addRow(count = 1) {
                    count = Math.max(1, count);
                    for (let i = 0; i < count; i++) {
                        this.rows.push({ qty: 0, unit: '', description: '', unitPrice: 0, amount: 0 });
                    }
                },

                updateAmount(row) {
                    row.amount = (row.qty || 0) * (row.unitPrice || 0);
                },

                deleteRow(i) {
                    this.rows.splice(i, 1);
                },

                async manualSave() {
                    if (!this.selectedCompany || !this.selectedDepartment || this.rows.length === 0) {
                        return alert("⚠️ Please fill out all fields and add at least one item.");
                    }

                    try {
                        const payload = {
                            invoice_date: this.invoiceDate,
                            client_id: this.selectedCompany,
                            client_department_id: null,
                            internal_department: this.internalDepartment,
                            rows: this.rows
                        };
                        // ✅ After saving, fetch the next invoice number again
                    const nextNum = await axios.get('{{ route('billing.invoice.nextInvoiceNumber') }}');
                    this.invoiceNumber = nextNum.data.next_invoice_number;

                        const res = await axios.post('{{ route('billing.invoice.store') }}', payload);
                        alert(`✅ ${res.data.message}\nInvoice ID: ${res.data.invoice_id}`);
                        this.invoiceNumber++;
                        this.rows = [];
                        this.selectedCompany = '';
                        this.selectedDepartment = '';
                        this.internalDepartment = '';
                    } catch (err) {
                        console.error(err);
                        alert("❌ Failed to save invoice. Please check console for details.");
                    }
                }
            }
        }
    </script>
</x-billing-app>