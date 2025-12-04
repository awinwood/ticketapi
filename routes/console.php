<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('tickets:generate')->everyMinute()->withoutOverlapping();

Schedule::command('tickets:process')->everyFiveMinutes()->withoutOverlapping();
