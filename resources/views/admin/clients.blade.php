<x-admin-layout>
    <title>Clients Management</title>

    <div x-data="clientApp({{ $clients->toJson() }})" x-cloak>

        <!-- Header -->
        <div class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <div class="flex items-center gap-4">
                        <button @click="sidebarOpen = !sidebarOpen"
                            class="p-2 rounded-md text-gray-600 hover:bg-gray-100 focus:outline-none transition">
                            <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <!-- Logo -->
                        <a href="{{ route('dashboard') }}" class="flex items-center">
                            <img src="{{ asset('img/logo_trans.png') }}" alt="logo" class="w-10 h-10">
                            <div class="ml-2 leading-tight">
                                <span class="block text-lg font-semibold text-gray-800">FARB SYSTEM</span>
                                <span class="block text-xs text-gray-500">Multi Purpose Cooperative</span>
                            </div>
                        </a>
                    </div>

                    <div class="hidden sm:flex sm:space-x-6">
                        <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('DASHBOARD') }}
                        </x-nav-link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="py-10 space-y-10 bg-gray-50 min-h-screen">

            <!-- Add Client Form -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white shadow-md rounded-2xl p-8 border border-gray-200">
                    <h1 class="text-2xl font-bold text-gray-800 mb-8 flex items-center gap-3">
                        <img class="w-10 h-10" src="{{ asset('img/client.png') }}" alt="Client">
                        Add New Client
                    </h1>

                    <form class="space-y-6" method="POST" action="{{ route('admin.clients.store') }}">
                        @csrf
                            @if ($errors->any())
                                <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700">
                                    <ul class="list-disc list-inside text-sm space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        <div>
                            <label class="block text-sm font-semibold">Client Company</label>
                            <input type="text" name="company"
                                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(dept, index) in departments" :key="index">
                                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                                    <div>
                                        <label class="block text-sm font-semibold">Department</label>
                                        <input type="text" :name="`departments[${index}][department]`" x-model="dept.department"
                                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold">Personnel</label>
                                        <input type="text" :name="`departments[${index}][personnel]`" x-model="dept.personnel"
                                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold">Position</label>
                                        <input type="text" :name="`departments[${index}][position]`" x-model="dept.position"
                                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div class="flex gap-2">
                                        <div class="flex-1">
                                            <label class="block text-sm font-semibold">Client Email</label>
                                            <input type="email" :name="`departments[${index}][email]`" x-model="dept.email"
                                                class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        </div>
                                        <button type="button"
                                            class="mt-7 px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition"
                                            @click="departments.splice(index, 1)">
                                            ✕
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <button type="button"
                                class="mt-2 px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 text-sm transition"
                                @click="departments.push({ department: '', email: '', personnel: '', position: '' })">
                                + Add Department
                            </button>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit"
                                class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg shadow hover:bg-indigo-700 transition">
                                Save Client
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Clients Table -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white shadow-md rounded-2xl p-8 border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">Client List</h2>

                    <!-- Search -->
                    <div class="mb-4 flex gap-2">
                        <input type="text"
                            placeholder="Search by company, department, personnel, position, or email"
                            x-model="searchQuery"
                            @input="currentPage = 1"
                            class="flex-1 px-4 py-2 border rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <button type="button"
                            @click="searchQuery = ''; currentPage = 1"
                            class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">
                            Reset
                        </button>
                    </div>

                    <div class="overflow-x-auto rounded-lg shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-indigo-600 text-white sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">#</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Company</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Department</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Personnel</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Position</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Email</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <template x-for="(client, index) in paginatedClients" :key="client.id">
                                    <tr class="odd:bg-gray-50 hover:bg-indigo-50 transition">
                                        <td class="px-4 py-3 text-sm text-gray-700"
                                            x-text="(currentPage - 1) * pageSize + index + 1"></td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900"
                                            x-text="client.company"></td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            <ul class="list-disc list-inside">
                                                <template x-for="dept in client.departments" :key="dept.id">
                                                    <li x-text="dept.department"></li>
                                                </template>
                                            </ul>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            <ul class="list-disc list-inside">
                                                <template x-for="dept in client.departments" :key="dept.id">
                                                    <li x-text="dept.personnel"></li>
                                                </template>
                                            </ul>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            <ul class="list-disc list-inside">
                                                <template x-for="dept in client.departments" :key="dept.id">
                                                    <li x-text="dept.position"></li>
                                                </template>
                                            </ul>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            <ul class="list-disc list-inside">
                                                <template x-for="dept in client.departments" :key="dept.id">
                                                    <li x-text="dept.email"></li>
                                                </template>
                                            </ul>
                                        </td>
                                        <td class="px-4 py-3 text-sm flex gap-2">
                                            <button type="button"
                                                @click="editModal = true; clientToEdit = JSON.parse(JSON.stringify(client))"
                                                class="px-3 py-1 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-xs transition">
                                                Edit
                                            </button>
                                            <button type="button"
                                                @click="deleteModal = true; clientIdToDelete = client.id"
                                                class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 text-xs transition">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="flex justify-between items-center mt-6">
                        <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                            class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300 disabled:opacity-50 transition">Previous</button>
                        <div class="flex gap-2">
                            <template x-for="page in totalPages" :key="page">
                                <button @click="goToPage(page)" class="px-3 py-1 rounded-lg"
                                    :class="page === currentPage ? 'bg-indigo-600 text-white' : 'bg-gray-200 hover:bg-gray-300'">
                                    <span x-text="page"></span>
                                </button>
                            </template>
                        </div>
                        <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                            class="px-3 py-1 bg-gray-200 rounded-lg hover:bg-gray-300 disabled:opacity-50 transition">Next</button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Delete Modal -->
        <div x-show="deleteModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Confirm Deletion</h2>
                <p class="text-gray-600 mb-6">Are you sure you want to delete this client? This action cannot be undone.</p>
                <div class="flex justify-end gap-3">
                    <button @click="deleteModal = false"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Cancel</button>
                    <form :action="`/admin/clients/${clientIdToDelete}`" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">Delete</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="editModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-2xl max-h-[80vh] overflow-y-auto" @click.away="editModal = false">
                <h2 class="text-lg font-bold text-gray-800 mb-6">Edit Client</h2>
                <form :action="`/admin/clients/${clientToEdit.id}`" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-semibold">Company</label>
                        <input type="text" name="company" x-model="clientToEdit.company"
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            required>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(dept, index) in clientToEdit.departments" :key="index">
                            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                                <div>
                                    <label class="block text-sm font-semibold">Department</label>
                                    <input type="text" :name="`departments[${index}][department]`" x-model="dept.department"
                                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold">Personnel</label>
                                    <input type="text" :name="`departments[${index}][personnel]`" x-model="dept.personnel"
                                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold">Position</label>
                                    <input type="text" :name="`departments[${index}][position]`" x-model="dept.position"
                                        class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                </div>
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <label class="block text-sm font-semibold">Email</label>
                                        <input type="email" :name="`departments[${index}][email]`" x-model="dept.email"
                                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <button type="button"
                                        class="mt-7 px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition"
                                        @click="clientToEdit.departments.splice(index, 1)">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </template>
                        <button type="button"
                            class="mt-2 px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 text-sm transition"
                            @click="clientToEdit.departments.push({ department: '', email: '', personnel: '', position: '' })">
                            + Add Department
                        </button>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" @click="editModal = false"
                            class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition">Cancel</button>
                        <button type="submit"
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alpine Script -->
    <script>
        function clientApp(serverClients) {
            return {
                // modals
                successModal: false,
                successMessage: '',
                deleteModal: false,
                editModal: false,
                clientIdToDelete: null,
                clientToEdit: { id: null, company: '', departments: [{ department: '', email: '', personnel: '', position: '' }] },

                // departments for Add Client form
                departments: [{ department: '', email: '', personnel: '', position: '' }],

                // data
                clients: serverClients,
                searchQuery: '',
                currentPage: 1,
                pageSize: 5,

                get filteredClients() {
                    if (!this.searchQuery) return this.clients;
                    return this.clients.filter(c =>
                        c.company.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                        c.departments.some(d =>
                            d.department.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            d.email.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            d.personnel.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            d.position.toLowerCase().includes(this.searchQuery.toLowerCase())
                        )
                    );
                },
                get totalPages() {
                    return Math.ceil(this.filteredClients.length / this.pageSize) || 1;
                },
                get paginatedClients() {
                    const start = (this.currentPage - 1) * this.pageSize;
                    return this.filteredClients.slice(start, start + this.pageSize);
                },
                goToPage(p) {
                    if (p >= 1 && p <= this.totalPages) this.currentPage = p;
                },

            }
        }
    </script>
</x-admin-layout>
