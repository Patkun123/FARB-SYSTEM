<x-admin-layout>
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Left Section -->
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none transition">
                        <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                <div class="hidden sm:flex sm:space-x-6">
                    <x-nav-link :href="route('admin.billing')" :active="request()->routeIs('billing')">
                        {{ __('Billing Summary') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="flex items-center">
                <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10">
            </a>
        </div>
    </div>
</div>

<!-- Main Content -->
<main class="py-8 bg-gray-50 min-h-screen">
    <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-lg rounded-2xl border border-gray-200 p-8 transition hover:shadow-xl">
            <h1 class="text-2xl font-extrabold text-gray-800 mb-6 flex items-center gap-3">
                <img class="w-12 h-12" src="{{ asset('img/billing_summaries.png') }}" alt="Billing">
                Billing Summary
            </h1>

            <div x-data="billingApp()" x-init="initKeyboard(); loadState()" class="space-y-10">
            <!-- Summary Info -->

            <section>
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Summary Info</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    <!-- Summary Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Summary Name</label>
                        <input type="text" x-model="summaryName"
                            @focus="saveHistory()" @input.debounce.300ms="saveHistory()"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring focus:ring-blue-300 text-sm" />
                    </div>

                    <!-- Department Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Department Name</label>
                        <input type="text" x-model="departmentName"
                            @focus="saveHistory()" @input.debounce.300ms="saveHistory()"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring focus:ring-blue-300 text-sm" />
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Start Date</label>
                        <input type="date" x-model="startDate" @change="onDateRangeChange()"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring focus:ring-blue-300 text-sm" />
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">End Date</label>
                        <input type="date" x-model="endDate" @change="onDateRangeChange()"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring focus:ring-blue-300 text-sm" />
                    </div>
                </div>
            </section>

                <!-- Global Rates -->
                <section>
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Global Rates</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-6 gap-6">
                        <template x-for="(rate, key) in rates" :key="key">
                            <div>
                                <label class="block text-sm font-medium text-gray-600"
                                    x-text="labels[key]"></label>
                                <input type="number" step="0.01" min="0" x-model.number="rates[key]"
                                    @focus="saveHistory(); recomputeAllSummaries()" @input.debounce.300ms="saveHistory(); recomputeAllSummaries()"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring focus:ring-blue-300 text-sm" />
                            </div>
                        </template>
                    </div>
                </section>

                <!-- Employee-Specific Rates -->
                <section>
                    <h2 class="text-lg font-semibold text-gray-700 mb-4">Employee-Specific Rates</h2>
                    <div class="overflow-x-auto rounded-lg border shadow-sm">
                        <table class="min-w-full text-sm border-collapse">
                            <thead class="bg-blue-50 text-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold">Employee</th>
                                    <th class="px-3 py-2 text-center font-semibold">Use Custom?</th>
                                    <th class="px-3 py-2 text-center font-semibold">Reg Hr Rate</th>
                                    <th class="px-3 py-2 text-center font-semibold">OT Rate</th>
                                    <th class="px-3 py-2 text-center font-semibold">NP Rate</th>
                                    <th class="px-3 py-2 text-center font-semibold">HPNP Rate</th>
                                    <th class="px-3 py-2 text-center font-semibold">Reg Hol Rate</th>
                                    <th class="px-3 py-2 text-center font-semibold">Spec Hol Rate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="(emp, i) in employees" :key="i">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2" x-text="emp.name || 'Unnamed'"></td>
                                        <td class="px-3 py-2 text-center">
                                            <input type="checkbox" x-model="emp.useCustom" @change="saveHistory(); recomputeAllSummaries()" />
                                        </td>
                                        <template x-for="field in ['reg_hr','ot','np','hpnp','reg_hol','spec_hol']" :key="field">
                                            <td class="px-3 py-2">
                                                <input type="number" step="0.01" min="0"
                                                    x-model.number="emp.customRates[field]"
                                                    @focus="saveHistory()"
                                                    @input.debounce.300ms="saveHistory(); recomputeAllSummaries()"
                                                    :disabled="!emp.useCustom"
                                                    class="w-full text-center rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-300 text-sm"
                                                    placeholder="Global" />
                                            </td>
                                        </template>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Breakdown Days Table -->
                <section>
                    <h2 class="text-xl font-bold mb-4">Breakdown by Days</h2>
                    <div class="overflow-x-auto border rounded-lg shadow-sm">
                        <table class="min-w-max text-sm border-collapse">
                            <thead class="bg-blue-50">
                                <tr>
                                    <th class="px-3 py-2 sticky left-0 bg-blue-50 z-20 text-left font-semibold">Employee Covered Date</th>
                                    <template x-for="(day, dIndex) in daysRange()" :key="dIndex">
                                        <th class="px-3 py-2 sticky top-0 bg-blue-50 z-10 text-center font-semibold">
                                            <div x-text="day"></div>
                                            <!-- visible dropdown to change day type + threshold -->
                                            <div class="mt-1 flex justify-center items-center gap-2">
                                                <select class="text-xs rounded-md border px-1 py-1"
                                                        x-model="daysMeta[dIndex].type"
                                                        @change="saveHistory(); recomputeAllSummaries()">
                                                    <option value="work">Work</option>
                                                    <option value="reg_hol">Reg Hol</option>
                                                    <option value="spec_hol">Spec Hol</option>
                                                    <option value="np">NP</option>
                                                    <option value="hpnp">HPNP</option>
                                                </select>

                                                <input type="number" step="0.5" min="0" class="w-12 text-xs rounded-md border px-1 py-1"
                                                       title="Work threshold (hrs) for Reg vs OT - can be overridden per-employee"
                                                       x-model.number="daysMeta[dIndex].threshold"
                                                       @input.debounce.200ms="saveHistory(); recomputeAllSummaries()" />
                                            </div>
                                        </th>
                                    </template>
                                    <th class="px-3 py-2 sticky top-0 bg-blue-50 z-10 text-center font-semibold">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="(emp, i) in employees" :key="i">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2 sticky left-0 bg-white z-20" x-text="emp.name || 'Unnamed'"></td>
                                        <template x-for="(day, dIndex) in daysRange()" :key="dIndex">
                                            <td class="px-2 py-1 text-center">
                                                <input type="number" step="0.1" min="0"
                                                    x-model.number="emp.daily[dIndex]"
                                                    @focus="saveHistory()" @input.debounce.150ms="onDailyInput(emp, dIndex)"
                                                    class="w-20 text-center border rounded-md shadow-sm focus:ring focus:ring-blue-200 text-sm" />
                                                <div class="flex items-center justify-center gap-1 mt-1">
                                                    <select class="text-xs rounded-md border px-1 py-0.5"
                                                            title="Per-employee override: if set, this day's hours for this employee will be treated as the selected type"
                                                            x-model="emp.dayOverrides[dIndex]"
                                                            @change="saveHistory(); computeEmpSummary(emp); saveState()">
                                                        <option value="">Use Global</option>
                                                        <option value="work">Work</option>
                                                        <option value="reg_hol">Reg Hol</option>
                                                        <option value="spec_hol">Spec Hol</option>
                                                        <option value="np">NP</option>
                                                        <option value="hpnp">HPNP</option>
                                                    </select>

                                                    <!-- small button to copy global threshold into employee's dayThreshold (toggle) -->
                                                    <button type="button" class="text-xs px-1 py-0.5 rounded border"
                                                            @click="toggleEmpDayThreshold(i, dIndex)"
                                                            title="Toggle per-employee threshold override (click to set/unset)">
                                                        <span x-text="emp.dayThresholds[dIndex] != null ? emp.dayThresholds[dIndex] : 'T'"></span>
                                                    </button>
                                                </div>
                                                <div class="text-xs mt-1" x-text="dayTypeLabel(daysMeta[dIndex].type)"></div>
                                            </td>
                                        </template>
                                        <td class="px-3 py-2 text-center font-medium text-blue-700" x-text="sumDaily(emp)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-gray-100 font-bold">
                                <tr>
                                    <td class="px-3 py-2 text-right">Totals</td>
                                    <template x-for="(day, dIndex) in daysRange()" :key="dIndex">
                                        <td class="px-2 py-2 text-center text-gray-700" x-text="(dailyTotal(dIndex)).toFixed(1)"></td>
                                    </template>
                                    <td class="px-3 py-2 text-center text-blue-800" x-text="(grandDailyTotal()).toFixed(1)"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </section>
                <div class="mt-8">
                    <h2 class="text-xl font-bold mb-4 flex justify-between items-center">
                        <div class="flex gap-4 items-center">
                            <!-- Add Employee Button -->
                            <button @click="addEmployee()" class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-600"> + Add Employee </button>

                            <!-- Undo Button -->
                            <button @click="Undo()" class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-600"> Undo </button>

                            <!-- Redo Button -->
                            <button @click="Redo()" class="bg-green-500 text-white px-3 py-1 rounded-md text-sm hover:bg-green-600"> Redo </button>

                            <!-- Reset Button (reset everything except global rates) -->
                            <button @click="resetExceptRates()" class="bg-gray-500 text-white px-3 py-1 rounded-md text-sm hover:bg-gray-600"> Reset </button>

                            <!-- Employee Counter -->
                            <div class="flex items-center gap-2">
                                <span class="text-gray-700 text-sm font-medium">Employees:</span>
                                <span class="px-2 py-1 bg-gray-100 rounded-md text-sm font-bold text-blue-700" x-text="employees.length"></span>
                            </div>

                            <!-- Add Multiple Employees -->
                            <div class="flex items-center gap-2">
                                <input type="number" min="1" x-model.number="employeeToAdd" class="w-20 rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200 text-sm" placeholder="Count" />
                                <button @click="addMultipleEmployees()" class="bg-purple-500 text-white px-3 py-1 rounded-md text-sm hover:bg-purple-600"> Add Count </button>
                            </div>
                        </div>
                    </h2>
                </div>

                <!-- Employees Table -->
                <section>
                    <h2 class="text-xl font-bold mb-4">Employees</h2>
                    <div class="overflow-x-auto border rounded-lg shadow-sm">
                        <table class="min-w-full text-sm border-collapse">
                            <thead class="bg-blue-50">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold">Name</th>
                                    <th class="px-3 py-2 text-center font-semibold">Manual Override</th>
                                    <th class="px-3 py-2 text-center font-semibold">Reg Hr</th>
                                    <th class="px-3 py-2 text-center font-semibold">OT</th>
                                    <th class="px-3 py-2 text-center font-semibold">NP</th>
                                    <th class="px-3 py-2 text-center font-semibold">HPNP</th>
                                    <th class="px-3 py-2 text-center font-semibold">Reg Hol</th>
                                    <th class="px-3 py-2 text-center font-semibold">Spec Hol</th>
                                    <th class="px-3 py-2 text-center font-semibold">Total Hours</th>
                                    <th class="px-3 py-2 text-center font-semibold">Total Pay (₱)</th>
                                    <th class="px-3 py-2 text-center font-semibold">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="(emp, i) in employees" :key="i">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2">
                                            <input type="text" x-model="emp.name"
                                                @focus="saveHistory()" @input.debounce.300ms="saveHistory()"
                                                class="w-full border rounded-md shadow-sm focus:ring focus:ring-blue-200 text-sm" />
                                        </td>

                                        <td class="px-3 py-2 text-center">
                                            <input type="checkbox" x-model="emp.manualOverride" @change="onManualOverrideToggle(emp)" />
                                        </td>

                                        <!-- summary fields editable only if manualOverride is true -->
                                        <template x-for="field in ['reg_hr','ot','np','hpnp','reg_hol','spec_hol']" :key="field">
                                            <td class="px-2 py-2 text-center">
                                                <input type="number" step="0.1" min="0"
                                                    x-model.number="emp[field]"
                                                    :readonly="!emp.manualOverride"
                                                    @focus="saveHistory()"
                                                    @input.debounce.200ms="saveHistory()"
                                                    class="w-full text-center border rounded-md bg-gray-50 text-sm"
                                                    :class="{'bg-white': emp.manualOverride, 'bg-gray-50': !emp.manualOverride}" />
                                            </td>
                                        </template>
                                        <td class="px-3 py-2 text-center font-medium text-gray-700" x-text="totalHours(emp)"></td>
                                        <td class="px-3 py-2 text-right font-semibold text-blue-700" x-text="currency(totalPay(emp))"></td>
                                        <td class="px-3 py-2 text-center">
                                            <button @click="deleteEmployee(i)" class="text-red-600 hover:underline text-sm">Delete</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-gray-100 font-bold">
                                <tr>
                                    <td class="px-3 py-2 text-right">Totals</td>
                                    <td></td>
                                    <td class="px-3 py-2 text-center" x-text="columnTotal('reg_hr')"></td>
                                    <td class="px-3 py-2 text-center" x-text="columnTotal('ot')"></td>
                                    <td class="px-3 py-2 text-center" x-text="columnTotal('np')"></td>
                                    <td class="px-3 py-2 text-center" x-text="columnTotal('hpnp')"></td>
                                    <td class="px-3 py-2 text-center" x-text="columnTotal('reg_hol')"></td>
                                    <td class="px-3 py-2 text-center" x-text="columnTotal('spec_hol')"></td>
                                    <td class="px-3 py-2 text-center" x-text="employees.reduce((sum, emp) => sum + totalHours(emp), 0)"></td>
                                    <td class="px-3 py-2 text-right text-blue-800" x-text="currency(grandTotal())"></td>
                                    <td class="px-3 py-2"></td>
                                </tr>
                                <!-- Subtotals -->
                                <tr>
                                    <td colspan="9" class="px-3 py-2 text-right">OT Total Pay:</td>
                                    <td class="px-3 py-2 text-right" x-text="currency(categoryTotalPay('ot'))"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="px-3 py-2 text-right">NP Total Pay:</td>
                                    <td class="px-3 py-2 text-right" x-text="currency(categoryTotalPay('np'))"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="px-3 py-2 text-right">HPNP Total Pay:</td>
                                    <td class="px-3 py-2 text-right" x-text="currency(categoryTotalPay('hpnp'))"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="px-3 py-2 text-right">Reg Hol Total Pay:</td>
                                    <td class="px-3 py-2 text-right" x-text="currency(categoryTotalPay('reg_hol'))"></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="px-3 py-2 text-right">Spec Hol Total Pay:</td>
                                    <td class="px-3 py-2 text-right" x-text="currency(categoryTotalPay('spec_hol'))"></td>
                                    <td></td>
                                </tr>
                                <tr class="bg-blue-100 text-lg">
                                    <td colspan="9" class="px-3 py-3 text-right">Grand Total Pay:</td>
                                    <td class="px-3 py-3 text-right text-blue-800 font-extrabold" x-text="currency(grandTotal())"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </section>
                    <!-- Save Button -->
            <div class="flex justify-end mt-6">
                <button @click="manualSave"
                        class="px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl shadow-lg hover:bg-indigo-700 active:scale-95 transition transform">
                    💾 Save
                </button>
            </div>
            </div>
        </div>

    </div>
</main>

<script>

    function billingApp() {
        return {
            // form state
            summaryName: "",
            departmentName: "",
            startDate: "",
            endDate: "",
            rates: {
                reg_hr: 73.38,
                ot: 73.78,
                np: 5.90,
                hpnp: 13.24,
                reg_hol: 59.02,
                spec_hol: 91.09
            },
            labels: {
                reg_hr: "Reg Hr Rate",
                ot: "OT Rate",
                np: "NP Rate",
                hpnp: "HPNP Rate",
                reg_hol: "Reg Hol Rate",
                spec_hol: "Spec Hol Rate"
            },

            // employees list
            employees: [],

            // days metadata: each entry is an object { type: 'work'|'reg_hol'|..., threshold: number }
            daysMeta: [],

            // undo/redo history
            history: [],
            redoStack: [],

            // debounce for history save
            debounceTimer: null,

            // for add multiple employees
            employeeToAdd: 1,

            // internal: storage key
            storageKey: 'billingState_v1',

            // ---------- Initialization ----------
            initKeyboard() {
                window.addEventListener('keydown', e => {
                    if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
                        e.preventDefault();
                        this.Undo();
                    }
                    if ((e.ctrlKey || e.metaKey) && e.key === 'y') {
                        e.preventDefault();
                        this.Redo();
                    }
                });
            },

            // Load persisted state (if any)
            loadState() {
                try {
                    const raw = localStorage.getItem(this.storageKey);
                    if (raw) {
                        const parsed = JSON.parse(raw);
                        // Only assign known properties to avoid injecting anything unexpected
                        if (parsed.summaryName != null) this.summaryName = parsed.summaryName;
                        if (parsed.departmentName != null) this.departmentName = parsed.departmentName;
                        if (parsed.startDate != null) this.startDate = parsed.startDate;
                        if (parsed.endDate != null) this.endDate = parsed.endDate;
                        if (parsed.rates != null) this.rates = parsed.rates;
                        if (Array.isArray(parsed.employees)) this.employees = parsed.employees;
                        if (Array.isArray(parsed.history)) this.history = parsed.history;
                        if (Array.isArray(parsed.redoStack)) this.redoStack = parsed.redoStack;
                        if (Array.isArray(parsed.daysMeta)) this.daysMeta = parsed.daysMeta;
                    } else {
                        // no saved state -> initialize routine
                        this.persistDefaults();
                    }

                    // ensure daily arrays match date range length after load
                    this.onDateRangeChange();

                    // compute initial summaries from daily
                    this.recomputeAllSummaries();

                } catch (err) {
                    console.error('Failed to load billing state:', err);
                }
            },

            // Save full state to localStorage
            saveState() {
                try {
                    const snapshot = {
                        summaryName: this.summaryName,
                        departmentName: this.departmentName,
                        startDate: this.startDate,
                        endDate: this.endDate,
                        rates: this.rates,
                        employees: this.employees,
                        history: this.history,
                        redoStack: this.redoStack,
                        daysMeta: this.daysMeta
                    };
                    localStorage.setItem(this.storageKey, JSON.stringify(snapshot));
                } catch (err) {
                    console.error('Failed to save billing state:', err);
                }
            },

            // set reasonable defaults on first use
            persistDefaults() {
                this.employees = [];
                this.history = [];
                this.redoStack = [];
                this.daysMeta = [];
                this.saveState();
            },

            // ---------- History (undo/redo) ----------
            saveHistory() {
                // debounce pushes to avoid flooding
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => {
                    // push snapshot to history
                    this.history.push({
                        employees: JSON.parse(JSON.stringify(this.employees)),
                        rates: JSON.parse(JSON.stringify(this.rates)),
                        startDate: this.startDate,
                        endDate: this.endDate,
                        summaryName: this.summaryName,
                        departmentName: this.departmentName,
                        daysMeta: JSON.parse(JSON.stringify(this.daysMeta))
                    });
                    // clear redo when new action
                    this.redoStack = [];
                    // persist
                    this.saveState();
                }, 300);
            },

            Undo() {
                if (this.history.length > 0) {
                    // push current state into redo
                    this.redoStack.push({
                        employees: JSON.parse(JSON.stringify(this.employees)),
                        rates: JSON.parse(JSON.stringify(this.rates)),
                        startDate: this.startDate,
                        endDate: this.endDate,
                        summaryName: this.summaryName,
                        departmentName: this.departmentName,
                        daysMeta: JSON.parse(JSON.stringify(this.daysMeta))
                    });

                    const last = this.history.pop();

                    // restore
                    this.employees = last.employees || [];
                    this.rates = last.rates || this.rates;
                    this.startDate = last.startDate || "";
                    this.endDate = last.endDate || "";
                    this.summaryName = last.summaryName || "";
                    this.departmentName = last.departmentName || "";
                    this.daysMeta = last.daysMeta || [];

                    // ensure arrays match range
                    this.onDateRangeChange();

                    // recompute
                    this.recomputeAllSummaries();

                    // persist
                    this.saveState();
                }
            },

            Redo() {
                if (this.redoStack.length > 0) {
                    this.history.push({
                        employees: JSON.parse(JSON.stringify(this.employees)),
                        rates: JSON.parse(JSON.stringify(this.rates)),
                        startDate: this.startDate,
                        endDate: this.endDate,
                        summaryName: this.summaryName,
                        departmentName: this.departmentName,
                        daysMeta: JSON.parse(JSON.stringify(this.daysMeta))
                    });

                    const next = this.redoStack.pop();

                    this.employees = next.employees || [];
                    this.rates = next.rates || this.rates;
                    this.startDate = next.startDate || "";
                    this.endDate = next.endDate || "";
                    this.summaryName = next.summaryName || "";
                    this.departmentName = next.departmentName || "";
                    this.daysMeta = next.daysMeta || [];

                    this.onDateRangeChange();
                    this.recomputeAllSummaries();
                    this.saveState();
                }
            },

            // ---------- Employees helpers ----------
            addMultipleEmployees() {
                if (this.employeeToAdd > 0) {
                    for (let i = 0; i < this.employeeToAdd; i++) {
                        this.addEmployee();
                    }
                    this.employeeToAdd = 1; // reset input
                }
            },

            // Called when start or end dates change — updates days range and ensures employee daily arrays align.
            onDateRangeChange() {
                const days = this.daysRange();
                const newLen = days.length;

                // ensure daysMeta matches range and is object form
                if (!Array.isArray(this.daysMeta)) this.daysMeta = [];
                if (this.daysMeta.length < newLen) {
                    for (let i = this.daysMeta.length; i < newLen; i++) this.daysMeta.push({type: 'work', threshold: 8});
                } else if (this.daysMeta.length > newLen) {
                    this.daysMeta.splice(newLen);
                } else {
                    // ensure each entry is object
                    this.daysMeta = this.daysMeta.map(m => (m && typeof m === 'object') ? m : {type: m || 'work', threshold: 8});
                }

                // After adjusting day arrays, ensure employee.daily arrays and override arrays match length
                this.employees.forEach(emp => {
                    if (!emp.daily) emp.daily = [];
                    if (emp.daily.length < newLen) {
                        for (let k = emp.daily.length; k < newLen; k++) emp.daily.push(0);
                    } else if (emp.daily.length > newLen) {
                        emp.daily.splice(newLen);
                    }

                    if (!Array.isArray(emp.dayOverrides)) emp.dayOverrides = [];
                    if (emp.dayOverrides.length < newLen) {
                        for (let k = emp.dayOverrides.length; k < newLen; k++) emp.dayOverrides.push('');
                    } else if (emp.dayOverrides.length > newLen) {
                        emp.dayOverrides.splice(newLen);
                    }

                    if (!Array.isArray(emp.dayThresholds)) emp.dayThresholds = [];
                    if (emp.dayThresholds.length < newLen) {
                        for (let k = emp.dayThresholds.length; k < newLen; k++) emp.dayThresholds.push(null);
                    } else if (emp.dayThresholds.length > newLen) {
                        emp.dayThresholds.splice(newLen);
                    }
                });

                // recompute summaries (since day types/length changed)
                this.recomputeAllSummaries();

                this.saveHistory();
            },

            // 🔹 Generate day range between start and end date
            daysRange() {
                if (!this.startDate || !this.endDate) return [];
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                const days = [];
                let current = new Date(start);
                while (current <= end) {
                    days.push(
                        current.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
                    );
                    current.setDate(current.getDate() + 1);
                }

                // make sure each emp.daily has correct length (use 0 default, manual breakdown)
                this.employees.forEach(emp => {
                    if (!emp.daily) emp.daily = [];
                    if (emp.daily.length < days.length) {
                        for (let k = emp.daily.length; k < days.length; k++) emp.daily.push(0);
                    } else if (emp.daily.length > days.length) {
                        emp.daily.splice(days.length);
                    }
                });

                // ensure daysMeta length and object form
                if (!Array.isArray(this.daysMeta)) this.daysMeta = [];
                if (this.daysMeta.length < days.length) {
                    for (let k = this.daysMeta.length; k < days.length; k++) this.daysMeta.push({type:'work', threshold:8});
                } else if (this.daysMeta.length > days.length) {
                    this.daysMeta.splice(days.length);
                } else {
                    this.daysMeta = this.daysMeta.map(m => (m && typeof m === 'object') ? m : { type: m || 'work', threshold: 8 });
                }

                return days;
            },

            // helper to toggleEmpDayThreshold: copy global threshold to employee day threshold or clear it
            toggleEmpDayThreshold(empIndex, dIndex) {
                const emp = this.employees[empIndex];
                if (!emp) return;
                if (!Array.isArray(emp.dayThresholds)) emp.dayThresholds = [];
                if (emp.dayThresholds[dIndex] == null) {
                    // copy from global
                    emp.dayThresholds.splice(dIndex, 1, (this.daysMeta[dIndex] && this.daysMeta[dIndex].threshold) || 8);
                } else {
                    // unset
                    emp.dayThresholds.splice(dIndex, 1, null);
                }
                this.saveHistory();
                this.computeEmpSummary(emp);
                this.saveState();
            },

            dayTypeLabel(t) {
                const map = {
                    work: 'Work',
                    reg_hol: 'Reg Hol',
                    spec_hol: 'Spec Hol',
                    np: 'NP',
                    hpnp: 'HPNP'
                };
                return map[t] || t || 'Work';
            },

            // return total hours for a specific day index across all employees
            dailyTotal(dIndex) {
                return this.employees.reduce((sum, emp) => {
                    const v = Number((emp.daily && emp.daily[dIndex]) || 0);
                    return sum + v;
                }, 0);
            },

            // NEW: grand total across all daily columns (sum of every employee's daily sum)
            grandDailyTotal() {
                return this.employees.reduce((sum, emp) => sum + this.sumDaily(emp), 0);
            },

            // sum an employee's daily array
            sumDaily(emp) {
                if (!emp || !emp.daily) return 0;
                return emp.daily.reduce((s, v) => s + (Number(v) || 0), 0);
            },

            // called when a daily input changes
            onDailyInput(emp, dIndex) {
                if (!emp.daily) emp.daily = [];
                if (emp.daily[dIndex] == null) emp.daily[dIndex] = 0;
                if (emp.daily[dIndex] < 0) emp.daily[dIndex] = 0;

                // recompute this employee's summary (only for non-manual fields)
                this.computeEmpSummary(emp);

                // Save history/state so the change persists and can be undone.
                this.saveHistory();
                this.saveState();
            },

            // Recomputes categories for a single employee from emp.daily & daysMeta
            computeEmpSummary(emp) {
                // If the entire employee is manually overridden, do not overwrite computed fields.
                const wasManual = !!emp.manualOverride;

                // If not manualOverride, recompute; if manualOverride, keep existing summary fields untouched.
                if (!wasManual) {
                    // reset computed fields
                    emp.reg_hr = 0;
                    emp.ot = 0;
                    emp.np = 0;
                    emp.hpnp = 0;
                    emp.reg_hol = 0;
                    emp.spec_hol = 0;
                } else {
                    // ensure fields exist to avoid NaN when summing
                    emp.reg_hr = Number(emp.reg_hr || 0);
                    emp.ot = Number(emp.ot || 0);
                    emp.np = Number(emp.np || 0);
                    emp.hpnp = Number(emp.hpnp || 0);
                    emp.reg_hol = Number(emp.reg_hol || 0);
                    emp.spec_hol = Number(emp.spec_hol || 0);
                    // If manual, we still may want to recompute categories that are zero? We'll respect manual entirely.
                }

                const daysCount = this.daysRange().length;
                if (!Array.isArray(emp.daily)) emp.daily = new Array(daysCount).fill(0);
                if (!Array.isArray(emp.dayOverrides)) emp.dayOverrides = new Array(daysCount).fill('');
                if (!Array.isArray(emp.dayThresholds)) emp.dayThresholds = new Array(daysCount).fill(null);

                for (let d = 0; d < daysCount; d++) {
                    const hours = Number(emp.daily[d] || 0);
                    // per-employee override has priority
                    const dtype = emp.dayOverrides[d] && emp.dayOverrides[d] !== '' ? emp.dayOverrides[d] : (this.daysMeta[d] && this.daysMeta[d].type) || 'work';
                    // threshold priority: emp.dayThresholds[d] -> daysMeta[d].threshold -> 8 default
                    const threshold = (emp.dayThresholds[d] != null) ? Number(emp.dayThresholds[d]) : ((this.daysMeta[d] && this.daysMeta[d].threshold) != null ? Number(this.daysMeta[d].threshold) : 8);

                    if (wasManual) {
                        // if manual override is active for the entire employee, skip adding to computed fields
                        continue;
                    }

                    if (dtype === 'work') {
                        const reg = Math.min(threshold, hours);
                        const ot = Math.max(0, hours - threshold);
                        emp.reg_hr += reg;
                        emp.ot += ot;
                    } else if (dtype === 'reg_hol') {
                        emp.reg_hol += hours;
                    } else if (dtype === 'spec_hol') {
                        emp.spec_hol += hours;
                    } else if (dtype === 'np') {
                        emp.np += hours;
                    } else if (dtype === 'hpnp') {
                        emp.hpnp += hours;
                    } else {
                        // fallback treat as work with threshold
                        const reg = Math.min(threshold, hours);
                        const ot = Math.max(0, hours - threshold);
                        emp.reg_hr += reg;
                        emp.ot += ot;
                    }
                }

                // Ensure numeric rounding
                emp.reg_hr = Number(emp.reg_hr || 0);
                emp.ot = Number(emp.ot || 0);
                emp.np = Number(emp.np || 0);
                emp.hpnp = Number(emp.hpnp || 0);
                emp.reg_hol = Number(emp.reg_hol || 0);
                emp.spec_hol = Number(emp.spec_hol || 0);
            },

            // Recompute all employees' summaries
            recomputeAllSummaries() {
                this.employees.forEach(emp => {
                    this.computeEmpSummary(emp);
                });
                // persist after compute (but don't push a history snapshot here)
                this.saveState();
            },

            // Called when user toggles manual override on/off for an employee
            onManualOverrideToggle(emp) {
                // if manualOverride was just enabled, leave summary fields as-is so the user can edit them.
                // if manualOverride was just disabled, recompute to restore auto-calculated values.
                if (!emp.manualOverride) {
                    // toggled OFF -> recompute to regenerate summary from daily
                    this.computeEmpSummary(emp);
                } else {
                    // toggled ON -> ensure fields exist and let user edit manually
                    emp.reg_hr = Number(emp.reg_hr || 0);
                    emp.ot = Number(emp.ot || 0);
                    emp.np = Number(emp.np || 0);
                    emp.hpnp = Number(emp.hpnp || 0);
                    emp.reg_hol = Number(emp.reg_hol || 0);
                    emp.spec_hol = Number(emp.spec_hol || 0);
                }
                this.saveHistory();
                this.saveState();
            },

            // Called when user edits fields in the Employees table (previously allowed)
            // Now summary fields are computed from daily unless manualOverride is true.
            onEmployeeTableInput(emp, field) {
                if (emp[field] == null) emp[field] = 0;
                if (emp[field] < 0) emp[field] = 0;
                this.saveHistory();
                this.saveState();
            },

            totalHours(emp) {
                // compute from computed summary fields (they are maintained by computeEmpSummary)
                return Number(
                    (emp.reg_hr || 0) +
                    (emp.ot || 0) +
                    (emp.np || 0) +
                    (emp.hpnp || 0) +
                    (emp.reg_hol || 0) +
                    (emp.spec_hol || 0)
                );
            },

            totalPay(emp) {
                const r = this.rates;
                const c = emp.useCustom ? emp.customRates : {};
                const getRate = (field) => emp.useCustom ? (c[field] != null ? c[field] : r[field]) : r[field];
                return (Number(emp.reg_hr || 0) * getRate('reg_hr')) +
                    (Number(emp.ot || 0) * getRate('ot')) +
                    (Number(emp.np || 0) * getRate('np')) +
                    (Number(emp.hpnp || 0) * getRate('hpnp')) +
                    (Number(emp.reg_hol || 0) * getRate('reg_hol')) +
                    (Number(emp.spec_hol || 0) * getRate('spec_hol'));
            },

            columnTotal(field) {
                return this.employees.reduce((sum, emp) => sum + (Number(emp[field] || 0)), 0);
            },

            categoryTotalPay(field) {
                return this.employees.reduce((sum, emp) => {
                    const hours = Number(emp[field] || 0);
                    const rate = emp.useCustom ? (emp.customRates[field] != null ? emp.customRates[field] : this.rates[field]) : this.rates[field];
                    return sum + (hours * rate);
                }, 0);
            },

            grandTotal() {
                return this.employees.reduce((sum, emp) => sum + this.totalPay(emp), 0);
            },

            grandTotalHours() {
                return this.employees.reduce((sum, emp) => sum + this.totalHours(emp), 0);
            },

            currency(val) {
                return '₱' + (Number(val) || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            addEmployee() {
                const daysCount = this.startDate && this.endDate ? this.daysRange().length : 0;
                // daily default is 0 (manual breakdown)
                const dailyInit = new Array(daysCount).fill(0);
                const emp = {
                    name: "",
                    reg_hr: 0,
                    ot: 0,
                    np: 0,
                    hpnp: 0,
                    reg_hol: 0,
                    spec_hol: 0,
                    useCustom: false,
                    customRates: {},
                    daily: dailyInit,
                    // new flags/arrays for overrides and manual editing
                    manualOverride: false,
                    dayOverrides: new Array(daysCount).fill(''), // '' means use global
                    dayThresholds: new Array(daysCount).fill(null) // null means use daysMeta threshold
                };
                this.employees.push(emp);

                // compute summary from daily (initially zeros)
                this.computeEmpSummary(emp);

                this.saveHistory();
                this.saveState();
            },

            deleteEmployee(i) {
                this.employees.splice(i, 1);
                this.saveHistory();
                this.saveState();
            },

            // Reset only non-global-rate inputs. Keeps rates intact.
            resetExceptRates() {
                // push current snapshot to history so undo can restore
                this.history.push({
                    employees: JSON.parse(JSON.stringify(this.employees)),
                    rates: JSON.parse(JSON.stringify(this.rates)),
                    startDate: this.startDate,
                    endDate: this.endDate,
                    summaryName: this.summaryName,
                    departmentName: this.departmentName,
                    daysMeta: JSON.parse(JSON.stringify(this.daysMeta))
                });

                // Clear everything except rates
                this.summaryName = "";
                this.departmentName = "";
                this.startDate = "";
                this.endDate = "";
                this.employees = [];
                this.daysMeta = [];
                this.redoStack = [];

                // persist cleared state but DO NOT push the cleared state into history immediately.
                // This allows Undo to work correctly (Undo will restore the pushed snapshot above).
                this.saveState();
            },

            // Manual Save (keeps behavior for your Save button)
            manualSave() {
                // recompute to ensure sync
                this.recomputeAllSummaries();
                this.saveHistory();
                this.saveState();
                alert('Saved');
            }
        };
    }


</script>

</x-admin-layout>
