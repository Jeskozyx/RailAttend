<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ScanReport;
use App\Models\RekapWaktuKereta;
use App\Observers\ScanReportObserver;

class HitungUlangRekap extends Command
{
    protected $signature = 'rekap:hitung-ulang';
    protected $description = 'Isi tabel rekap_waktu_kereta dari data scan_reports yang ada';

    public function handle()
    {
        $this->info('Mengosongkan tabel rekap lama...');
        RekapWaktuKereta::truncate();

        $this->info('Mulai memproses data scan report...');
        
        // Ambil semua report completed, URUTKAN dari yang terlama ke terbaru BERDASARKAN WAKTU SUBMIT
        // (Penting agar logika Gap dan Sesi berjalan urut secara kronologis)
        $reports = ScanReport::where('status', 'completed')
                    ->with('verifications')
                    ->orderBy('submitted_at', 'asc') // Ubah dari created_at ke submitted_at
                    ->cursor(); 

        $bar = $this->output->createProgressBar($reports->count());
        $observer = new ScanReportObserver();

        foreach ($reports as $report) {
            $observer->prosesRekap($report);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Selesai! Tabel rekap_waktu_kereta sudah terisi.');
    }
}