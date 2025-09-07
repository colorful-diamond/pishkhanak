
@extends('front.layouts.app')

@section('title', $service->title . ' - نتیجه')

@php
$fieldLabels = [
    'card_number' => 'شماره کارت',
    'iban' => 'شماره شبا',
    'account_number' => 'شماره حساب',
    'bank_name' => 'نام بانک',
    'account_type' => 'نوع حساب',
    'branch_code' => 'کد شعبه',
    'is_valid' => 'وضعیت اعتبار',
    'bank_code' => 'کد بانک',
    'owner_name' => 'نام صاحب حساب',
    'conversion_date' => 'تاریخ تبدیل',
    'validation_date' => 'تاریخ اعتبارسنجی',
    'account_status' => 'وضعیت حساب',
    'account_description' => 'توضیحات حساب',
    'account_comment' => 'توضیحات تکمیلی',
    'account_owners' => 'صاحبان حساب',
    'provider' => 'ارائه‌دهنده سرویس'
];

// Function to translate Finnotech account status codes
function translateAccountStatus($status) {
    $statusMap = [
        '01' => 'حساب بسته شده است',
        '02' => 'حساب فعال است',
        '03' => 'حساب مسدود با قابلیت واریز',
        '04' => 'حساب مسدود بدون قابلیت واریز',
        '05' => 'حساب راکد است',
        '06' => 'بروز خطا در پاسخ‌دهی',
        '07' => 'خطای نامشخص سامانه شبا',
        '08' => 'متقاضی فاقد حساب می‌باشد'
    ];
    
    return $statusMap[$status] ?? $status;
}

// Function to translate bank codes/slugs to Persian names
function translateBankName($bankName) {
    // If it's already in Persian, return as-is
    if (preg_match('/[\x{0600}-\x{06FF}]/u', $bankName)) {
        return $bankName;
    }
    
    $bankTranslations = [
        // Jibit slugs
        'MARKAZI' => 'بانک مرکزی جمهوری اسلامی ایران',
        'SANAT_VA_MADAN' => 'بانک صنعت و معدن',
        'MELLAT' => 'بانک ملت',
        'REFAH' => 'بانک رفاه کارگران',
        'MASKAN' => 'بانک مسکن',
        'SEPAH' => 'بانک سپه',
        'KESHAVARZI' => 'بانک کشاورزی',
        'MELLI' => 'بانک ملی ایران',
        'TEJARAT' => 'بانک تجارت',
        'SADERAT' => 'بانک صادرات ایران',
        'TOSEAH_SADERAT' => 'بانک توسعه صادرات ایران',
        'POST' => 'پست بانک ایران',
        'TOSEAH_TAAVON' => 'بانک توسعه تعاون',
        'KARAFARIN' => 'بانک کارآفرین',
        'PARSIAN' => 'بانک پارسیان',
        'EGHTESAD_NOVIN' => 'بانک اقتصاد نوین',
        'SAMAN' => 'بانک سامان',
        'PASARGAD' => 'بانک پاسارگاد',
        'SARMAYEH' => 'بانک سرمایه',
        'SINA' => 'بانک سینا',
        'MEHR_IRAN' => 'بانک قرض‌الحسنه مهر ایران',
        'SHAHR' => 'بانک شهر',
        'AYANDEH' => 'بانک آینده',
        'GARDESHGARI' => 'بانک گردشگری',
        'DAY' => 'بانک دی',
        'IRANZAMIN' => 'بانک ایران زمین',
        'RESALAT' => 'بانک قرض‌الحسنه رسالت',
        'MELAL' => 'موسسه اعتباری ملل',
        'KHAVARMIANEH' => 'بانک خاورمیانه',
        'NOOR' => 'موسسه اعتباری نور',
        'IRAN_VENEZUELA' => 'بانک دوملیته ایران ونزوئلا',
        
        // Finnotech 3-digit codes
        '001' => 'بانک مرکزی جمهوری اسلامی ایران',
        '011' => 'بانک صنعت و معدن',
        '012' => 'بانک ملت',
        '013' => 'بانک رفاه کارگران',
        '014' => 'بانک مسکن',
        '015' => 'بانک سپه',
        '016' => 'بانک کشاورزی',
        '017' => 'بانک ملی ایران',
        '018' => 'بانک تجارت',
        '019' => 'بانک صادرات ایران',
        '020' => 'بانک توسعه صادرات ایران',
        '021' => 'پست بانک ایران',
        '022' => 'بانک توسعه تعاون',
        '054' => 'بانک پارسیان',
        '055' => 'بانک اقتصاد نوین',
        '056' => 'بانک سامان',
        '057' => 'بانک پاسارگاد',
        '058' => 'بانک سرمایه',
        '059' => 'بانک سینا',
        '060' => 'بانک قرض‌الحسنه مهر ایران',
        '061' => 'بانک شهر',
        '062' => 'بانک آینده',
        '063' => 'بانک گردشگری',
        '064' => 'بانک دی',
        '065' => 'بانک ایران زمین',
        '066' => 'بانک قرض‌الحسنه رسالت',
        '067' => 'موسسه اعتباری ملل',
        '068' => 'بانک خاورمیانه',
        '069' => 'موسسه اعتباری نور',
        '070' => 'بانک دوملیته ایران ونزوئلا',
        
        // Additional common variations
        'melli' => 'بانک ملی ایران',
        'mellat' => 'بانک ملت',
        'sepah' => 'بانک سپه',
        'tejarat' => 'بانک تجارت',
        'saderat' => 'بانک صادرات ایران',
        'parsian' => 'بانک پارسیان',
        'pasargad' => 'بانک پاسارگاد',
        'saman' => 'بانک سامان',
        'ayandeh' => 'بانک آینده',
        'sina' => 'بانک سینا',
        'karafarin' => 'بانک کارآفرین',
        'day' => 'بانک دی',
        'dey' => 'بانک دی',
    ];
    
    // Try exact match first
    if (isset($bankTranslations[$bankName])) {
        return $bankTranslations[$bankName];
    }
    
    // Try case-insensitive match
    $lowerBankName = strtolower($bankName);
    if (isset($bankTranslations[$lowerBankName])) {
        return $bankTranslations[$lowerBankName];
    }
    
    // Try partial match for cases like "BANK_MELLI" or "melli_bank"
    foreach ($bankTranslations as $code => $persianName) {
        if (stripos($bankName, $code) !== false || stripos($code, $bankName) !== false) {
            return $persianName;
        }
    }
    
    // Return original if no match found
    return $bankName;
}

// Function to format account owners
function formatAccountOwners($owners) {
    if (is_string($owners)) {
        return $owners;
    }
    
    if (is_array($owners)) {
        $formatted = [];
        foreach ($owners as $owner) {
            if (is_array($owner) || is_object($owner)) {
                $ownerData = (array) $owner;
                if (isset($ownerData['firstName']) && isset($ownerData['lastName'])) {
                    $formatted[] = $ownerData['firstName'] . ' ' . $ownerData['lastName'];
                } elseif (isset($ownerData['name'])) {
                    $formatted[] = $ownerData['name'];
                } else {
                    $formatted[] = json_encode($ownerData, JSON_UNESCAPED_UNICODE);
                }
            } else {
                $formatted[] = (string) $owner;
            }
        }
        return implode('، ', $formatted);
    }
    
    return json_encode($owners, JSON_UNESCAPED_UNICODE);
}
@endphp

@section('content')
<div class="min-h-screen/2/2">
    <div class="max-w-4xl mx-auto md:p-6 p-2">
        
        <!-- Print Header -->
        <div class="hidden print:block text-center mb-6 p-6 border-2 border-gray-300 rounded-lg">
            <h1 class="text-gray-900 mb-2 text-xl font-bold">پیشخوانک - pishkhanak.com</h1>
            <p class="text-gray-700">ارائه دهنده خدمات استعلام و تبدیل اطلاعات بانکی</p>
            <p class="text-sm mt-2">تاریخ تولید: {{ \Hekmatinasser\Verta\Verta::now()->format('Y/n/j H:i:s') }}</p>
        </div>

        <!-- Header -->
        <div class="md:bg-white rounded-lg md:shadow-sm md:border border-gray-200 md:p-6 p-2 mb-6">
            <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
                <h1 class="text-gray-900 w-full md:w-auto md:text-2xl text-xl font-bold text-center md:text-right">نتیجه {{ $service->title }}</h1>
                <div class="flex items-center gap-3">
                    <div class="date flex items-center gap-2">
                        <span>{{ \Hekmatinasser\Verta\Verta::now()->format('Y/n/j') }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="text-gray-600 text-sm">
                        {{ number_format($service->price) }} تومان
                    </div>
                    <div class="bg-green-100 text-green-800 px-3 py-1 rounded-lg text-sm font-medium">
                        عملیات موفق
                    </div>
                </div>
            </div>

            <!-- Input Data Section -->
            @if(isset($inputData) && !empty($inputData))
            <div class="mb-8">
                <div class="space-y-2">
                    <p class="text-gray-700 text-sm font-medium">
                        جهت کپی هر کدام از اطلاعات میتوانید روی آن کلیک کنید.
                    </p>
                    @foreach($inputData as $key => $value)
                    <div 
                        onclick="copyValue('{{ $value }}')" 
                        class="grid grid-cols-7 bg-yellow-50 gap-4 items-center py-3 px-4 border border-yellow-200 rounded-lg hover:bg-sky-50 transition-colors cursor-pointer"
                        title="کلیک کنید تا کپی شود"
                    >
                        <div class="col-span-2 text-gray-700 text-sm font-medium">
                            {{ $fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}
                        </div>
                        <div class="col-span-5 bg-yellow-100 text-gray-900 px-3 py-2 rounded border border-yellow-300 font-mono text-sm text-center" dir="ltr">
                            {{ $value }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Results Section -->
            <div class="mb-6">
                            <div class="space-y-2">
                @foreach($result as $key => $value)
                    @if(!in_array($key, ['processed_at', 'result_id', 'api_response', 'raw_response', 'bank_id', 'bank_code', 'provider']) && !$service->isFieldHidden($key))
                    <div 
                        onclick="copyValue('{{ $key === 'account_owners' ? formatAccountOwners($value) : ($key === 'account_status' ? translateAccountStatus($value) : ($key === 'bank_name' ? translateBankName($value) : (is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value))) }}')" 
                        class="grid bg-white grid-cols-7 gap-4 items-center py-3 px-4 border border-gray-200 rounded-lg hover:bg-sky-50 transition-colors cursor-pointer {{ $key === 'iban' ? 'bg-sky-50 border-sky-200' : '' }}"
                        title="کلیک کنید تا کپی شود"
                    >
                        <div class="col-span-2 text-gray-700 text-sm font-medium">
                            {{ $fieldLabels[$key] ?? ucfirst(str_replace('_', ' ', $key)) }}
                        </div>
                        <div class="col-span-5 bg-sky-50 text-gray-900 px-3 py-2 rounded border border-gray-200 font-mono text-sm text-center bg-sky-100 border-sky-300 {{ $key === 'iban' || $key === 'account_number' ? 'font-bold' : '' }}" dir="{{ $key === 'account_owners' || $key === 'account_status' || $key === 'account_description' || $key === 'account_comment' || $key === 'bank_name' || $key === 'provider' ? 'rtl' : 'ltr' }}">
                            @if($key === 'is_valid')
                                @if($value)
                                    <span class="text-green-600 font-bold">معتبر و تأیید شده</span>
                                @else
                                    <span class="text-red-600 font-bold">نامعتبر</span>
                                @endif
                            @elseif($key === 'account_status')
                                <span class="font-bold {{ $value === '02' ? 'text-green-600' : ($value === '01' || $value === '04' || $value === '05' ? 'text-red-600' : 'text-yellow-600') }}">
                                    {{ translateAccountStatus($value) }}
                                </span>
                            @elseif($key === 'account_owners')
                                <span class="font-medium text-gray-900">
                                    {{ formatAccountOwners($value) }}
                                </span>
                            @elseif($key === 'bank_name')
                                <span class="font-medium text-gray-900">
                                    {{ translateBankName($value) }}
                                </span>
                            @elseif($key === 'provider')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $value === 'finnotech' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $value === 'finnotech' ? 'فینوتک' : 'جیبیت' }}
                                </span>
                            @elseif(is_array($value))
                                {{ json_encode($value, JSON_UNESCAPED_UNICODE) }}
                            @else
                                {{ $value }}
                            @endif
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            </div>
        </div>

        <!-- Actions Bar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6 print:hidden">
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-3">
                <button 
                    onclick="copyAllResults()"
                    class="bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    کپی گزارش
                </button>
                <button 
                    onclick="generatePDF()"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    دانلود PDF
                </button>
                <button 
                    onclick="shareResults()"
                    class="bg-sky-100 border border-gray-300 hover:bg-sky-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                    </svg>
                    اشتراک‌گذاری
                </button>
                <button 
                    onclick="window.print()"
                    class="bg-sky-100 border border-gray-300 hover:bg-sky-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    چاپ گزارش
                </button>
                <a 
                    href="{{ $service->getUrl() }}" 
                    class="bg-sky-100 border border-gray-300 hover:bg-sky-50 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2 text-center"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    تبدیل جدید
                </a>
                <a 
                    href="{{ route('app.page.home') }}" 
                    class="bg-sky-100 border border-gray-300 hover:bg-sky-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2 text-center"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    صفحه اصلی
                </a>
            </div>
        </div>

        <!-- Sharing Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 print:hidden">
            <h4 class="text-gray-900 text-lg font-bold mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                </svg>
                اشتراک‌گذاری نتیجه
            </h4>
            <div class="grid grid-cols-4 gap-3">
                <button 
                    onclick="shareToTelegram()"
                    class="bg-sky-100 hover:bg-sky-200 text-gray-700 p-4 rounded-lg text-center transition-colors flex flex-col items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/>
                    </svg>
                    <span class="text-xs">تلگرام</span>
                </button>
                <button 
                    onclick="shareToWhatsApp()"
                    class="bg-sky-100 hover:bg-sky-200 text-gray-700 p-4 rounded-lg text-center transition-colors flex flex-col items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.525 3.488"/>
                    </svg>
                    <span class="text-xs">واتساپ</span>
                </button>
                <button 
                    onclick="shareToEmail()"
                    class="bg-sky-100 hover:bg-sky-200 text-gray-700 p-4 rounded-lg text-center transition-colors flex flex-col items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.73a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-xs">ایمیل</span>
                </button>
                <button 
                    onclick="shareLink()"
                    class="bg-sky-100 hover:bg-sky-200 text-gray-700 p-4 rounded-lg text-center transition-colors flex flex-col items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    <span class="text-xs">لینک</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Helper function for field labels
    function getLabel(key) {
        const labels = {
            'card_number': 'شماره کارت',
            'iban': 'شماره شبا',
            'account_number': 'شماره حساب',
            'bank_name': 'نام بانک',
            'account_type': 'نوع حساب',
            'branch_code': 'کد شعبه',
            'is_valid': 'وضعیت اعتبار',
            'bank_code': 'کد بانک',
            'owner_name': 'نام صاحب حساب',
            'conversion_date': 'تاریخ تبدیل',
            'validation_date': 'تاریخ اعتبارسنجی',
            'account_status': 'وضعیت حساب',
            'account_description': 'توضیحات حساب',
            'account_comment': 'توضیحات تکمیلی',
            'account_owners': 'صاحبان حساب',
            'provider': 'ارائه‌دهنده سرویس'
        };
        
        return labels[key] || key.replace('_', ' ');
    }

    // Copy single value
    function copyValue(value) {
        navigator.clipboard.writeText(value).then(() => {
            showToast('کپی شد!', 'success');
        }).catch(() => {
            showToast('خطا در کپی کردن', 'error');
        });
    }

    // Copy all results
    function copyAllResults() {
        const results = @json($result);
        const servicePrice = @json($service->price);
        let textToCopy = '';
        
        // Build result text
        const serviceName = @json($service->title);
        textToCopy += `گزارش - ${serviceName}\n`;
        textToCopy += '='.repeat(40) + '\n\n';
        
        textToCopy += `تاریخ: ${new Date().toLocaleDateString('fa-IR')}\n`;
        textToCopy += `زمان: ${new Date().toLocaleTimeString('fa-IR')}\n`;
        textToCopy += `هزینه سرویس: ${servicePrice.toLocaleString()} تومان\n\n`;
        
        for (const [key, value] of Object.entries(results)) {
            if (!['processed_at', 'result_id', 'api_response', 'raw_response', 'bank_id', 'bank_code', 'provider'].includes(key)) {
                const label = getLabel(key);
                let displayValue = value;
                
                // Handle special fields
                if (key === 'account_status') {
                    const statusMap = {
                        '01': 'حساب بسته شده است',
                        '02': 'حساب فعال است',
                        '03': 'حساب مسدود با قابلیت واریز',
                        '04': 'حساب مسدود بدون قابلیت واریز',
                        '05': 'حساب راکد است',
                        '06': 'بروز خطا در پاسخ‌دهی',
                        '07': 'خطای نامشخص سامانه شبا',
                        '08': 'متقاضی فاقد حساب می‌باشد'
                    };
                    displayValue = statusMap[value] || value;
                } else if (key === 'account_owners' && Array.isArray(value)) {
                    displayValue = value.map(owner => {
                        if (typeof owner === 'object' && owner.firstName && owner.lastName) {
                            return `${owner.firstName} ${owner.lastName}`;
                        }
                        return owner;
                    }).join('، ');
                } else if (key === 'provider') {
                    displayValue = value === 'finnotech' ? 'فینوتک' : 'جیبیت';
                } else if (key === 'bank_name') {
                    // Translate bank codes/slugs to Persian names
                    const bankTranslations = {
                        'MARKAZI': 'بانک مرکزی جمهوری اسلامی ایران',
                        'SANAT_VA_MADAN': 'بانک صنعت و معدن',
                        'MELLAT': 'بانک ملت',
                        'REFAH': 'بانک رفاه کارگران',
                        'MASKAN': 'بانک مسکن',
                        'SEPAH': 'بانک سپه',
                        'KESHAVARZI': 'بانک کشاورزی',
                        'MELLI': 'بانک ملی ایران',
                        'TEJARAT': 'بانک تجارت',
                        'SADERAT': 'بانک صادرات ایران',
                        'TOSEAH_SADERAT': 'بانک توسعه صادرات ایران',
                        'POST': 'پست بانک ایران',
                        'TOSEAH_TAAVON': 'بانک توسعه تعاون',
                        'KARAFARIN': 'بانک کارآفرین',
                        'PARSIAN': 'بانک پارسیان',
                        'EGHTESAD_NOVIN': 'بانک اقتصاد نوین',
                        'SAMAN': 'بانک سامان',
                        'PASARGAD': 'بانک پاسارگاد',
                        'SARMAYEH': 'بانک سرمایه',
                        'SINA': 'بانک سینا',
                        'MEHR_IRAN': 'بانک قرض‌الحسنه مهر ایران',
                        'SHAHR': 'بانک شهر',
                        'AYANDEH': 'بانک آینده',
                        'GARDESHGARI': 'بانک گردشگری',
                        'DAY': 'بانک دی',
                        'IRANZAMIN': 'بانک ایران زمین',
                        'RESALAT': 'بانک قرض‌الحسنه رسالت',
                        'MELAL': 'موسسه اعتباری ملل',
                        'KHAVARMIANEH': 'بانک خاورمیانه',
                        'NOOR': 'موسسه اعتباری نور',
                        'IRAN_VENEZUELA': 'بانک دوملیته ایران ونزوئلا',
                        '001': 'بانک مرکزی جمهوری اسلامی ایران',
                        '011': 'بانک صنعت و معدن',
                        '012': 'بانک ملت',
                        '013': 'بانک رفاه کارگران',
                        '014': 'بانک مسکن',
                        '015': 'بانک سپه',
                        '016': 'بانک کشاورزی',
                        '017': 'بانک ملی ایران',
                        '018': 'بانک تجارت',
                        '019': 'بانک صادرات ایران',
                        '020': 'بانک توسعه صادرات ایران',
                        '021': 'پست بانک ایران',
                        '022': 'بانک توسعه تعاون',
                        '054': 'بانک پارسیان',
                        '055': 'بانک اقتصاد نوین',
                        '056': 'بانک سامان',
                        '057': 'بانک پاسارگاد',
                        '058': 'بانک سرمایه',
                        '059': 'بانک سینا',
                        '060': 'بانک قرض‌الحسنه مهر ایران',
                        '061': 'بانک شهر',
                        '062': 'بانک آینده',
                        '063': 'بانک گردشگری',
                        '064': 'بانک دی',
                        '065': 'بانک ایران زمین',
                        '066': 'بانک قرض‌الحسنه رسالت',
                        '067': 'موسسه اعتباری ملل',
                        '068': 'بانک خاورمیانه',
                        '069': 'موسسه اعتباری نور',
                        '070': 'بانک دوملیته ایران ونزوئلا'
                    };
                    displayValue = bankTranslations[value] || bankTranslations[value.toLowerCase()] || value;
                }
                
                textToCopy += `${label}: ${displayValue}\n`;
            }
        }
        
        textToCopy += '\n' + 'تولید شده در پیشخوانک (pishkhanak.com)';
        
        navigator.clipboard.writeText(textToCopy).then(() => {
            showToast('گزارش کپی شد!', 'success');
        }).catch(() => {
            showToast('خطا در کپی کردن', 'error');
        });
    }

    // Share functions
    function shareResults() {
        if (navigator.share) {
            const serviceName = @json($service->title);
            navigator.share({
                title: `${serviceName} - نتیجه`,
                text: 'نتیجه تبدیل من در پیشخوانک',
                url: window.location.href
            });
        } else {
            copyAllResults();
        }
    }

    function shareToTelegram() {
        const text = encodeURIComponent('نتیجه تبدیل من در پیشخوانک');
        const url = encodeURIComponent(window.location.href);
        window.open(`https://t.me/share/url?url=${url}&text=${text}`, '_blank');
    }

    function shareToWhatsApp() {
        const text = encodeURIComponent('نتیجه تبدیل من در پیشخوانک - ' + window.location.href);
        window.open(`https://wa.me/?text=${text}`, '_blank');
    }

    function shareToEmail() {
        const subject = encodeURIComponent('نتیجه تبدیل - پیشخوانک');
        const body = encodeURIComponent('نتیجه تبدیل من در پیشخوانک: ' + window.location.href);
        window.open(`mailto:?subject=${subject}&body=${body}`, '_blank');
    }

    function shareLink() {
        copyValue(window.location.href);
        showToast('لینک کپی شد!', 'success');
    }

    function generatePDF() {
        showToast('درحال تولید PDF...', 'info');
        
        // Get all stylesheets from current page
        const stylesheets = Array.from(document.querySelectorAll('link[rel="stylesheet"], style')).map(sheet => {
            if (sheet.tagName === 'LINK') {
                return `<link rel="stylesheet" href="${sheet.href}">`;
            } else {
                return `<style>${sheet.innerHTML}</style>`;
            }
        }).join('\n');
        
        // Get the main content without print:hidden elements
        const originalContent = document.querySelector('.min-h-screen/2\\/2').cloneNode(true);
        
        // Remove print:hidden elements
        const hiddenElements = originalContent.querySelectorAll('.print\\:hidden');
        hiddenElements.forEach(el => el.remove());
        
        // Make print header visible
        const printHeader = originalContent.querySelector('.hidden.print\\:block');
        if (printHeader) {
            printHeader.classList.remove('hidden');
            printHeader.classList.remove('print:block');
            printHeader.style.display = 'block';
        }
        
        // Create a new window for PDF with exact same styling
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html dir="rtl" lang="fa">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>${@json($service->title)} - نتیجه</title>
                ${stylesheets}
                <style>
                    @import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@100;200;300;400;500;600;700;800;900&display=swap');
                    
                    * {
                        box-sizing: border-box;
                        -webkit-print-color-adjust: exact !important;
                        color-adjust: exact !important;
                        print-color-adjust: exact !important;
                    }
                    
                    body {
                        font-family: 'Vazirmatn', 'IRANSans', 'Tahoma', Arial, sans-serif !important;
                        direction: rtl;
                        background: white !important;
                        margin: 0;
                        padding: 0;
                        line-height: 1.6;
                        color: #111827;
                    }
                    
                    /* Ensure all colors and backgrounds are visible */
                    .bg-white { background-color: white !important; }
                    .bg-sky-50 { background-color: #f9fafb !important; }
                    .bg-sky-100 { background-color: #f3f4f6 !important; }
                    .bg-sky-50 { background-color: #f0f9ff !important; }
                    .bg-sky-100 { background-color: #e0f2fe !important; }
                    .bg-green-100 { background-color: #dcfce7 !important; }
                    .bg-sky-50 { background-color: #eff6ff !important; }
                    .border-gray-200 { border-color: #e5e7eb !important; }
                    .border-gray-300 { border-color: #d1d5db !important; }
                    .border-sky-200 { border-color: #bae6fd !important; }
                    .border-sky-300 { border-color: #7dd3fc !important; }
                    .border-sky-200 { border-color: #bfdbfe !important; }
                    .text-gray-600 { color: #4b5563 !important; }
                    .text-gray-700 { color: #374151 !important; }
                    .text-gray-900 { color: #111827 !important; }
                    .text-sky-600 { color: #0284c7 !important; }
                    .text-green-600 { color: #16a34a !important; }
                    .text-green-800 { color: #166534 !important; }
                    .text-red-600 { color: #dc2626 !important; }
                    .text-sky-600 { color: #2563eb !important; }
                    .text-sky-700 { color: #1d4ed8 !important; }
                    .text-sky-900 { color: #1e3a8a !important; }
                    
                    /* Print specific styles */
                    @media print {
                        body { 
                            margin: 0 !important; 
                            padding: 20px !important;
                            font-size: 12pt !important;
                        }
                        .max-w-4xl { max-width: 100% !important; }
                        .p-6 { padding: 1rem !important; }
                        .mb-6 { margin-bottom: 1rem !important; }
                        .shadow-sm { box-shadow: none !important; }
                        .rounded-lg { border-radius: 8px !important; }
                        .border { border: 1px solid #e5e7eb !important; }
                        .grid { display: grid !important; }
                        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)) !important; }
                        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) !important; }
                        .gap-4 { gap: 1rem !important; }
                        .py-3 { padding-top: 0.75rem !important; padding-bottom: 0.75rem !important; }
                        .px-4 { padding-left: 1rem !important; padding-right: 1rem !important; }
                        .font-mono { font-family: 'Courier New', monospace !important; }
                        .font-bold { font-weight: 700 !important; }
                        .font-medium { font-weight: 500 !important; }
                        .font-semibold { font-weight: 600 !important; }
                        .text-sm { font-size: 0.875rem !important; }
                        .text-base { font-size: 1rem !important; }
                        .text-lg { font-size: 1.125rem !important; }
                        .text-xl { font-size: 1.25rem !important; }
                        .text-2xl { font-size: 1.5rem !important; }
                        .text-center { text-align: center !important; }
                        .flex { display: flex !important; }
                        .items-center { align-items: center !important; }
                        .justify-between { justify-content: space-between !important; }
                        .space-y-2 > * + * { margin-top: 0.5rem !important; }
                        .mb-2 { margin-bottom: 0.5rem !important; }
                        .mb-4 { margin-bottom: 1rem !important; }
                        .mb-8 { margin-bottom: 2rem !important; }
                        .mt-2 { margin-top: 0.5rem !important; }
                    }
                    
                    @page {
                        margin: 1cm;
                        size: A4;
                    }
                </style>
            </head>
            <body>
                ${originalContent.outerHTML}
            </body>
            </html>
        `);
        
        printWindow.document.close();
        
        // Wait for styles to load then print
        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
            showToast('PDF آماده است!', 'success');
        }, 1000);
    }

    // Simple toast notification
    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 px-4 py-2 rounded-lg text-white text-sm transition-all transform duration-300 ${
            type === 'success' ? 'bg-green-500' :
            type === 'error' ? 'bg-red-500' :
            'bg-sky-500'
        }`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                document.body.removeChild(toast);
            }, 300);
        }, 2000);
    }
</script>
@endsection 