<div class="form-group">
    <label for="violation_serial" class="block text-sm font-medium text-gray-700 mb-2">
        <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        سریال خلافی *
    </label>
    <input 
        type="text" 
        id="violation_serial" 
        name="violation_serial" 
        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
        placeholder="مثال: 47691362 یا ABC123456"
        maxlength="20"
        pattern="[a-zA-Z0-9]+"
        required
        value="{{ old('violation_serial') }}"
        autocomplete="off"
        style="direction: ltr; text-align: left;"
    >
    <div class="mt-1 text-xs text-gray-500">
        <div class="flex items-start space-x-reverse space-x-2">
            <svg class="w-3 h-3 text-blue-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <p>سریال خلافی را از برگه جریمه یا سامانه راهور ۱۲۰ دریافت کنید</p>
                <p>شامل حروف انگلیسی و اعداد، بین ۸ تا ۲۰ کاراکتر</p>
            </div>
        </div>
    </div>
    @error('violation_serial')
        <div class="mt-1 text-sm text-red-600 flex items-center">
            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            {{ $message }}
        </div>
    @enderror
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const violationSerialInput = document.getElementById('violation_serial');
    
    if (violationSerialInput) {
        // Format input as user types
        violationSerialInput.addEventListener('input', function(e) {
            // Remove any non-alphanumeric characters
            let value = e.target.value.replace(/[^a-zA-Z0-9]/g, '');
            // Limit to 20 characters
            value = value.substring(0, 20);
            e.target.value = value.toUpperCase();
        });

        // Add sample examples on focus
        violationSerialInput.addEventListener('focus', function() {
            const examples = ['47691362', '12851362', 'ABC123456', 'TR789012'];
            const randomExample = examples[Math.floor(Math.random() * examples.length)];
            if (!this.value) {
                this.placeholder = `مثال: ${randomExample}`;
            }
        });

        violationSerialInput.addEventListener('blur', function() {
            this.placeholder = 'مثال: 47691362 یا ABC123456';
        });
    }
});
</script>