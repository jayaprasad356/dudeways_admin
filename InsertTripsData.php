<?php

// Load Laravel's autoloader
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Trips;

$data = [
    'trip_type' => 'Road Trip',
    'location' => 'chennai',
    'from_date' => '2024-06-29',
    'to_date' => '2024-07-02',
    'trip_title' => 'Summer Trip',
    'trip_description' => 'Enjoying summer vacation',
    'user_id' => 1, // Replace with the actual user ID
    'trip_datetime' => now(),
    'trip_status' => 1,
    'trip_image' => 'SnXxFLBKoQslEfdyXjAzVvfoHHtT61C6bcJNnXhb.jpg',
];

$numberOfInserts = 50;

for ($i = 0; $i < $numberOfInserts; $i++) {
    Trips::create($data);
}

echo "Inserted ".$numberOfInserts." records into trips table.\n";
