<?php

namespace App\Observers;

use App\Models\ScanReport;
use App\Models\RekapWaktuKereta;
use Carbon\Carbon;

class ScanReportObserver
{
    /**
     * Handle the ScanReport "updated" event.
     */
    public function updated(ScanReport $scanReport): void
    {
        // Hanya jalankan jika status berubah jadi 'completed'
        if ($scanReport->isDirty('status') && $scanReport->status === 'completed') {
            $this->prosesRekap($scanReport);
        }
    }

    /**
     * Logic perhitungan dipisah agar bisa dipanggil manual oleh Command Backfill
     */
    public function prosesRekap(ScanReport $report)
    {
        // 1. Ambil data verifikasi (detail scan gerbong)
        $verifs = $report->verifications->sortBy('verified_at');

        if ($verifs->isEmpty()) return; // Skip jika kosong

        // 2. Tentukan Waktu Awal & Akhir Real
        // Gunakan Min dan Max untuk memastikan urutan benar
        $start = Carbon::parse($verifs->min('verified_at'));
        $end = Carbon::parse($verifs->max('verified_at'));
        
        // Pastikan durasi selalu positif (absolute difference)
        $durasi = $start->diffInSeconds($end); 

        // 3. Ambil Data Rekap TERAKHIR user ini di jadwal yang sama
        $lastRekap = RekapWaktuKereta::where('user_id', $report->user_id)
                        ->where('schedule_id', $report->schedule_id)
                        ->orderBy('waktu_akhir', 'desc') // Ambil yang paling baru
                        ->first();

        // 4. Logic Reset Sesi (7 Jam / 25200 Detik)
        $gap = 0;
        $sesiKe = 1;
        $rondeKe = 1;

        if ($lastRekap) {
            // Hitung jarak dari SELESAI putaran sebelumnya ke MULAI putaran ini
            $lastEnd = Carbon::parse($lastRekap->waktu_akhir);
            
            // Jika waktu mundur (data tidak urut), gap dianggap 0
            if ($start->lessThan($lastEnd)) {
                $gap = 0; 
            } else {
                $gap = $lastEnd->diffInSeconds($start);
            }

            if ($gap > 25200) { // Jika istirahat > 7 Jam
                $sesiKe = $lastRekap->sesi_ke + 1; // Ganti Sesi
                $gap = 0; // Reset gap jadi 0
                $rondeKe = 1; // Reset ronde
            } else {
                $sesiKe = $lastRekap->sesi_ke; // Lanjut Sesi
                $rondeKe = $lastRekap->ronde_ke + 1; // Lanjut Ronde
            }
        }

        // 5. Tentukan Status Warna
        $status = 'normal';
        if ($durasi > 2700) $status = 'danger';       // > 45 Menit
        elseif ($durasi > 1800) $status = 'warning';  // > 30 Menit

        // 6. SIMPAN KE TABEL rekap_waktu_kereta
        RekapWaktuKereta::updateOrCreate(
            ['scan_report_id' => $report->id], // Kunci unik
            [
                'user_id' => $report->user_id,
                'schedule_id' => $report->schedule_id,
                'tanggal' => $start->format('Y-m-d'),
                'waktu_awal' => $start,
                'waktu_akhir' => $end,
                'durasi_detik' => $durasi,
                'jarak_waktu_detik' => $gap,
                'sesi_ke' => $sesiKe,
                'ronde_ke' => $rondeKe,
                'status' => $status
            ]
        );
    }
}