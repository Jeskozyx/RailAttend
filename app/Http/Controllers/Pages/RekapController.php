<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\RekapWaktuKereta; // Pakai Model Baru
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
// use Maatwebsite\Excel\Facades\Excel; // Removed
use App\Exports\RekapWaktuExport;

class RekapController extends Controller
{
    public function export(Request $request)
    {
        $dateStr = date('Y-m-d_H-i');
        // Instansiasi class export dan panggil method download
        return (new RekapWaktuExport($request))->download("rekap_waktu_{$dateStr}.xlsx");
    }

    public function index(Request $request)
    {
        // 1. Data Pendukung Filter
        $users = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'Admin'))->orderBy('name')->get();
        $roles = Role::where('name', '!=', 'Admin')->orderBy('name')->get();
        $schedules = Schedule::with('train')->orderBy('no_ka')->get();

        // 2. Query ke Tabel Baru (Cepat!)
        $query = RekapWaktuKereta::with(['user.roles', 'schedule.train', 'scan_report.verifications.rangkaian']);

        // Filter-filter
        if ($request->user_id) $query->where('user_id', $request->user_id);
        if ($request->schedule_id) $query->where('schedule_id', $request->schedule_id);
        
        // Default Date Range: Today if not specified
        $dateFrom = $request->date_from ?? now()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');
        
        $query->whereDate('tanggal', '>=', $dateFrom);
        $query->whereDate('tanggal', '<=', $dateTo);
        
        if ($request->role_id) {
            $query->whereHas('user.roles', function($q) use ($request) {
                $q->where('id', $request->role_id);
            });
        }

        // Urutkan (Terlama di atas agar Putaran 1 duluan)
        $rekapModels = $query->orderBy('waktu_awal', 'asc')->get();

        // 3. Transform Data untuk View (Kembalikan ke format array yang diharapkan View)
        $rekap = $rekapModels->map(function ($item) {
            // Build details array for child rows
            $details = [];
            if ($item->scan_report && $item->scan_report->verifications) {
                foreach ($item->scan_report->verifications as $verification) {
                    $details[] = [
                        'nama_gerbong' => $verification->rangkaian->name ?? '-',
                        'waktu_scan' => \Carbon\Carbon::parse($verification->verified_at)->format('H:i:s'),
                    ];
                }
            }

            // User & Schedule Info
            $user = $item->user;
            $roleName = $user?->roles->first()?->name ?? '-';
            $schedule = $item->schedule;
            $trainName = $schedule?->train?->name ?? '-';
            
            // Session Color Logic (Based on Sesi Ke)
            // Pastikan warna kontras antar sesi
            $sessionColors = [
                'border-blue-500',    'border-red-500', 
                'border-green-500',   'border-orange-500', 
                'border-purple-500',  'border-yellow-500',
                'border-pink-500',    'border-cyan-500', 
                'border-indigo-500',  'border-teal-500'
            ];
            // Gunakan modulo agar warna berputar jika sesi > jumlah warna
            $colorIndex = ($item->sesi_ke - 1) % count($sessionColors);

            return [
                'id' => $item->id,
                'ronde' => $item->ronde_ke,
                'tanggal' => \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y'),
                'waktu_awal' => \Carbon\Carbon::parse($item->waktu_awal)->format('H:i:s'),
                'waktu_akhir' => \Carbon\Carbon::parse($item->waktu_akhir)->format('H:i:s'),
                'durasi_detik' => $item->durasi_detik,
                'durasi_formatted' => $this->formatDuration($item->durasi_detik),
                'jarak_waktu_detik' => $item->jarak_waktu_detik,
                'jarak_waktu_formatted' => $this->formatDuration($item->jarak_waktu_detik),
                'status' => $item->status,
                'session_color' => $sessionColors[$colorIndex],
                'details' => $details,
                
                // User Info
                'user_name' => $user?->name ?? '-',
                'user_id' => $user?->id,
                'user_nipp' => $user?->nipp ?? '-',
                'user_jabatan' => $roleName,
                'avatar_url' => $user?->avatar_url,
                
                // Train Info
                'train_name' => $trainName,
                'no_ka' => $schedule?->no_ka ?? '-',
                'schedule_id' => $item->schedule_id,
                'schedule_info' => $schedule ? ($schedule->no_ka . ' (' . $schedule->origin . ' → ' . $schedule->destination . ')') : '-',
                'submitted_at' => \Carbon\Carbon::parse($item->waktu_akhir)->format('H:i:s')
            ];
        });

        // 4. Hitung Summary (Statistik)
        $userAverages = [];
        $totalUserAverages = 0;
        $validUsersCount = 0;

        // Group by User ID first
        $groupedByUser = $rekap->groupBy('user_id');

        foreach ($groupedByUser as $uid => $items) {
            $totalDurasi = $items->sum('total_durasi_gap_valid' ?? 'durasi_detik'); // Wait, logic recap is different
            // Let's use the new robust logic based on the models
            
            // Filter items where jarak_waktu > 0 (valid gaps)
            $validGaps = $items->where('jarak_waktu_detik', '>', 0);
            $countValid = $validGaps->count();
            $sumValid = $validGaps->sum('jarak_waktu_detik');
            
            $avgSeconds = $countValid > 0 ? round($sumValid / $countValid) : 0;
            
            // Build Schedule Summary
            $schedulesStats = [];
            $groupedBySchedule = $items->groupBy('schedule_id');
            
            foreach ($groupedBySchedule as $schId => $schItems) {
                $schValid = $schItems->where('jarak_waktu_detik', '>', 0);
                $schCount = $schValid->count();
                $schSum = $schValid->sum('jarak_waktu_detik');
                $schAvg = $schCount > 0 ? round($schSum / $schCount) : 0;
                
                // Tooltip text per date
                $datesText = $schItems->groupBy('tanggal')->map(function($dateItems, $date) {
                     $dValid = $dateItems->where('jarak_waktu_detik', '>', 0);
                     $dCount = $dValid->count();
                     $dSum = $dValid->sum('jarak_waktu_detik');
                     $dAvg = $dCount > 0 ? round($dSum / $dCount) : 0;
                     $dFormatted = $this->formatDuration($dAvg);
                     return "$date: $dFormatted ({$dCount} Putaran)";
                })->values()->implode(' &#013; ');

                $firstItem = $schItems->first();
                $schName = $firstItem['train_name'] . ' (' . $firstItem['no_ka'] . ')';

                $schedulesStats[$schName] = [
                    'rerata_formatted' => $this->formatDuration($schAvg),
                    'jumlah_ronde' => $schItems->count(), // Total rounds including first of session
                    'tooltip_text' => $datesText
                ];
            }

            $firstItem = $items->first();
            $userAverages[$uid] = [
                'user_name' => $firstItem['user_name'],
                'user_jabatan' => $firstItem['user_jabatan'],
                'avatar_url' => $firstItem['avatar_url'],
                'rerata_formatted' => $this->formatDuration($avgSeconds),
                'rerata_detik' => $avgSeconds,
                'jumlah_ronde' => $items->count(),
                'jumlah_sesi' => $items->where('jarak_waktu_detik', 0)->count(),
                'schedules' => $schedulesStats
            ];

            if ($avgSeconds > 0) {
                $totalUserAverages += $avgSeconds;
                $validUsersCount++;
            }
        }

        $rerataJarakWaktu = $validUsersCount > 0 ? round($totalUserAverages / $validUsersCount) : 0;
        $rerataJarakWaktuFormatted = $this->formatDuration($rerataJarakWaktu);

        // 5. Return View
        return view('pages.rekap.index', [
            'rekap' => $rekap, // Now strictly an array/collection of arrays
            'users' => $users,
            'roles' => $roles,
            'schedules' => $schedules,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'userId' => $request->user_id,
            'scheduleId' => $request->schedule_id,
            'roleId' => $request->role_id,
            'userAverages' => $userAverages,
            'rerataJarakWaktu' => $rerataJarakWaktu,
            'rerataJarakWaktuFormatted' => $rerataJarakWaktuFormatted
        ]);
    }

    /**
     * Helper to format seconds into human readable string
     */
    private function formatDuration($seconds)
    {
        if ($seconds < 60) {
            return $seconds . ' Detik';
        } elseif ($seconds < 3600) {
            $menit = floor($seconds / 60);
            $detik = $seconds % 60;
            return $detik > 0 
                ? $menit . ' Menit ' . $detik . ' Detik'
                : $menit . ' Menit';
        } else {
            $jam = floor($seconds / 3600);
            $sisaDetik = $seconds % 3600;
            $menit = floor($sisaDetik / 60);
            $detik = $sisaDetik % 60;
            $result = $jam . ' Jam';
            if ($menit > 0) {
                $result .= ' ' . $menit . ' Menit';
            }
            if ($detik > 0) {
                $result .= ' ' . $detik . ' Detik';
            }
            return $result;
        }
    }
}