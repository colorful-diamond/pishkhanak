<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;
use Illuminate\Support\Facades\DB;

if ($argc < 3) {
    echo "Usage: php update-service-content.php <service_id> <content_file_path>\n";
    exit(1);
}

$serviceId = $argv[1];
$contentFilePath = $argv[2];

if (!file_exists($contentFilePath)) {
    echo "Content file not found: $contentFilePath\n";
    exit(1);
}

$service = Service::find($serviceId);

if (!$service) {
    echo "Service with ID $serviceId not found.\n";
    exit(1);
}

$content = file_get_contents($contentFilePath);

$service->content = $content;
$service->save();

echo "Service ID: $serviceId updated successfully.\n"; 