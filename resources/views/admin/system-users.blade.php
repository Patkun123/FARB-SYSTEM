<x-admin-layout>
    <title>System Users</title>

    <!-- Header -->
    <header class="sticky top-0 left-0 right-0 bg-white border-b border-gray-200 shadow-sm z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none transition">
                        <svg x-show="!sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <svg x-show="sidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
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
                    <x-nav-link :href="route('admin.invoice')" :active="request()->routeIs('admin.invoice')">
                        {{ __('Invoice') }}
                    </x-nav-link>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @include('admin.register-user')

            <div class="bg-white shadow-lg rounded-2xl p-6 border border-gray-200">

                <h1 class="text-2xl font-bold mb-4">System Users</h1>


                
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse table-auto">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3 text-left text-gray-700 font-semibold">Name</th>
                                <th class="p-3 text-left text-gray-700 font-semibold">Email</th>
                                <th class="p-3 text-left text-gray-700 font-semibold">Role</th>
                                <th class="p-3 text-left text-gray-700 font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3">{{ $user->name }}</td>
                                    <td class="p-3">{{ $user->email }}</td>
                                    <td class="p-3 capitalize">{{ $user->role }}</td>
                                    <td class="p-3 flex gap-2">
                                        <!-- Edit Button -->
                                        <button onclick="openEditModal({{ $user->id }})"
                                                class="px-4 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                                            Edit
                                        </button>

                                        <!-- Delete Button -->
                                        <form method="POST" action="{{ route('admin.system.users.destroy', $user->id) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="px-4 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </main>

    <!-- Edit Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-xl shadow-xl w-96 transform transition-transform duration-300 scale-90 opacity-0"
             id="editModalContent">
            <h2 class="text-lg font-bold mb-4">Edit User</h2>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <label class="block mb-1 font-medium">Name:</label>
                <input type="text" name="name" id="editName" class="w-full mb-2 border rounded p-2" required>

                <label class="block mb-1 font-medium">Email:</label>
                <input type="email" name="email" id="editEmail" class="w-full mb-2 border rounded p-2" required>

                <label class="block mb-1 font-medium">Role:</label>
                <select name="role" id="editRole" class="w-full mb-2 border rounded p-2" required>
                    <option value="admin">Admin</option>
                    <option value="billing_clerk">Billing Clerk</option>
                    <option value="receivable_clerk">Receivable Clerk</option>
                </select>

                <label class="block mb-1 font-medium">Password: <small>(leave blank if not changing)</small></label>

                <input type="password" name="password" class="w-full mb-2 border rounded p-2">
                <input type="password" name="password_confirmation" class="w-full mb-4 border rounded p-2">

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-1 bg-gray-400 text-white rounded hover:bg-gray-500 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
    

    <script>
        function openEditModal(userId) {
            fetch(`/admin/system-users/${userId}`)
                .then(res => res.json())
                .then(user => {
                    document.getElementById('editName').value = user.name;
                    document.getElementById('editEmail').value = user.email;
                    document.getElementById('editRole').value = user.role;
                    document.getElementById('editForm').action = `/admin/system-users/${user.id}`;
                    const modal = document.getElementById('editModal');
                    const modalContent = document.getElementById('editModalContent');
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        modalContent.classList.remove('scale-90', 'opacity-0');
                        modalContent.classList.add('scale-100', 'opacity-100');
                    }, 10);
                });
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            const modalContent = document.getElementById('editModalContent');
            modalContent.classList.add('scale-90', 'opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 200);
        }
    </script>
</x-admin-layout>
