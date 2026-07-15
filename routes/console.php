<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Schedule::command('backup:run')->daily()->at('02:00');
Schedule::command('backup:clean')->daily()->at('03:00');
Schedule::command('model:prune')->daily();
Schedule::command('queue:prune-failed')->daily();
Schedule::command('session:gc')->daily();
Schedule::call(function () {
    Log::info('Monthly report generation triggered.');
})->monthly()->description('Monthly report generation');
