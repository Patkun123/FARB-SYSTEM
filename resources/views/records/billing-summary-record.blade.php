

    <main 
        class="pb-6 px-4 sm:px-6 lg:px-8" 
        x-data="billingSummaryApp()"
    >
        <div class="max-w-7xl mx-auto mt-6">
            <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-200">

                <!-- Page Header -->
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <img class="w-10 h-10" src="{{ asset('img/invoice_history.png') }}" alt="Billing">
                        Billing Management
                    </h1>
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        + Create Summary
                    </button>
                </div>

                <!-- Billing Summaries Table -->
                <div>
                    <div class="flex justify-between mb-4">
                        <input type="text" placeholder="Search summary..." class="border px-3 py-2 rounded-md w-1/3 text-sm">
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Summary ID</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Summary Name</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Department</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Start Date</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">End Date</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Created At</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="summary in summaries" :key="summary.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2" x-text="'#' + summary.id"></td>
                                        <td class="px-4 py-2" x-text="summary.summary_name"></td>
                                        <td class="px-4 py-2" x-text="summary.department_name"></td>
                                        <td class="px-4 py-2" x-text="formatDate(summary.start_date)"></td>
                                        <td class="px-4 py-2" x-text="formatDate(summary.end_date)"></td>
                                        <td class="px-4 py-2" x-text="formatDateTime(summary.created_at)"></td>
                                        <td class="px-4 py-2 text-center space-x-2">
                                            <button 
                                                @click="viewSummary(summary)" 
                                                class="text-indigo-600 hover:underline"
                                            >View</button>

                                            <button 
                                                @click="confirmDelete(summary)" 
                                                class="text-red-600 hover:underline"
                                            >Delete</button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- View Modal -->
        <div 
            x-show="showModal"
            x-transition
            class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
            style="display: none;"
        >
            <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-6xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">
                        Billing Summary Details
                    </h2>
                    <button @click="showModal = false" class="text-gray-500 hover:text-gray-700 text-xl">&times;</button>
                </div>

                <!-- Summary Info -->
                <div class="text-sm text-gray-700 space-y-2 border-b border-gray-200 pb-3 mb-3">
                    <p><strong>Summary Name:</strong> <span x-text="selected.summary_name"></span></p>
                    <p><strong>Department:</strong> <span x-text="selected.department_name"></span></p>
                    <p><strong>Start Date:</strong> <span x-text="formatDate(selected.start_date)"></span></p>
                    <p><strong>End Date:</strong> <span x-text="formatDate(selected.end_date)"></span></p>
                </div>

                <!-- Tabs -->
                <div class="flex space-x-4 mb-4 border-b border-gray-200">
                    <button 
                        class="pb-2 px-3 text-sm font-medium border-b-2"
                        :class="modalTab === 'rates' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        @click="modalTab = 'rates'"
                    >Employee Billing Rates</button>

                    <button 
                        class="pb-2 px-3 text-sm font-medium border-b-2"
                        :class="modalTab === 'employees' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        @click="modalTab = 'employees'"
                    >Employee Summary</button>

                    <button 
                        class="pb-2 px-3 text-sm font-medium border-b-2"
                        :class="modalTab === 'totals' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                        @click="modalTab = 'totals'"
                    >Totals</button>
                </div>

                <!-- Rates Tab -->
                <div x-show="modalTab === 'rates'" x-transition>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Rate Type</th>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Rate</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr>
                                    <td class="px-2 py-1">Regular Day</td>
                                    <td class="px-2 py-1" x-text="selected.rates.regular_day"></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1">Regular OT</td>
                                    <td class="px-2 py-1" x-text="selected.rates.regular_ot"></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1">Night Premium</td>
                                    <td class="px-2 py-1" x-text="selected.rates.night_premium"></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1">Night Premium OT</td>
                                    <td class="px-2 py-1" x-text="selected.rates.night_premium_ot"></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1">Rest Day</td>
                                    <td class="px-2 py-1" x-text="selected.rates.rest_day"></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1">Sunday OT</td>
                                    <td class="px-2 py-1" x-text="selected.rates.sunday_ot"></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1">Sunday Night Premium</td>
                                    <td class="px-2 py-1" x-text="selected.rates.sunday_night_premium"></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1">Sunday Night Premium OT</td>
                                    <td class="px-2 py-1" x-text="selected.rates.sunday_night_premium_ot"></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1">Regular Holiday</td>
                                    <td class="px-2 py-1" x-text="selected.rates.regular_holiday"></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1">Regular Holiday OT</td>
                                    <td class="px-2 py-1" x-text="selected.rates.regular_holiday_ot"></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1">Reg Hol Night Premium</td>
                                    <td class="px-2 py-1" x-text="selected.rates.reg_hol_night_premium"></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1">Reg Hol Night Premium OT</td>
                                    <td class="px-2 py-1" x-text="selected.rates.reg_hol_night_premium_ot"></td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-1">Unworked Regular Day</td>
                                    <td class="px-2 py-1" x-text="selected.rates.unworked_regular_day"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Employees Tab -->
                <div x-show="modalTab === 'employees'" x-transition>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-xs">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Employee</th>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Regular Day</th>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Regular OT</th>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Night Premium</th>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Night Premium OT</th>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Rest Day</th>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Sunday OT</th>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Sunday Night Premium</th>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Sunday Night Premium OT</th>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Regular Holiday</th>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Regular Holiday OT</th>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Reg Hol Night Premium</th>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Reg Hol Night Premium OT</th>
                                    <th class="px-2 py-1 text-left font-medium text-gray-500 uppercase">Unworked Regular Day</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <template x-for="employee in selected.employees" :key="employee.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-2 py-1" x-text="employee.name"></td>
                                        <td class="px-2 py-1" x-text="employee.regular_day"></td>
                                        <td class="px-2 py-1" x-text="employee.regular_ot"></td>
                                        <td class="px-2 py-1" x-text="employee.night_premium"></td>
                                        <td class="px-2 py-1" x-text="employee.night_premium_ot"></td>
                                        <td class="px-2 py-1" x-text="employee.rest_day"></td>
                                        <td class="px-2 py-1" x-text="employee.sunday_ot"></td>
                                        <td class="px-2 py-1" x-text="employee.sunday_night_premium"></td>
                                        <td class="px-2 py-1" x-text="employee.sunday_night_premium_ot"></td>
                                        <td class="px-2 py-1" x-text="employee.regular_holiday"></td>
                                        <td class="px-2 py-1" x-text="employee.regular_holiday_ot"></td>
                                        <td class="px-2 py-1" x-text="employee.reg_hol_night_premium"></td>
                                        <td class="px-2 py-1" x-text="employee.reg_hol_night_premium_ot"></td>
                                        <td class="px-2 py-1" x-text="employee.unworked_regular_day"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Totals Tab -->
                <div x-show="modalTab === 'totals'" x-transition>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Category</th>
                                    <th class="px-4 py-2 text-left font-medium text-gray-500 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-4 py-2">Regular Day Total</td>
                                    <td class="px-4 py-2" x-text="selected.totals.regular_day_total"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Regular OT Total</td>
                                    <td class="px-4 py-2" x-text="selected.totals.regular_ot_total"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Night Premium Total</td>
                                    <td class="px-4 py-2" x-text="selected.totals.night_premium_total"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Night Premium OT Total</td>
                                    <td class="px-4 py-2" x-text="selected.totals.night_premium_ot_total"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Rest Day Total</td>
                                    <td class="px-4 py-2" x-text="selected.totals.rest_day_total"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Sunday OT Total</td>
                                    <td class="px-4 py-2" x-text="selected.totals.sunday_ot_total"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Sunday Night Premium Total</td>
                                    <td class="px-4 py-2" x-text="selected.totals.sunday_night_premium_total"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Sunday Night Premium OT Total</td>
                                    <td class="px-4 py-2" x-text="selected.totals.sunday_night_premium_ot_total"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Regular Holiday Total</td>
                                    <td class="px-4 py-2" x-text="selected.totals.regular_holiday_total"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Regular Holiday OT Total</td>
                                    <td class="px-4 py-2" x-text="selected.totals.regular_holiday_ot_total"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Reg Hol Night Premium Total</td>
                                    <td class="px-4 py-2" x-text="selected.totals.reg_hol_night_premium_total"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Reg Hol Night Premium OT Total</td>
                                    <td class="px-4 py-2" x-text="selected.totals.reg_hol_night_premium_ot_total"></td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2">Unworked Regular Day Total</td>
                                    <td class="px-4 py-2" x-text="selected.totals.unworked_regular_day_total"></td>
                                </tr>
                                <tr class="bg-gray-50">
                                    <td class="px-4 py-2 font-semibold">Grand Total</td>
                                    <td class="px-4 py-2 font-semibold text-green-600" x-text="selected.totals.grand_total"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 text-right">
                    <button @click="showModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation -->
        <div 
            x-show="showDeleteConfirm"
            x-transition
            class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50"
            style="display: none;"
        >
            <div class="bg-white p-6 rounded-xl shadow-xl w-full max-w-sm text-center">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">Delete Billing Summary?</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to delete "<span x-text="deleteTarget ? deleteTarget.summary_name : ''"></span>"? This action cannot be undone.</p>
                <div class="flex justify-center gap-3">
                    <button 
                        @click="showDeleteConfirm = false"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-md text-sm"
                    >Cancel</button>
                    <button 
                        @click="deleteSummary()"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm"
                    >Delete</button>
                </div>
            </div>
        </div>
    </main>

    <script>
        function billingSummaryApp() {
            return {
                showModal: false,
                showDeleteConfirm: false,
                modalTab: 'rates',
                selected: {},
                deleteTarget: null,

                summaries: [
                    {
                        id: 1,
                        summary_name: 'October 2025 Billing',
                        department_name: 'Operations',
                        start_date: '2025-10-01',
                        end_date: '2025-10-31',
                        created_at: '2025-10-15 10:30:00',
                        rates: {
                            regular_day: '1.00',
                            regular_ot: '1.25',
                            night_premium: '1.10',
                            night_premium_ot: '1.375',
                            rest_day: '1.30',
                            sunday_ot: '1.69',
                            sunday_night_premium: '1.43',
                            sunday_night_premium_ot: '1.859',
                            regular_holiday: '2.00',
                            regular_holiday_ot: '2.60',
                            reg_hol_night_premium: '2.20',
                            reg_hol_night_premium_ot: '2.86',
                            unworked_regular_day: '1.00'
                        },
                        employees: [
                            { 
                                id: 1, 
                                name: 'John Doe',
                                regular_day: '8.00',
                                regular_ot: '2.50',
                                night_premium: '0.00',
                                night_premium_ot: '0.00',
                                rest_day: '1.30',
                                sunday_ot: '0.00',
                                sunday_night_premium: '0.00',
                                sunday_night_premium_ot: '0.00',
                                regular_holiday: '0.00',
                                regular_holiday_ot: '0.00',
                                reg_hol_night_premium: '0.00',
                                reg_hol_night_premium_ot: '0.00',
                                unworked_regular_day: '0.00'
                            },
                            { 
                                id: 2, 
                                name: 'Jane Smith',
                                regular_day: '7.50',
                                regular_ot: '2.00',
                                night_premium: '1.50',
                                night_premium_ot: '0.75',
                                rest_day: '1.20',
                                sunday_ot: '0.00',
                                sunday_night_premium: '0.00',
                                sunday_night_premium_ot: '0.00',
                                regular_holiday: '0.00',
                                regular_holiday_ot: '0.00',
                                reg_hol_night_premium: '0.00',
                                reg_hol_night_premium_ot: '0.00',
                                unworked_regular_day: '0.00'
                            }
                        ],
                        totals: {
                            regular_day_total: '$4,500.00',
                            regular_ot_total: '$2,200.00',
                            night_premium_total: '$450.00',
                            night_premium_ot_total: '$180.00',
                            rest_day_total: '$1,200.00',
                            sunday_ot_total: '$0.00',
                            sunday_night_premium_total: '$0.00',
                            sunday_night_premium_ot_total: '$0.00',
                            regular_holiday_total: '$0.00',
                            regular_holiday_ot_total: '$0.00',
                            reg_hol_night_premium_total: '$0.00',
                            reg_hol_night_premium_ot_total: '$0.00',
                            unworked_regular_day_total: '$0.00',
                            grand_total: '$12,450.00'
                        }
                    },
                    {
                        id: 2,
                        summary_name: 'November 2025 Billing',
                        department_name: 'Sales',
                        start_date: '2025-11-01',
                        end_date: '2025-11-30',
                        created_at: '2025-11-05 14:20:00',
                        rates: {
                            regular_day: '1.00',
                            regular_ot: '1.25',
                            night_premium: '1.10',
                            night_premium_ot: '1.375',
                            rest_day: '1.30',
                            sunday_ot: '1.69',
                            sunday_night_premium: '1.43',
                            sunday_night_premium_ot: '1.859',
                            regular_holiday: '2.00',
                            regular_holiday_ot: '2.60',
                            reg_hol_night_premium: '2.20',
                            reg_hol_night_premium_ot: '2.86',
                            unworked_regular_day: '1.00'
                        },
                        employees: [
                            { 
                                id: 3, 
                                name: 'Robert Johnson',
                                regular_day: '8.00',
                                regular_ot: '1.50',
                                night_premium: '0.00',
                                night_premium_ot: '0.00',
                                rest_day: '0.80',
                                sunday_ot: '0.00',
                                sunday_night_premium: '0.00',
                                sunday_night_premium_ot: '0.00',
                                regular_holiday: '0.00',
                                regular_holiday_ot: '0.00',
                                reg_hol_night_premium: '0.00',
                                reg_hol_night_premium_ot: '0.00',
                                unworked_regular_day: '0.00'
                            }
                        ],
                        totals: {
                            regular_day_total: '$3,200.00',
                            regular_ot_total: '$900.00',
                            night_premium_total: '$0.00',
                            night_premium_ot_total: '$0.00',
                            rest_day_total: '$450.00',
                            sunday_ot_total: '$0.00',
                            sunday_night_premium_total: '$0.00',
                            sunday_night_premium_ot_total: '$0.00',
                            regular_holiday_total: '$0.00',
                            regular_holiday_ot_total: '$0.00',
                            reg_hol_night_premium_total: '$0.00',
                            reg_hol_night_premium_ot_total: '$0.00',
                            unworked_regular_day_total: '$0.00',
                            grand_total: '$4,550.00'
                        }
                    }
                ],

                viewSummary(summary) {
                    this.selected = summary;
                    this.modalTab = 'rates';
                    this.showModal = true;
                },

                confirmDelete(summary) {
                    this.deleteTarget = summary;
                    this.showDeleteConfirm = true;
                },

                deleteSummary() {
                    this.summaries = this.summaries.filter(s => s.id !== this.deleteTarget.id);
                    this.showDeleteConfirm = false;
                    this.deleteTarget = null;
                },

                formatDate(dateString) {
                    if (!dateString) return '';
                    const date = new Date(dateString);
                    return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
                },

                formatDateTime(dateTimeString) {
                    if (!dateTimeString) return '';
                    const date = new Date(dateTimeString);
                    return date.toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            }
        }
    </script>
