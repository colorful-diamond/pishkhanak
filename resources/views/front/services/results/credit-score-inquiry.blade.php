@extends('front.layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 mb-2">گزارش امتیاز اعتباری</h1>
                <p class="text-gray-600">کد ملی {{ $data['data']['national_code'] ?? 'نامشخص' }}</p>
            </div>
            <div class="text-right">
                @php
                    $score = $data['data']['credit_score']['score'] ?? 0;
                    $statusClass = match(true) {
                        $score >= 750 => 'bg-green-100 text-green-800',
                        $score >= 650 => 'bg-sky-100 text-sky-800',
                        $score >= 550 => 'bg-yellow-100 text-yellow-800',
                        $score >= 450 => 'bg-orange-100 text-orange-800',
                        default => 'bg-red-100 text-red-800'
                    };
                    $status = $data['data']['credit_score']['score_status'] ?? 'نامشخص';
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClass }}">
                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                        @if($score >= 650)
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        @else
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        @endif
                    </svg>
                    {{ $status }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-3 space-y-6">
            <!-- Credit Score Dashboard -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 ml-2 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    امتیاز اعتباری شما
                </h2>
                
                <div class="text-center mb-8">
                    <!-- Main Score Display -->
                    <div class="relative mb-6">
                        <div class="w-48 h-24 mx-auto relative">
                            <!-- Score Arc Background -->
                            <svg class="w-full h-full" viewBox="0 0 200 100" style="transform: rotate(0deg)">
                                <path d="M 20 80 A 80 80 0 0 1 180 80" stroke="#e5e7eb" stroke-width="8" fill="none"></path>
                                @php 
                                    $percentage = min(100, ($score / 850) * 100);
                                    $strokeDasharray = ($percentage / 100) * 251.2; // 251.2 is approximate circumference
                                    $scoreColor = match(true) {
                                        $score >= 750 => '#10b981',
                                        $score >= 650 => '#3b82f6',
                                        $score >= 550 => '#f59e0b',
                                        $score >= 450 => '#f97316',
                                        default => '#ef4444'
                                    };
                                @endphp
                                <path d="M 20 80 A 80 80 0 0 1 180 80" stroke="{{ $scoreColor }}" stroke-width="8" fill="none" 
                                      stroke-dasharray="{{ $strokeDasharray }} 251.2" stroke-linecap="round"></path>
                            </svg>
                            <!-- Score Number -->
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <div class="text-4xl font-bold" style="color: {{ $scoreColor }}">{{ $score }}</div>
                                <div class="text-sm text-gray-500">از {{ $data['data']['credit_score']['max_score'] ?? 850 }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Score Status -->
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold mb-2" style="color: {{ $scoreColor }}">
                            {{ $data['data']['credit_score']['score_status'] ?? 'نامشخص' }}
                        </h3>
                        <p class="text-gray-600">{{ $data['data']['credit_score']['score_range'] ?? 'نامشخص' }}</p>
                        @if(!empty($data['data']['credit_score']['percentile']))
                            <p class="text-sm text-gray-500 mt-2">شما بهتر از {{ $data['data']['credit_score']['percentile'] }}% افراد هستید</p>
                        @endif
                    </div>

                    <!-- Score Range Visual Guide -->
                    <div class="bg-sky-50 rounded-lg p-4">
                        <div class="flex justify-between items-center text-xs text-gray-600 mb-2">
                            <span>300</span>
                            <span>450</span>
                            <span>550</span>
                            <span>650</span>
                            <span>750</span>
                            <span>850</span>
                        </div>
                        <div class="h-3 bg-gradient-to-r from-red-500 via-orange-500 via-yellow-500 via-sky-500 to-green-500 rounded-full relative">
                            @php $position = min(100, max(0, (($score - 300) / 550) * 100)); @endphp
                            <div class="absolute w-4 h-4 bg-white border-2 border-gray-800 rounded-full -top-0.5 transform -translate-x-2" 
                                 style="left: {{ $position }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-2">
                            <span>بسیار ضعیف</span>
                            <span>ضعیف</span>
                            <span>متوسط</span>
                            <span>خوب</span>
                            <span>عالی</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Score Breakdown -->
            @if(!empty($data['data']['score_breakdown']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                        </svg>
                        تحلیل امتیاز اعتباری
                    </h2>
                    
                    @php $breakdown = $data['data']['score_breakdown']; @endphp
                    <div class="space-y-6">
                        @if(!empty($breakdown['payment_history']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-900">سابقه پرداخت</span>
                                    <span class="font-bold text-sky-600">{{ $breakdown['payment_history'] }}%</span>
                                </div>
                                <div class="w-full bg-sky-200 rounded-full h-2">
                                    <div class="bg-sky-600 h-2 rounded-full transition-all duration-300" style="width: {{ $breakdown['payment_history'] }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">35% از امتیاز کل (مهم‌ترین عامل)</div>
                            </div>
                        @endif
                        
                        @if(!empty($breakdown['credit_utilization']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-900">میزان استفاده از اعتبار</span>
                                    <span class="font-bold text-green-600">{{ $breakdown['credit_utilization'] }}%</span>
                                </div>
                                <div class="w-full bg-sky-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: {{ $breakdown['credit_utilization'] }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">30% از امتیاز کل</div>
                            </div>
                        @endif
                        
                        @if(!empty($breakdown['credit_history_length']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-900">طول سابقه اعتباری</span>
                                    <span class="font-bold text-purple-600">{{ $breakdown['credit_history_length'] }}%</span>
                                </div>
                                <div class="w-full bg-sky-200 rounded-full h-2">
                                    <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ $breakdown['credit_history_length'] }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">15% از امتیاز کل</div>
                            </div>
                        @endif
                        
                        @if(!empty($breakdown['credit_mix']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-900">تنوع اعتبارات</span>
                                    <span class="font-bold text-orange-600">{{ $breakdown['credit_mix'] }}%</span>
                                </div>
                                <div class="w-full bg-sky-200 rounded-full h-2">
                                    <div class="bg-orange-600 h-2 rounded-full transition-all duration-300" style="width: {{ $breakdown['credit_mix'] }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">10% از امتیاز کل</div>
                            </div>
                        @endif
                        
                        @if(!empty($breakdown['new_credit']))
                            <div class="bg-sky-50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-medium text-gray-900">اعتبارات جدید</span>
                                    <span class="font-bold text-red-600">{{ $breakdown['new_credit'] }}%</span>
                                </div>
                                <div class="w-full bg-sky-200 rounded-full h-2">
                                    <div class="bg-red-600 h-2 rounded-full transition-all duration-300" style="width: {{ $breakdown['new_credit'] }}%"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1">10% از امتیاز کل</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Credit Information -->
            @if(!empty($data['data']['credit_info']))
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        اطلاعات اعتباری
                    </h2>
                    
                    @php $creditInfo = $data['data']['credit_info']; @endphp
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @if(!empty($creditInfo['total_accounts']))
                            <div class="bg-sky-50 border border-sky-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-sky-600 mb-1">{{ $creditInfo['total_accounts'] }}</div>
                                <div class="text-sm text-sky-700">کل حساب‌ها</div>
                            </div>
                        @endif
                        
                        @if(!empty($creditInfo['active_accounts']))
                            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-green-600 mb-1">{{ $creditInfo['active_accounts'] }}</div>
                                <div class="text-sm text-green-700">حساب‌های فعال</div>
                            </div>
                        @endif
                        
                        @if(!empty($creditInfo['closed_accounts']))
                            <div class="bg-sky-50 border border-gray-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-gray-600 mb-1">{{ $creditInfo['closed_accounts'] }}</div>
                                <div class="text-sm text-gray-700">حساب‌های بسته</div>
                            </div>
                        @endif
                        
                        @if(!empty($creditInfo['total_credit_limit']))
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 text-center">
                                <div class="text-lg font-bold text-purple-600 mb-1">{{ number_format(($creditInfo['total_credit_limit'] ?? 0) / 10) }}</div>
                                <div class="text-xs text-purple-700">کل سقف اعتباری (تومان)</div>
                            </div>
                        @endif
                        
                        @if(!empty($creditInfo['total_balance']))
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
                                <div class="text-lg font-bold text-yellow-600 mb-1">{{ number_format(($creditInfo['total_balance'] ?? 0) / 10) }}</div>
                                <div class="text-xs text-yellow-700">کل بدهی (تومان)</div>
                            </div>
                        @endif
                        
                        @if(!empty($creditInfo['credit_utilization_ratio']))
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-orange-600 mb-1">{{ $creditInfo['credit_utilization_ratio'] }}%</div>
                                <div class="text-sm text-orange-700">نرخ استفاده از اعتبار</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Negative Factors -->
            @if(!empty($data['data']['negative_factors']))
                @php 
                    $negativeFactors = $data['data']['negative_factors'];
                    $hasNegativeFactors = collect($negativeFactors)->filter(fn($value) => $value > 0)->isNotEmpty();
                @endphp
                <div class="bg-white rounded-xl border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6 flex items-center">
                        <svg class="w-6 h-6 ml-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        عوامل منفی
                    </h2>
                    
                    @if($hasNegativeFactors)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            @if(($negativeFactors['late_payments'] ?? 0) > 0)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-red-600 mb-1">{{ $negativeFactors['late_payments'] }}</div>
                                    <div class="text-sm text-red-700">پرداخت تأخیری</div>
                                </div>
                            @endif
                            
                            @if(($negativeFactors['defaults'] ?? 0) > 0)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-red-600 mb-1">{{ $negativeFactors['defaults'] }}</div>
                                    <div class="text-sm text-red-700">نکول</div>
                                </div>
                            @endif
                            
                            @if(($negativeFactors['bankruptcies'] ?? 0) > 0)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-red-600 mb-1">{{ $negativeFactors['bankruptcies'] }}</div>
                                    <div class="text-sm text-red-700">ورشکستگی</div>
                                </div>
                            @endif
                            
                            @if(($negativeFactors['collections'] ?? 0) > 0)
                                <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                                    <div class="text-2xl font-bold text-red-600 mb-1">{{ $negativeFactors['collections'] }}</div>
                                    <div class="text-sm text-red-700">وصولی</div>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-green-700 mb-2">عالی!</h3>
                            <p class="text-green-600">هیچ عامل منفی در پرونده اعتباری شما وجود ندارد</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Actions -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">عملیات</h3>
                <div class="space-y-3">
                    <button onclick="window.print()" class="w-full bg-sky-600 hover:bg-sky-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        چاپ گزارش
                    </button>
                    <button onclick="copyCreditReport()" class="w-full bg-sky-100 hover:bg-sky-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        کپی گزارش
                    </button>
                    <button onclick="shareReport()" class="w-full bg-green-100 hover:bg-green-200 text-green-700 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        اشتراک‌گذاری
                    </button>
                </div>
            </div>

            <!-- Recommendations -->
            @if(!empty($data['data']['recommendations']))
                <div class="bg-sky-50 border border-sky-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-sky-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        توصیه‌های بهبود
                    </h3>
                    <ul class="text-sm text-sky-700 space-y-2">
                        @foreach($data['data']['recommendations'] as $recommendation)
                            <li class="flex items-start">
                                <svg class="w-4 h-4 text-sky-600 mt-0.5 ml-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $recommendation }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Score Summary -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">خلاصه امتیاز</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">امتیاز فعلی:</span>
                        <span class="font-bold text-2xl" style="color: {{ $scoreColor }}">{{ $score }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">وضعیت:</span>
                        <span class="font-medium">{{ $status }}</span>
                    </div>
                    @if(!empty($data['data']['credit_score']['percentile']))
                        <div class="flex justify-between">
                            <span class="text-gray-600">رتبه:</span>
                            <span class="font-medium">{{ $data['data']['credit_score']['percentile'] }}%</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-gray-600">تاریخ بروزرسانی:</span>
                        <span class="font-medium">{{ $data['data']['last_updated'] ?? 'نامشخص' }}</span>
                    </div>
                </div>
            </div>

            <!-- Score Improvement Tips -->
            <div class="bg-green-50 border border-green-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-green-800 mb-3">نکات بهبود امتیاز</h3>
                <ul class="text-sm text-green-700 space-y-2">
                    <li>• پرداخت‌ها را به موقع انجام دهید</li>
                    <li>• استفاده از اعتبار را کمتر از 30% نگه دارید</li>
                    <li>• حساب‌های اعتباری قدیمی را نبندید</li>
                    <li>• درخواست اعتبار جدید را محدود کنید</li>
                    <li>• گزارش اعتباری را مرتب بررسی کنید</li>
                </ul>
            </div>

            <!-- Understanding Credit Score -->
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-yellow-800 mb-3">درباره امتیاز اعتباری</h3>
                <div class="text-sm text-yellow-700 space-y-2">
                    <p><strong>300-449:</strong> بسیار ضعیف</p>
                    <p><strong>450-549:</strong> ضعیف</p>
                    <p><strong>550-649:</strong> متوسط</p>
                    <p><strong>650-749:</strong> خوب</p>
                    <p><strong>750-850:</strong> عالی</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const creditData = @json($data['data'] ?? []);

function copyCreditReport() {
    let text = 'گزارش امتیاز اعتباری\n';
    text += '='.repeat(40) + '\n\n';
    text += `کد ملی: ${creditData.national_code || 'نامشخص'}\n`;
    text += `تاریخ گزارش: {{ \Hekmatinasser\Verta\Verta::now()->format('Y/m/d H:i') }}\n\n`;
    
    // Main Score
    if (creditData.credit_score) {
        text += `امتیاز اعتباری: ${creditData.credit_score.score || 0} از ${creditData.credit_score.max_score || 850}\n`;
        text += `وضعیت: ${creditData.credit_score.score_status || 'نامشخص'}\n`;
        text += `رده: ${creditData.credit_score.score_range || 'نامشخص'}\n`;
        if (creditData.credit_score.percentile) text += `رتبه: بهتر از ${creditData.credit_score.percentile}% افراد\n`;
        text += '\n';
    }
    
    // Score Breakdown
    if (creditData.score_breakdown) {
        text += 'تحلیل امتیاز:\n';
        if (creditData.score_breakdown.payment_history) text += `سابقه پرداخت: ${creditData.score_breakdown.payment_history}%\n`;
        if (creditData.score_breakdown.credit_utilization) text += `استفاده از اعتبار: ${creditData.score_breakdown.credit_utilization}%\n`;
        if (creditData.score_breakdown.credit_history_length) text += `طول سابقه: ${creditData.score_breakdown.credit_history_length}%\n`;
        if (creditData.score_breakdown.credit_mix) text += `تنوع اعتبارات: ${creditData.score_breakdown.credit_mix}%\n`;
        if (creditData.score_breakdown.new_credit) text += `اعتبارات جدید: ${creditData.score_breakdown.new_credit}%\n`;
        text += '\n';
    }
    
    // Credit Info
    if (creditData.credit_info) {
        text += 'اطلاعات اعتباری:\n';
        if (creditData.credit_info.total_accounts) text += `کل حساب‌ها: ${creditData.credit_info.total_accounts}\n`;
        if (creditData.credit_info.active_accounts) text += `حساب‌های فعال: ${creditData.credit_info.active_accounts}\n`;
        if (creditData.credit_info.total_credit_limit) text += `سقف اعتباری: ${(creditData.credit_info.total_credit_limit / 10).toLocaleString()} تومان\n`;
        if (creditData.credit_info.total_balance) text += `کل بدهی: ${(creditData.credit_info.total_balance / 10).toLocaleString()} تومان\n`;
        if (creditData.credit_info.credit_utilization_ratio) text += `نرخ استفاده: ${creditData.credit_info.credit_utilization_ratio}%\n`;
        text += '\n';
    }
    
    // Recommendations
    if (creditData.recommendations && creditData.recommendations.length > 0) {
        text += 'توصیه‌ها:\n';
        creditData.recommendations.forEach((rec, index) => {
            text += `${index + 1}. ${rec}\n`;
        });
        text += '\n';
    }
    
    text += 'تولید شده در پیشخوانک (pishkhanak.com)';
    
    copyToClipboard(text);
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('کپی شد!', 'success');
    }).catch(() => {
        showToast('خطا در کپی کردن', 'error');
    });
}

function shareReport() {
    if (navigator.share) {
        navigator.share({
            title: 'گزارش امتیاز اعتباری',
            text: 'گزارش امتیاز اعتباری من از پیشخوانک',
            url: window.location.href
        });
    } else {
        copyToClipboard(window.location.href);
        showToast('لینک کپی شد!', 'success');
    }
}

function showToast(message, type) {
    const toast = document.createElement('div');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-sky-500'
    };
    toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 ${colors[type] || 'bg-sky-500'}`;
    toast.textContent = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .bg-sky-50 { background: #f9fafb !important; }
    .bg-sky-50 { background: #eff6ff !important; }
    .bg-green-50 { background: #f0fdf4 !important; }
    .bg-red-50 { background: #fef2f2 !important; }
    .bg-yellow-50 { background: #fefce8 !important; }
    .border { border: 1px solid #e5e7eb !important; }
}
</style>
@endsection 