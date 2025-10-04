    <!-- Confirmation Modal -->
<div x-show="showConfirmModal"
     class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
     x-transition>
    <div class="bg-white rounded-xl shadow-lg p-6 w-96">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Confirm Save</h2>
        <p class="text-sm text-gray-600 mb-6">
            Are you sure you want to save this billing summary?
        </p>
        <div class="flex justify-end gap-3">
            <button @click="showConfirmModal = false"
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400">
                Cancel
            </button>
            <button @click="confirmSave"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Confirm
            </button>
        </div>
    </div>
</div>
<!-- Success Modal -->
<div x-show="showSuccessModal"
     class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
     x-transition>
    <div class="bg-white rounded-xl shadow-lg p-6 w-96 text-center">
        <h2 class="text-lg font-semibold text-green-700 mb-4">Success!</h2>
        <p class="text-sm text-gray-600 mb-6">
            Billing summary has been saved successfully.
        </p>
        <button @click="showSuccessModal = false"
                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            OK
        </button>
    </div>
</div>
