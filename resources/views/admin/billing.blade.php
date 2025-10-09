<x-admin-layout>
    <!-- Header -->
    <header class="sticky top-0 left-0 right-0 bg-white border-b border-gray-200 shadow-sm z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Left Section: Sidebar Toggle + Logo -->
                <div class="flex items-center gap-3">
                    <!-- Sidebar Toggle -->
                    <button
                        @click="sidebarOpen = !sidebarOpen"
                        class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none transition"
                    >
                        <!-- Hamburger Icon -->
                        <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <!-- Close Icon -->
                        <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Logo -->
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10 object-contain">
                        <div class="ml-2 leading-tight">
                            <span class="text-lg font-semibold text-gray-800">FARB SYSTEM</span>
                            <p class="text-[11px] text-gray-500">Multi Purpose Cooperative</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden sm:flex sm:space-x-6">
                    <x-nav-link :href="route('admin.billing')" :active="request()->routeIs('billing')">
                        {{ __('Billing') }}
                    </x-nav-link>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-10 space-y-10 bg-gray-50 min-h-screen">
        <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-md hover:shadow-lg transition rounded-2xl p-8 border border-gray-200">

                <!-- Page Title -->
                <h1 class="text-2xl font-bold text-gray-800 mb-8 flex items-center gap-3">
                    <img class="w-10 h-10" src="{{ asset('img/billing.png') }}" alt="Billing">
                    Billing – Statement of Account
                </h1>

                <!-- SOA Details -->
              <form class="space-y-8" >
                    <!-- SOA Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">SOA Title</label>
                        <input  required type="text" placeholder="Enter SOA title"
                            class="mt-2 w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>


                    <!-- Client Company & Department -->
                        <div x-data="billingDropdown()" x-init="init()" class="flex flex-col sm:flex-row gap-6">

                            <!-- Client -->
                            <div class="flex-1 relative">
                                <label class="block text-sm font-medium text-gray-700">Client Company</label>
                                <div class="relative mt-2">
                                    <input required type="text" x-model="clientSearch" @input="filterClients"
                                        placeholder="Search Client..."
                                        :class="{'border-red-500 pr-10': clientError}"
                                        class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <!-- ❌ Error icon -->
                                    <span x-show="clientError" class="absolute right-3 top-2.5 text-red-500 text-lg">❌</span>
                                </div>

                                <!-- Dropdown list -->
                                <ul x-show="filteredClients.length" @click.outside="filteredClients = []"
                                    class="absolute z-10 bg-white border rounded shadow mt-1 w-full max-h-40 overflow-auto">
                                    <template x-for="client in filteredClients" :key="client.id">
                                        <li @click="selectClient(client)"
                                            class="px-3 py-2 hover:bg-indigo-100 cursor-pointer"
                                            x-text="client.company"></li>
                                    </template>
                                </ul>
                            </div>

                            <!-- Department -->
                            <div class="flex-1 relative">
                                <label class="block text-sm font-medium text-gray-700">Department</label>
                                <div class="relative mt-2">
                                    <input type="text" x-model="departmentSearch" @input="filterDepartments"
                                        placeholder="Search Department..."
                                        :class="{'border-red-500 pr-10': departmentError}"
                                        class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                    <!-- ❌ Error icon -->
                                    <span x-show="departmentError" class="absolute right-3 top-2.5 text-red-500 text-lg">❌</span>
                                </div>

                                <!-- Dropdown list -->
                                <ul x-show="filteredDepartments.length" @click.outside="filteredDepartments = []"
                                    class="absolute z-10 bg-white border rounded shadow mt-1 w-full max-h-40 overflow-auto">
                                    <template x-for="dept in filteredDepartments" :key="dept.id">
                                        <li @click="selectDepartment(dept)"
                                            class="px-3 py-2 hover:bg-indigo-100 cursor-pointer"
                                            x-text="dept.department"></li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                        <script>
                        function billingDropdown() {
                                    return {
                                        clients: [],
                                        filteredClients: [],
                                        selectedClient: null,
                                        clientSearch: '',
                                        clientError: false,

                                        departments: [],
                                        filteredDepartments: [],
                                        selectedDepartment: null,
                                        departmentSearch: '',
                                        departmentError: false,

                                        clientsUrl: '{{ route("admin.billing.clients") }}',
                                        departmentsUrl: '{{ route("admin.billing.departments") }}',

                                        init() {
                                            fetch(this.clientsUrl)
                                                .then(res => res.json())
                                                .then(data => {
                                                    this.clients = data;
                                                });
                                        },

                                        filterClients() {
                                            this.filteredClients = this.clients.filter(c =>
                                                c.company.toLowerCase().includes(this.clientSearch.toLowerCase())
                                            );
                                            this.clientError = false; // reset error while typing
                                        },

                                        selectClient(client) {
                                            this.selectedClient = client;
                                            this.clientSearch = client.company;
                                            this.clientError = false;
                                            this.fetchDepartments();
                                            this.filteredClients = [];
                                        },

                                        fetchDepartments() {
                                            this.departments = [];
                                            this.filteredDepartments = [];
                                            this.departmentSearch = '';
                                            this.selectedDepartment = null;
                                            this.departmentError = false;

                                            if (!this.selectedClient) return;

                                            fetch(`${this.departmentsUrl}?client_id=${this.selectedClient.id}`)
                                                .then(res => res.json())
                                                .then(data => {
                                                    this.departments = data;
                                                });
                                        },

                                        filterDepartments() {
                                            this.filteredDepartments = this.departments.filter(d =>
                                                d.department.toLowerCase().includes(this.departmentSearch.toLowerCase())
                                            );
                                            this.departmentError = false;
                                        },

                                        selectDepartment(dept) {
                                            this.selectedDepartment = dept;
                                            this.departmentSearch = dept.department;
                                            this.departmentError = false;
                                            this.filteredDepartments = [];
                                        },

                                        validateAndSubmit() {
                                            // Check if client exists
                                            const foundClient = this.clients.find(c => c.company.toLowerCase() === this.clientSearch.toLowerCase());
                                            if (!foundClient) {
                                                this.clientError = true;
                                                alert("Please select a valid client company.");
                                                return;
                                            }

                                            // Check if department exists for the selected client
                                            const foundDept = this.departments.find(d => d.department.toLowerCase() === this.departmentSearch.toLowerCase());
                                            if (!foundDept) {
                                                this.departmentError = true;
                                                alert("Please select a valid department for the selected client.");
                                                return;
                                            }

                                            // If both valid, proceed with submission (replace this with your save logic)
                                            this.clientError = this.departmentError = false;
                                            alert("Form submitted successfully!");
                                            // Example: document.querySelector("form").submit();
                                        }
                                    };
                                }
                            </script>


                    <!-- Covered Date -->
                    <div class="space-y-4 border-t pt-6">
                        <h2 class="text-lg font-semibold text-gray-700">Covered Date</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label required class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="date"
                                    class="mt-2 w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                            <div>
                                <label required class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="date"
                                    class="mt-2 w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                        <!-- Due Date -->
                            <div class="space-y-4 border-t pt-6">
                                <h2 class="text-lg font-semibold text-gray-700">Due Date</h2>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Set Payment Due Date</label>
                                    <input required type="date"
                                        class="mt-2 w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </div>
                            </div>

                    </div>

                    <!-- SOA Format -->
                    <div class="space-y-4 border-t pt-6">
                        <h2 class="text-lg font-semibold text-gray-700">Format </h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <input required type="text" placeholder="Personel Name"
                                class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <input required type="text" placeholder="Position"
                                class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">

                            </div>
                        <textarea rows="3" placeholder="Statement text..."
                                class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                    </div>



                    <!-- Billing Summary -->
                    <div x-data="billSummaryApp()" x-init="init()" class="space-y-4 border-t pt-6">
                        <h2 class="text-lg font-semibold text-gray-700">Bill Summary</h2>

                        <!-- Search input -->
                        <div class="mb-4 relative">
                            <input type="text" x-model="searchTerm" @input="filterSummaries"
                                placeholder="Search by summary name, department, or date..."
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">

                            <!-- Dropdown results -->
                            <ul x-show="filteredSummaries.length && searchTerm"
                                @click.outside="filteredSummaries = []"
                                class="absolute z-10 bg-white border rounded shadow mt-1 w-full max-h-80 overflow-auto">

                                <template x-for="summary in filteredSummaries" :key="summary.id">
                                    <li
                                        @click="!isAlreadySelected(summary.id) && selectSummary(summary)"
                                        class="px-4 py-3 hover:bg-indigo-100 cursor-pointer flex justify-between items-start"
                                        :class="isAlreadySelected(summary.id) ? 'opacity-50 cursor-not-allowed' : ''">

                                        <div>
                                            <div class="font-semibold text-gray-800"
                                                x-text="summary.summary_name"></div>
                                            <div class="text-sm text-gray-600"
                                                x-text="'Dept: ' + (summary.department_name ?? 'N/A')"></div>
                                            <div class="text-xs text-gray-500 italic"
                                                x-text="'Covered: ' + formatDate(summary.start_date) + ' → ' + formatDate(summary.end_date)"></div>
                                            <div class="text-xs text-gray-400 italic"
                                                x-text="'Created: ' + formatDate(summary.created_at)"></div>
                                        </div>

                                        <!-- Already Added Indicator -->
                                        <span x-show="isAlreadySelected(summary.id)"
                                            class="text-xs text-red-600 font-semibold">Added ✓</span>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <!-- Selected summaries -->
                        <template x-for="(item, index) in selectedSummaries" :key="index">
                            <div class="flex items-center gap-4 border p-4 rounded-lg bg-gray-50">
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800"
                                        x-text="item.summary_name ?? 'Select from search above'"></div>
                                    <div class="text-sm text-gray-600"
                                        x-text="'Dept: ' + (item.department_name ?? 'N/A')"></div>
                                    <div class="text-xs text-gray-500 italic"
                                        x-text="'Covered: ' + formatDate(item.start_date) + ' → ' + formatDate(item.end_date)"></div>
                                    <div class="text-xs text-gray-400 italic"
                                        x-text="'Created: ' + formatDate(item.created_at)"></div>
                                </div>
                                <span class="text-gray-700 font-semibold"
                                    x-text="'₱' + (item.total ?? 0).toLocaleString()"></span>
                                <button type="button" @click="removeSummary(index)"
                                    class="text-red-600 hover:text-red-800 font-semibold">×</button>
                            </div>
                        </template>

                        <!-- Removed Add Empty Summary button -->

                        <div class="mt-6 text-right border-t pt-4">
                            <span class="text-gray-700 font-semibold">Total Amount Due: </span>
                            <span class="text-2xl text-indigo-600 font-bold" x-text="'₱' + totalAmount.toLocaleString()"></span>
                        </div>
                    </div>

                    <script>
                    function billSummaryApp() {
                        return {
                            summaries: [],
                            filteredSummaries: [],
                            selectedSummaries: [],
                            searchTerm: '',
                            totalAmount: 0,

                            init() {
                                fetch('{{ route("admin.billing.summaries") }}')
                                    .then(res => res.json())
                                    .then(data => {
                                        this.summaries = data.map(s => ({
                                            ...s,
                                            created_at: s.created_at || new Date().toISOString()
                                        }));
                                    });
                            },

                            formatDate(dateStr) {
                                if (!dateStr) return '-';
                                const date = new Date(dateStr);
                                return date.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
                            },

                            filterSummaries() {
                                const term = this.searchTerm.toLowerCase();
                                this.filteredSummaries = this.summaries.filter(s =>
                                    s.summary_name.toLowerCase().includes(term) ||
                                    (s.department_name && s.department_name.toLowerCase().includes(term)) ||
                                    (s.start_date && this.formatDate(s.start_date).toLowerCase().includes(term)) ||
                                    (s.end_date && this.formatDate(s.end_date).toLowerCase().includes(term)) ||
                                    (s.created_at && this.formatDate(s.created_at).toLowerCase().includes(term))
                                );
                            },

                            selectSummary(summary) {
                                if (this.isAlreadySelected(summary.id)) return; // prevent duplicates
                                this.selectedSummaries.push({
                                    summary_id: summary.id,
                                    summary_name: summary.summary_name,
                                    department_name: summary.department_name,
                                    start_date: summary.start_date,
                                    end_date: summary.end_date,
                                    created_at: summary.created_at,
                                    total: parseFloat(summary.grand_total ?? 0)
                                });
                                this.searchTerm = '';
                                this.filteredSummaries = [];
                                this.computeTotal();
                            },

                            isAlreadySelected(id) {
                                return this.selectedSummaries.some(s => s.summary_id === id);
                            },

                            removeSummary(index) {
                                this.selectedSummaries.splice(index, 1);
                                this.computeTotal();
                            },

                            computeTotal() {
                                this.totalAmount = this.selectedSummaries.reduce((sum, s) => sum + (s.total || 0), 0);
                            }
                        }
                    }
                    </script>
<!-- Submit Button -->
                    <div class="pt-6 text-right">
                        <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white rounded-lg shadow hover:bg-indigo-700 transition">
                            Save Billing
                        </button>
                    </div>

                        </div>


                    </div>


                </form>

            </div>
        </div>

    </main>
</x-admin-layout>
