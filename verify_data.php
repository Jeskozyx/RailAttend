<?php

use Illuminate\Contracts\Console\Kernel;
use App\Models\User;
use App\Models\Schedule;
use Carbon\Carbon;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$user = User::where('nipp', 'DEMO123')->first();
$schedule = Schedule::whereDate('date', Carbon::today())->where('no_ka', 'KA-DEMO')->first();

echo "User: " . ($user ? $user->name : "Not Found") . "\n";
echo "Schedule: " . ($schedule ? $schedule->no_ka : "Not Found") . "\n";
echo "Reports: " . ($schedule ? $schedule->scan_reports()->count() : "0") . "\n";
