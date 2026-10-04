<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('likhae:publish-announcements')->everyMinute()->withoutOverlapping();
Schedule::command('likhae:purge-expired-soft-deletes')->daily()->withoutOverlapping();
