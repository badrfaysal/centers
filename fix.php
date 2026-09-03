<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$schedules = \App\Models\SessionSchedule::where('status', 'cancelled')->where('attendance_status', 'absent')->get();
foreach($schedules as $schedule) {
    $hasLog = \App\Models\TherapySession::where('child_id', $schedule->child_id)->where('session_date', $schedule->session_date)->exists();
    if ($hasLog) {
        $schedule->update([
            'status' => 'completed',
            'attendance_status' => 'attended',
            'notes' => str_replace('?? ???????? ?? ????? ??????? ?? ??? ????????. ', '', $schedule->notes ?? '')
        ]);
        echo 'Restored schedule ID: ' . $schedule->id . PHP_EOL;
    }
}
