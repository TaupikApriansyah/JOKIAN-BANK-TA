<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('bank:sla-refresh')->everyFiveMinutes()->withoutOverlapping();
