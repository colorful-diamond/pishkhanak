# Dynamic Service Controller System

This document explains how to use and extend the dynamic service controller system in the Pishkhanak project.

## Overview

The dynamic service controller system allows you to create dedicated controllers for different services that handle form submissions dynamically. When a user submits data to a service page, the system automatically finds and calls the appropriate controller based on the service slug. Results are stored in the database with unique hash IDs and displayed on separate result pages.

## How It Works

1. **Service Detection**: When a user submits a form to `/services/{slug1}/{slug2?}`, the system finds the corresponding service in the database.

2. **Controller Resolution**: The system uses the `ServiceControllerFactory` to find the appropriate controller for the service slug.

3. **Dynamic Execution**: If a controller is found, its `handle()` method is called with the request and service data.

4. **Result Storage**: The result is stored in the database with a unique 16-character hash ID.

5. **Result Display**: User is redirected to `/services/result/{hash}` to view the result.

6. **Dynamic Show**: The system calls the controller's `show()` method to display the result.

7. **Error Handling**: If no controller is found, an error message is returned to the user.

## File Structure

```
app/Http/Controllers/Services/
├── BaseServiceController.php          # Interface for all service controllers
├── ServiceControllerFactory.php       # Factory for resolving controllers
├── CardIbanController.php             # Example: Card to IBAN conversion
├── CardAccountController.php          # Example: Card to Account conversion
├── IbanAccountController.php          # Example: IBAN to Account conversion
├── IbanValidatorController.php        # Example: IBAN validation
└── [YourServiceController].php        # Your custom service controllers

app/Models/
├── ServiceResult.php                  # Model for storing service results

database/migrations/
└── create_service_results_table.php   # Migration for service results

resources/views/front/services/
├── result.blade.php                   # Result display page
└── custom/partials/
    └── results-section.blade.php      # Reusable results component
```

## Creating a New Service Controller

### Step 1: Create the Controller Class

Create a new controller in `app/Http/Controllers/Services/` directory:

```php
<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\BaseServiceController;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class YourServiceController extends Controller implements BaseServiceController
{
    /**
     * Handle the service submission
     *
     * @param Request $request
     * @param Service $service
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Service $service)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'field_name' => 'required|string|max:255',
            // Add more validation rules as needed
        ], [
            'field_name.required' => 'فیلد الزامی است.',
            // Add Persian error messages
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Process the request
        $result = $this->processService($request->all());
        
        // Store the result in database
        $serviceResult = ServiceResult::create([
            'service_id' => $service->id,
            'input_data' => $request->all(),
            'output_data' => $result,
            'status' => 'success',
            'processed_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Redirect to result page
        return redirect()->route('services.result', [
            'id' => $serviceResult->result_hash
        ])->with([
            'success' => true,
            'message' => 'عملیات با موفقیت انجام شد.',
        ]);
    }

    /**
     * Show the service result
     *
     * @param string $resultId
     * @param Service $service
     * @return \Illuminate\Http\Response
     */
    public function show(string $resultId, Service $service)
    {
        // Find the result by hash
        $result = ServiceResult::where('result_hash', $resultId)
            ->where('service_id', $service->id)
            ->where('status', 'success')
            ->firstOrFail();

        // Check if result is expired
        if ($result->isExpired()) {
            abort(410, 'این نتیجه منقضی شده است.');
        }

        $formattedResult = $result->getFormattedResult();

        return view('front.services.result', [
            'service' => $service,
            'result' => $formattedResult,
            'inputData' => $result->input_data,
            'resultId' => $resultId,
        ]);
    }

    /**
     * Process the service logic
     *
     * @param array $data
     * @return array
     */
    private function processService(array $data): array
    {
        // Your service logic here
        return [
            'input' => $data['field_name'],
            'output' => 'Processed result',
            'processed_date' => now()->format('Y/m/d H:i:s'),
        ];
    }
}
```

### Step 2: Register the Controller

Add your controller to the mapping in `ServiceControllerFactory.php`:

```php
private static $serviceMapping = [
    // ... existing mappings
    'your-service-slug' => YourServiceController::class,
];
```

### Step 3: Create the View

Create a view for your service in `resources/views/front/services/custom/`:

```blade
@extends('front.services.custom.upper-base')

@section('service_title', 'عنوان سرویس شما')

@section('submit_text', 'ارسال')

@section('form_fields')
    <div class="space-y-4">
        <div>
            <label for="field_name" class="block text-sm font-medium text-gray-700 mb-1">
                نام فیلد
            </label>
            <input 
                type="text" 
                id="field_name" 
                name="field_name" 
                value="{{ old('field_name') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-normal focus:border-transparent"
                placeholder="مقدار را وارد کنید"
            >
        </div>
    </div>
@endsection

@section('results_section')
    @include('front.services.custom.partials.results-section')
@endsection
```

## Available Service Controllers

### 1. CardIbanController
- **Slug**: `card-iban`, `card-to-sheba`
- **Purpose**: Converts card numbers to IBAN
- **Input**: `card_number` (16 digits)
- **Output**: IBAN, bank name, account type

### 2. CardAccountController
- **Slug**: `card-account`, `card-to-account`
- **Purpose**: Converts card numbers to account numbers
- **Input**: `card_number` (16 digits)
- **Output**: Account number, bank name, branch code

### 3. IbanAccountController
- **Slug**: `iban-account`, `sheba-account`
- **Purpose**: Converts IBAN to account information
- **Input**: `iban` (IR + 24 digits)
- **Output**: Account number, bank name, branch code

### 4. IbanValidatorController
- **Slug**: `iban-validator`, `sheba-validator`
- **Purpose**: Validates IBAN numbers
- **Input**: `iban` (IR + 24 digits)
- **Output**: Validation result, bank information

## Results Display

Results are displayed on dedicated pages at `/services/result/{hash}` with the following features:

- **Beautiful Design**: Modern, responsive design with clear sections
- **Input Data**: Shows the original input data submitted by the user
- **Output Data**: Displays the processed results in a structured format
- **Processing Info**: Shows processing date and result ID
- **Copy Functionality**: One-click copying of results to clipboard
- **Share Options**: Share results via native sharing or copy URL
- **Action Buttons**: Quick access to create new service or go home
- **Expiration Handling**: Results expire after 30 days for privacy

## Error Handling

### Controller Not Found
If no controller is found for a service slug, the system returns:
```php
return back()->withErrors([
    'service_error' => 'سرویس مورد نظر در حال حاضر در دسترس نیست. لطفاً با پشتیبانی تماس بگیرید.'
])->withInput();
```

### Validation Errors
Validation errors are automatically displayed in the view using Laravel's built-in error handling.

## Best Practices

1. **Naming Convention**: Use PascalCase for controller names and kebab-case for service slugs.

2. **Validation**: Always validate user input with appropriate rules and Persian error messages.

3. **Error Handling**: Provide meaningful error messages to users.

4. **Security**: Sanitize and validate all input data.

5. **Performance**: Keep service logic efficient and consider caching for expensive operations.

6. **Logging**: Log important events and errors for debugging.

## Extending the System

### Adding New Service Types

1. Create a new controller implementing `BaseServiceController`
2. Add the mapping to `ServiceControllerFactory`
3. Create the corresponding view
4. Test the implementation

### Custom Result Display

You can create custom result display components by extending the `results-section.blade.php` template or creating new ones for specific services.

### Service Aliases

You can add multiple slugs for the same controller by adding aliases to the `$serviceMapping` array:

```php
'primary-slug' => YourController::class,
'alias-slug' => YourController::class, // Same controller, different slug
```

## Testing

To test your service controller:

1. Create a service in the database with the appropriate slug
2. Navigate to the service page
3. Submit the form with test data
4. Verify you're redirected to the result page
5. Check that the result is stored in the database
6. Test the result page with the hash ID
7. Test error scenarios (invalid input, missing controller, expired results, etc.)

## Management Commands

### List Service Controllers
```bash
php artisan services:list-controllers
```

### Cleanup Expired Results
```bash
php artisan services:cleanup-results --days=30
```

## Database Schema

The `service_results` table stores:
- `service_id`: Reference to the service
- `result_hash`: Unique 16-character hash for the result
- `input_data`: JSON data of user input
- `output_data`: JSON data of processed results
- `status`: Processing status (success, failed, processing)
- `processed_at`: Timestamp of processing
- `ip_address`: User's IP address
- `user_agent`: User's browser information

## Troubleshooting

### Controller Not Found
- Check if the controller class exists in the correct namespace
- Verify the mapping in `ServiceControllerFactory`
- Check the service slug in the database

### Validation Errors Not Displaying
- Ensure the view includes the error display section
- Check that validation rules are correct
- Verify error messages are in Persian

### Results Not Showing
- Check that the `results_section` is included in the view
- Verify the session data is being set correctly
- Ensure the result array structure matches the expected format 