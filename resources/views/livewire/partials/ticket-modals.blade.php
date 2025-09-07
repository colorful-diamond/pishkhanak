<!-- Quick Action Modals -->
<div>
    <!-- Placeholder for modals -->
    @if(isset($showBulkModal) && $showBulkModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-black bg-opacity-50">
            <div class="flex items-center justify-center min-h-screen/2 p-4">
                <div class="bg-white rounded-lg p-6 max-w-md w-full">
                    <h3 class="text-lg font-medium mb-4">عملیات دسته‌ای</h3>
                    <p class="text-gray-600 mb-4">این ویژگی در حال توسعه است.</p>
                    <button wire:click="$set('showBulkModal', false)" 
                            class="px-4 py-2 bg-sky-500 text-white rounded hover:bg-gray-600">
                        بستن
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
