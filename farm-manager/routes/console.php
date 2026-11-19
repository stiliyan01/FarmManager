<?php

use Illuminate\Support\Facades\Schedule;

// Всеки ден в 7:00 – напомняне за ваксинациите.
// Локално графикът се пуска с php artisan schedule:work.
Schedule::command('reminders:vaccinations')->dailyAt('07:00');
