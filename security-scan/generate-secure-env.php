#!/usr/bin/env php
<?php

/**
 * Secure Environment Generator
 * Generates secure credentials and creates a protected .env file
 */

echo "╔═══════════════════════════════════════════════════════════════╗\n";
echo "║           SECURE ENVIRONMENT GENERATOR                        ║\n";
echo "╚═══════════════════════════════════════════════════════════════╝\n\n";

// Function to generate secure random strings
function generateSecureKey($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// Function to generate Laravel-compatible keys
function generateLaravelKey() {
    return 'base64:' . base64_encode(random_bytes(32));
}

// Generate all secure keys
$credentials = [
    'APP_KEY' => generateLaravelKey(),
    'ENC_KEY' => generateLaravelKey(),
    'DB_PASSWORD' => generateSecureKey(20),
    'REDIS_PASSWORD' => generateSecureKey(24),
    'JIBIT_PPG_WEBHOOK_SECRET' => generateSecureKey(32),
];

// Read the template
$template = file_get_contents('.env.secure.template');

// Create a new secure .env.generated file
$newEnv = $template;

// Replace placeholders with generated values
$newEnv = str_replace('CHANGE_ME_APP_KEY', $credentials['APP_KEY'], $newEnv);
$newEnv = str_replace('CHANGE_ME_ENC_KEY', $credentials['ENC_KEY'], $newEnv);
$newEnv = str_replace('CHANGE_ME_DB_PASSWORD', $credentials['DB_PASSWORD'], $newEnv);
$newEnv = str_replace('CHANGE_ME_REDIS_PASSWORD', $credentials['REDIS_PASSWORD'], $newEnv);
$newEnv = str_replace('CHANGE_ME_JIBIT_PPG_WEBHOOK_SECRET', $credentials['JIBIT_PPG_WEBHOOK_SECRET'], $newEnv);

// Save to a new file
file_put_contents('.env.generated', $newEnv);
chmod('.env.generated', 0600);

echo "✅ Generated secure credentials:\n\n";
echo "APP_KEY: " . substr($credentials['APP_KEY'], 0, 20) . "...\n";
echo "ENC_KEY: " . substr($credentials['ENC_KEY'], 0, 20) . "...\n";
echo "DB_PASSWORD: " . $credentials['DB_PASSWORD'] . "\n";
echo "REDIS_PASSWORD: " . $credentials['REDIS_PASSWORD'] . "\n";
echo "WEBHOOK_SECRET: " . substr($credentials['JIBIT_PPG_WEBHOOK_SECRET'], 0, 20) . "...\n";

echo "\n📁 Saved to: .env.generated\n";
echo "\n⚠️  IMPORTANT NEXT STEPS:\n";
echo "1. Update your database password:\n";
echo "   sudo -u postgres psql -c \"ALTER USER postgres PASSWORD '{$credentials['DB_PASSWORD']}'\"\n";
echo "\n2. Update Redis password in redis.conf:\n";
echo "   requirepass {$credentials['REDIS_PASSWORD']}\n";
echo "\n3. Replace the remaining CHANGE_ME placeholders with your actual API keys\n";
echo "\n4. When ready, backup current .env and replace it:\n";
echo "   cp .env .env.compromised\n";
echo "   cp .env.generated .env\n";
echo "   chmod 600 .env\n";

// Create a secure storage file for the generated credentials
$secureStorage = [
    'generated_at' => date('Y-m-d H:i:s'),
    'credentials' => $credentials,
    'warning' => 'Store these credentials securely. Never commit to version control.',
];

file_put_contents(
    './security-scan/generated-credentials.json',
    json_encode($secureStorage, JSON_PRETTY_PRINT)
);
chmod('./security-scan/generated-credentials.json', 0600);

echo "\n✅ Credentials also saved to: ./security-scan/generated-credentials.json\n";
echo "   (This file has restricted permissions: 600)\n";