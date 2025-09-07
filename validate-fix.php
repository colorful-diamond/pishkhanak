<?php
/**
 * Quick validation script to test the field name fix
 */

// Test data
$testData = [
    'mobile' => '09153887809',
    'national_code' => '0924254742',  // Note: snake_case, not camelCase
    'provider' => 'nics24'
];

echo "🔍 Testing Field Name Fix\n";
echo "========================\n";
echo "Test data format:\n";
echo json_encode($testData, JSON_PRETTY_PRINT) . "\n";

// Test the API endpoint
$localApiUrl = 'http://127.0.0.1:9999';
$serviceSlug = 'credit-score-rating';

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $localApiUrl . '/api/services/' . $serviceSlug,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($testData),
    CURLOPT_TIMEOUT => 10,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json'
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "API Response:\n";
echo "HTTP Code: $httpCode\n";

if ($error) {
    echo "❌ Error: $error\n";
} else {
    echo "✅ Response received\n";
    if ($response) {
        $data = json_decode($response, true);
        if ($data) {
            echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
            
            if (isset($data['status']) && $data['status'] === 'success') {
                echo "✅ Field names are now correct!\n";
            } elseif (isset($data['message']) && strpos($data['message'], 'الزامی است') !== false) {
                echo "❌ Still getting 'required' error - field names might still be wrong\n";
            } else {
                echo "ℹ️  Got different response - check if this is expected\n";
            }
        }
    }
}
?>