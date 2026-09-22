<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('likhae:publish-announcements')->everyMinute()->withoutOverlapping();
