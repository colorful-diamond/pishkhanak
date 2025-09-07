@extends('front.layouts.app')

@section('content')
<div class="">
    <div class="container mx-auto px-4 py-3">
        <!-- Header - more compact -->
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-lg font-bold text-gray-900 mb-1">پروفایل کاربری</h1>
                    <p class="text-gray-600 text-xs">مدیریت اطلاعات شخصی و تنظیمات حساب کاربری</p>
                </div>
            </div>
        </div>

        <!-- Include Sidebar Component -->

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            @include('front.user.partials.sidebar')
            
            <!-- Main Content -->
            <div class="lg:col-span-3 col-span-4">
                <!-- Profile Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">اطلاعات شخصی</h2>

                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl">
                            <div class="flex">
                                <svg class="w-4 h-4 text-green-400 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-green-800">اطلاعات با موفقیت بروزرسانی شد</h3>
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                            <div class="flex">
                                <svg class="w-4 h-4 text-red-400 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-red-800 mb-2">خطاهای موجود:</h3>
                                    <ul class="text-sm text-red-700 space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>• {{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('app.user.profile.update') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">نام و نام خانوادگی</label>
                                <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">ایمیل</label>
                                <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">شماره تلفن</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors"
                                       placeholder="09xxxxxxxxx">
                            </div>

                            <!-- National Code -->
                            <div>
                                <label for="national_code" class="block text-sm font-medium text-gray-700 mb-2">کد ملی</label>
                                <input type="text" id="national_code" name="national_code" value="{{ old('national_code', Auth::user()->national_code) }}"
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors"
                                       placeholder="xxxxxxxxxx">
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="px-4 py-2 text-sm bg-sky-600 text-white rounded-xl hover:bg-sky-700 transition-colors">
                                بروزرسانی اطلاعات
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-4">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">تغییر رمز عبور</h2>

                    @if(session('password_success'))
                        <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-xl">
                            <div class="flex">
                                <svg class="w-4 h-4 text-green-400 mt-0.5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-green-800">رمز عبور با موفقیت تغییر یافت</h3>
                                    <p class="text-sm text-green-700">{{ session('password_success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('app.user.profile.password') }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Current Password -->
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">رمز عبور فعلی</label>
                                <input type="password" id="current_password" name="current_password" required
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                            </div>

                            <!-- New Password -->
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">رمز عبور جدید</label>
                                <input type="password" id="new_password" name="new_password" required
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                            </div>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">تکرار رمز عبور جدید</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" required
                                   class="w-full px-3 py-2 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 transition-colors">
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" 
                                    class="px-4 py-2 text-sm bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-colors">
                                تغییر رمز عبور
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Account Statistics -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                    <h2 class="text-base font-semibold text-gray-900 mb-4">آمار حساب کاربری</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-sky-50 rounded-xl p-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-sky-100 rounded-xl flex items-center justify-center ml-3">
                                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">تاریخ عضویت</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ \Verta::instance(Auth::user()->created_at)->format('Y/m/d') }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-sky-50 rounded-xl p-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-xl flex items-center justify-center ml-3">
                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">کل تراکنش‌ها</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $totalTransactions ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-sky-50 rounded-xl p-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-sky-100 rounded-xl flex items-center justify-center ml-3">
                                    <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">درخواست‌های باز</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $openTickets ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 