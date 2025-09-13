@extends('front.layouts.app')

@push('styles')
@vite(['resources/css/unified-ai-search.css'])
@endpush

@section('title', 'خانه')

@section('content')
    <!-- Hero Section -->
    <section class="flex flex-col justify-center p-3 py-6 md:p-6  w-full md:bg-gradient-to-br from-sky-50 to-white rounded-[24px]md:shadow-sm animate-fadeIn md:border border-sky-200"
             aria-label="Hero Section">
        <div class="text-center max-w-2xl mx-auto mb-8">
            <h1 class="text-lg md:text-3xl font-bold text-sky-900 mb-4">
                <span class="text-yellow-500 hover:text-yellow-600 transition-colors duration-300">پیشخوانک</span>
                ؛ میز خدمتِ هوشمند شما
            </h1>
        </div>
        
        <!-- AI Search Component -->
        <div class="max-w-5xl mx-auto w-full">
            <x-unified-ai-search 
                :auto_typing_examples="[
                    'چطور خلافی خودرو چک کنم؟',
                    'محاسبه شماره شبا',
                    'استعلام کد پستی',
                    'بررسی وضعیت چک صیادی',
                    'استعلام کارت ملی'
                ]"
                :show_mode_selector="false"
                :show_ai_status="true"
                placeholder="سوال خود را از دستیار هوشمند بپرسید..." />
        </div>
        
        <!-- Popular Services -->
        @if($popularServices && $popularServices->count() > 0)
        <div class="mt-8 md:mt-12">
            <h2 class="text-xl md:text-2xl font-bold text-sky-900 mb-6 text-center">خدمات پر درخواست</h2>
            <div class="flex flex-wrap justify-center gap-3 md:gap-4">
                @foreach($popularServices as $service)
                    <a href="{{ $service->getUrl() }}" 
                       class="px-4 py-3 md:px-6 md:py-3 bg-white text-sky-700 rounded-xl border border-sky-200 hover:bg-sky-50 hover:border-yellow-400 transition-all duration-300 text-sm md:text-base font-medium shadow-sm hover:shadow-md transform hover:-translate-y-1">
                        {{ $service->getDisplayTitle() }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </section>

    <!-- Services by Category -->
    <section class="box-border flex relative flex-col shrink-0 animate-fadeIn mt-6" aria-label="Available Services">
        @foreach($categories as $category)
        @if($category->services && $category->services->count() > 0)
        <section
            class="flex overflow-hidden flex-col p-6 md:p-10 mt-6 w-full rounded-[32px] text-sky-900 relative"
            style="background: linear-gradient(135deg, {{ $category->background_color ?? '#f0f9ff' }} 0%, #ffffff 100%); border: 1px solid {{ $category->border_color ?? '#e0f2fe' }};">
            
            @if($category->hasMedia('background_image'))
                <div class="animate-float h-36 w-32 absolute left-12 top-0 opacity-30 pointer-events-none">
                    <img src="{{ $category->getFirstMediaUrl('background_image') }}" 
                         alt="{{ $category->name }} Background" 
                         class="w-full h-full object-contain">
                </div>
            @elseif($category->background_icon)
                <div class="animate-float h-36 w-32 absolute left-12 top-0 opacity-30 pointer-events-none">
                    {!! $category->background_icon !!}
                </div>
            @endif
            
            <h2 class="flex gap-1 items-center self-start text-lg md:text-xl font-bold leading-relaxed text-center z-10">
                <span class="self-stretch my-auto">{{ $category->name }}</span>
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-3 mt-6 md:mt-8 w-full z-10">
                @foreach($category->services->reverse() as $service)
                    <a href="{{ $service->getUrl() }}"
                       class="service-button flex flex-col md:flex-row gap-3 items-center p-3 bg-white rounded-xl border border-solid hover:shadow-md focus:outline-none transition-all duration-300 text-center md:text-right min-h-[80px] md:min-h-[auto]"
                       style="hover:border-color: {{ $category->hover_border_color ?? '#fbbf24' }}; hover:background-color: {{ $category->hover_background_color ?? '#fefce8' }}; border-color: {{ $category->border_color ?? '#e0f2fe' }};"
                       aria-label="{{ $service->getDisplayTitle() }}">
                        
                        @if($service->hasMedia('icon'))
                            <img loading="lazy"
                                 src="{{ $service->getFirstMediaUrl('icon') }}"
                                 class="object-contain shrink-0 w-12 h-12 md:w-16 md:h-16" 
                                 alt="{{ $service->getDisplayTitle() }} Icon" />
                        @else
                            <div class="w-8 h-8 md:w-12 md:h-12 bg-sky-200 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 md:w-6 md:h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        <span class="text-xs md:text-sm font-medium leading-5 md:leading-6 text-sky-900 flex-1">
                            {{ $service->getDisplayTitle() }}
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
        @endif
        @endforeach
    </section>

         <!-- Statistics Section -->
     <section class="flex flex-col p-6 md:p-10 mt-6 w-full bg-sky-50 border border-sky-200 rounded-[24px] animate-fadeIn">
         <div class="text-center mb-12">
             <h2 class="text-2xl md:text-3xl font-bold text-sky-900 mb-4">
                 پیشخوانک در <span class="text-yellow-500">اعداد</span>
             </h2>
             <p class="text-gray-700 max-w-2xl mx-auto text-base leading-relaxed">
                 آمار عملکرد پیشخوانک نشان‌دهنده اعتماد کاربران و کیفیت خدمات ماست
             </p>
         </div>
         
         <div class="grid grid-cols-2 md:grid-cols-4 gap-6 md:gap-8">
             <div class="text-center p-6 bg-white rounded-lg border border-sky-100 hover:shadow-md transition-all duration-300">
                 <div class="text-3xl md:text-4xl font-bold text-sky-900 mb-2">+۵۰۰۰</div>
                 <p class="text-gray-600 text-sm">کاربر فعال</p>
             </div>
             <div class="text-center p-6 bg-white rounded-lg border border-sky-100 hover:shadow-md transition-all duration-300">
                 <div class="text-3xl md:text-4xl font-bold text-sky-900 mb-2">+۱۵۰۰۰</div>
                 <p class="text-gray-600 text-sm">استعلام انجام شده</p>
             </div>
             <div class="text-center p-6 bg-white rounded-lg border border-sky-100 hover:shadow-md transition-all duration-300">
                 <div class="text-3xl md:text-4xl font-bold text-sky-900 mb-2">۹۸٪</div>
                 <p class="text-gray-600 text-sm">نرخ موفقیت</p>
             </div>
             <div class="text-center p-6 bg-white rounded-lg border border-sky-100 hover:shadow-md transition-all duration-300">
                 <div class="text-3xl md:text-4xl font-bold text-sky-900 mb-2">۲۴/۷</div>
                 <p class="text-gray-600 text-sm">پشتیبانی</p>
             </div>
         </div>
     </section>

     <!-- Brand Mission Section -->
     <section class="flex flex-col p-6 md:p-10 mt-6 w-full bg-white border border-gray-200 rounded-[24px] animate-fadeIn">
         <div class="text-center mb-8">
             <h2 class="text-2xl md:text-3xl font-bold text-sky-900 mb-4">
                 چرا <span class="text-yellow-500">پیشخوانک</span>؟
             </h2>
             <p class="text-gray-700 max-w-3xl mx-auto text-base md:text-lg leading-relaxed">
                 پیشخوانک با هدف ساده‌سازی دسترسی به خدمات الکترونیک دولتی و بانکی طراحی شده است
             </p>
         </div>
         
         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
             <div class="flex flex-col items-center p-6 bg-sky-50 rounded-lg border border-sky-100 hover:shadow-md transition-all duration-300">
                 <div class="w-16 h-16 bg-sky-500 rounded-lg flex items-center justify-center mb-4">
                     <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                     </svg>
                 </div>
                 <h3 class="text-lg font-bold text-sky-900 mb-2">سرعت بالا</h3>
                 <p class="text-gray-600 text-center text-sm">دریافت نتایج در کمتر از ۳۰ثانیه</p>
             </div>
             
             <div class="flex flex-col items-center p-6 bg-green-50 rounded-lg border border-green-100 hover:shadow-md transition-all duration-300">
                 <div class="w-16 h-16 bg-green-500 rounded-lg flex items-center justify-center mb-4">
                     <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                     </svg>
                 </div>
                 <h3 class="text-lg font-bold text-sky-900 mb-2">قابل اعتماد</h3>
                 <p class="text-gray-600 text-center text-sm">اتصال مستقیم به منابع رسمی</p>
             </div>
             
             <div class="flex flex-col items-center p-6 bg-purple-50 rounded-lg border border-purple-100 hover:shadow-md transition-all duration-300">
                 <div class="w-16 h-16 bg-purple-500 rounded-lg flex items-center justify-center mb-4">
                     <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                     </svg>
                 </div>
                 <h3 class="text-lg font-bold text-sky-900 mb-2">امن و محفوظ</h3>
                 <p class="text-gray-600 text-center text-sm">رمزنگاری پیشرفته اطلاعات</p>
             </div>
             
             <div class="flex flex-col items-center p-6 bg-yellow-50 rounded-lg border border-yellow-100 hover:shadow-md transition-all duration-300">
                 <div class="w-16 h-16 bg-yellow-500 rounded-lg flex items-center justify-center mb-4">
                     <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                     </svg>
                 </div>
                 <h3 class="text-lg font-bold text-sky-900 mb-2">پشتیبانی ۲۴/۷</h3>
                 <p class="text-gray-600 text-center text-sm">دستیار هوشمند همیشه در دسترس</p>
             </div>
         </div>
     </section>

     <!-- How it Works Section -->
     <section class="flex flex-col p-6 md:p-10 mt-6 w-full bg-gray-50 border border-gray-200 rounded-[24px] animate-fadeIn">
         <div class="text-center mb-12">
             <h2 class="text-2xl md:text-3xl font-bold text-sky-900 mb-4">چگونه کار می‌کند؟</h2>
             <p class="text-gray-700 max-w-2xl mx-auto text-base md:text-lg leading-relaxed">
                 در ۴ مرحله ساده به خدمات مورد نیاز خود دسترسی پیدا کنید
             </p>
         </div>
         
         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
             <div class="text-center">
                 <div class="w-20 h-20 bg-sky-500 rounded-full flex items-center justify-center mx-auto mb-4">
                     <span class="text-2xl font-bold text-white">۱</span>
                 </div>
                 <h3 class="text-lg font-bold text-sky-900 mb-3">ثبت‌نام</h3>
                 <p class="text-gray-600 text-sm leading-relaxed">با شماره موبایل خود ثبت‌نام کنید</p>
             </div>
             
             <div class="text-center">
                 <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4">
                     <span class="text-2xl font-bold text-white">۲</span>
                 </div>
                 <h3 class="text-lg font-bold text-sky-900 mb-3">انتخاب خدمت</h3>
                 <p class="text-gray-600 text-sm leading-relaxed">خدمت مورد نظر خود را انتخاب کنید</p>
             </div>
             
             <div class="text-center">
                 <div class="w-20 h-20 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                     <span class="text-2xl font-bold text-white">۳</span>
                 </div>
                 <h3 class="text-lg font-bold text-sky-900 mb-3">پرداخت</h3>
                 <p class="text-gray-600 text-sm leading-relaxed">مبلغ خدمت را پرداخت کنید</p>
             </div>
             
             <div class="text-center">
                 <div class="w-20 h-20 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-4">
                     <span class="text-2xl font-bold text-white">۴</span>
                 </div>
                 <h3 class="text-lg font-bold text-sky-900 mb-3">دریافت نتیجه</h3>
                 <p class="text-gray-600 text-sm leading-relaxed">نتیجه را در کمتر از ۳۰ ثانیه دریافت کنید</p>
             </div>
         </div>
     </section>

         <!-- Services Overview Section -->
     <section class="flex flex-col p-6 md:p-10 mt-6 w-full bg-white border border-gray-200 rounded-[24px] animate-fadeIn">
         <div class="text-center mb-10">
             <h2 class="text-2xl md:text-3xl font-bold text-sky-900 mb-4">خدمات پیشخوانک</h2>
             <p class="text-gray-700 max-w-2xl mx-auto text-base md:text-lg leading-relaxed">
                 دسترسی آسان و سریع به بیش از ۱۵ خدمت الکترونیک در سه دسته اصلی
             </p>
         </div>
         
         <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
             <!-- Banking Services -->
             <div class="bg-sky-50 rounded-lg p-6 border border-sky-100 hover:shadow-md transition-all duration-300">
                 <div class="w-14 h-14 bg-sky-500 rounded-lg flex items-center justify-center mb-4">
                     <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                         <path d="M2 6h20v2H2zm0 5h20v2H2zm0 5h20v2H2z"/>
                     </svg>
                 </div>
                 <h3 class="text-xl font-bold text-sky-900 mb-4">خدمات بانکی</h3>
                 <ul class="space-y-3 text-gray-700">
                     <li class="flex items-center">
                         <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                         </svg>
                         استعلام وضعیت رنگ چک
                     </li>
                     <li class="flex items-center">
                         <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                         </svg>
                         محاسبه شماره شبا از کارت
                     </li>
                     <li class="flex items-center">
                         <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                         </svg>
                         وام و تسهیلات بانکی
                     </li>
                     <li class="flex items-center">
                         <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                         </svg>
                         اعتبارسنجی بانکی
                     </li>
                 </ul>
             </div>

             <!-- Vehicle Services -->
             <div class="bg-red-50 rounded-lg p-6 border border-red-100 hover:shadow-md transition-all duration-300">
                 <div class="w-14 h-14 bg-red-500 rounded-lg flex items-center justify-center mb-4">
                     <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                         <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11C5.84 5 5.28 5.42 5.08 6.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-1.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                     </svg>
                 </div>
                 <h3 class="text-xl font-bold text-sky-900 mb-4">خودرو و موتور</h3>
                 <ul class="space-y-3 text-gray-700">
                     <li class="flex items-center">
                         <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                         </svg>
                         استعلام خلافی خودرو
                     </li>
                     <li class="flex items-center">
                         <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                         </svg>
                         استعلام خلافی موتورسیکلت
                     </li>
                     <li class="flex items-center">
                         <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                         </svg>
                         لیست پلاک‌های فعال
                     </li>
                     <li class="flex items-center">
                         <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                         </svg>
                         سوابق بیمه شخص ثالث
                     </li>
                 </ul>
             </div>

             <!-- Other Services -->
             <div class="bg-purple-50 rounded-lg p-6 border border-purple-100 hover:shadow-md transition-all duration-300">
                 <div class="w-14 h-14 bg-purple-500 rounded-lg flex items-center justify-center mb-4">
                     <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                         <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                     </svg>
                 </div>
                 <h3 class="text-xl font-bold text-sky-900 mb-4">سایر خدمات</h3>
                 <ul class="space-y-3 text-gray-700">
                     <li class="flex items-center">
                         <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                         </svg>
                         استعلام کد پستی
                     </li>
                     <li class="flex items-center">
                         <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                         </svg>
                         استعلام وضعیت حیات
                     </li>
                     <li class="flex items-center">
                         <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                         </svg>
                         وضعیت نظام وظیفه
                     </li>
                     <li class="flex items-center">
                         <svg class="w-4 h-4 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                         </svg>
                         استعلام شناسه ملی
                     </li>
                 </ul>
             </div>
         </div>
     </section>

     <!-- Mobile Application Section -->
     <section class="flex flex-col p-6 md:p-10 mt-6 w-full bg-sky-50 border border-sky-200 rounded-[24px] animate-fadeIn">
         <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
             <div>
                 <h2 class="text-2xl md:text-3xl font-bold text-sky-900 mb-4">
                     اپلیکیشن موبایل <span class="text-yellow-500">پیشخوانک</span>
            </h2>
                 <p class="text-gray-700 text-base md:text-lg leading-relaxed mb-6">
                     همه خدمات پیشخوانک را در جیب خود داشته باشید. اپلیکیشن موبایل پیشخوانک تجربه کاملی از کلیه خدمات را برای شما فراهم می‌کند.
                 </p>
                 
                 <div class="space-y-4 mb-8">
                     <div class="flex items-center">
                         <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center ml-3">
                             <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                             </svg>
                         </div>
                         <span class="text-gray-700">دسترسی آسان به تمام خدمات</span>
                     </div>
                     <div class="flex items-center">
                         <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center ml-3">
                             <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                             </svg>
                         </div>
                         <span class="text-gray-700">رابط کاربری بهینه شده برای موبایل</span>
                     </div>
                     <div class="flex items-center">
                         <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center ml-3">
                             <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                             </svg>
                         </div>
                         <span class="text-gray-700">اعلان‌های لحظه‌ای نتایج</span>
                     </div>
                     <div class="flex items-center">
                         <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center ml-3">
                             <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                             </svg>
                         </div>
                         <span class="text-gray-700">ذخیره‌سازی سابقه استعلامات</span>
                     </div>
                 </div>
                 
                 <div class="flex flex-col sm:flex-row gap-4">
                     <a href="#" class="flex items-center justify-center px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors duration-300">
                         <svg class="w-6 h-6 ml-2" fill="currentColor" viewBox="0 0 24 24">
                             <path d="M17.05 20.28c-.98.95-2.05.8-3.08.35-1.09-.46-2.09-.48-3.24 0-1.44.62-2.2.44-3.06-.35C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.57 1.5-1.31 2.99-2.54 4.09l.01-.01zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"/>
                         </svg>
                         دانلود از App Store
                     </a>
                     <a href="#" class="flex items-center justify-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-300">
                         <svg class="w-6 h-6 ml-2" fill="currentColor" viewBox="0 0 24 24">
                             <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
                         </svg>
                         دانلود از Google Play
                     </a>
                 </div>
             </div>
             
             <div class="text-center">
                 <div class="w-64 h-96 bg-gray-800 rounded-3xl mx-auto p-2 shadow-lg">
                     <div class="w-full h-full bg-white rounded-2xl flex items-center justify-center">
                         <div class="text-center">
                             <div class="w-20 h-20 bg-sky-500 rounded-2xl mx-auto mb-4 flex items-center justify-center">
                                 <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                 </svg>
                             </div>
                             <p class="text-sky-900 font-bold">اپلیکیشن پیشخوانک</p>
                             <p class="text-gray-500 text-sm mt-2">بزودی در دسترس</p>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </section>

     <!-- Security and Trust Section -->
     <section class="flex flex-col p-6 md:p-10 mt-6 w-full bg-green-50 border border-green-200 rounded-[24px] animate-fadeIn">
         <div class="text-center mb-10">
             <h2 class="text-2xl md:text-3xl font-bold text-sky-900 mb-4">امنیت و اعتماد</h2>
             <p class="text-gray-700 max-w-2xl mx-auto text-base md:text-lg leading-relaxed">
                 امنیت اطلاعات شما برای ما اولویت اول است. تمامی فرآیندها با بالاترین استانداردهای امنیتی انجام می‌شود
             </p>
         </div>
         
         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
             <div class="text-center p-6 bg-white rounded-lg border border-green-100 hover:shadow-md transition-all duration-300">
                 <div class="w-16 h-16 bg-green-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                     <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                     </svg>
                 </div>
                 <h3 class="text-lg font-bold text-sky-900 mb-3">رمزنگاری SSL</h3>
                 <p class="text-gray-600 text-sm">تمامی اطلاعات با رمزنگاری ۲۵۶ بیتی محافظت می‌شوند</p>
             </div>
             
             <div class="text-center p-6 bg-white rounded-lg border border-green-100 hover:shadow-md transition-all duration-300">
                 <div class="w-16 h-16 bg-green-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                     <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                     </svg>
                 </div>
                 <h3 class="text-lg font-bold text-sky-900 mb-3">عدم ذخیره‌سازی</h3>
                 <p class="text-gray-600 text-sm">اطلاعات حساس شما در سرورهای ما ذخیره نمی‌شود</p>
             </div>
             
             <div class="text-center p-6 bg-white rounded-lg border border-green-100 hover:shadow-md transition-all duration-300">
                 <div class="w-16 h-16 bg-green-500 rounded-lg flex items-center justify-center mx-auto mb-4">
                     <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                     </svg>
                 </div>
                 <h3 class="text-lg font-bold text-sky-900 mb-3">مطابقت با قوانین</h3>
                 <p class="text-gray-600 text-sm">کاملاً مطابق با قوانین حفاظت از داده‌های شخصی</p>
             </div>
         </div>
     </section>

         <!-- FAQ Section -->
     <section class="flex flex-col p-6 md:p-10 mt-6 w-full bg-yellow-50 border border-yellow-200 rounded-[24px] animate-fadeIn">
        <div class="text-center mb-10">
            <h2 class="text-2xl md:text-3xl font-bold text-sky-900 mb-4">سؤالات متداول</h2>
            <p class="text-gray-600 max-w-2xl mx-auto text-base md:text-lg leading-relaxed">
                پاسخ سؤالات رایج کاربران درباره خدمات و نحوه استفاده از پیشخوانک
            </p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-xl p-6 border border-yellow-200 hover:shadow-lg transition-all duration-300">
                <h3 class="font-bold text-sky-900 mb-3">چگونه از خدمات پیشخوانک استفاده کنم؟</h3>
                <p class="text-gray-700 text-sm leading-relaxed">
                    ابتدا خدمت مورد نظر خود را از طریق دستیار هوشمند یا منوی خدمات انتخاب کنید. سپس اطلاعات مورد نیاز را وارد کرده و پرداخت را انجام دهید. نتیجه در کمتر از ۳۰ ثانیه آماده خواهد شد.
                </p>
            </div>
            
                         <div class="bg-white rounded-xl p-6 border border-yellow-200 hover:shadow-lg transition-all duration-300">
                 <h3 class="font-bold text-sky-900 mb-3">آیا نیاز به ثبت‌نام دارم؟</h3>
                 <p class="text-gray-700 text-sm leading-relaxed">
                     بله، تمامی خدمات بعد از ثبت‌نام با شماره موبایل به شما ارائه می‌شود. ثبت‌نام بسیار ساده و سریع است و تنها با وارد کردن شماره موبایل و تأیید کد پیامکی انجام می‌شود.
                 </p>
             </div>
            
            <div class="bg-white rounded-xl p-6 border border-yellow-200 hover:shadow-lg transition-all duration-300">
                <h3 class="font-bold text-sky-900 mb-3">روش‌های پرداخت چیست؟</h3>
                <p class="text-gray-700 text-sm leading-relaxed">
                    پرداخت از طریق کلیه کارت‌های بانکی عضو شتاب، درگاه‌های معتبر بانکی و کیف پول الکترونیک امکان‌پذیر است. تمامی تراکنش‌ها کاملاً امن و رمزنگاری شده است.
                </p>
            </div>
            
            <div class="bg-white rounded-xl p-6 border border-yellow-200 hover:shadow-lg transition-all duration-300">
                <h3 class="font-bold text-sky-900 mb-3">چه مدت زمان برای دریافت نتیجه نیاز است؟</h3>
                <p class="text-gray-700 text-sm leading-relaxed">
                    اکثر خدمات در کمتر از ۳۰ ثانیه نتیجه ارائه می‌دهند. برخی خدمات پیچیده‌تر ممکن است تا ۲ دقیقه زمان نیاز داشته باشند. در صورت بروز مشکل، مبلغ پرداختی بازگردانده می‌شود.
                </p>
            </div>
            
            <div class="bg-white rounded-xl p-6 border border-yellow-200 hover:shadow-lg transition-all duration-300">
                <h3 class="font-bold text-sky-900 mb-3">اطلاعات من محفوظ است؟</h3>
                <p class="text-gray-700 text-sm leading-relaxed">
                    بله، کلیه اطلاعات شخصی شما با بالاترین استانداردهای امنیتی محافظت می‌شود. ما هیچ‌گونه اطلاعات شخصی را ذخیره یا به اشتراک نمی‌گذاریم و تنها برای ارائه خدمت استفاده می‌کنیم.
                </p>
            </div>
            
            <div class="bg-white rounded-xl p-6 border border-yellow-200 hover:shadow-lg transition-all duration-300">
                <h3 class="font-bold text-sky-900 mb-3">آیا کد تأیید پیامکی نیاز است؟</h3>
                <p class="text-gray-700 text-sm leading-relaxed">
                    برای برخی خدمات که نیاز به تأیید هویت دارند، کد تأیید پیامکی ارسال می‌شود. این کار برای افزایش امنیت و اطمینان از درستی اطلاعات است.
                </p>
            </div>
            
            <div class="bg-white rounded-xl p-6 border border-yellow-200 hover:shadow-lg transition-all duration-300">
                <h3 class="font-bold text-sky-900 mb-3">چگونه می‌توانم نتایج قبلی خود را ببینم؟</h3>
                <p class="text-gray-700 text-sm leading-relaxed">
                    در صورت ثبت‌نام، تمامی نتایج خدمات در پنل کاربری شما ذخیره می‌شود. کاربران مهمان می‌توانند با کد رهگیری ارائه شده، نتایج خود را مشاهده کنند.
                </p>
            </div>
            
            <div class="bg-white rounded-xl p-6 border border-yellow-200 hover:shadow-lg transition-all duration-300">
                <h3 class="font-bold text-sky-900 mb-3">هزینه خدمات چقدر است؟</h3>
                <p class="text-gray-700 text-sm leading-relaxed">
                    هزینه هر خدمت متفاوت است و بر اساس پیچیدگی و منبع داده تعیین می‌شود. قیمت‌ها شفاف و قبل از پرداخت نمایش داده می‌شود. برخی خدمات از ۱۰۰۰ تومان شروع می‌شود.
                </p>
            </div>
            
            <div class="bg-white rounded-xl p-6 border border-yellow-200 hover:shadow-lg transition-all duration-300">
                <h3 class="font-bold text-sky-900 mb-3">در صورت خطا چه اتفاقی می‌افتد؟</h3>
                <p class="text-gray-700 text-sm leading-relaxed">
                    در صورت بروز خطا یا عدم دریافت نتیجه، مبلغ پرداختی به‌صورت خودکار به حساب شما بازگردانده می‌شود. همچنین می‌توانید با پشتیبانی تماس بگیرید.
                </p>
            </div>
            
            <div class="bg-white rounded-xl p-6 border border-yellow-200 hover:shadow-lg transition-all duration-300">
                <h3 class="font-bold text-sky-900 mb-3">آیا خدمات در تمام ساعات شبانه‌روز فعال است؟</h3>
                <p class="text-gray-700 text-sm leading-relaxed">
                    بله، پیشخوانک ۲۴ ساعته در خدمت شماست. دستیار هوشمند همیشه آماده پاسخگویی است و اکثر خدمات در تمام ساعات قابل استفاده هستند.
                </p>
            </div>
            
            <div class="bg-white rounded-xl p-6 border border-yellow-200 hover:shadow-lg transition-all duration-300">
                <h3 class="font-bold text-sky-900 mb-3">چگونه با پشتیبانی تماس بگیرم؟</h3>
                <p class="text-gray-700 text-sm leading-relaxed">
                    از طریق دستیار هوشمند، تیکت پشتیبانی در پنل کاربری، یا صفحه تماس با ما می‌توانید با تیم پشتیبانی در ارتباط باشید. پاسخگویی معمولاً کمتر از ۲۴ ساعت انجام می‌شود.
                </p>
            </div>
            
            <div class="bg-white rounded-xl p-6 border border-yellow-200 hover:shadow-lg transition-all duration-300">
                <h3 class="font-bold text-sky-900 mb-3">آیا از دستیار هوشمند برای همه سؤالاتم استفاده کنم؟</h3>
                <p class="text-gray-700 text-sm leading-relaxed">
                    بله، دستیار هوشمند پیشخوانک می‌تواند شما را در انتخاب خدمت مناسب راهنمایی کند، سؤالات شما را پاسخ دهد و مراحل استفاده از خدمات را توضیح دهد.
                </p>
            </div>
        </div>
    </section>

         <!-- Customer Reviews Section -->
     <section class="flex flex-col p-6 md:p-10 mt-6 w-full bg-gray-50 border border-gray-200 rounded-[24px] animate-fadeIn">
         <div class="text-center mb-10">
             <h2 class="text-2xl md:text-3xl font-bold text-sky-900 mb-4">نظرات کاربران</h2>
             <p class="text-gray-700 max-w-2xl mx-auto text-base md:text-lg leading-relaxed">
                 تجربه کاربران ما از استفاده از خدمات پیشخوانک
             </p>
         </div>
         
         <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
             <div class="bg-white p-6 rounded-lg border border-gray-100 hover:shadow-md transition-all duration-300">
                 <div class="flex items-center mb-4">
                     <div class="w-12 h-12 bg-sky-500 rounded-full flex items-center justify-center ml-3">
                         <span class="text-white font-bold">ا.م</span>
                     </div>
                     <div>
                         <h4 class="font-bold text-sky-900">احمد محمدی</h4>
                         <div class="flex text-yellow-400">
                             ★★★★★
                         </div>
                     </div>
                 </div>
                 <p class="text-gray-600 text-sm leading-relaxed">
                     "خیلی راحت و سریع بود. استعلام خلافی خودرومو تو کمتر از ۳۰ ثانیه گرفتم. واقعاً خدمات عالیه."
                 </p>
             </div>
             
             <div class="bg-white p-6 rounded-lg border border-gray-100 hover:shadow-md transition-all duration-300">
                 <div class="flex items-center mb-4">
                     <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center ml-3">
                         <span class="text-white font-bold">ز.ا</span>
                     </div>
                     <div>
                         <h4 class="font-bold text-sky-900">زهرا احمدی</h4>
                         <div class="flex text-yellow-400">
                             ★★★★★
                         </div>
                     </div>
                 </div>
                 <p class="text-gray-600 text-sm leading-relaxed">
                     "دستیار هوشمند خیلی کمک کرده توی پیدا کردن خدمت مورد نیازم. رابط کاربری هم خیلی ساده‌ست."
                 </p>
             </div>
             
             <div class="bg-white p-6 rounded-lg border border-gray-100 hover:shadow-md transition-all duration-300">
                 <div class="flex items-center mb-4">
                     <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center ml-3">
                         <span class="text-white font-bold">م.ر</span>
                     </div>
                     <div>
                         <h4 class="font-bold text-sky-900">محسن رضایی</h4>
                         <div class="flex text-yellow-400">
                             ★★★★★
                         </div>
                     </div>
                 </div>
                 <p class="text-gray-600 text-sm leading-relaxed">
                     "قیمت‌ها منصفانه هست و خدماتش هم دقیق. مخصوصاً برای محاسبه شبا خیلی مفیده."
                 </p>
             </div>
         </div>
     </section>

     <!-- Partners Section -->
     <section class="flex flex-col p-6 md:p-10 mt-6 w-full bg-white border border-gray-200 rounded-[24px] animate-fadeIn">
         <div class="text-center mb-10">
             <h2 class="text-2xl md:text-3xl font-bold text-sky-900 mb-4">شرکای ما</h2>
             <p class="text-gray-700 max-w-2xl mx-auto text-base md:text-lg leading-relaxed">
                 پیشخوانک با مراجع معتبر و سازمان‌های رسمی در ارتباط است
             </p>
         </div>
         
         <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
             <div class="text-center p-6 bg-gray-50 rounded-lg border border-gray-100 hover:shadow-md transition-all duration-300">
                 <div class="w-16 h-16 bg-gray-200 rounded-lg mx-auto mb-4 flex items-center justify-center">
                     <span class="text-gray-500 text-xs font-bold">بانک مرکزی</span>
                 </div>
                 <p class="text-gray-600 text-sm">بانک مرکزی ایران</p>
             </div>
             
             <div class="text-center p-6 bg-gray-50 rounded-lg border border-gray-100 hover:shadow-md transition-all duration-300">
                 <div class="w-16 h-16 bg-gray-200 rounded-lg mx-auto mb-4 flex items-center justify-center">
                     <span class="text-gray-500 text-xs font-bold">پلیس راهور</span>
                 </div>
                 <p class="text-gray-600 text-sm">پلیس راهور ناجا</p>
             </div>
             
             <div class="text-center p-6 bg-gray-50 rounded-lg border border-gray-100 hover:shadow-md transition-all duration-300">
                 <div class="w-16 h-16 bg-gray-200 rounded-lg mx-auto mb-4 flex items-center justify-center">
                     <span class="text-gray-500 text-xs font-bold">ثبت احوال</span>
                 </div>
                 <p class="text-gray-600 text-sm">سازمان ثبت احوال</p>
             </div>
             
             <div class="text-center p-6 bg-gray-50 rounded-lg border border-gray-100 hover:shadow-md transition-all duration-300">
                 <div class="w-16 h-16 bg-gray-200 rounded-lg mx-auto mb-4 flex items-center justify-center">
                     <span class="text-gray-500 text-xs font-bold">جی‌بیت</span>
                 </div>
                 <p class="text-gray-600 text-sm">ارائه‌دهنده خدمات</p>
             </div>
         </div>
     </section>

     <!-- Info Section -->
     <section class="flex flex-col p-6 md:p-10 mt-6 w-full bg-zinc-50 border border-zinc-200 rounded-[24px] animate-fadeIn">
         <article class="w-full">
             <h2 class="text-2xl md:text-3xl font-bold text-sky-900 mb-6 text-center">
                 درباره <span class="text-yellow-500">پیشخوانک</span>
             </h2>
             <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                 <div>
                     <p class="text-gray-700 text-base md:text-lg leading-relaxed mb-6">
                         پیشخوانک یک پلتفرم جامع و هوشمند برای ارائه خدمات الکترونیک دولتی و بانکی است که با هدف ساده‌سازی و تسریع فرآیندهای اداری طراحی شده است.
                     </p>
                     <p class="text-gray-700 text-base leading-relaxed mb-6">
                         ما تلاش می‌کنیم تا با استفاده از جدیدترین تکنولوژی‌ها و بهترین رویه‌های امنیتی، خدماتی سریع، قابل اعتماد و در دسترس ارائه دهیم.
                     </p>
                     <div class="space-y-3">
                         <div class="flex items-center">
                             <svg class="w-5 h-5 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                             </svg>
                             <span class="text-gray-700">ارتباط مستقیم با منابع رسمی</span>
                         </div>
                         <div class="flex items-center">
                             <svg class="w-5 h-5 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                             </svg>
                             <span class="text-gray-700">پشتیبانی ۲۴ ساعته و هفت روز هفته</span>
                         </div>
                         <div class="flex items-center">
                             <svg class="w-5 h-5 text-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                             </svg>
                             <span class="text-gray-700">حفاظت کامل از اطلاعات شخصی</span>
                         </div>
                     </div>
                 </div>
                 <div class="text-center">
                     <div class="w-full h-64 bg-sky-100 rounded-lg flex items-center justify-center border border-sky-200">
                         <div class="text-center">
                             <div class="w-20 h-20 bg-sky-500 rounded-full mx-auto mb-4 flex items-center justify-center">
                                 <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                 </svg>
                             </div>
                             <h3 class="text-xl font-bold text-sky-900 mb-2">پیشخوانک</h3>
                             <p class="text-gray-600">میز خدمت هوشمند شما</p>
                         </div>
                     </div>
                 </div>
            </div>
        </article>
    </section>
@endsection

@push('head')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@push('scripts')
@vite(['resources/js/enhanced-ai-chat.js'])
@endpush
