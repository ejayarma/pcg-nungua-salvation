<?php

use App\Console\Commands\CheckSmsBalance;
use App\Console\Commands\ProcessMessageBroadcast;
use App\Console\Commands\SendBirthdayMessage;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command(SendBirthdayMessage::class)->dailyAt('6:00');

Schedule::command(ProcessMessageBroadcast::class)->everyFiveMinutes();

Schedule::command(CheckSmsBalance::class)->dailyAt('7:00');
