<?php

namespace App\Services;

use Carbon\Carbon;

class TimeGapService
{
    /**
     * Batas waktu toleransi antar ronde dalam satu sesi (7 Jam).
     * Jika jarak antar ronde (Selesai -> Mulai) lebih dari ini,
     * maka dianggap sudah masuk SESI BARU (atau hari kerja baru).
     */
    const MAX_SESSION_GAP_SECONDS = 25200; // 7 Hours * 3600

    /**
     * Hitung jarak waktu antara dua timestamp dan tentukan apakah reset sesi diperlukan.
     * 
     * @param Carbon|string $currentStart Waktu mulai ronde saat ini
     * @param Carbon|string $previousEnd Waktu selesai ronde sebelumnya
     * @return array ['gap' => int, 'is_new_session' => bool]
     */
    public static function calculateSessionGap($currentStart, $previousEnd)
    {
        $currentStart = Carbon::parse($currentStart);
        $previousEnd = Carbon::parse($previousEnd);

        // Jika waktu mundur (Anomali: Current Start < Previous End), gap 0
        // Atau bisa juga kita anggap 0 tapi tidak reset session (masih satu rangkaian aneh)
        if ($currentStart->lessThan($previousEnd)) {
            return [
                'gap' => 0,
                'is_new_session' => false
            ];
        }

        $gap = $previousEnd->diffInSeconds($currentStart);

        // Cek Safety Rule
        if ($gap > self::MAX_SESSION_GAP_SECONDS) {
            return [
                'gap' => 0, // Gap direset agar tidak merusak rata-rata
                'is_new_session' => true
            ];
        }

        return [
            'gap' => $gap,
            'is_new_session' => false
        ];
    }
}
