<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
try {
    echo view('layouts.app')->render();
} catch (\Throwable $e) {
    echo $e->getMessage();
}
