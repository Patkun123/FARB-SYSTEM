<x-billing-app>
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
                    <x-nav-link :href="route('billing.billing')" :active="request()->routeIs('billing')">
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
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Billing Summary Info</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    <!-- Summary Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Summary Name</label>
                        <input required type="text" x-model="summaryName"
                            @focus="saveHistory()" @input.debounce.300ms="saveHistory()"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring focus:ring-blue-300 text-sm" />
                    </div>

                    <!-- Department Name -->
                    <div>
                        <label required class="block text-sm font-medium text-gray-600">Department Name</label>
                        <input type="text" x-model="departmentName"
                            @focus="saveHistory()" @input.debounce.300ms="saveHistory()"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring focus:ring-blue-300 text-sm" />
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Start Date</label>
                        <input required type="date" x-model="startDate" @change="onDateRangeChange()"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring focus:ring-blue-300 text-sm" />
                    </div>

                    <!-- End Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-600">End Date</label>
                        <input required type="date" x-model="endDate" @change="onDateRangeChange()"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:ring focus:ring-blue-300 text-sm" />
                    </div>

                    <!-- Button Section -->
                    <div class="mt-4 flex items-center gap-2">
                        <button @click="generateBreakdown()"
                                class="bg-blue-500 text-white px-3 py-1 rounded-md text-sm hover:bg-blue-600">
                            ➕ Breakdown by Days
                        </button>
                        <div x-show="dateRangeError" class="text-red-500 text-sm">
                            ⚠ Please enter a valid Start Date and End Date where Start Date is before End Date.
                        </div>
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
                                    x-text="rateLabels[key]"></label>
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
                                    <th class="px-3 py-2 text-center font-semibold">Regular Day</th>
                                    <th class="px-3 py-2 text-center font-semibold">Regular OT</th>
                                    <th class="px-3 py-2 text-center font-semibold">Night Premium</th>
                                    <th class="px-3 py-2 text-center font-semibold">Night Premium OT</th>
                                    <th class="px-3 py-2 text-center font-semibold">Rest Day/Sunday/Spec Hol</th>
                                    <th class="px-3 py-2 text-center font-semibold">Sunday OT</th>
                                    <th class="px-3 py-2 text-center font-semibold">Sunday Night Premium</th>
                                    <th class="px-3 py-2 text-center font-semibold">Sunday Night Premium OT</th>
                                    <th class="px-3 py-2 text-center font-semibold">Regular Holiday</th>
                                    <th class="px-3 py-2 text-center font-semibold">Regular Holiday OT</th>
                                    <th class="px-3 py-2 text-center font-semibold">Reg Hol Night Premium</th>
                                    <th class="px-3 py-2 text-center font-semibold">Reg Hol Night Premium OT</th>
                                    <th class="px-3 py-2 text-center font-semibold">Unworked Regular Day</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <template x-for="(emp, i) in employees" :key="i">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2" x-text="emp.name || 'Unnamed'"></td>
                                        <td class="px-3 py-2 text-center">
                                            <input type="checkbox" x-model="emp.useCustom" @change="saveHistory(); recomputeAllSummaries()" />
                                        </td>
                                        <template x-for="field in customRateFields" :key="field">
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
                <section x-show="breakdownReady">
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
                                                    <option value="regular_day">Regular Day</option>
                                                    <option value="regular_ot">Regular OT</option>
                                                    <option value="night_premium">Night Premium</option>
                                                    <option value="night_premium_ot">Night Premium OT</option>
                                                    <option value="rest_day">Rest Day/Sunday/Spec Hol</option>
                                                    <option value="sunday_ot">Sunday OT</option>
                                                    <option value="sunday_night_premium">Sunday Night Premium</option>
                                                    <option value="sunday_night_premium_ot">Sunday Night Premium OT</option>
                                                    <option value="regular_holiday">Regular Holiday</option>
                                                    <option value="regular_holiday_ot">Regular Holiday OT</option>
                                                    <option value="reg_hol_night_premium">Reg Hol Night Premium</option>
                                                    <option value="reg_hol_night_premium_ot">Reg Hol Night Premium OT</option>
                                                    <option value="unworked_regular_day">Unworked Regular Day</option>
                                                </select>

                                                <input type="number" step="0.5" min="0" class="w-12 text-xs rounded-md border px-1 py-1"
                                                       title="Work threshold (hrs) for Reg vs OT - can be overridden per-employee"
                                                       x-model.number="daysMeta[dIndex].threshold"
                                                       @input.debounce.200ms="saveHistory(); recomputeAllSummaries()" />
                                            </div>
                                        </th>
                                    </template>
                                    <!--  TOTAL header sticky on the right -->
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
                                                        <option value="regular_day">Regular Day</option>
                                                        <option value="regular_ot">Regular OT</option>
                                                        <option value="night_premium">Night Premium</option>
                                                        <option value="night_premium_ot">Night Premium OT</option>
                                                        <option value="rest_day">Rest Day/Sunday/Spec Hol</option>
                                                        <option value="sunday_ot">Sunday OT</option>
                                                        <option value="sunday_night_premium">Sunday Night Premium</option>
                                                        <option value="sunday_night_premium_ot">Sunday Night Premium OT</option>
                                                        <option value="regular_holiday">Regular Holiday</option>
                                                        <option value="regular_holiday_ot">Regular Holiday OT</option>
                                                        <option value="reg_hol_night_premium">Reg Hol Night Premium</option>
                                                        <option value="reg_hol_night_premium_ot">Reg Hol Night Premium OT</option>
                                                        <option value="unworked_regular_day">Unworked Regular Day</option>
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
                                    <th class="px-3 py-2 text-center font-semibold">Regular Day</th>
                                    <th class="px-3 py-2 text-center font-semibold">Regular OT</th>
                                    <th class="px-3 py-2 text-center font-semibold">Night Premium</th>
                                    <th class="px-3 py-2 text-center font-semibold">Night Premium OT</th>
                                    <th class="px-3 py-2 text-center font-semibold">Rest Day/Sunday/Spec Hol</th>
                                    <th class="px-3 py-2 text-center font-semibold">Sunday OT</th>
                                    <th class="px-3 py-2 text-center font-semibold">Sunday Night Premium</th>
                                    <th class="px-3 py-2 text-center font-semibold">Sunday Night Premium OT</th>
                                    <th class="px-3 py-2 text-center font-semibold">Regular Holiday</th>
                                    <th class="px-3 py-2 text-center font-semibold">Regular Holiday OT</th>
                                    <th class="px-3 py-2 text-center font-semibold">Reg Hol Night Premium</th>
                                    <th class="px-3 py-2 text-center font-semibold">Reg Hol Night Premium OT</th>
                                    <th class="px-3 py-2 text-center font-semibold">Unworked Regular Day</th>
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
                                        <template x-for="field in summaryFields" :key="field">
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
                                    <template x-for="field in summaryFields" :key="field">
                                        <td class="px-3 py-2 text-center" x-text="columnTotal(field)"></td>
                                    </template>
                                    <td class="px-3 py-2 text-center" x-text="employees.reduce((sum, emp) => sum + totalHours(emp), 0)"></td>
                                    <td class="px-3 py-2 text-right text-blue-800" x-text="currency(grandTotal())"></td>
                                    <td class="px-3 py-2"></td>
                                </tr>
                                <!-- Subtotals -->
                                <template x-for="field in summaryFields" :key="field">
                                    <tr>
                                        <td colspan="16" class="px-3 py-2 text-right" x-text="rateLabels[field] + ' Total Pay:'"></td>
                                        <td class="px-3 py-2 text-right" x-text="currency(categoryTotalPay(field))"></td>
                                        <td></td>
                                    </tr>
                                </template>
                                <tr class="bg-blue-100 text-lg">
                                    <td colspan="16" class="px-3 py-3 text-right">Grand Total Pay:</td>
                                    <td class="px-3 py-3 text-right text-blue-800 font-extrabold" x-text="currency(grandTotal())"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </section>
                  <!-- Save Button -->
                    <div class="flex justify-end mt-6" x-data="{ confirmModal: false, successModal: false }">
                        <button
                            type="button"
                            @click="
                                if (!breakdownReady) { alert('Please generate Breakdown by Days before submitting.'); return; }

                                employees.forEach(emp => {
                                    emp.dailyEntries = emp.daily.map((hours, i) => ({
                                        hours: Number(hours || 0),
                                        override_type: emp.dayOverrides[i] || null,
                                        override_threshold: emp.dayThresholds[i] != null ? Number(emp.dayThresholds[i]) : null
                                    }));
                                    delete emp.daily;
                                    delete emp.dayOverrides;
                                    delete emp.dayThresholds;
                                });

                                recomputeTotals(); // <-- make totals reactive

                                confirmModal = true;
                            "
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Save Billing Summary
                        </button>


                        <form x-ref="billingForm" action="{{ route('admin.billing-summary.save') }}" method="POST" class="hidden">
                            @csrf
                            <input type="hidden" name="summary_name" :value="summaryName">
                            <input type="hidden" name="department_name" :value="departmentName">
                            <input type="hidden" name="start_date" :value="startDate">
                            <input type="hidden" name="end_date" :value="endDate">
                            <input type="hidden" name="rates" :value="JSON.stringify(rates)">
                            <input type="hidden" name="daysMeta" :value="JSON.stringify(daysMeta)">
                            <input type="hidden" name="employees" :value="JSON.stringify(employees)">
                            <input type="hidden" name="totals" :value="JSON.stringify(totals)">
                        </form>

                        <!-- Confirm Modal -->
                        <div x-show="confirmModal" class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                            <div class="bg-white rounded-xl shadow-lg w-96 p-6">
                                <h2 class="text-lg font-bold mb-4">Confirm Save</h2>
                                <p class="mb-6">Are you sure you want to save this billing summary?</p>
                                <div class="flex justify-end gap-3">
                                    <button @click="confirmModal = false" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancel</button>
                                    <button @click="$refs.billingForm.submit(); confirmModal = false; successModal = true;" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Yes, Save</button>
                                </div>
                            </div>
                        </div>

                        <!-- Success Modal -->
                        <div x-show="successModal" x-transition class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
                            <div class="bg-white rounded-xl shadow-lg w-96 p-6 text-center">
                                <h2 class="text-lg font-bold mb-4">Success</h2>
                                <p class="mb-6">Billing summary saved successfully!</p>
                                <button @click="successModal = false" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Close</button>
                            </div>
                        </div>
                    </div>

            </div>
        </div>

    </div>


</main>

<script>
function billingApp() {
    return {
        summaryName: '',
        departmentName: '',
        startDate: '',
        endDate: '',
        dateRangeError: false,
        breakdownReady: false,
        employeeToAdd: 1,
        
        // Comprehensive rate structure
        rates: {
            regular_day: 1.0,
            regular_ot: 1.25,
            night_premium: 1.1,
            night_premium_ot: 1.375,
            rest_day: 1.3,
            sunday_ot: 1.69,
            sunday_night_premium: 1.43,
            sunday_night_premium_ot: 1.859,
            regular_holiday: 2.0,
            regular_holiday_ot: 2.6,
            reg_hol_night_premium: 2.2,
            reg_hol_night_premium_ot: 2.86,
            unworked_regular_day: 1.0
        },

        rateLabels: {
            regular_day: 'Regular Day',
            regular_ot: 'Regular OT',
            night_premium: 'Night Premium',
            night_premium_ot: 'Night Premium OT',
            rest_day: 'Rest Day/Sunday/Spec Hol',
            sunday_ot: 'Sunday OT',
            sunday_night_premium: 'Sunday Night Premium',
            sunday_night_premium_ot: 'Sunday Night Premium OT',
            regular_holiday: 'Regular Holiday',
            regular_holiday_ot: 'Regular Holiday OT',
            reg_hol_night_premium: 'Reg Hol Night Premium',
            reg_hol_night_premium_ot: 'Reg Hol Night Premium OT',
            unworked_regular_day: 'Unworked Regular Day'
        },

        customRateFields: [
            'regular_day', 'regular_ot', 'night_premium', 'night_premium_ot', 
            'rest_day', 'sunday_ot', 'sunday_night_premium', 'sunday_night_premium_ot',
            'regular_holiday', 'regular_holiday_ot', 'reg_hol_night_premium', 
            'reg_hol_night_premium_ot', 'unworked_regular_day'
        ],

        summaryFields: [
            'regular_day', 'regular_ot', 'night_premium', 'night_premium_ot', 
            'rest_day', 'sunday_ot', 'sunday_night_premium', 'sunday_night_premium_ot',
            'regular_holiday', 'regular_holiday_ot', 'reg_hol_night_premium', 
            'reg_hol_night_premium_ot', 'unworked_regular_day'
        ],

        daysMeta: [],
        employees: [],
        history: [],
        historyIndex: -1,

        initKeyboard() {
            // Keyboard shortcuts initialization
            document.addEventListener('keydown', (e) => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'z' && !e.shiftKey) {
                    e.preventDefault();
                    this.Undo();
                }
                if ((e.ctrlKey || e.metaKey) && e.key === 'y') {
                    e.preventDefault();
                    this.Redo();
                }
            });
        },

        saveState() {
            const state = JSON.stringify({
                summaryName: this.summaryName,
                departmentName: this.departmentName,
                startDate: this.startDate,
                endDate: this.endDate,
                rates: this.rates,
                daysMeta: this.daysMeta,
                employees: this.employees
            });
            localStorage.setItem('billingState', state);
        },

        loadState() {
            const saved = localStorage.getItem('billingState');
            if (saved) {
                const state = JSON.parse(saved);
                Object.assign(this, state);
                if (this.daysMeta.length > 0) {
                    this.breakdownReady = true;
                }
            }
        },

        saveHistory() {
            this.history = this.history.slice(0, this.historyIndex + 1);
            const snapshot = {
                summaryName: this.summaryName,
                departmentName: this.departmentName,
                startDate: this.startDate,
                endDate: this.endDate,
                rates: {...this.rates},
                daysMeta: JSON.parse(JSON.stringify(this.daysMeta)),
                employees: JSON.parse(JSON.stringify(this.employees))
            };
            this.history.push(snapshot);
            this.historyIndex++;
            this.saveState();
        },

        Undo() {
            if (this.historyIndex > 0) {
                this.historyIndex--;
                this.restoreFromHistory();
            }
        },

        Redo() {
            if (this.historyIndex < this.history.length - 1) {
                this.historyIndex++;
                this.restoreFromHistory();
            }
        },

        restoreFromHistory() {
            const snapshot = this.history[this.historyIndex];
            Object.assign(this, snapshot);
        },

        onDateRangeChange() {
            if (this.startDate && this.endDate) {
                const start = new Date(this.startDate);
                const end = new Date(this.endDate);
                this.dateRangeError = start >= end;
            }
        },

        generateBreakdown() {
            if (!this.startDate || !this.endDate) {
                alert('Please enter both Start Date and End Date.'); 
                return;
            }

            const start = new Date(this.startDate);
            const end = new Date(this.endDate);
            
            if (start >= end) {
                this.dateRangeError = true;
                alert('Start Date must be before End Date.');
                return;
            }

            this.dateRangeError = false;
            this.saveHistory();

            // Initialize days metadata
            const days = this.daysRange();
            this.daysMeta = days.map(() => ({
                type: 'regular_day',
                threshold: 8.0
            }));

            // Initialize employee daily data
            this.employees.forEach(emp => {
                emp.daily = new Array(days.length).fill(0);
                emp.dayOverrides = new Array(days.length).fill('');
                emp.dayThresholds = new Array(days.length).fill(null);
            });

            this.breakdownReady = true;
            this.recomputeAllSummaries();
        },

        daysRange() {
            if (!this.startDate || !this.endDate) return [];
            const start = new Date(this.startDate);
            const end = new Date(this.endDate);
            const days = [];
            const current = new Date(start);
            
            while (current <= end) {
                days.push(current.toISOString().split('T')[0]);
                current.setDate(current.getDate() + 1);
            }
            return days;
        },

        addEmployee() {
            const newEmp = {
                name: `Employee ${this.employees.length + 1}`,
                useCustom: false,
                manualOverride: false,
                customRates: Object.fromEntries(this.customRateFields.map(field => [field, 0])),
                daily: new Array(this.daysRange().length).fill(0),
                dayOverrides: new Array(this.daysRange().length).fill(''),
                dayThresholds: new Array(this.daysRange().length).fill(null),
                ...Object.fromEntries(this.summaryFields.map(field => [field, 0]))
            };
            this.employees.push(newEmp);
            this.saveHistory();
        },

        addMultipleEmployees() {
            const count = Math.max(1, this.employeeToAdd);
            for (let i = 0; i < count; i++) {
                this.addEmployee();
            }
            this.employeeToAdd = 1;
        },

        deleteEmployee(index) {
            this.employees.splice(index, 1);
            this.saveHistory();
        },

        onDailyInput(emp, dayIndex) {
            this.saveHistory();
            this.computeEmpSummary(emp);
        },

        toggleEmpDayThreshold(empIndex, dayIndex) {
            const emp = this.employees[empIndex];
            if (emp.dayThresholds[dayIndex] != null) {
                emp.dayThresholds[dayIndex] = null;
            } else {
                emp.dayThresholds[dayIndex] = this.daysMeta[dayIndex].threshold;
            }
            this.saveHistory();
            this.computeEmpSummary(emp);
        },

        onManualOverrideToggle(emp) {
            if (!emp.manualOverride) {
                // Reset to computed values when turning off manual override
                this.computeEmpSummary(emp);
            }
            this.saveHistory();
        },

        computeEmpSummary(emp) {
            if (emp.manualOverride) return;

            // Reset all summary fields
            this.summaryFields.forEach(field => emp[field] = 0);

            emp.daily.forEach((hours, dayIndex) => {
                if (!hours) return;

                const dayType = emp.dayOverrides[dayIndex] || this.daysMeta[dayIndex].type;
                const threshold = emp.dayThresholds[dayIndex] != null ? 
                    emp.dayThresholds[dayIndex] : this.daysMeta[dayIndex].threshold;

                let regHours = 0, otHours = 0;

                if (hours <= threshold) {
                    regHours = hours;
                } else {
                    regHours = threshold;
                    otHours = hours - threshold;
                }

                // Apply rates based on day type
                const rateKey = dayType;
                const otRateKey = this.getOTRateKey(dayType);

                if (regHours > 0) {
                    emp[rateKey] += regHours;
                }
                if (otHours > 0 && otRateKey) {
                    emp[otRateKey] += otHours;
                }
            });
        },

        getOTRateKey(dayType) {
            const otMap = {
                'regular_day': 'regular_ot',
                'night_premium': 'night_premium_ot',
                'rest_day': 'sunday_ot',
                'sunday_night_premium': 'sunday_night_premium_ot',
                'regular_holiday': 'regular_holiday_ot',
                'reg_hol_night_premium': 'reg_hol_night_premium_ot'
            };
            return otMap[dayType] || null;
        },

        recomputeAllSummaries() {
            this.employees.forEach(emp => this.computeEmpSummary(emp));
        },

        recomputeTotals() {
            // Force recomputation of all employee summaries
            this.recomputeAllSummaries();
        },

        sumDaily(emp) {
            return emp.daily.reduce((sum, hours) => sum + (Number(hours) || 0), 0).toFixed(1);
        },

        dailyTotal(dayIndex) {
            return this.employees.reduce((sum, emp) => sum + (Number(emp.daily[dayIndex]) || 0), 0);
        },

        grandDailyTotal() {
            return this.employees.reduce((sum, emp) => 
                sum + emp.daily.reduce((empSum, hours) => empSum + (Number(hours) || 0), 0), 0
            );
        },

        totalHours(emp) {
            return this.summaryFields.reduce((sum, field) => sum + (Number(emp[field]) || 0), 0).toFixed(1);
        },

        totalPay(emp) {
            return this.summaryFields.reduce((total, field) => {
                const hours = Number(emp[field]) || 0;
                const rate = emp.useCustom && emp.customRates[field] ? 
                    emp.customRates[field] : this.rates[field];
                return total + (hours * rate);
            }, 0);
        },

        columnTotal(field) {
            return this.employees.reduce((sum, emp) => sum + (Number(emp[field]) || 0), 0).toFixed(1);
        },

        categoryTotalPay(field) {
            return this.employees.reduce((sum, emp) => {
                const hours = Number(emp[field]) || 0;
                const rate = emp.useCustom && emp.customRates[field] ? 
                    emp.customRates[field] : this.rates[field];
                return sum + (hours * rate);
            }, 0);
        },

        grandTotal() {
            return this.employees.reduce((sum, emp) => sum + this.totalPay(emp), 0);
        },

        currency(value) {
            return '₱' + Number(value).toLocaleString('en-PH', { 
                minimumFractionDigits: 2, 
                maximumFractionDigits: 2 
            });
        },

        dayTypeLabel(type) {
            return this.rateLabels[type] || type;
        },

        resetExceptRates() {
            if (confirm('Are you sure you want to reset everything except global rates?')) {
                this.summaryName = '';
                this.departmentName = '';
                this.startDate = '';
                this.endDate = '';
                this.daysMeta = [];
                this.employees = [];
                this.breakdownReady = false;
                this.saveHistory();
            }
        }
    };
}
</script>

</x-billing-app>