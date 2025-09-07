@extends('front.layouts.app')

@section('title', 'نتیجه استعلام جزئیات ضمانت‌نامه')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-sky-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">جزئیات ضمانت‌نامه</h1>
                        <p class="text-gray-600">اطلاعات کامل ضمانت‌نامه بر اساس کد 13 رقمی</p>
                    </div>
                </div>
                <div class="text-left">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        موفق
                    </span>
                </div>
            </div>
        </div>

        <!-- Input Information -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">اطلاعات ورودی</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">کد ضمانت‌نامه</div>
                    <div class="font-medium text-gray-900 font-mono">{{ $data['input_info']['guarantee_id'] ?? 'نامشخص' }}</div>
                </div>
                <div class="bg-sky-50 rounded-lg p-4">
                    <div class="text-sm text-gray-600 mb-1">شناسه پیگیری</div>
                    <div class="font-medium text-gray-900">{{ $data['input_info']['track_id'] ?? 'نامشخص' }}</div>
                </div>
            </div>
        </div>

        <!-- Guarantee Details -->
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">جزئیات ضمانت‌نامه</h2>
            
            @if(isset($data['guarantee_details']))
            <div class="space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-sky-50 rounded-lg p-4">
                        <div class="text-sm text-sky-600 mb-1">کد ضمانت‌نامه</div>
                        <div class="font-bold text-sky-900 text-lg font-mono">{{ $data['guarantee_details']['guarantee_id'] ?? 'نامشخص' }}</div>
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-sm text-green-600 mb-1">وضعیت ضمانت‌نامه</div>
                        <div class="font-bold text-green-900">{{ $data['guarantee_details']['guarantee_status_description'] ?? 'نامشخص' }}</div>
                    </div>
                </div>
                
                <!-- Financial Information -->
                <div class="bg-sky-50 rounded-lg p-6">
                    <h3 class="text-md font-semibold text-gray-900 mb-4">اطلاعات مالی</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">مبلغ بدهی</div>
                            <div class="font-bold text-gray-900 text-lg">{{ $data['guarantee_details']['debt_amount_formatted'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">مبلغ کارمزد صدور</div>
                            <div class="font-bold text-gray-900">{{ $data['guarantee_details']['issue_charge_amount_formatted'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">مبلغ کارمزد تمدید</div>
                            <div class="font-bold text-gray-900">{{ $data['guarantee_details']['last_renew_charge_amount_formatted'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">کل کارمزد</div>
                            <div class="font-bold text-gray-900">{{ $data['guarantee_details']['total_charge_formatted'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">مبلغ بدهی قبلی شرکت</div>
                            <div class="font-bold text-gray-900">{{ $data['guarantee_details']['company_pre_debt_amount_formatted'] ?? 'نامشخص' }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Dates Information -->
                <div class="bg-sky-50 rounded-lg p-6">
                    <h3 class="text-md font-semibold text-gray-900 mb-4">تاریخ‌ها</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">تاریخ صدور</div>
                            <div class="font-medium text-gray-900">{{ $data['guarantee_details']['issue_date_formatted'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">تاریخ سررسید</div>
                            <div class="font-medium text-gray-900">{{ $data['guarantee_details']['maturity_date_formatted'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">تاریخ تمدید</div>
                            <div class="font-medium text-gray-900">{{ $data['guarantee_details']['renew_date_formatted'] ?? 'نامشخص' }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Additional Information -->
                <div class="bg-sky-50 rounded-lg p-6">
                    <h3 class="text-md font-semibold text-gray-900 mb-4">اطلاعات تکمیلی</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">کد شعبه</div>
                            <div class="font-medium text-gray-900">{{ $data['guarantee_details']['branch_code'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">کد CIF</div>
                            <div class="font-medium text-gray-900">{{ $data['guarantee_details']['cif'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">شناسه سپام</div>
                            <div class="font-medium text-gray-900">{{ $data['guarantee_details']['sepam_id'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">کد نوع ضمانت‌نامه</div>
                            <div class="font-medium text-gray-900">{{ $data['guarantee_details']['central_bank_type_code'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">نوع ضمانت‌نامه</div>
                            <div class="font-medium text-gray-900">{{ $data['guarantee_details']['guarantee_sub_type_desc'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">کد وضعیت</div>
                            <div class="font-medium text-gray-900">{{ $data['guarantee_details']['guarantee_status_code'] ?? 'نامشخص' }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Economic Information -->
                <div class="bg-sky-50 rounded-lg p-6">
                    <h3 class="text-md font-semibold text-gray-900 mb-4">اطلاعات اقتصادی</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">بخش اقتصادی</div>
                            <div class="font-medium text-gray-900">{{ $data['guarantee_details']['economic_desc'] ?? 'نامشخص' }}</div>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <div class="text-sm text-gray-600 mb-1">زیربخش اقتصادی</div>
                            <div class="font-medium text-gray-900">{{ $data['guarantee_details']['economic_subsection_desc'] ?? 'نامشخص' }}</div>
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                @if(isset($data['guarantee_details']['ret_code_description']))
                <div class="bg-yellow-50 rounded-lg p-4">
                    <div class="text-sm text-yellow-600 mb-1">توضیحات</div>
                    <div class="font-medium text-yellow-900">{{ $data['guarantee_details']['ret_code_description'] }}</div>
                </div>
                @endif
            </div>
            @else
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">اطلاعاتی یافت نشد</h3>
                <p class="text-gray-600">برای کد ضمانت‌نامه وارد شده، اطلاعاتی یافت نشد.</p>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('front.services.show', $service->slug) }}" class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-medium py-3 px-6 rounded-lg text-center transition duration-200">
                    استعلام جدید
                </a>
                <button onclick="window.print()" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200">
                    چاپ نتیجه
                </button>
                <a href="{{ route('front.dashboard') }}" class="flex-1 bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg text-center transition duration-200">
                    بازگشت به داشبورد
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style media="print">
    @media print {
        .container { max-width: none; }
        .shadow-lg { box-shadow: none; }
        .bg-white { background: white !important; }
        .text-gray-900 { color: black !important; }
        .text-gray-600 { color: #666 !important; }
        .bg-sky-50 { background: #f0f8ff !important; }
        .bg-green-50 { background: #f0fff4 !important; }
        .bg-sky-50 { background: #f9f9f9 !important; }
        .bg-yellow-50 { background: #fffbeb !important; }
        .bg-green-100 { background: #f0fff4 !important; }
        .bg-sky-100 { background: #ebf8ff !important; }
        .text-sky-600 { color: #2563eb !important; }
        .text-sky-900 { color: #1e3a8a !important; }
        .text-green-600 { color: #059669 !important; }
        .text-green-900 { color: #14532d !important; }
        .text-green-800 { color: #166534 !important; }
        .text-yellow-600 { color: #d97706 !important; }
        .text-yellow-900 { color: #92400e !important; }
        .rounded-lg { border-radius: 0.5rem; }
        .rounded-full { border-radius: 9999px; }
        .font-bold { font-weight: bold; }
        .font-medium { font-weight: 500; }
        .font-mono { font-family: monospace; }
        .text-sm { font-size: 0.875rem; }
        .text-lg { font-size: 1.125rem; }
        .text-2xl { font-size: 1.5rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mt-6 { margin-top: 1.5rem; }
        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .py-8 { padding-top: 2rem; padding-bottom: 2rem; }
        .px-4 { padding-left: 1rem; padding-right: 1rem; }
        .py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
        .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .w-12 { width: 3rem; }
        .h-12 { height: 3rem; }
        .w-6 { width: 1.5rem; }
        .h-6 { height: 1.5rem; }
        .w-8 { width: 2rem; }
        .h-8 { height: 2rem; }
        .w-16 { width: 4rem; }
        .h-16 { height: 4rem; }
        .w-4 { width: 1rem; }
        .h-4 { height: 1rem; }
        .space-y-4 > * + * { margin-top: 1rem; }
        .space-y-6 > * + * { margin-top: 1.5rem; }
        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .gap-4 { gap: 1rem; }
        .gap-6 { gap: 1.5rem; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .justify-between { justify-content: space-between; }
        .text-center { text-align: center; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .space-x-4 > * + * { margin-right: 1rem; }
        .space-x-reverse > * + * { margin-right: 0; margin-left: 1rem; }
        .inline-flex { display: inline-flex; }
        .mr-1 { margin-right: 0.25rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .hidden { display: none; }
    }
</style>
@endsection 