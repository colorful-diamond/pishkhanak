{{-- Toll Road Inquiry Preview Form Component --}}
{{-- فرم پیش‌نمایش استعلام عوارض آزادراهی --}}

<div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
    <h3 class="text-lg font-bold text-dark-sky-700 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0M15 17a2 2 0 104 0"></path>
        </svg>
        استعلام سریع عوارض آزادراه
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- License Plate Input -->
        <div>
            <label for="preview_plate" class="block text-sm font-medium text-gray-700 mb-2">
                شماره پلاک خودرو
            </label>
            <div class="relative">
                <input 
                    type="text" 
                    id="preview_plate" 
                    name="preview_plate"
                    placeholder="مثال: 12ج345-67"
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-transparent text-right"
                    maxlength="20"
                >
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H8a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Date Range Selection -->
        <div>
            <label for="preview_date_range" class="block text-sm font-medium text-gray-700 mb-2">
                بازه زمانی استعلام
            </label>
            <select 
                id="preview_date_range" 
                name="preview_date_range"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-transparent text-right"
            >
                <option value="30">۳۰ روز گذشته</option>
                <option value="60">۶۰ روز گذشته</option>
                <option value="90">۹۰ روز گذشته</option>
                <option value="180">۶ ماه گذشته</option>
                <option value="365">سال گذشته</option>
            </select>
        </div>
    </div>

    <!-- Highway Selection -->
    <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700 mb-2">
            آزادراه‌های مورد نظر (اختیاری)
        </label>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
            <label class="flex items-center">
                <input type="checkbox" name="highways[]" value="tehran-qom" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                <span class="mr-2 text-sm">تهران-قم</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="highways[]" value="tehran-karaj" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                <span class="mr-2 text-sm">تهران-کرج</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="highways[]" value="tehran-mashhad" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                <span class="mr-2 text-sm">تهران-مشهد</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="highways[]" value="tehran-isfahan" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                <span class="mr-2 text-sm">تهران-اصفهان</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="highways[]" value="tehran-shiraz" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                <span class="mr-2 text-sm">تهران-شیراز</span>
            </label>
            <label class="flex items-center">
                <input type="checkbox" name="highways[]" value="all-highways" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                <span class="mr-2 text-sm">همه آزادراه‌ها</span>
            </label>
        </div>
    </div>

    <!-- Submit Button -->
    <div class="mt-6">
        <button 
            type="button"
            onclick="performTollInquiry()"
            class="w-full md:w-auto px-8 py-3 bg-gradient-to-r from-sky-600 to-blue-600 text-white font-medium rounded-xl hover:from-sky-700 hover:to-blue-700 transition-all duration-200 flex items-center justify-center gap-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            استعلام عوارض آزادراهی
        </button>
    </div>
</div>

<script>
function performTollInquiry() {
    const plate = document.getElementById('preview_plate').value;
    const dateRange = document.getElementById('preview_date_range').value;
    
    if (!plate) {
        alert('لطفاً شماره پلاک را وارد کنید.');
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> در حال استعلام...';
    
    // Simulate API call
    setTimeout(() => {
        button.innerHTML = originalText;
        showTollResults();
    }, 2000);
}

function showTollResults() {
    const resultsDiv = document.getElementById('toll-results');
    if (resultsDiv) {
        resultsDiv.style.display = 'block';
        resultsDiv.scrollIntoView({ behavior: 'smooth' });
    }
}
</script>