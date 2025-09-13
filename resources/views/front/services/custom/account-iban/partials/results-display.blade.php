{{-- SHEBA Results Display Component --}}
{{-- Usage: @include('front.services.custom.account-iban.partials.results-display') --}}

<div id="resultsContainer" class="results-display-container hidden">
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-green-500 to-emerald-500 px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center ml-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">تبدیل موفق به شبا</h3>
                        <p class="text-green-100 text-sm">شماره شبا شما آماده است</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-white/80 text-xs">زمان تولید</div>
                    <div id="generationTime" class="text-white font-mono text-sm">--:--:--</div>
                </div>
            </div>
        </div>

        {{-- Main Results --}}
        <div class="p-8 space-y-6">
            
            {{-- SHEBA Number Display --}}
            <div class="text-center mb-8">
                <label class="block text-sm font-semibold text-gray-700 mb-3">شماره شبا شما</label>
                <div class="relative">
                    <div id="shebaNumber" class="bg-gray-50 border-2 border-gray-200 rounded-xl px-6 py-4 text-2xl font-mono text-center text-gray-900 tracking-wider select-all focus:ring-2 focus:ring-blue-500 focus:border-blue-500 cursor-pointer hover:bg-gray-100 transition-colors">
                        IR00 0000 0000 0000 0000 0000 00
                    </div>
                    <button id="copySheba" class="absolute left-3 top-1/2 transform -translate-y-1/2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors flex items-center">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        کپی
                    </button>
                </div>
                <div class="mt-3 flex justify-center space-x-4 space-x-reverse">
                    <span id="copyStatus" class="text-xs text-green-600 font-semibold hidden">✓ کپی شد</span>
                    <span class="text-xs text-gray-500">کلیک کنید تا کپی شود</span>
                </div>
            </div>

            {{-- Account Information Grid --}}
            <div class="grid md:grid-cols-2 gap-6">
                
                {{-- Original Account Details --}}
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center ml-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-bold text-blue-800">اطلاعات حساب</h4>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <span class="block text-sm font-semibold text-blue-700 mb-1">شماره حساب اصلی</span>
                            <span id="originalAccount" class="block font-mono text-blue-900 bg-white px-3 py-2 rounded-lg border">--</span>
                        </div>
                        <div>
                            <span class="block text-sm font-semibold text-blue-700 mb-1">نام بانک</span>
                            <span id="bankNameDisplay" class="block text-blue-900 bg-white px-3 py-2 rounded-lg border">--</span>
                        </div>
                        <div>
                            <span class="block text-sm font-semibold text-blue-700 mb-1">کد بانک</span>
                            <span id="bankCodeDisplay" class="block font-mono text-blue-900 bg-white px-3 py-2 rounded-lg border">--</span>
                        </div>
                    </div>
                </div>

                {{-- SHEBA Details --}}
                <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center ml-3">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h4 class="text-lg font-bold text-green-800">جزئیات شبا</h4>
                    </div>
                    <div class="space-y-3">
                        <div>
                            <span class="block text-sm font-semibold text-green-700 mb-1">کد کشور</span>
                            <span class="block font-mono text-green-900 bg-white px-3 py-2 rounded-lg border">IR</span>
                        </div>
                        <div>
                            <span class="block text-sm font-semibold text-green-700 mb-1">رقم کنترلی</span>
                            <span id="checkDigits" class="block font-mono text-green-900 bg-white px-3 py-2 rounded-lg border">--</span>
                        </div>
                        <div>
                            <span class="block text-sm font-semibold text-green-700 mb-1">استاندارد</span>
                            <span class="block text-green-900 bg-white px-3 py-2 rounded-lg border">ISO 13616</span>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                <button id="downloadBtn" class="flex-1 bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    دانلود PDF
                </button>
                <button id="shareBtn" class="flex-1 bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                    </svg>
                    اشتراک‌گذاری
                </button>
                <button id="newConversionBtn" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center justify-center">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    تبدیل جدید
                </button>
            </div>

        </div>

        {{-- Validation Info Footer --}}
        <div class="bg-gray-50 px-8 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center text-green-600">
                    <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>تأیید شده با الگوریتم MOD-97</span>
                </div>
                <div class="text-gray-500">
                    <span>تولید شده توسط پیشخوانک</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Additional Information --}}
    <div class="mt-8 grid md:grid-cols-2 gap-6">
        
        {{-- Usage Instructions --}}
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6">
            <h4 class="text-lg font-bold text-blue-800 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                نحوه استفاده
            </h4>
            <ul class="space-y-2 text-blue-700 text-sm">
                <li class="flex items-start">
                    <span class="w-5 h-5 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold ml-2 mt-0.5">1</span>
                    <span>شماره شبا را کپی کنید</span>
                </li>
                <li class="flex items-start">
                    <span class="w-5 h-5 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold ml-2 mt-0.5">2</span>
                    <span>در فرم‌های بانکی یا اپلیکیشن‌ها وارد کنید</span>
                </li>
                <li class="flex items-start">
                    <span class="w-5 h-5 bg-blue-500 text-white rounded-full flex items-center justify-center text-xs font-bold ml-2 mt-0.5">3</span>
                    <span>برای دریافت پول استفاده کنید</span>
                </li>
            </ul>
        </div>

        {{-- Security Notes --}}
        <div class="bg-green-50 border border-green-200 rounded-xl p-6">
            <h4 class="text-lg font-bold text-green-800 mb-4 flex items-center">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                نکات امنیتی
            </h4>
            <ul class="space-y-2 text-green-700 text-sm">
                <li class="flex items-start">
                    <span class="text-green-500 ml-2 mt-1">✓</span>
                    <span>شبا فقط برای دریافت پول استفاده می‌شود</span>
                </li>
                <li class="flex items-start">
                    <span class="text-green-500 ml-2 mt-1">✓</span>
                    <span>هیچ‌گونه رمز عبوری لازم نیست</span>
                </li>
                <li class="flex items-start">
                    <span class="text-green-500 ml-2 mt-1">✓</span>
                    <span>اطلاعات شما محفوظ و ایمن است</span>
                </li>
            </ul>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const copyBtn = document.getElementById('copySheba');
    const shareBtn = document.getElementById('shareBtn');
    const downloadBtn = document.getElementById('downloadBtn');
    const newConversionBtn = document.getElementById('newConversionBtn');
    const copyStatus = document.getElementById('copyStatus');
    const shebaDisplay = document.getElementById('shebaNumber');

    // Copy SHEBA number functionality
    function copySheba() {
        const shebaText = shebaDisplay.textContent.replace(/\s/g, '');
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(shebaText).then(() => {
                showCopySuccess();
            });
        } else {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = shebaText;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            showCopySuccess();
        }
    }

    function showCopySuccess() {
        copyStatus.classList.remove('hidden');
        copyBtn.innerHTML = `
            <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            کپی شد
        `;
        copyBtn.classList.remove('bg-blue-500', 'hover:bg-blue-600');
        copyBtn.classList.add('bg-green-500', 'hover:bg-green-600');

        setTimeout(() => {
            copyStatus.classList.add('hidden');
            copyBtn.innerHTML = `
                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                کپی
            `;
            copyBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
            copyBtn.classList.add('bg-blue-500', 'hover:bg-blue-600');
        }, 2000);
    }

    // Event listeners
    copyBtn.addEventListener('click', copySheba);
    shebaDisplay.addEventListener('click', copySheba);

    // Share functionality
    shareBtn.addEventListener('click', function() {
        const shebaText = shebaDisplay.textContent.replace(/\s/g, '');
        const shareData = {
            title: 'شماره شبا من',
            text: `شماره شبا: ${shebaText}`,
            url: window.location.href
        };

        if (navigator.share) {
            navigator.share(shareData);
        } else {
            // Fallback - copy to clipboard
            copySheba();
        }
    });

    // Download PDF functionality
    downloadBtn.addEventListener('click', function() {
        // Create a simple PDF-like content for download
        const content = `
شماره شبا: ${shebaDisplay.textContent}
تاریخ تولید: ${new Date().toLocaleDateString('fa-IR')}
تولید شده توسط پیشخوانک
        `.trim();

        const element = document.createElement('a');
        element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(content));
        element.setAttribute('download', 'sheba-number.txt');
        element.style.display = 'none';
        document.body.appendChild(element);
        element.click();
        document.body.removeChild(element);
    });

    // New conversion functionality
    newConversionBtn.addEventListener('click', function() {
        // Hide results and reset form
        document.getElementById('resultsContainer').classList.add('hidden');
        document.getElementById('accountNumber').value = '';
        document.getElementById('accountNumber').focus();
        
        // Reset validation states
        document.getElementById('validationStatus').classList.add('hidden');
        document.getElementById('errorMessage').classList.add('hidden');
        document.getElementById('successMessage').classList.add('hidden');
        document.getElementById('bankSelectionContainer').classList.add('hidden');
    });

    // Function to display results (called from parent component)
    window.displayShebaResults = function(data) {
        // Populate the results
        document.getElementById('shebaNumber').textContent = formatSheba(data.sheba);
        document.getElementById('originalAccount').textContent = data.account;
        document.getElementById('bankNameDisplay').textContent = data.bankName;
        document.getElementById('bankCodeDisplay').textContent = data.bankCode;
        document.getElementById('checkDigits').textContent = data.sheba.substring(2, 4);
        document.getElementById('generationTime').textContent = new Date().toLocaleTimeString('fa-IR');
        
        // Show results container
        document.getElementById('resultsContainer').classList.remove('hidden');
        
        // Smooth scroll to results
        document.getElementById('resultsContainer').scrollIntoView({ 
            behavior: 'smooth', 
            block: 'start' 
        });
    };

    // Format SHEBA number with spaces
    function formatSheba(sheba) {
        return sheba.replace(/(.{4})/g, '$1 ').trim();
    }
});
</script>

<style>
.results-display-container {
    animation: slideUp 0.5s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#shebaNumber {
    transition: all 0.2s ease;
}

#shebaNumber:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

#copySheba:active {
    transform: scale(0.95);
}

/* RTL support */
[dir="rtl"] #shebaNumber {
    text-align: center;
    direction: ltr;
}
</style>