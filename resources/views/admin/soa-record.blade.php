<x-admin-layout>

    <main class="pb-6 px-4 sm:px-6 lg:px-8" x-data="{ search: '', showModal: false }">
        <div class="max-w-7xl mx-auto mt-6">
            <div class="bg-white shadow-lg rounded-2xl p-8 border border-gray-200">
                
                <!-- Page Title -->
                <h1 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <img class="w-10 h-10" src="{{ asset('img/invoice_history.png') }}" alt="SOA">
                    Statement of Account
                </h1>

                <!-- Search Bar -->
                <div class="mb-4">
                    <input 
                        type="text" 
                        placeholder="Search by client or department..." 
                        x-model="search"
                        class="w-full sm:w-1/3 px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:outline-none"
                    />
                </div>

                <!-- Statement of Account Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">SOA Title</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Client</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Covered Start</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Covered End</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Personnel</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Total Amount Due</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Sample Row (replace with dynamic data later) -->
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm text-gray-700">SOA - Oct 2025</td>
                                <td class="px-4 py-2 text-sm text-gray-700">Sunrise Café</td>
                                <td class="px-4 py-2 text-sm text-gray-700">Marketing</td>
                                <td class="px-4 py-2 text-sm text-gray-700">2025-10-01</td>
                                <td class="px-4 py-2 text-sm text-gray-700">2025-10-31</td>
                                <td class="px-4 py-2 text-sm text-gray-700">2025-11-10</td>
                                <td class="px-4 py-2 text-sm text-gray-700">John Doe</td>
                                <td class="px-4 py-2 text-sm text-gray-700">Billing Officer</td>
                                <td class="px-4 py-2 text-sm font-semibold text-green-600">$2,450.00</td>
                                <td class="px-4 py-2 text-sm text-indigo-600 hover:underline cursor-pointer">
                                    <button @click="showModal = true">View</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-4 flex justify-end">
                    <nav class="inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                        <a href="#" class="px-3 py-1 border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">Previous</a>
                        <a href="#" class="px-3 py-1 border border-gray-300 bg-white text-gray-700 hover:bg-gray-50">1</a>
                        <a href="#" class="px-3 py-1 border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">Next</a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div 
            x-show="showModal"
            class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50"
            x-transition
        >
            <div class="bg-white rounded-xl shadow-lg max-w-lg w-full p-6">
                <h2 class="text-lg font-semibold mb-4 text-gray-800 flex justify-between items-center">
                    SOA Details
                    <button @click="showModal = false" class="text-gray-500 hover:text-gray-700 text-xl">&times;</button>
                </h2>

                <!-- SOA Main Info -->
                <div class="space-y-2 text-sm text-gray-700 border-b pb-4 mb-4">
                    <p><strong>SOA Title:</strong> SOA - Oct 2025</p>
                    <p><strong>Client:</strong> Sunrise Café</p>
                    <p><strong>Department:</strong> Marketing</p>
                    <p><strong>Covered Period:</strong> 2025-10-01 to 2025-10-31</p>
                    <p><strong>Due Date:</strong> 2025-11-10</p>
                    <p><strong>Personnel:</strong> John Doe (Billing Officer)</p>
                    <p><strong>Total Amount Due:</strong> $2,450.00</p>
                </div>

                <!-- Summary Items -->
                <h3 class="text-md font-semibold mb-2 text-gray-800">Summary Items</h3>
                <div class="overflow-x-auto border rounded-lg">
                    <table class="min-w-full text-sm divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Billing Summary ID</th>
                                <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase">Grand Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="px-3 py-2 text-gray-700">#BS-1001</td>
                                <td class="px-3 py-2 text-gray-700">$1,250.00</td>
                            </tr>
                            <tr>
                                <td class="px-3 py-2 text-gray-700">#BS-1002</td>
                                <td class="px-3 py-2 text-gray-700">$1,200.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Options -->
                <div class="flex flex-col gap-2 mt-4">
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Edit SOA</button>
                    <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 text-sm">Download PDF</button>
                    <button class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">Delete SOA</button>
                </div>

                <div class="mt-6 text-right">
                    <button 
                        @click="showModal = false"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </main>
