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
                                    <!-- make the TOTAL header sticky on the right -->
                                    <th class="px-3 py-2 sticky right-0 top-0 bg-blue-50 z-30 text-center font-semibold w-28">Total</th>
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
                                        <!-- make per-employee TOTAL sticky on the right -->
                                        <td class="px-3 py-2 text-center font-medium text-blue-700 sticky right-0 bg-white z-20 w-28" x-text="sumDaily(emp)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-gray-100 font-bold">
                                <tr>
                                    <td class="px-3 py-2 text-right">Totals</td>
                                    <template x-for="(day, dIndex) in daysRange()" :key="dIndex">
                                        <td class="px-2 py-2 text-center text-gray-700" x-text="(dailyTotal(dIndex)).toFixed(1)"></td>
                                    </template>
                                    <!-- make grand total sticky on the right in the footer -->
                                    <td class="px-3 py-2 text-center text-blue-800 sticky right-0 bg-gray-100 z-20 w-28" x-text="(grandDailyTotal()).toFixed(1)"></td>
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

<script src="{{ asset('js/billing-sumarries.js') }}"></script>

</x-admin-layout>
