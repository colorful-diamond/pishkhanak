<div class="mb-6">
    <label class="block text-sm font-medium text-gray-700 mb-3">نوع مشتری</label>
    <div class="grid grid-cols-2 gap-3">
        <button type="button" id="personal-btn" class="customer-type-btn active flex items-center justify-center gap-2 p-3 rounded-lg border-2 border-primary-normal bg-primary-normal text-white font-medium transition-all duration-300 hover:bg-primary-dark hover:border-primary-dark" data-type="personal">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
            </svg>
            حقیقی
        </button>
        <button type="button" id="corporate-btn" class="customer-type-btn flex items-center justify-center gap-2 p-3 rounded-lg border-2 border-primary-200 bg-primary-50 text-primary-700 font-medium transition-all duration-300 hover:bg-primary-100 hover:border-primary-300 hover:text-primary-800" data-type="corporate">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4z" clip-rule="evenodd"/>
            </svg>
            حقوقی
        </button>
    </div>
</div>

<!-- Customer Type Hidden Field -->
<input type="hidden" id="customer_type" name="customer_type" value="personal"> 