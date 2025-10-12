<x-admin-layout>
    <title>Billing - Statement of Account</title>

    <!-- Header -->
    <header class="sticky top-0 left-0 right-0 bg-white border-b border-gray-200 shadow-sm z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Sidebar Toggle + Logo -->
                <div class="flex items-center gap-3">
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

                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10 object-contain">
                        <div class="ml-2 leading-tight">
                            <span class="text-lg font-semibold text-gray-800">FARB SYSTEM</span>
                            <p class="text-[11px] text-gray-500">Multi Purpose Cooperative</p>
                        </div>
                    </a>
                </div>

                <nav class="hidden sm:flex sm:space-x-6">
                    <x-nav-link :href="route('admin.billing')" :active="request()->routeIs('billing')">
                        {{ __('Billing') }}
                    </x-nav-link>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-6 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
              <div class="flex justify-between items-center mb-6">
                    <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                        <img class="w-10 h-10" src="{{ asset('img/billing.png') }}" alt="Billing">
                        Billing-Statement of Account
                    </h1>

                </div>

            <form
                x-data="billingApp()"
                x-init="init()"
                x-ref="billingForm"
                action="{{ route('admin.billing.store') }}"
                method="POST"
                @submit.prevent="prepareSubmit"
            >
                @csrf

                <!-- CLIENT INFO -->
                <section class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Client Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-gray-700 font-medium">SOA Title</label>
                            <input name="soa_title" required type="text" placeholder="Enter SOA title"
                                class="mt-2 w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2">
                        </div>

                        <div>
                            <label class="text-gray-700 font-medium">Client Company</label>
                            <select name="client_id" x-model="selectedClientId" @change="fetchDepartments" required
                                class="mt-2 w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2">
                                <option value="">Select Client</option>
                                <template x-for="client in clients" :key="client.id">
                                    <option :value="client.id" x-text="client.company"></option>
                                </template>
                            </select>
                        </div>


                        <div>
                            <label class="text-gray-700 font-medium">Department</label>
                            <select name="department_id"
                                x-model="selectedDepartmentId"
                                @change="updatePersonnelAndPosition"
                                required
                                class="mt-2 w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2">
                                <option value="">Select Department</option>
                                <template x-for="department in departments" :key="department.id">
                                    <option :value="department.id" x-text="department.department"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- BILLING DATES -->
                <section class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Billing Dates</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="text-gray-700 font-medium">Covered Start Date</label>
                            <input name="covered_start_date" type="date" required
                                class="mt-2 w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2">
                        </div>

                        <div>
                            <label class="text-gray-700 font-medium">Covered End Date</label>
                            <input name="covered_end_date" type="date" required
                                class="mt-2 w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2">
                        </div>

                        <div>
                            <label class="text-gray-700 font-medium">Due Date</label>
                            <input name="due_date" type="date" required
                                class="mt-2 w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2">
                        </div>
                    </div>
                </section>

                <!-- PERSONNEL INFO -->
                <section class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Personnel Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-gray-700 font-medium">Personnel Name</label>
                            <div class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 bg-gray-100">
                                <span x-text="selectedPersonnelName || 'N/A'"></span>
                            </div>
                            <input type="hidden" name="personnel_name" :value="selectedPersonnelName">
                        </div>

                        <div>
                            <label class="text-gray-700 font-medium">Position</label>
                            <div class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2 bg-gray-100">
                                <span x-text="selectedPosition || 'N/A'"></span>
                            </div>
                            <input type="hidden" name="position" :value="selectedPosition">
                        </div>
                    </div>
                </section>

                <!-- STATEMENT SUMMARY -->
                <section class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Statement Summary</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-gray-700 font-medium">Statement Text</label>
                            <textarea name="statement_text" rows="3" placeholder="Statement text..."
                                class="w-full rounded-lg border border-gray-300 shadow-sm px-3 py-2"></textarea>
                        </div>

                        <div>
                            <label class="text-gray-700 font-medium">Billing Summaries</label>

                            <div class="relative mt-2">
                                <div class="flex items-center border border-gray-300 rounded-lg px-3 py-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                                    </svg>
                                    <input type="text"
                                        x-model="searchQuery"
                                        @input="debouncedFilterSummaries"
                                        placeholder="Search summary name, department, or date..."
                                        class="w-full outline-none text-gray-700"
                                    >
                                    <template x-if="loadingSearch">
                                        <svg class="animate-spin h-4 w-4 text-gray-400 ml-2"
                                            xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8v8z"></path>
                                        </svg>
                                    </template>
                                </div>

                                <ul x-show="searchQuery && filteredSummaries.length > 0"
                                    class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow mt-1 max-h-64 overflow-auto">
                                    <template x-for="summary in filteredSummaries" :key="summary.id">
                                        <li @click="selectSummary(summary)"
                                            class="px-3 py-2 cursor-pointer hover:bg-indigo-50 transition"
                                            :class="{'bg-gray-100 cursor-not-allowed opacity-60': isSelected(summary.id)}">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <p class="font-medium text-gray-800" x-text="summary.summary_name"></p>
                                                    <p class="text-sm text-gray-500">
                                                        <span x-text="summary.department_name"></span> •
                                                        <span x-text="summary.start_date"></span> -
                                                        <span x-text="summary.end_date"></span>
                                                    </p>
                                                    <p class="text-xs text-gray-400 mt-1">
                                                        Created: <span x-text="new Date(summary.created_at).toLocaleDateString()"></span>
                                                    </p>
                                                </div>
                                                <template x-if="isSelected(summary.id)">
                                                    <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-0.5 rounded">✓ Selected</span>
                                                </template>
                                            </div>
                                        </li>
                                    </template>
                                </ul>

                                <div x-show="searchQuery && !filteredSummaries.length && !loadingSearch"
                                    class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow mt-1 p-3 text-gray-500 text-sm">
                                    No results found.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border border-gray-300 rounded-lg p-3 mt-3 bg-gray-50">
                        <template x-if="selectedSummaries.length > 0">
                            <div>
                                <template x-for="item in selectedSummaries" :key="item.id">
                                    <div class="p-3 bg-white border border-gray-200 rounded-lg shadow-sm mb-2">
                                        <div class="flex justify-between items-center mb-2">
                                            <div class="flex items-center gap-2">
                                                <span class="text-xs font-semibold text-green-600 bg-green-100 px-2 py-0.5 rounded">✓ Selected</span>
                                                <h3 class="font-semibold text-gray-800" x-text="item.summary_name"></h3>
                                            </div>
                                            <button type="button" @click="removeSummary(item.id)"
                                                class="text-red-500 hover:text-red-700 text-sm font-medium">Cancel</button>
                                        </div>
                                        <div class="grid sm:grid-cols-2 text-sm text-gray-600 gap-x-6">
                                            <div>
                                                <p><strong>Department:</strong> <span x-text="item.department_name"></span></p>
                                                <p><strong>Created:</strong> <span x-text="new Date(item.created_at).toLocaleDateString()"></span></p>
                                            </div>
                                            <div>
                                                <p><strong>Start:</strong> <span x-text="item.start_date"></span></p>
                                                <p><strong>End:</strong> <span x-text="item.end_date"></span></p>
                                            </div>
                                        </div>
                                        <div class="flex justify-end mt-2 text-gray-800 font-semibold">
                                            ₱ <span x-text="Number(item.grand_total).toFixed(2)"></span>
                                        </div>
                                    </div>
                                </template>

                                <div class="flex justify-between font-semibold text-gray-900 border-t border-gray-300 pt-3 mt-3">
                                    <span>Total Amount Due:</span>
                                    <span>₱ <span x-text="totalAmount.toFixed(2)"></span></span>
                                </div>
                            </div>
                        </template>

                        <template x-if="selectedSummaries.length === 0">
                            <p class="text-sm text-gray-500">No billing summaries selected yet.</p>
                        </template>

                        <!-- Hidden Inputs -->
                        <input type="hidden" name="summaries" x-ref="summariesInput">
                        <input type="hidden" name="total_amount_due" x-ref="totalAmountInput">
                    </div>
                </section>

                <!-- SUBMIT BUTTON -->
                <div class="flex justify-end mt-6">
                    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg shadow-sm hover:bg-indigo-700 transition">
                        Save Statement
                    </button>
                </div>
            </form>
        </div>
    </main>

<script>
function billingApp() {
    return {
        clients: [],
        departments: [],
        selectedClientId: '',
        selectedDepartmentId: '',
        selectedPersonnelName: '',
        selectedPosition: '',
        filteredSummaries: [],
        selectedSummaries: [],
        searchQuery: '',
        totalAmount: 0,
        loadingSearch: false,
        debounceTimer: null,
        controller: null,

        init() {
            fetch('{{ route("admin.billing.clients") }}')
                .then(res => res.json())
                .then(data => this.clients = data);
        },

        fetchDepartments() {
            if (!this.selectedClientId) return;
            fetch(`{{ route("admin.billing.departments") }}?client_id=${this.selectedClientId}`)
                .then(res => res.json())
                .then(data => this.departments = data);
        },

        updatePersonnelAndPosition() {
            const selected = this.departments.find(d => d.id == this.selectedDepartmentId);
            this.selectedPersonnelName = selected?.personnel || '';
            this.selectedPosition = selected?.position || '';
        },

        debouncedFilterSummaries() {
            clearTimeout(this.debounceTimer);
            this.loadingSearch = true;
            this.debounceTimer = setTimeout(() => this.searchSummaries(), 400);
        },

        async searchSummaries() {
            const q = this.searchQuery.trim();
            if (!q) {
                this.filteredSummaries = [];
                this.loadingSearch = false;
                return;
            }

            if (this.controller) this.controller.abort();
            this.controller = new AbortController();

            try {
                const res = await fetch(`{{ route('admin.billing.summaries') }}?search=${encodeURIComponent(q)}`, {
                    signal: this.controller.signal
                });
                const data = await res.json();
                this.filteredSummaries = data;
            } catch (err) {
                if (err.name !== 'AbortError') console.error(err);
            } finally {
                this.loadingSearch = false;
            }
        },

        selectSummary(summary) {
            if (this.isSelected(summary.id)) return;
            this.selectedSummaries.push(summary);
            this.updateTotal();
            this.searchQuery = '';
            this.filteredSummaries = [];
        },

        removeSummary(id) {
            this.selectedSummaries = this.selectedSummaries.filter(s => s.id !== id);
            this.updateTotal();
        },

        isSelected(id) {
            return this.selectedSummaries.some(s => s.id === id);
        },

        updateTotal() {
            this.totalAmount = this.selectedSummaries.reduce((sum, s) => sum + parseFloat(s.grand_total || 0), 0);
        },

        prepareSubmit() {
            // Update hidden inputs manually
            this.$refs.summariesInput.value = JSON.stringify(this.selectedSummaries);
            this.$refs.totalAmountInput.value = this.totalAmount.toFixed(2);

            // Submit the form using form ref
            this.$refs.billingForm.submit();
        }
    }
}
</script>
</x-admin-layout>
