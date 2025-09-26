<x-admin-layout>
    <!-- Header -->
    <div class="bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Left Section -->
                <div class="flex items-center gap-4">
                    <!-- Toggle Sidebar -->
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

                    <!-- Nav Links -->
                    <div class="hidden sm:flex sm:space-x-6">
                        <x-nav-link :href="route('admin.billing')" :active="request()->routeIs('billing')">
                            {{ __('Billing') }}
                        </x-nav-link>
                    </div>
                </div>

                <!-- Right Section (Logo) -->
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10">
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-md hover:shadow-lg transition rounded-2xl p-8 border border-gray-200">
                <h1 class="text-2xl font-bold text-gray-800 mb-8 flex items-center gap-3">
                    <img class="w-10 h-10" src="{{ asset('img/billing.png') }}" alt="Billing">
                    Billing Summary
                </h1>

                <div x-data="billingApp()" class="space-y-8">
                    <!-- Global Rates Section -->
                    <div>
                        <h2 class="text-xl font-bold mb-4 mt-6">Global Rates</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-6 gap-4">
                            <template x-for="(rate, key) in rates" :key="key">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700" x-text="labels[key]"></label>
                                    <input type="number" step="0.01" x-model.number="rates[key]" @input="saveHistory()"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200 text-sm" />
                                </div>
                            </template>
                        </div>

                        <!-- Employee-Specific Rates -->
                        <h2 class="text-xl font-bold mb-4 mt-8">Employee-Specific Rates</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-300 text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border px-2 py-1">Employee</th>
                                        <th class="border px-2 py-1">Use Custom?</th>
                                        <th class="border px-2 py-1">Reg Hr Rate</th>
                                        <th class="border px-2 py-1">OT Rate</th>
                                        <th class="border px-2 py-1">NP Rate</th>
                                        <th class="border px-2 py-1">HPNP Rate</th>
                                        <th class="border px-2 py-1">Reg Hol Rate</th>
                                        <th class="border px-2 py-1">Spec Hol Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(emp, i) in employees" :key="i">
                                        <tr>
                                            <td class="border px-2 py-1" x-text="emp.name || 'Unnamed'"></td>
                                            <td class="border px-2 py-1 text-center">
                                                <input type="checkbox" x-model="emp.useCustom" @change="saveHistory()" />
                                            </td>
                                            <template x-for="field in ['reg_hr','ot','np','hpnp','reg_hol','spec_hol']" :key="field">
                                                <td class="border px-2 py-1">
                                                    <input type="number" step="0.01"
                                                           x-model.number="emp.customRates[field]"
                                                           @input="saveHistory()"
                                                           :disabled="!emp.useCustom"
                                                           class="w-full text-center border-none focus:ring-0 text-sm"
                                                           placeholder="Global" />
                                                </td>
                                            </template>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Employees Table -->
                    <div>
                        <h2 class="text-xl font-bold mb-4 flex justify-between items-center">
                            Employees
                            <div class="flex gap-2">
                                <button @click="addEmployee()" class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-600">
                                    + Add Employee
                                </button>
                                <button @click="Undo()" class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-600">
                                    Undo
                                </button>
                                <button @click="Redo()" class="bg-green-500 text-white px-3 py-1 rounded-md text-sm hover:bg-green-600">
                                    Redo
                                </button>
                            </div>
                        </h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-300 text-sm">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border px-2 py-1">Name</th>
                                        <th class="border px-2 py-1">Reg Hr</th>
                                        <th class="border px-2 py-1">OT</th>
                                        <th class="border px-2 py-1">NP</th>
                                        <th class="border px-2 py-1">HPNP</th>
                                        <th class="border px-2 py-1">Reg Hol</th>
                                        <th class="border px-2 py-1">Spec Hol</th>
                                        <th class="border px-2 py-1">Total Hours</th>
                                        <th class="border px-2 py-1">Total Pay (₱)</th>
                                        <th class="border px-2 py-1">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(emp, i) in employees" :key="i">
                                        <tr>
                                            <td class="border px-2 py-1">
                                                <input type="text" x-model="emp.name" @input="saveHistory()" class="w-full border-none focus:ring-0 text-sm" />
                                            </td>
                                            <template x-for="field in ['reg_hr','ot','np','hpnp','reg_hol','spec_hol']" :key="field">
                                                <td class="border px-2 py-1">
                                                    <input type="number" step="0.1" x-model.number="emp[field]" @input="saveHistory()"
                                                           class="w-full text-center border-none focus:ring-0 text-sm" />
                                                </td>
                                            </template>
                                            <td class="border px-2 py-1 text-center" x-text="totalHours(emp)"></td>
                                            <td class="border px-2 py-1 text-right font-medium"
                                                x-text="currency(totalPay(emp))"></td>
                                            <td class="border px-2 py-1 text-center">
                                                <button @click="deleteEmployee(i)" class="text-red-600 hover:underline text-sm">
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="bg-gray-100 font-bold">
                                    <tr>
                                        <td class="border px-2 py-2 text-right">Totals</td>
                                        <td class="border px-2 py-2 text-center" x-text="columnTotal('reg_hr')"></td>
                                        <td class="border px-2 py-2 text-center" x-text="columnTotal('ot')"></td>
                                        <td class="border px-2 py-2 text-center" x-text="columnTotal('np')"></td>
                                        <td class="border px-2 py-2 text-center" x-text="columnTotal('hpnp')"></td>
                                        <td class="border px-2 py-2 text-center" x-text="columnTotal('reg_hol')"></td>
                                        <td class="border px-2 py-2 text-center" x-text="columnTotal('spec_hol')"></td>
                                        <td class="border px-2 py-2 text-center" x-text="employees.reduce((sum, emp) => sum + totalHours(emp), 0)"></td>
                                        <td class="border px-2 py-2 text-right" x-text="currency(grandTotal())"></td>
                                        <td class="border px-2 py-2"></td>
                                    </tr>
                                    <tr class="bg-blue-100 text-lg">
                                        <td colspan="8" class="border px-2 py-3 text-right">Grand Total Pay:</td>
                                        <td class="border px-2 py-3 text-right text-blue-800 font-extrabold" x-text="currency(grandTotal())"></td>
                                        <td class="border px-2 py-3"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        function billingApp() {
            return {
                rates: {
                    reg_hr: 73.38, ot: 73.78, np: 5.90, hpnp: 13.24, reg_hol: 59.02, spec_hol: 91.09
                },
                labels: {
                    reg_hr: "Reg Hr Rate", ot: "OT Rate", np: "NP Rate",
                    hpnp: "HPNP Rate", reg_hol: "Reg Hol Rate", spec_hol: "Spec Hol Rate"
                },
                employees: [
                    { name: 'Collador, Alex', reg_hr: 96, ot: 5, np: 55, hpnp: 7, reg_hol: 8, spec_hol: 0, useCustom: false, customRates: {} },
                    { name: 'Estoloso, Ben', reg_hr: 96, ot: 0, np: 0, hpnp: 0, reg_hol: 8, spec_hol: 0, useCustom: false, customRates: {} },
                    { name: 'Dueñas, Ronald', reg_hr: 32, ot: 0, np: 0, hpnp: 0, reg_hol: 8, spec_hol: 0, useCustom: false, customRates: {} }
                ],
                history: [],
                redoStack: [],

                saveHistory() {
                    // Save deep copy of current state
                    this.history.push({
                        employees: JSON.parse(JSON.stringify(this.employees)),
                        rates: JSON.parse(JSON.stringify(this.rates))
                    });
                    // Clear redo stack whenever new action occurs
                    this.redoStack = [];
                },

                Undo() {
                    if (this.history.length > 0) {
                        this.redoStack.push({
                            employees: JSON.parse(JSON.stringify(this.employees)),
                            rates: JSON.parse(JSON.stringify(this.rates))
                        });
                        const last = this.history.pop();
                        this.employees = last.employees;
                        this.rates = last.rates;
                    }
                },

                Redo() {
                    if (this.redoStack.length > 0) {
                        this.history.push({
                            employees: JSON.parse(JSON.stringify(this.employees)),
                            rates: JSON.parse(JSON.stringify(this.rates))
                        });
                        const next = this.redoStack.pop();
                        this.employees = next.employees;
                        this.rates = next.rates;
                    }
                },

                totalHours(emp) {
                    return emp.reg_hr + emp.ot + emp.np + emp.hpnp + emp.reg_hol + emp.spec_hol;
                },
                totalPay(emp) {
                    const r = this.rates;
                    const c = emp.customRates || {};
                    if (emp.useCustom) {
                        return (emp.reg_hr * (c.reg_hr || r.reg_hr)) +
                               (emp.ot * (c.ot || r.ot)) +
                               (emp.np * (c.np || r.np)) +
                               (emp.hpnp * (c.hpnp || r.hpnp)) +
                               (emp.reg_hol * (c.reg_hol || r.reg_hol)) +
                               (emp.spec_hol * (c.spec_hol || r.spec_hol));
                    } else {
                        return (emp.reg_hr * r.reg_hr) +
                               (emp.ot * r.ot) +
                               (emp.np * r.np) +
                               (emp.hpnp * r.hpnp) +
                               (emp.reg_hol * r.reg_hol) +
                               (emp.spec_hol * r.spec_hol);
                    }
                },
                grandTotal() {
                    return this.employees.reduce((sum, emp) => sum + this.totalPay(emp), 0);
                },
                columnTotal(field) {
                    return this.employees.reduce((sum, emp) => sum + (emp[field] || 0), 0);
                },
                currency(val) {
                    return "₱ " + val.toFixed(2);
                },
                addEmployee() {
                    this.saveHistory();
                    this.employees.push({ name: '', reg_hr: 0, ot: 0, np: 0, hpnp: 0, reg_hol: 0, spec_hol: 0, useCustom: false, customRates: {} });
                },
                deleteEmployee(i) {
                    this.saveHistory();
                    this.employees.splice(i, 1);
                }
            }
        }
    </script>

    <script src="//unpkg.com/alpinejs" defer></script>
</x-admin-layout>
