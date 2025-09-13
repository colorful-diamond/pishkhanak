{{-- Toll Road Inquiry Results Table Component --}}
{{-- جدول نتایج استعلام عوارض آزادراهی --}}

<div id="toll-results" class="bg-white rounded-2xl border border-gray-200 p-6 mb-6" style="display: none;">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-dark-sky-700 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            نتایج استعلام عوارض آزادراهی
        </h3>
        <div class="text-sm text-gray-600">
            آخرین به‌روزرسانی: {{ date('Y/m/d H:i') }}
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-sky-50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">کل عوارض</p>
                    <p class="text-2xl font-bold text-blue-600">۴۲۳,۰۰۰</p>
                    <p class="text-xs text-gray-500">تومان</p>
                </div>
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">پرداخت شده</p>
                    <p class="text-2xl font-bold text-green-600">۱۸۵,۰۰۰</p>
                    <p class="text-xs text-gray-500">تومان</p>
                </div>
                <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">باقی‌مانده</p>
                    <p class="text-2xl font-bold text-red-600">۲۳۸,۰۰۰</p>
                    <p class="text-xs text-gray-500">تومان</p>
                </div>
                <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">تعداد سفر</p>
                    <p class="text-2xl font-bold text-purple-600">۱۵</p>
                    <p class="text-xs text-gray-500">عبور</p>
                </div>
                <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Results Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b">
                    <th class="text-right p-3 font-medium text-gray-700">ردیف</th>
                    <th class="text-right p-3 font-medium text-gray-700">نام آزادراه</th>
                    <th class="text-right p-3 font-medium text-gray-700">تاریخ عبور</th>
                    <th class="text-right p-3 font-medium text-gray-700">ساعت عبور</th>
                    <th class="text-right p-3 font-medium text-gray-700">مبلغ عوارض</th>
                    <th class="text-right p-3 font-medium text-gray-700">وضعیت پرداخت</th>
                    <th class="text-center p-3 font-medium text-gray-700">عملیات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <tr class="hover:bg-gray-50">
                    <td class="p-3">۱</td>
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-sky-500 rounded-full"></div>
                            آزادراه تهران-قم
                        </div>
                    </td>
                    <td class="p-3 text-gray-600">۱۴۰۳/۰۹/۱۰</td>
                    <td class="p-3 text-gray-600">۱۴:۳۵</td>
                    <td class="p-3">
                        <span class="font-bold text-gray-900">۲۸,۰۰۰</span>
                        <span class="text-xs text-gray-500">تومان</span>
                    </td>
                    <td class="p-3">
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                            پرداخت نشده
                        </span>
                    </td>
                    <td class="p-3 text-center">
                        <button class="px-3 py-1 bg-sky-600 text-white text-xs rounded-lg hover:bg-sky-700 transition-colors">
                            پرداخت
                        </button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="p-3">۲</td>
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            آزادراه تهران-کرج
                        </div>
                    </td>
                    <td class="p-3 text-gray-600">۱۴۰۳/۰۹/۰۸</td>
                    <td class="p-3 text-gray-600">۰۹:۲۰</td>
                    <td class="p-3">
                        <span class="font-bold text-gray-900">۱۵,۰۰۰</span>
                        <span class="text-xs text-gray-500">تومان</span>
                    </td>
                    <td class="p-3">
                        <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                            پرداخت شده
                        </span>
                    </td>
                    <td class="p-3 text-center">
                        <button class="px-3 py-1 bg-gray-300 text-gray-500 text-xs rounded-lg cursor-not-allowed" disabled>
                            تسویه شده
                        </button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="p-3">۳</td>
                    <td class="p-3">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                            آزادراه تهران-اصفهان
                        </div>
                    </td>
                    <td class="p-3 text-gray-600">۱۴۰۳/۰۹/۰۵</td>
                    <td class="p-3 text-gray-600">۱۶:۱۵</td>
                    <td class="p-3">
                        <span class="font-bold text-gray-900">۹۵,۰۰۰</span>
                        <span class="text-xs text-gray-500">تومان</span>
                    </td>
                    <td class="p-3">
                        <span class="px-2 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium">
                            پرداخت نشده
                        </span>
                    </td>
                    <td class="p-3 text-center">
                        <button class="px-3 py-1 bg-sky-600 text-white text-xs rounded-lg hover:bg-sky-700 transition-colors">
                            پرداخت
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Bulk Actions -->
    <div class="mt-6 flex flex-col md:flex-row gap-4 items-center justify-between pt-4 border-t">
        <div class="flex items-center gap-4">
            <button class="px-6 py-2 bg-gradient-to-r from-sky-600 to-blue-600 text-white font-medium rounded-lg hover:from-sky-700 hover:to-blue-700 transition-all duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                پرداخت کل عوارض
            </button>
            <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                دانلود گزارش
            </button>
        </div>
        <div class="text-sm text-gray-600">
            مجموع نتایج: <span class="font-bold">۱۵ مورد</span>
        </div>
    </div>
</div>