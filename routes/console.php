<?php

use App\Models\Agenda;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('agenda:auto-draft-services', function () {
    $count = Agenda::syncUpcomingAgendasToDraftServices();
    $this->info("Auto-draft selesai: {$count} agenda (< 1 minggu) dikonversi menjadi draft layanan.");
})->purpose('Auto-draft agenda yang mulai dalam < 1 minggu ke tabel Layanan beserta notifikasi review');

Schedule::command('agenda:auto-draft-services')->dailyAt('07:00');
