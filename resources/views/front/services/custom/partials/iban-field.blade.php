<div>
    <label for="iban" class="block text-sm font-medium text-dark-sky-500 mb-1">شماره شبا (24 رقم بدون IR)</label>
    <div class="relative">
        <input type="tel" id="iban" name="iban" placeholder="************************"
               class="w-full p-3 bg-sky-50 rounded-lg border border-dark-sky-200 text-dark-sky-600 placeholder-dark-sky-300 focus:ring-2 focus:ring-primary-normal focus:border-primary-normal text-right pr-12"
               dir="ltr" data-validate="required|iban" value="{{ old('iban') }}">
        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-dark-sky-500 font-medium">IR</span>
    </div>
    <div id="iban-error" class="text-red-500 text-sm mt-1 hidden"></div>
</div> 