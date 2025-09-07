@extends('front.layouts.app')

@section('content')
<div class="py-10 px-4 md:px-0 flex justify-center items-center">
    <div class="w-full max-w-[1032px] p-6 md:p-10 bg-white rounded-[32px] flex flex-col justify-start items-center gap-6 md:gap-10 overflow-hidden shadow-lg">
        {{-- SVG Image Section --}}
        
        <!-- Hero Section -->
        <div class="flex flex-col md:flex-row items-center gap-5 md:gap-7">
            <div class="w-full md:w-1/2 text-right">
                <h2 class="text-3xl font-bold text-dark-sky-600 sm:text-4xl">
                    درباره پیشخوانک
                </h2>
                <p class="mt-4 text-lg text-dark-sky-500">
                    ما در پیشخوانک، با هدف ساده‌سازی و تسریع دسترسی به خدمات آنلاین، گرد هم آمده‌ایم تا تجربه‌ای نوین و مطمئن را برای شما به ارمغان آوریم. پلتفرم ما، یک پنجره واحد برای انجام امور روزمره شماست.
                </p>
            </div>
            <div class="w-full md:w-1/2">
                <!-- SVG Illustration for About Us -->
                <img src="{{ asset('assets/images/about-us.webp') }}" alt="About Us" class="w-full h-auto">
            </div>
        </div>

        <!-- Mission and Vision Section -->
        <div class="mt-20 grid grid-cols-1 md:grid-cols-2 gap-16 text-right">
            <div>
                <h3 class="text-2xl font-bold text-dark-sky-600">ماموریت ما</h3>
                <p class="mt-4 text-dark-sky-500">
                    ماموریت ما در پیشخوانک، ارائه دسترسی آسان، امن و یکپارچه به طیف گسترده‌ای از خدمات دولتی و خصوصی است. ما می‌کوشیم با استفاده از فناوری‌های نوین، فرآیندهای پیچیده و زمان‌بر را به تجربه‌ای ساده و لذت‌بخش برای تمام کاربران تبدیل کنیم.
                </p>
            </div>
            <div>
                <h3 class="text-2xl font-bold text-dark-sky-600">چشم‌انداز ما</h3>
                <p class="mt-4 text-dark-sky-500">
                    چشم‌انداز ما، تبدیل شدن به معتبرترین و جامع‌ترین دستیار آنلاین برای شهروندان ایرانی است؛ پلتفرمی که نه تنها نیازهای روزمره را برطرف می‌کند، بلکه با نوآوری مداوم، کیفیت زندگی دیجیتال کاربران خود را ارتقا می‌بخشد.
                </p>
            </div>
        </div>

        <!-- Divider -->
        <div class="w-full border-t border-gray-200 my-16"></div>

        <!-- Our Services Section -->
        <div class="w-full text-right">
            <h2 class="text-3xl font-bold text-dark-sky-600 text-center">خدمات ما در یک نگاه</h2>
            <p class="mt-4 text-lg text-dark-sky-500 max-w-3xl mx-auto text-center">
                پیشخوانک دسترسی شما را به مجموعه‌ای کامل از خدمات ضروری فراهم می‌کند تا بتوانید امور خود را با سرعت و راحتی بیشتری مدیریت کنید.
            </p>
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Service 1: Financial -->
                <div class="bg-sky-50 p-6 rounded-2xl">
                    <div class="flex items-center justify-center h-12 w-12 rounded-full bg-sky-100 text-sky-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <h4 class="mt-4 text-lg font-bold">خدمات مالی و بانکی</h4>
                    <p class="mt-2 text-base text-gray-500">پرداخت قبوض، عوارض و انجام تراکنش‌های بانکی به سادگی و در کمترین زمان ممکن.</p>
                </div>
                <!-- Service 2: Automotive -->
                <div class="bg-sky-50 p-6 rounded-2xl">
                    <div class="flex items-center justify-center h-12 w-12 rounded-full bg-sky-100 text-sky-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4z"/></svg>
                    </div>
                    <h4 class="mt-4 text-lg font-bold">استعلامات خودرو</h4>
                    <p class="mt-2 text-base text-gray-500">استعلام خلافی، نمره منفی گواهینامه، وضعیت پلاک و سایر امور مربوط به خودروی شما.</p>
                </div>
                <!-- Service 3: Utilities -->
                <div class="bg-sky-50 p-6 rounded-2xl">
                    <div class="flex items-center justify-center h-12 w-12 rounded-full bg-sky-100 text-sky-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <h4 class="mt-4 text-lg font-bold">خدمات شهری و رفاهی</h4>
                    <p class="mt-2 text-base text-gray-500">دریافت سوابق بیمه تامین اجتماعی، فیش حقوقی و مدیریت خدمات مربوط به شهروندی.</p>
                </div>
            </div>
        </div>
        
        <!-- Why Pishkhanak Section -->
        <div class="w-full text-right mt-16">
            <h2 class="text-3xl font-bold text-dark-sky-600 text-center">چرا پیشخوانک؟</h2>
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Benefit 1: 24/7 Access -->
                <div class="text-center">
                    <div class="flex items-center justify-center h-16 w-16 mx-auto rounded-full bg-primary-normal text-white">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h4 class="mt-4 text-lg font-bold">دسترسی شبانه‌روزی</h4>
                    <p class="mt-2 text-base text-gray-500">خدمات ما در هر ساعت از شبانه‌روز و از هر کجا که باشید در دسترس شماست.</p>
                </div>
                <!-- Benefit 2: Time & Cost Saving -->
                <div class="text-center">
                    <div class="flex items-center justify-center h-16 w-16 mx-auto rounded-full bg-primary-normal text-white">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <h4 class="mt-4 text-lg font-bold">صرفه‌جویی در زمان و هزینه</h4>
                    <p class="mt-2 text-base text-gray-500">بدون نیاز به مراجعه حضوری، در وقت و هزینه‌های خود صرفه‌جویی کنید.</p>
                </div>
                <!-- Benefit 3: Security -->
                <div class="text-center">
                    <div class="flex items-center justify-center h-16 w-16 mx-auto rounded-full bg-primary-normal text-white">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 20.944a11.955 11.955 0 0118 0 12.02 12.02 0 00-2.382-8.984z"/></svg>
                    </div>
                    <h4 class="mt-4 text-lg font-bold">امنیت و اطمینان</h4>
                    <p class="mt-2 text-base text-gray-500">ما با استفاده از پروتکل‌های امنیتی پیشرفته، از اطلاعات شما محافظت می‌کنیم.</p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection 