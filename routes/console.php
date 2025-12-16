<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('telescope:prune')->daily()->at('03:00');
if (app()->environment('production')) {
    Schedule::command('backup:run')->weeklyOn(5)->at('23:30'); // friday
}
Schedule::command(\Spatie\Health\Commands\RunHealthChecksCommand::class)->everyMinute();
Schedule::command(\Spatie\Health\Commands\DispatchQueueCheckJobsCommand::class)->everyMinute();
Schedule::command(\Spatie\Health\Commands\ScheduleCheckHeartbeatCommand::class)->everyMinute();
