@extends('front.layouts.app')

@section('content')
<div class="py-10 px-4 md:px-0 flex justify-center">
    <div class="w-full max-w-[1032px] p-6 md:p-10 bg-white rounded-[32px] flex flex-col md:flex-row-reverse justify-start items-center gap-6 md:gap-10 overflow-hidden shadow-lg">
        {{-- SVG Image Section --}}
        <div class="w-full md:w-1/2 lg:w-2/5 self-stretch flex justify-center items-center rounded-xl p-4 md:p-6 order-last md:order-first">
            <img src="{{ asset('assets/images/contact-us.webp') }}" alt="{{ __('messages.contact.image_alt') }}" class="max-w-full h-auto max-h-[400px] md:max-h-full object-contain">
        </div>

        {{-- Form Section --}}
        <div class="w-full md:w-1/2 lg:w-3/5 self-stretch flex flex-col justify-center items-end gap-6">
            <div class="self-stretch inline-flex items-center gap-2">
                <h1 class="text-dark-sky-600 text-xl font-bold leading-loose">فرم تماس با ما</h1>
            </div>

            @if(session('success'))
                <div class="self-stretch p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg text-right font-['IRANSans']">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="self-stretch p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-right font-['IRANSans']">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="self-stretch p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-right font-['IRANSans']">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('app.contact.store') }}" method="POST" class="self-stretch flex flex-col justify-start items-end gap-4">
                @csrf

                {{-- Name Input --}}
                <div class="self-stretch flex flex-col justify-start items-end gap-2">
                    <label for="full_name" class="self-stretch text-right justify-center">
                        <span class="text-dark-sky-600 text-base font-normal leading-normal">نام و نام خانوادگی</span>
                        <span class="text-red-500 text-base font-normal leading-normal">*</span>
                    </label>
                    <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required
                           class="self-stretch p-3 bg-white rounded-lg border border-gray-300 text-right text-dark-sky-500 text-base font-normal leading-normal focus:outline-none focus:ring-2 focus:ring-primary-normal focus:border-primary-normal hover:border-gray-400 placeholder-gray-400"
                           placeholder="نام کامل خود را وارد کنید">
                </div>

                {{-- Email Input --}}
                <div class="self-stretch flex flex-col justify-start items-end gap-2">
                    <label for="email" class="self-stretch text-right justify-center">
                        <span class="text-dark-sky-600 text-base font-normal leading-normal">ایمیل</span>
                        <span class="text-red-500 text-base font-normal leading-normal">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           class="self-stretch p-3 bg-white rounded-lg border border-gray-300 text-right text-dark-sky-500 text-base font-normal leading-normal focus:outline-none focus:ring-2 focus:ring-primary-normal focus:border-primary-normal hover:border-gray-400 placeholder-gray-400"
                           placeholder="name@example.com">
                </div>

                {{-- Subject Input --}}
                <div class="self-stretch flex flex-col justify-start items-end gap-2">
                    <label for="subject" class="self-stretch text-right justify-center">
                        <span class="text-dark-sky-600 text-base font-normal leading-normal">موضوع پیام</span>
                        <span class="text-red-500 text-base font-normal leading-normal">*</span>
                    </label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required
                           class="self-stretch p-3 bg-white rounded-lg border border-gray-300 text-right text-dark-sky-500 text-base font-normal leading-normal focus:outline-none focus:ring-2 focus:ring-primary-normal focus:border-primary-normal hover:border-gray-400 placeholder-gray-400"
                           placeholder="موضوع پیام خود را بنویسید">
                </div>

                {{-- Message Textarea --}}
                <div class="self-stretch flex flex-col justify-start items-end gap-2">
                    <label for="message_text" class="self-stretch text-right justify-center">
                        <span class="text-dark-sky-600 text-base font-normal leading-normal">متن پیام</span>
                        <span class="text-red-500 text-base font-normal leading-normal">*</span>
                    </label>
                    <textarea id="message_text" name="message_text" rows="5" required
                              class="self-stretch p-3 bg-white rounded-lg border border-gray-300 text-right text-dark-sky-50045261 font-normal leading-normal focus:outline-none focus:ring-2 focus:ring-primary-normal focus:border-primary-normal hover:border-gray-400 placeholder-gray-400 min-h-[120px]"
                              placeholder="پیام خود را اینجا بنویسید">{{ old('message_text') }}</textarea>
                </div>

                {{-- Captcha Section --}}
                <div class="self-stretch flex flex-col justify-start items-end gap-2">
                    <label for="captcha" class="self-stretch text-right justify-center">
                        <span class="text-dark-sky-600 text-base font-normal capitalize leading-normal">{{ __('messages.contact.captcha') }}</span>
                        <span class="text-red-500 text-base font-normal capitalize leading-normal">*</span>
                    </label>
                    <div class="self-stretch flex items-center justify-end gap-3">
                        <input type="text" id="captcha" name="captcha" required
                               class="w-full md:w-1/2 p-3 bg-white rounded-lg border border-gray-300 text-center text-dark-sky-500 text-base font-normal leading-normal focus:outline-none focus:ring-2 focus:ring-primary-normal focus:border-primary-normal hover:border-gray-400 placeholder-gray-400"
                               placeholder="{{ __('messages.contact.captcha_placeholder') }}">
                        <img id="captcha_image" src="{{ route('captcha.image') }}" alt="{{ __('messages.contact.captcha_image_alt') }}" class="rounded-lg border border-gray-300">
                        <button type="button" id="refresh_captcha_button" title="{{ __('messages.contact.captcha_refresh_button') }}" class="p-2 text-dark-sky-500 hover:text-primary-normal">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit"
                        class="self-stretch px-4 py-3 bg-primary-normal text-white text-base font-medium leading-normal rounded-lg inline-flex justify-center items-center overflow-hidden hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary-normal focus:ring-offset-2 active:bg-primary-darker transition-colors duration-150">
                    ارسال پیام
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Additional fine-tuning if needed, but Tailwind classes should cover most aspects */
    #captcha_image {
        width: 150px; /* Match canvas width */
        height: 50px; /* Match canvas height */
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var refreshButton = document.getElementById('refresh_captcha_button');
        var captchaImage = document.getElementById('captcha_image');

        if (refreshButton && captchaImage) {
            refreshButton.addEventListener('click', function () {
                captchaImage.src = '{{ route("captcha.image") }}?' + new Date().getTime();
            });
        }
    });
</script>
@endpush
