{{-- Advanced Results Display Component for Coming Check Inquiry --}}
{{-- Persian RTL Formatting with Accessibility Support --}}

<div class="results-container hidden" id="results-container">
    
    {{-- Results Header --}}
    <div class="results-header bg-gradient-to-br from-emerald-50 to-blue-50 rounded-2xl p-6 mb-6 border border-emerald-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4 space-x-reverse">
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-blue-500 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-1">نتایج استعلام چک در راه</h2>
                    <p class="text-gray-600">اطلاعات بازیابی شده از سیستم صیاد بانک مرکزی</p>
                </div>
            </div>
            <div class="text-left">
                <div class="text-sm text-gray-500">تاریخ استعلام</div>
                <div class="text-lg font-semibold text-gray-800" id="inquiry-date">
                    {{ jdate()->format('Y/m/d - H:i') }}
                </div>
            </div>
        </div>
    </div>

    {{-- User Information Summary --}}
    <div class="user-info-summary bg-white rounded-2xl shadow-lg p-6 mb-6 border border-gray-200">
        <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-6 h-6 text-blue-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            اطلاعات کاربر
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 p-4 rounded-xl">
                <div class="text-sm text-gray-600 mb-1">کد ملی</div>
                <div class="text-lg font-semibold text-gray-800" id="user-national-code">----------</div>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl">
                <div class="text-sm text-gray-600 mb-1">شماره موبایل</div>
                <div class="text-lg font-semibold text-gray-800" id="user-mobile">-----------</div>
            </div>
        </div>
    </div>

    {{-- Results Statistics --}}
    <div class="results-stats grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="stat-card bg-white rounded-2xl shadow-lg p-6 text-center border-t-4 border-blue-500">
            <div class="text-3xl font-bold text-blue-600 mb-2" id="total-checks">0</div>
            <div class="text-sm text-gray-600">تعداد کل چک‌ها</div>
        </div>
        <div class="stat-card bg-white rounded-2xl shadow-lg p-6 text-center border-t-4 border-green-500">
            <div class="text-3xl font-bold text-green-600 mb-2" id="pending-checks">0</div>
            <div class="text-sm text-gray-600">در انتظار ارائه</div>
        </div>
        <div class="stat-card bg-white rounded-2xl shadow-lg p-6 text-center border-t-4 border-orange-500">
            <div class="text-3xl font-bold text-orange-600 mb-2" id="processing-checks">0</div>
            <div class="text-sm text-gray-600">در حال بررسی</div>
        </div>
        <div class="stat-card bg-white rounded-2xl shadow-lg p-6 text-center border-t-4 border-purple-500">
            <div class="text-3xl font-bold text-purple-600 mb-2" id="total-amount">۰ تومان</div>
            <div class="text-sm text-gray-600">مجموع مبلغ</div>
        </div>
    </div>

    {{-- Checks List --}}
    <div class="checks-list bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-200">
        <div class="bg-gradient-to-r from-slate-50 to-slate-100 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-800">جزئیات چک‌های در راه</h3>
                <div class="flex items-center space-x-2 space-x-reverse">
                    <button class="filter-btn active" data-status="all">همه</button>
                    <button class="filter-btn" data-status="pending">در انتظار</button>
                    <button class="filter-btn" data-status="processing">در حال بررسی</button>
                    <button class="filter-btn" data-status="ready">آماده پرداخت</button>
                </div>
            </div>
        </div>

        {{-- Table Header --}}
        <div class="table-header bg-gray-50 px-6 py-3 border-b border-gray-200 hidden md:block">
            <div class="grid grid-cols-7 gap-4 text-sm font-semibold text-gray-700">
                <div>ردیف</div>
                <div>شماره چک</div>
                <div>مبلغ</div>
                <div>تاریخ صدور</div>
                <div>تاریخ سررسید</div>
                <div>بانک</div>
                <div>وضعیت</div>
            </div>
        </div>

        {{-- Checks Container --}}
        <div id="checks-container" class="checks-container">
            {{-- Loading State --}}
            <div class="loading-state p-8 text-center" id="loading-state">
                <div class="inline-flex items-center space-x-2 space-x-reverse">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    <span class="text-gray-600">در حال بارگذاری اطلاعات چک‌ها...</span>
                </div>
            </div>

            {{-- Empty State --}}
            <div class="empty-state p-8 text-center hidden" id="empty-state">
                <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-2">هیچ چک در راهی یافت نشد</h3>
                <p class="text-gray-500">شما در حال حاضر چک صادرشده‌ای که هنوز ارائه نشده باشد ندارید.</p>
            </div>

            {{-- Checks will be populated here dynamically --}}
        </div>
    </div>

    {{-- Important Notes --}}
    <div class="important-notes bg-amber-50 border border-amber-200 rounded-2xl p-6 mt-6">
        <div class="flex items-start space-x-3 space-x-reverse">
            <div class="w-6 h-6 text-amber-600 mt-1">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-amber-800 mb-2">نکات مهم:</h4>
                <ul class="text-amber-700 text-sm space-y-1">
                    <li>• اطلاعات نمایش داده شده مستقیماً از سیستم صیاد بانک مرکزی دریافت شده است</li>
                    <li>• چک‌های نمایش داده شده هنوز به بانک ارائه نشده‌اند</li>
                    <li>• برای جلوگیری از برگشت چک، از کفایت موجودی حساب اطمینان حاصل کنید</li>
                    <li>• در صورت تغییر وضعیت چک، مجدداً استعلام کنید</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="action-buttons flex flex-col md:flex-row gap-4 mt-6">
        <button class="btn-primary flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-xl font-semibold transition-colors" onclick="window.print()">
            <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            چاپ نتایج
        </button>
        <button class="btn-secondary flex-1 bg-green-600 hover:bg-green-700 text-white py-3 px-6 rounded-xl font-semibold transition-colors" onclick="downloadResults()">
            <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            دانلود PDF
        </button>
        <button class="btn-tertiary flex-1 bg-gray-600 hover:bg-gray-700 text-white py-3 px-6 rounded-xl font-semibold transition-colors" onclick="newInquiry()">
            <svg class="w-5 h-5 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
            </svg>
            استعلام جدید
        </button>
    </div>
</div>

{{-- Check Item Template (Hidden) --}}
<template id="check-item-template">
    <div class="check-item border-b border-gray-100 hover:bg-gray-50 transition-colors" data-status="">
        <div class="md:hidden p-4">
            {{-- Mobile Layout --}}
            <div class="flex items-center justify-between mb-3">
                <div class="font-semibold text-gray-800 check-number">چک #------</div>
                <div class="status-badge px-3 py-1 rounded-full text-sm font-medium">
                    <span class="status-text">نامشخص</span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 text-sm">
                <div>
                    <span class="text-gray-600">مبلغ:</span>
                    <span class="font-semibold check-amount">--- تومان</span>
                </div>
                <div>
                    <span class="text-gray-600">بانک:</span>
                    <span class="font-semibold check-bank">------</span>
                </div>
                <div>
                    <span class="text-gray-600">تاریخ صدور:</span>
                    <span class="check-issue-date">----/--/--</span>
                </div>
                <div>
                    <span class="text-gray-600">سررسید:</span>
                    <span class="check-due-date">----/--/--</span>
                </div>
            </div>
        </div>
        
        <div class="hidden md:block p-4">
            {{-- Desktop Layout --}}
            <div class="grid grid-cols-7 gap-4 items-center text-sm">
                <div class="check-index font-semibold">-</div>
                <div class="check-number font-semibold">------</div>
                <div class="check-amount font-semibold">--- تومان</div>
                <div class="check-issue-date">----/--/--</div>
                <div class="check-due-date">----/--/--</div>
                <div class="check-bank">------</div>
                <div class="status-badge px-3 py-1 rounded-full text-xs font-medium w-fit">
                    <span class="status-text">نامشخص</span>
                </div>
            </div>
        </div>
    </div>
</template>

{{-- JavaScript for Results Display --}}
<script>
class PersianResultsDisplay {
    constructor() {
        this.checkData = [];
        this.filteredData = [];
        this.currentFilter = 'all';
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Filter buttons
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.setActiveFilter(e.target.dataset.status);
            });
        });
    }

    displayResults(data) {
        this.checkData = data.checks || [];
        this.updateUserInfo(data.user_info || {});
        this.updateStatistics();
        this.renderChecks();
        this.showResults();
    }

    updateUserInfo(userInfo) {
        document.getElementById('user-national-code').textContent = 
            this.formatNationalCode(userInfo.national_code || '');
        document.getElementById('user-mobile').textContent = 
            this.formatMobileNumber(userInfo.mobile || '');
    }

    updateStatistics() {
        const stats = this.calculateStatistics();
        
        document.getElementById('total-checks').textContent = 
            this.toPersianNumber(stats.total);
        document.getElementById('pending-checks').textContent = 
            this.toPersianNumber(stats.pending);
        document.getElementById('processing-checks').textContent = 
            this.toPersianNumber(stats.processing);
        document.getElementById('total-amount').textContent = 
            this.formatCurrency(stats.totalAmount) + ' تومان';
    }

    calculateStatistics() {
        const stats = {
            total: this.checkData.length,
            pending: 0,
            processing: 0,
            ready: 0,
            totalAmount: 0
        };

        this.checkData.forEach(check => {
            stats.totalAmount += parseFloat(check.amount || 0);
            
            switch (check.status) {
                case 'pending':
                    stats.pending++;
                    break;
                case 'processing':
                    stats.processing++;
                    break;
                case 'ready':
                    stats.ready++;
                    break;
            }
        });

        return stats;
    }

    renderChecks() {
        const container = document.getElementById('checks-container');
        const template = document.getElementById('check-item-template');
        
        // Clear existing content
        container.innerHTML = '';

        if (this.filteredData.length === 0) {
            this.showEmptyState();
            return;
        }

        this.filteredData.forEach((check, index) => {
            const checkElement = template.content.cloneNode(true);
            this.populateCheckItem(checkElement, check, index + 1);
            container.appendChild(checkElement);
        });
    }

    populateCheckItem(element, check, index) {
        // Set data attributes
        const checkItem = element.querySelector('.check-item');
        checkItem.dataset.status = check.status;

        // Populate data
        element.querySelector('.check-index').textContent = this.toPersianNumber(index);
        element.querySelector('.check-number').textContent = check.number || '------';
        element.querySelector('.check-amount').textContent = 
            this.formatCurrency(check.amount) + ' تومان';
        element.querySelector('.check-issue-date').textContent = 
            this.formatPersianDate(check.issue_date);
        element.querySelector('.check-due-date').textContent = 
            this.formatPersianDate(check.due_date);
        element.querySelector('.check-bank').textContent = check.bank || '------';

        // Status
        const statusElement = element.querySelector('.status-badge');
        const statusText = this.getStatusText(check.status);
        element.querySelector('.status-text').textContent = statusText.text;
        statusElement.classList.add(statusText.class);
    }

    getStatusText(status) {
        const statusMap = {
            'pending': { text: 'در انتظار ارائه', class: 'bg-blue-100 text-blue-800' },
            'processing': { text: 'در حال بررسی', class: 'bg-yellow-100 text-yellow-800' },
            'ready': { text: 'آماده پرداخت', class: 'bg-green-100 text-green-800' },
            'expired': { text: 'سررسید گذشته', class: 'bg-red-100 text-red-800' },
            'cancelled': { text: 'لغو شده', class: 'bg-gray-100 text-gray-800' }
        };

        return statusMap[status] || { text: 'نامشخص', class: 'bg-gray-100 text-gray-800' };
    }

    setActiveFilter(status) {
        this.currentFilter = status;
        
        // Update button states
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-status="${status}"]`).classList.add('active');

        // Filter data
        this.filteredData = status === 'all' 
            ? [...this.checkData]
            : this.checkData.filter(check => check.status === status);

        this.renderChecks();
    }

    showResults() {
        document.getElementById('loading-state').style.display = 'none';
        document.getElementById('results-container').classList.remove('hidden');
    }

    showEmptyState() {
        document.getElementById('loading-state').style.display = 'none';
        document.getElementById('empty-state').classList.remove('hidden');
    }

    // Utility Functions
    formatNationalCode(code) {
        return code.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
    }

    formatMobileNumber(mobile) {
        return mobile.replace(/(\d{4})(\d{3})(\d{4})/, '$1-$2-$3');
    }

    formatCurrency(amount) {
        const number = parseFloat(amount || 0);
        return this.toPersianNumber(number.toLocaleString('fa-IR'));
    }

    formatPersianDate(date) {
        if (!date) return '----/--/--';
        
        // Convert to Persian calendar if needed
        try {
            const persianDate = new Date(date).toLocaleDateString('fa-IR');
            return this.toPersianNumber(persianDate);
        } catch (e) {
            return this.toPersianNumber(date);
        }
    }

    toPersianNumber(str) {
        const persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        return str.toString().replace(/\d/g, digit => persianNumbers[parseInt(digit)]);
    }

    // Public Methods
    reset() {
        this.checkData = [];
        this.filteredData = [];
        this.currentFilter = 'all';
        document.getElementById('results-container').classList.add('hidden');
        document.getElementById('loading-state').style.display = 'block';
        document.getElementById('empty-state').classList.add('hidden');
    }
}

// Initialize Results Display
const resultsDisplay = new PersianResultsDisplay();

// Export for global use
window.PersianResultsDisplay = resultsDisplay;

// Global Functions
function downloadResults() {
    // Generate PDF download
    const element = document.getElementById('results-container');
    const opt = {
        margin: 1,
        filename: `check-inquiry-${Date.now()}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, direction: 'rtl' },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    
    // Use html2pdf library if available
    if (typeof html2pdf !== 'undefined') {
        html2pdf().set(opt).from(element).save();
    } else {
        alert('امکان دانلود PDF در حال حاضر فراهم نیست');
    }
}

function newInquiry() {
    // Reset form and hide results
    resultsDisplay.reset();
    document.querySelector('form[data-service="coming-check-inquiry"]').reset();
    window.PersianFormValidator.reset();
    
    // Smooth scroll to form
    document.querySelector('form').scrollIntoView({ 
        behavior: 'smooth' 
    });
}
</script>

{{-- CSS Styles --}}
<style>
.results-container {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.check-item:hover {
    transform: translateX(-2px);
}

.filter-btn {
    padding: 0.5rem 1rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
    background: white;
    border: 1px solid #e5e7eb;
    color: #6b7280;
}

.filter-btn.active,
.filter-btn:hover {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
}

.stat-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

/* Print Styles */
@media print {
    .action-buttons,
    .filter-btn {
        display: none !important;
    }
    
    .results-container {
        box-shadow: none !important;
        border: none !important;
    }
    
    .check-item {
        break-inside: avoid;
    }
}

/* RTL Enhancements */
[dir="rtl"] .results-container {
    text-align: right;
}

/* Accessibility Improvements */
@media (prefers-reduced-motion: reduce) {
    .results-container,
    .check-item,
    .stat-card {
        animation: none;
        transition: none;
    }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
    .check-item {
        border-bottom: 2px solid #000;
    }
    
    .status-badge {
        border: 2px solid #000;
        background: #fff !important;
        color: #000 !important;
    }
}
</style>