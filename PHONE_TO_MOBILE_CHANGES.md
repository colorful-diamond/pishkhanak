# Phone Number to Mobile Field Changes

## Summary of Changes

✅ **Completed Changes:**

### 1. User Model Changes
- **Removed**: `phone_number` from fillable fields
- **Kept**: `mobile` field (already existed)
- **Field Type**: `mobile` - stores Iranian mobile numbers in format `09xxxxxxxxx`

### 2. UserResource (Filament) Updates
- **Form Field**: Changed `phone_number` input to `mobile` with Persian label
- **Table Column**: Updated to display mobile with copy functionality
- **Validation**: Added proper mobile validation

### 3. Controllers Updated
- **UserDashboardController**: Changed validation from `phone_number` to `mobile`
- **ServicePaymentService**: Updated session key from `guest_phone_number` to `guest_mobile`
- **ServicePreviewController**: Updated variable names
- **GuestPaymentController**: Updated variable names and session keys

### 4. Number Conversion System
- **New Helper Class**: `App\Helpers\NumberConverter`
  - `toEnglish()` - Convert Persian/Arabic digits to English
  - `toPersian()` - Convert English to Persian digits
  - `cleanMobile()` - Clean and format mobile numbers
  - `isValidIranianMobile()` - Validate Iranian mobile format
  - Supports card numbers, IBAN, national codes

- **Updated Helper**: `fa2en()` function now uses NumberConverter
- **New Form Request**: `UserUpdateRequest` with automatic number conversion

### 5. Validation Features
- **Automatic Conversion**: Persian/Arabic numbers automatically converted to English
- **Mobile Validation**: Ensures 11-digit format starting with `09`
- **Field Detection**: Automatically detects numeric fields for conversion

## Usage Examples

### In Controllers:
```php
use App\Helpers\NumberConverter;

// Clean mobile number
$cleanMobile = NumberConverter::cleanMobile('۰۹۱۲۳۴۵۶۷۸۹');
// Result: '09123456789'

// Validate mobile
$isValid = NumberConverter::isValidIranianMobile('09123456789');
// Result: true

// Convert any Persian numbers
$english = NumberConverter::toEnglish('۱۲۳۴۵');
// Result: '12345'
```

### In Forms:
```php
// Use UserUpdateRequest for automatic conversion
public function update(UserUpdateRequest $request, User $user)
{
    // Mobile number is automatically cleaned and validated
    $user->update($request->validated());
}
```

### In Validation Rules:
```php
'mobile' => [
    'nullable',
    'string',
    'max:15',
    function ($attribute, $value, $fail) {
        if ($value && !NumberConverter::isValidIranianMobile($value)) {
            $fail('شماره موبایل معتبر نیست.');
        }
    },
],
```

## Database Considerations

**No migration needed** - The `mobile` column already exists and `phone_number` is being removed from fillable fields only.

### Current Mobile Column:
- **Type**: `varchar(15)`
- **Nullable**: Yes
- **Unique**: Yes
- **Format**: Iranian mobile numbers (`09xxxxxxxxx`)

## Frontend Integration

### JavaScript Helper Functions
Persian to English conversion is handled in:
- `resources/js/services.js` - `faToEn()` function
- `resources/js/login.js` - Mobile validation and formatting
- Form inputs automatically convert numbers on submit

### Validation Messages
All error messages are in Persian/Farsi:
- "شماره موبایل معتبر نیست"
- "باید با 09 شروع شود و 11 رقم باشد"

## Session Key Changes

### Before:
- `guest_phone_number`
- `guest_payment_phone`

### After:
- `guest_mobile`
- `guest_payment_phone` (kept for backward compatibility)

## Benefits

1. **Consistency**: Single field name (`mobile`) throughout the application
2. **Automatic Conversion**: Persian/Arabic numbers converted seamlessly
3. **Better Validation**: Proper Iranian mobile format validation
4. **Clean Data**: Numbers stored consistently in English format
5. **User Friendly**: Users can input in Persian, stored in English

## Testing

### Test Cases to Verify:
1. **User Registration**: Mobile number with Persian digits
2. **User Profile Update**: Mobile field validation
3. **Service Forms**: Card numbers, IBAN with Persian digits
4. **Guest Payments**: Mobile number handling
5. **Admin Panel**: User management with mobile field

### Example Test Data:
```php
// Valid inputs (should work):
'۰۹۱۲۳۴۵۶۷۸۹' // Persian digits
'09123456789'     // English digits
'9123456789'      // Without leading zero

// Invalid inputs (should fail):
'08123456789'     // Wrong prefix
'091234567'       // Too short
'0912345678901'   // Too long
```

Your phone number to mobile conversion is now complete! 🎉 