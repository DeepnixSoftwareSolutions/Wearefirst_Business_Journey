<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Your Midnight Payout Engine
Schedule::command('payouts:calculate-daily')->dailyAt('02:35')->timezone('Asia/Colombo');

// Payment Reminder & Cutoff Engine for Pending Admissions
Schedule::command('mlm:check-payments')->dailyAt('06:00')->timezone('Asia/Colombo');