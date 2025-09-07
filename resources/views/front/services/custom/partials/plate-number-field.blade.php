<div id="plate-number-field">
    <label for="plate_number" class="block text-sm font-medium text-dark-sky-500 mb-1">شماره پلاک (9 رقم)</label>
    <input type="tel" id="plate_number" name="plate_number" placeholder="123456789"
           class="w-full p-3 ltr bg-sky-50 rounded-lg border border-dark-sky-200 text-dark-sky-600 placeholder-dark-sky-300 focus:ring-2 focus:ring-primary-normal focus:border-primary-normal text-right"
           dir="ltr" data-validate="required|digits:9" value="{{ old('plate_number') }}">
    <div id="plate_number-error" class="text-red-500 text-sm mt-1 hidden"></div>
</div> 