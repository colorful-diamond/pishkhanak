@extends('front.layouts.app')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/search-designs.css') }}">
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-sky-900 mb-8 text-center">10 طرح مختلف جستجو</h1>
    
    <!-- Design 1: Minimalist Clean -->
    <section class="mb-12 p-6 bg-white rounded-2xl border border-sky-100">
        <h2 class="text-xl font-semibold text-sky-800 mb-4">طرح 1: مینیمال و تمیز</h2>
        <div class="search-design-1 max-w-2xl mx-auto">
            <div class="relative">
                <input type="search" 
                       class="w-full px-6 py-4 text-lg text-sky-900 bg-sky-50 border-2 border-sky-200 rounded-full focus:border-sky-400 focus:bg-white transition-all duration-300 pr-14"
                       placeholder="جستجو کنید..."
                       id="search1">
                <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                    <svg class="w-6 h-6 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <div class="search-suggestions-1 hidden absolute top-full mt-2 w-full bg-white border border-sky-200 rounded-2xl shadow-lg z-50">
                    <div class="p-4">
                        <div class="text-sm text-sky-600 mb-2">پیشنهادات:</div>
                        <div class="space-y-2">
                            <div class="search-suggestion px-3 py-2 hover:bg-sky-50 rounded-lg cursor-pointer">استعلام خلافی خودرو</div>
                            <div class="search-suggestion px-3 py-2 hover:bg-sky-50 rounded-lg cursor-pointer">محاسبه شبا</div>
                            <div class="search-suggestion px-3 py-2 hover:bg-sky-50 rounded-lg cursor-pointer">استعلام مالیاتی</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Design 2: Card Style with Icons -->
    <section class="mb-12 p-6 bg-white rounded-2xl border border-sky-100">
        <h2 class="text-xl font-semibold text-sky-800 mb-4">طرح 2: کارتی با آیکون‌ها</h2>
        <div class="search-design-2 max-w-2xl mx-auto">
            <div class="bg-sky-50 p-6 rounded-2xl border border-sky-200">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-yellow-400 rounded-full flex items-center justify-center ml-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-sky-900">جستجوی هوشمند</h3>
                </div>
                <div class="relative">
                    <input type="search" 
                           class="w-full px-4 py-3 text-lg text-sky-900 bg-white border border-sky-300 rounded-xl focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 transition-all duration-300"
                           placeholder="چه چیزی می‌خواهید پیدا کنید؟"
                           id="search2">
                    <div class="search-suggestions-2 hidden absolute top-full mt-2 w-full bg-white border border-sky-200 rounded-xl shadow-lg z-50">
                        <div class="p-3">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
                                <div class="search-suggestion flex items-center px-3 py-2 hover:bg-yellow-50 rounded-lg cursor-pointer">
                                    <span class="w-2 h-2 bg-yellow-400 rounded-full ml-2"></span>
                                    استعلام خلافی
                                </div>
                                <div class="search-suggestion flex items-center px-3 py-2 hover:bg-yellow-50 rounded-lg cursor-pointer">
                                    <span class="w-2 h-2 bg-yellow-400 rounded-full ml-2"></span>
                                    محاسبه شبا
                                </div>
                                <div class="search-suggestion flex items-center px-3 py-2 hover:bg-yellow-50 rounded-lg cursor-pointer">
                                    <span class="w-2 h-2 bg-yellow-400 rounded-full ml-2"></span>
                                    استعلام مالیاتی
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Design 3: Split Button Style -->
    <section class="mb-12 p-6 bg-white rounded-2xl border border-sky-100">
        <h2 class="text-xl font-semibold text-sky-800 mb-4">طرح 3: دکمه تقسیم شده</h2>
        <div class="search-design-3 max-w-2xl mx-auto">
            <div class="flex border-2 border-sky-200 rounded-2xl overflow-hidden bg-white">
                <input type="search" 
                       class="flex-1 px-6 py-4 text-lg text-sky-900 bg-transparent focus:outline-none"
                       placeholder="جستجو در پیشخوانک..."
                       id="search3">
                <button class="px-6 py-4 bg-sky-500 text-white hover:bg-sky-600 transition-colors duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
            <div class="search-suggestions-3 hidden mt-2 bg-white border border-sky-200 rounded-xl shadow-lg z-50">
                <div class="p-4">
                    <div class="text-sm text-sky-600 mb-3">جستجوهای محبوب:</div>
                    <div class="flex flex-wrap gap-2">
                        <span class="search-suggestion px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-sm cursor-pointer hover:bg-sky-200">استعلام خلافی</span>
                        <span class="search-suggestion px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-sm cursor-pointer hover:bg-sky-200">محاسبه شبا</span>
                        <span class="search-suggestion px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-sm cursor-pointer hover:bg-sky-200">استعلام مالیاتی</span>
                        <span class="search-suggestion px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-sm cursor-pointer hover:bg-sky-200">استعلام چک</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Design 4: Floating Label Style -->
    <section class="mb-12 p-6 bg-white rounded-2xl border border-sky-100">
        <h2 class="text-xl font-semibold text-sky-800 mb-4">طرح 4: برچسب شناور</h2>
        <div class="search-design-4 max-w-2xl mx-auto">
            <div class="relative">
                <input type="search" 
                       class="w-full px-4 py-4 pt-6 text-lg text-sky-900 bg-white border-2 border-sky-200 rounded-xl focus:border-yellow-400 transition-all duration-300 peer"
                       placeholder=" "
                       id="search4">
                <label for="search4" class="absolute right-4 top-4 text-sky-500 transition-all duration-300 peer-focus:top-2 peer-focus:text-sm peer-focus:text-yellow-500 peer-[:not(:placeholder-shown)]:top-2 peer-[:not(:placeholder-shown)]:text-sm">
                    جستجو کنید...
                </label>
                <div class="search-suggestions-4 hidden absolute top-full mt-2 w-full bg-white border border-sky-200 rounded-xl shadow-lg z-50">
                    <div class="p-4">
                        <div class="space-y-3">
                            <div class="search-suggestion flex items-center px-3 py-2 hover:bg-yellow-50 rounded-lg cursor-pointer">
                                <div class="w-8 h-8 bg-sky-100 rounded-lg flex items-center justify-center ml-3">
                                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-sky-900">استعلام خلافی خودرو</div>
                                    <div class="text-sm text-sky-600">بررسی جریمه‌های رانندگی</div>
                                </div>
                            </div>
                            <div class="search-suggestion flex items-center px-3 py-2 hover:bg-yellow-50 rounded-lg cursor-pointer">
                                <div class="w-8 h-8 bg-sky-100 rounded-lg flex items-center justify-center ml-3">
                                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-medium text-sky-900">محاسبه شبا</div>
                                    <div class="text-sm text-sky-600">تبدیل شماره حساب به شبا</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Design 5: Tab Style Search -->
    <section class="mb-12 p-6 bg-white rounded-2xl border border-sky-100">
        <h2 class="text-xl font-semibold text-sky-800 mb-4">طرح 5: جستجوی تب‌دار</h2>
        <div class="search-design-5 max-w-2xl mx-auto">
            <div class="bg-sky-50 p-6 rounded-2xl">
                <div class="flex mb-4 bg-white rounded-lg p-1">
                    <button class="search-tab flex-1 px-4 py-2 text-sm font-medium text-sky-600 bg-sky-100 rounded-md transition-colors duration-300 active">همه</button>
                    <button class="search-tab flex-1 px-4 py-2 text-sm font-medium text-sky-600 hover:bg-sky-100 rounded-md transition-colors duration-300">خدمات</button>
                    <button class="search-tab flex-1 px-4 py-2 text-sm font-medium text-sky-600 hover:bg-sky-100 rounded-md transition-colors duration-300">مقالات</button>
                </div>
                <div class="relative">
                    <input type="search" 
                           class="w-full px-4 py-3 text-lg text-sky-900 bg-white border border-sky-300 rounded-xl focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 transition-all duration-300"
                           placeholder="جستجو در بخش انتخاب شده..."
                           id="search5">
                    <div class="search-suggestions-5 hidden absolute top-full mt-2 w-full bg-white border border-sky-200 rounded-xl shadow-lg z-50">
                        <div class="p-4">
                            <div class="text-sm text-sky-600 mb-3">نتایج در بخش "همه":</div>
                            <div class="space-y-2">
                                <div class="search-suggestion px-3 py-2 hover:bg-sky-50 rounded-lg cursor-pointer border-r-2 border-yellow-400">
                                    <div class="font-medium text-sky-900">استعلام خلافی خودرو</div>
                                    <div class="text-sm text-sky-600">در بخش خدمات</div>
                                </div>
                                <div class="search-suggestion px-3 py-2 hover:bg-sky-50 rounded-lg cursor-pointer border-r-2 border-sky-400">
                                    <div class="font-medium text-sky-900">راهنمای استعلام خلافی</div>
                                    <div class="text-sm text-sky-600">در بخش مقالات</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Design 6: Voice Search Style -->
    <section class="mb-12 p-6 bg-white rounded-2xl border border-sky-100">
        <h2 class="text-xl font-semibold text-sky-800 mb-4">طرح 6: جستجوی صوتی</h2>
        <div class="search-design-6 max-w-2xl mx-auto">
            <div class="relative">
                <input type="search" 
                       class="w-full px-6 py-4 text-lg text-sky-900 bg-sky-50 border border-sky-200 rounded-2xl focus:border-sky-400 focus:bg-white transition-all duration-300 pl-20"
                       placeholder="جستجو با تایپ یا صدا..."
                       id="search6">
                <div class="absolute left-4 top-1/2 transform -translate-y-1/2 flex items-center space-x-2">
                    <button class="voice-search-btn w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center hover:bg-yellow-500 transition-colors duration-300">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                        </svg>
                    </button>
                    <button class="w-8 h-8 bg-sky-500 rounded-full flex items-center justify-center hover:bg-sky-600 transition-colors duration-300">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
                <div class="search-suggestions-6 hidden absolute top-full mt-2 w-full bg-white border border-sky-200 rounded-2xl shadow-lg z-50">
                    <div class="p-4">
                        <div class="text-sm text-sky-600 mb-3">پیشنهادات صوتی:</div>
                        <div class="space-y-2">
                            <div class="search-suggestion flex items-center px-3 py-2 hover:bg-yellow-50 rounded-lg cursor-pointer">
                                <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center ml-2">
                                    <svg class="w-3 h-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                    </svg>
                                </div>
                                "استعلام خلافی خودرو"
                            </div>
                            <div class="search-suggestion flex items-center px-3 py-2 hover:bg-yellow-50 rounded-lg cursor-pointer">
                                <div class="w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center ml-2">
                                    <svg class="w-3 h-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                    </svg>
                                </div>
                                "محاسبه شبا"
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Design 7: Multi-Step Search -->
    <section class="mb-12 p-6 bg-white rounded-2xl border border-sky-100">
        <h2 class="text-xl font-semibold text-sky-800 mb-4">طرح 7: جستجوی چند مرحله‌ای</h2>
        <div class="search-design-7 max-w-2xl mx-auto">
            <div class="bg-sky-50 p-6 rounded-2xl">
                <div class="flex items-center mb-4">
                    <div class="flex items-center space-x-2 ml-4">
                        <div class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center text-white font-bold text-sm">1</div>
                        <div class="w-4 h-0.5 bg-yellow-400"></div>
                        <div class="w-8 h-8 bg-sky-200 rounded-full flex items-center justify-center text-sky-600 font-bold text-sm">2</div>
                        <div class="w-4 h-0.5 bg-sky-200"></div>
                        <div class="w-8 h-8 bg-sky-200 rounded-full flex items-center justify-center text-sky-600 font-bold text-sm">3</div>
                    </div>
                    <span class="text-sm text-sky-600">انتخاب نوع جستجو</span>
                </div>
                <div class="relative">
                    <input type="search" 
                           class="w-full px-4 py-3 text-lg text-sky-900 bg-white border border-sky-300 rounded-xl focus:border-yellow-400 transition-all duration-300"
                           placeholder="مرحله 1: چه چیزی می‌خواهید جستجو کنید؟"
                           id="search7">
                    <div class="search-suggestions-7 hidden absolute top-full mt-2 w-full bg-white border border-sky-200 rounded-xl shadow-lg z-50">
                        <div class="p-4">
                            <div class="text-sm text-sky-600 mb-3">انتخاب کنید:</div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="search-suggestion p-3 border border-sky-200 rounded-lg hover:border-yellow-400 hover:bg-yellow-50 cursor-pointer transition-all duration-300">
                                    <div class="font-medium text-sky-900">استعلامات خودرو</div>
                                    <div class="text-sm text-sky-600">خلافی، بیمه، فنی</div>
                                </div>
                                <div class="search-suggestion p-3 border border-sky-200 rounded-lg hover:border-yellow-400 hover:bg-yellow-50 cursor-pointer transition-all duration-300">
                                    <div class="font-medium text-sky-900">خدمات بانکی</div>
                                    <div class="text-sm text-sky-600">شبا، حساب، کارت</div>
                                </div>
                                <div class="search-suggestion p-3 border border-sky-200 rounded-lg hover:border-yellow-400 hover:bg-yellow-50 cursor-pointer transition-all duration-300">
                                    <div class="font-medium text-sky-900">استعلامات مالی</div>
                                    <div class="text-sm text-sky-600">مالیات، چک، سهام</div>
                                </div>
                                <div class="search-suggestion p-3 border border-sky-200 rounded-lg hover:border-yellow-400 hover:bg-yellow-50 cursor-pointer transition-all duration-300">
                                    <div class="font-medium text-sky-900">خدمات اداری</div>
                                    <div class="text-sm text-sky-600">جواز، مدرک، گواهی</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Design 8: Quick Actions Search -->
    <section class="mb-12 p-6 bg-white rounded-2xl border border-sky-100">
        <h2 class="text-xl font-semibold text-sky-800 mb-4">طرح 8: جستجو با اکشن‌های سریع</h2>
        <div class="search-design-8 max-w-2xl mx-auto">
            <div class="relative">
                <input type="search" 
                       class="w-full px-6 py-4 text-lg text-sky-900 bg-white border-2 border-sky-200 rounded-2xl focus:border-sky-400 transition-all duration-300 shadow-sm"
                       placeholder="جستجو کنید یا از اکشن‌های سریع استفاده کنید..."
                       id="search8">
                <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                    <button class="w-8 h-8 bg-sky-500 rounded-full flex items-center justify-center hover:bg-sky-600 transition-colors duration-300">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
                <div class="search-suggestions-8 hidden absolute top-full mt-2 w-full bg-white border border-sky-200 rounded-2xl shadow-lg z-50">
                    <div class="p-4">
                        <div class="text-sm text-sky-600 mb-3">اکشن‌های سریع:</div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-4">
                            <button class="quick-action p-3 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors duration-300">
                                <div class="w-8 h-8 bg-yellow-400 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="text-xs text-sky-900">خلافی</div>
                            </button>
                            <button class="quick-action p-3 bg-sky-50 border border-sky-200 rounded-lg hover:bg-sky-100 transition-colors duration-300">
                                <div class="w-8 h-8 bg-sky-400 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <div class="text-xs text-sky-900">شبا</div>
                            </button>
                            <button class="quick-action p-3 bg-yellow-50 border border-yellow-200 rounded-lg hover:bg-yellow-100 transition-colors duration-300">
                                <div class="w-8 h-8 bg-yellow-400 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div class="text-xs text-sky-900">مالیات</div>
                            </button>
                            <button class="quick-action p-3 bg-sky-50 border border-sky-200 rounded-lg hover:bg-sky-100 transition-colors duration-300">
                                <div class="w-8 h-8 bg-sky-400 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="text-xs text-sky-900">چک</div>
                            </button>
                        </div>
                        <div class="text-sm text-sky-600 mb-2">یا جستجو کنید:</div>
                        <div class="space-y-2">
                            <div class="search-suggestion px-3 py-2 hover:bg-sky-50 rounded-lg cursor-pointer">استعلام خلافی خودرو</div>
                            <div class="search-suggestion px-3 py-2 hover:bg-sky-50 rounded-lg cursor-pointer">محاسبه شبا</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Design 9: Smart Suggestions -->
    <section class="mb-12 p-6 bg-white rounded-2xl border border-sky-100">
        <h2 class="text-xl font-semibold text-sky-800 mb-4">طرح 9: پیشنهادات هوشمند</h2>
        <div class="search-design-9 max-w-2xl mx-auto">
            <div class="bg-sky-50 p-6 rounded-2xl border border-sky-200">
                <div class="flex items-center mb-4">
                    <div class="w-10 h-10 bg-gradient-to-r from-sky-400 to-yellow-400 rounded-full flex items-center justify-center ml-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-sky-900">جستجوی هوشمند</h3>
                        <p class="text-sm text-sky-600">پیشنهادات بر اساس نیاز شما</p>
                    </div>
                </div>
                <div class="relative">
                    <input type="search" 
                           class="w-full px-4 py-3 text-lg text-sky-900 bg-white border border-sky-300 rounded-xl focus:border-yellow-400 focus:ring-2 focus:ring-yellow-200 transition-all duration-300"
                           placeholder="مثال: خلافی، شبا، مالیات..."
                           id="search9">
                    <div class="search-suggestions-9 hidden absolute top-full mt-2 w-full bg-white border border-sky-200 rounded-xl shadow-lg z-50">
                        <div class="p-4">
                            <div class="text-sm text-sky-600 mb-3">💡 پیشنهادات هوشمند:</div>
                            <div class="space-y-3">
                                <div class="search-suggestion p-3 border border-sky-100 rounded-lg hover:border-yellow-400 hover:bg-yellow-50 cursor-pointer transition-all duration-300">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-sky-100 rounded-lg flex items-center justify-center ml-3">
                                            <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium text-sky-900">استعلام خلافی خودرو</div>
                                            <div class="text-sm text-sky-600">محبوب‌ترین جستجوی امروز</div>
                                        </div>
                                        <div class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">پرطرفدار</div>
                                    </div>
                                </div>
                                <div class="search-suggestion p-3 border border-sky-100 rounded-lg hover:border-yellow-400 hover:bg-yellow-50 cursor-pointer transition-all duration-300">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center ml-3">
                                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="font-medium text-sky-900">محاسبه شبا</div>
                                            <div class="text-sm text-sky-600">تبدیل سریع شماره حساب</div>
                                        </div>
                                        <div class="text-xs bg-sky-100 text-sky-800 px-2 py-1 rounded-full">سریع</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Design 10: Compact Mobile-First -->
    <section class="mb-12 p-6 bg-white rounded-2xl border border-sky-100">
        <h2 class="text-xl font-semibold text-sky-800 mb-4">طرح 10: فشرده و موبایل محور</h2>
        <div class="search-design-10 max-w-2xl mx-auto">
            <div class="space-y-4">
                <div class="flex items-center space-x-2">
                    <div class="flex-1 relative">
                        <input type="search" 
                               class="w-full px-4 py-3 text-base text-sky-900 bg-white border border-sky-300 rounded-xl focus:border-yellow-400 transition-all duration-300 pl-10"
                               placeholder="جستجو..."
                               id="search10">
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2">
                            <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                    <button class="w-12 h-12 bg-yellow-400 rounded-xl flex items-center justify-center hover:bg-yellow-500 transition-colors duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                        </svg>
                    </button>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="quick-tag px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-sm cursor-pointer hover:bg-sky-200 transition-colors duration-300">خلافی</span>
                    <span class="quick-tag px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm cursor-pointer hover:bg-yellow-200 transition-colors duration-300">شبا</span>
                    <span class="quick-tag px-3 py-1 bg-sky-100 text-sky-700 rounded-full text-sm cursor-pointer hover:bg-sky-200 transition-colors duration-300">مالیات</span>
                    <span class="quick-tag px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm cursor-pointer hover:bg-yellow-200 transition-colors duration-300">چک</span>
                </div>
                <div class="search-suggestions-10 hidden bg-white border border-sky-200 rounded-xl shadow-lg z-50">
                    <div class="p-3">
                        <div class="space-y-2">
                            <div class="search-suggestion flex items-center px-3 py-2 hover:bg-sky-50 rounded-lg cursor-pointer">
                                <div class="w-6 h-6 bg-sky-100 rounded-lg flex items-center justify-center ml-2">
                                    <svg class="w-3 h-3 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-sky-900">استعلام خلافی خودرو</span>
                            </div>
                            <div class="search-suggestion flex items-center px-3 py-2 hover:bg-sky-50 rounded-lg cursor-pointer">
                                <div class="w-6 h-6 bg-yellow-100 rounded-lg flex items-center justify-center ml-2">
                                    <svg class="w-3 h-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-sky-900">محاسبه شبا</span>
                            </div>
                            <div class="search-suggestion flex items-center px-3 py-2 hover:bg-sky-50 rounded-lg cursor-pointer">
                                <div class="w-6 h-6 bg-sky-100 rounded-lg flex items-center justify-center ml-2">
                                    <svg class="w-3 h-3 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-sky-900">استعلام مالیاتی</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
@endsection

@push('scripts')
<script src="{{ asset('js/search-designs.js') }}"></script>
@endpush 