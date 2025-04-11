<?php

use App\Models\Assay;
use App\Models\Calendar;
use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::call(function () {

    $events = Calendar::all();
    foreach ($events as $event) {

        $today = new DateTimeImmutable('now');
        $event_final_date = new DateTimeImmutable($event->final_date);
        if ($event_final_date < $today) {

            $event->delete();
        }
    }
})->daily();


Schedule::call(function () {

    $assays = Assay::all();
    
    $today = new DateTimeImmutable('now');
    foreach ($assays as $assay) {
        
        $initial_date = new DateTimeImmutable($assay->initial_date);
        $final_date = new DateTimeImmutable($assay->final_date);

        if ($initial_date <= $today && $today <= $final_date) {
            
            $assay->update(['is_answerable', true]);
        }
    }
})->daily();