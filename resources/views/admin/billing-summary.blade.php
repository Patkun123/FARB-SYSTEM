<x-admin-layout>
    <!-- Header -->
    <div class="bg-white border-b border-gray-100 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Left Section -->
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none transition" >
                        <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <div class="hidden sm:flex sm:space-x-6">
                        <x-nav-link :href="route('admin.billing')" :active="request()->routeIs('billing')" >
                            {{ __('Billing') }}
                        </x-nav-link>
                    </div>
                </div>

                <!-- Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center">
                    <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10" >
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="py-10 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-md hover:shadow-lg transition rounded-2xl p-8 border border-gray-200">
                <h1 class="text-2xl font-bold text-gray-800 mb-8 flex items-center gap-3">
                    <img class="w-10 h-10" src="{{ asset('img/billing_summaries.png') }}" alt="Billing">
                    Billing Summary
                </h1>

                <div x-data="billingApp()" x-init="initKeyboard(); loadState()" class="space-y-8">
                    <!-- Department and Covered Date -->
                    <div>
                        <h2 class="text-xl font-bold mb-4 mt-6">Summary Info</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                            <!-- Summary Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Summary Name</label>
                                <input type="text" x-model="summaryName" @focus="saveHistory()" @input.debounce.300ms="saveHistory()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200 text-sm" />
                            </div>

                            <!-- Department Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Department Name</label>
                                <input type="text" x-model="departmentName" @focus="saveHistory()" @input.debounce.300ms="saveHistory()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200 text-sm" />
                            </div>

                            <!-- Start Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="date" x-model="startDate" @change="onDateRangeChange()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200 text-sm" />
                            </div>

                            <!-- End Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="date" x-model="endDate" @change="onDateRangeChange()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200 text-sm" />
                            </div>
                        </div>
                    </div>

                    <!-- Global Rates -->
                    <div>
                        <h2 class="text-xl font-bold mb-4 mt-6">Global Rates</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-6 gap-4">
                            <template x-for="(rate, key) in rates" :key="key">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700" x-text="labels[key]"></label>
                                    <input type="number" step="0.01" min="0" x-model.number="rates[key]" @focus="saveHistory()" @input.debounce.300ms="saveHistory()" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-blue-200 text-sm" />
                                </div>
                            </template>
                        </div>

                        <!-- Employee-specific rates -->
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
                                                    <input type="number" step="0.01" min="0" x-model.number="emp.customRates[field]" @focus="saveHistory()" @input.debounce.300ms="saveHistory()" :disabled="!emp.useCustom" class="w-full text-center border-none focus:ring-0 text-sm" placeholder="Global" />
                                                </td>
                                            </template>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Breakdown Days Table -->
                    <div class="mt-8">
                        <h2 class="text-xl font-bold mb-4">Breakdown by Days</h2>

                        <!-- 🔹 Make table scrollable horizontally -->
                        <div class="overflow-x-auto max-w-full">
                            <table class="min-w-max border border-gray-300 text-sm relative">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <!-- Sticky left Employee col -->
                                        <th class="border px-2 py-1 sticky left-0 bg-gray-100 z-20">Employee Covered Date</th>

                                        <!-- Sticky top date headers -->
                                        <template x-for="(day, dIndex) in daysRange()" :key="dIndex">
                                            <th class="border px-2 py-1 sticky top-0 bg-gray-100 z-10" x-text="day"></th>
                                        </template>

                                        <!-- NEW: Total column header -->
                                        <th class="border px-2 py-1 sticky top-0 bg-gray-100 z-10">Total</th>
                                    </tr>

                                    <!-- NOTE: Date Tag row REMOVED per requirement -->
                                </thead>

                                <tbody>
                                    <template x-for="(emp, i) in employees" :key="i">
                                        <tr>
                                            <!-- Sticky Employee Name -->
                                            <td class="border px-2 py-1 sticky left-0 bg-white z-20" x-text="emp.name || 'Unnamed'"></td>

                                            <!-- Scrollable Inputs -->
                                            <template x-for="(day, dIndex) in daysRange()" :key="dIndex">
                                                <td class="border px-2 py-1">
                                                    <input type="number" step="0.1" min="0" x-model.number="emp.daily[dIndex]" @focus="saveHistory()" @input.debounce.150ms="onDailyInput(emp, dIndex)" class="w-24 text-center border-none focus:ring-0 text-sm" />
                                                </td>
                                            </template>

                                            <!-- NEW: Per-employee total column -->
                                            <td class="border px-2 py-1 text-center font-medium" x-text="sumDaily(emp)"></td>
                                        </tr>
                                    </template>
                                </tbody>

                                <!-- NEW: Daily Totals Row -->
                                <tfoot class="bg-gray-100 font-bold">
                                    <tr>
                                        <td class="border px-2 py-2 text-right">Totals</td>
                                        <template x-for="(day, dIndex) in daysRange()" :key="dIndex">
                                            <td class="border px-2 py-2 text-center" x-text="(dailyTotal(dIndex)).toFixed(1)"></td>
                                        </template>

                                        <!-- NEW: Grand total of all daily totals -->
                                        <td class="border px-2 py-2 text-center" x-text="(grandDailyTotal()).toFixed(1)"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

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
                    <div>
                        <h2 class="text-xl font-bold mb-4 flex justify-between items-center"> Employees </h2>
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
                                                <input type="text" x-model="emp.name" @focus="saveHistory()" @input.debounce.300ms="saveHistory()" class="w-full border-none focus:ring-0 text-sm" />
                                            </td>

                                            <template x-for="field in ['reg_hr','ot','np','hpnp','reg_hol','spec_hol']" :key="field">
                                                <td class="border px-2 py-1">
                                                    <!-- These fields are MANUAL. Do NOT auto-distribute into daily. -->
                                                    <input type="number" step="0.1" min="0" x-model.number="emp[field]" @focus="saveHistory()" @input.debounce.300ms="onEmployeeTableInput(emp, field)" class="w-full text-center border-none focus:ring-0 text-sm" />
                                                </td>
                                            </template>

                                            <td class="border px-2 py-1 text-center" x-text="totalHours(emp)"></td>
                                            <td class="border px-2 py-1 text-right font-medium" x-text="currency(totalPay(emp))"></td>

                                            <td class="border px-2 py-1 text-center">
                                                <button @click="deleteEmployee(i)" class="text-red-600 hover:underline text-sm"> Delete </button>
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

                                    <!-- NEW: Subtotals per category (pay) -->
                                    <tr>
                                        <td colspan="8" class="border px-2 py-2 text-right">OT Total Pay:</td>
                                        <td class="border px-2 py-2 text-right" x-text="currency(categoryTotalPay('ot'))"></td>
                                        <td class="border px-2 py-2"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" class="border px-2 py-2 text-right">NP Total Pay:</td>
                                        <td class="border px-2 py-2 text-right" x-text="currency(categoryTotalPay('np'))"></td>
                                        <td class="border px-2 py-2"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" class="border px-2 py-2 text-right">HPNP Total Pay:</td>
                                        <td class="border px-2 py-2 text-right" x-text="currency(categoryTotalPay('hpnp'))"></td>
                                        <td class="border px-2 py-2"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" class="border px-2 py-2 text-right">Reg Hol Total Pay:</td>
                                        <td class="border px-2 py-2 text-right" x-text="currency(categoryTotalPay('reg_hol'))"></td>
                                        <td class="border px-2 py-2"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="8" class="border px-2 py-2 text-right">Spec Hol Total Pay:</td>
                                        <td class="border px-2 py-2 text-right" x-text="currency(categoryTotalPay('spec_hol'))"></td>
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
                        } else {
                            // no saved state -> initialize routine
                            this.persistDefaults();
                        }

                        // ensure daily arrays match date range length after load
                        this.onDateRangeChange();

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
                            redoStack: this.redoStack
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
                            departmentName: this.departmentName
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
                            departmentName: this.departmentName
                        });

                        const last = this.history.pop();

                        // restore
                        this.employees = last.employees || [];
                        this.rates = last.rates || this.rates;
                        this.startDate = last.startDate || "";
                        this.endDate = last.endDate || "";
                        this.summaryName = last.summaryName || "";
                        this.departmentName = last.departmentName || "";

                        // ensure arrays match range
                        this.onDateRangeChange();

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
                            departmentName: this.departmentName
                        });

                        const next = this.redoStack.pop();

                        this.employees = next.employees || [];
                        this.rates = next.rates || this.rates;
                        this.startDate = next.startDate || "";
                        this.endDate = next.endDate || "";
                        this.summaryName = next.summaryName || "";
                        this.departmentName = next.departmentName || "";

                        this.onDateRangeChange();
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

                    // After adjusting day arrays, ensure employee.daily arrays match length
                    this.employees.forEach(emp => {
                        if (!emp.daily) emp.daily = [];
                        if (emp.daily.length < newLen) {
                            for (let k = emp.daily.length; k < newLen; k++) emp.daily.push(0);
                        } else if (emp.daily.length > newLen) {
                            emp.daily.splice(newLen);
                        }
                        // IMPORTANT: per requirements, DO NOT compute category totals from daily.
                        // The category fields (reg_hr, ot, np, hpnp, reg_hol, spec_hol) are manual and remain unchanged.
                    });

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

                    return days;
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
                    // Per requirements: daily inputs are manual and DO NOT auto-calculate category totals.
                    // We simply save history/state so the change persists and can be undone.
                    this.saveHistory();
                    this.saveState();
                },

                // Called when user edits fields in the Employees table (reg_hr, ot, etc)
                // IMPORTANT: per request, do NOT auto-distribute edited totals into daily.
                onEmployeeTableInput(emp, field) {
                    if (emp[field] == null) emp[field] = 0;
                    if (emp[field] < 0) emp[field] = 0;
                    // Save history so user can undo this change.
                    this.saveHistory();
                    // persist
                    this.saveState();
                },

                totalHours(emp) {
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
                    this.employees.push({
                        name: "",
                        reg_hr: 0,
                        ot: 0,
                        np: 0,
                        hpnp: 0,
                        reg_hol: 0,
                        spec_hol: 0,
                        useCustom: false,
                        customRates: {},
                        daily: dailyInit
                    });

                    // Do NOT compute category totals from daily (per requirements)
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
                        departmentName: this.departmentName
                    });

                    // Clear everything except rates
                    this.summaryName = "";
                    this.departmentName = "";
                    this.startDate = "";
                    this.endDate = "";
                    this.employees = [];
                    this.redoStack = [];

                    // persist cleared state but DO NOT push the cleared state into history immediately.
                    // This allows Undo to work correctly (Undo will restore the pushed snapshot above).
                    this.saveState();
                }
            };
        }
    </script>
</x-admin-layout>
