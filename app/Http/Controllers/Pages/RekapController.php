<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\ScanReport;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class RekapController extends Controller
{
    /**
     * Display rekap waktu page with expandable table
     */
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

    /**
     * Display rekap waktu page with expandable table
     */
    public function index(Request $request)
    {
        // Get all non-admin users for filter dropdown
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'Admin');
        })->orderBy('name')->get();

        // Get all schedules for filter dropdown (with train relation)
        $schedules = Schedule::with('train')->orderBy('date', 'desc')->orderBy('departure_time')->get();

        // Get all non-admin roles for filter dropdown
        $roles = Role::where('name', '!=', 'Admin')->orderBy('name')->get();

        // Get filter parameters (date range)
        $dateFrom = $request->input('date_from', now()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));
        $userId = $request->input('user_id');
        $scheduleId = $request->input('schedule_id');
        $roleId = $request->input('role_id');

        // Query scan reports
        $query = ScanReport::with(['user.roles', 'train', 'schedule.train', 'verifications.rangkaian'])
            ->where('status', 'completed')
            ->whereDate('submitted_at', '>=', $dateFrom)
            ->whereDate('submitted_at', '<=', $dateTo);

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($scheduleId) {
            $query->where('schedule_id', $scheduleId);
        }

        // Filter by role (jabatan)
        if ($roleId) {
            $query->whereHas('user.roles', function ($q) use ($roleId) {
                $q->where('roles.id', $roleId);
            });
        }

        $scanReports = $query->orderBy('submitted_at')->get();

        // Build rekap data structure
        $rekap = [];
        
        // Color palette for session grouping (Tailwind border colors)
        $sessionColors = [
            'border-blue-500',      'border-green-500', 
            'border-purple-500',    'border-amber-500',
            'border-rose-500',      'border-cyan-500',
            'border-indigo-500',    'border-teal-500',
            'border-fuchsia-500',   'border-orange-500',
            'border-lime-500',      'border-pink-500',
            'border-sky-500',       'border-emerald-500',
            'border-violet-500',    'border-red-500'
        ];

        foreach ($scanReports as $index => $report) {
            $verifications = $report->verifications->sortBy('verified_at');
            
            if ($verifications->isEmpty()) {
                continue;
            }

            $userId = $report->user_id;
            $scheduleId = $report->schedule_id;
            
            // Create tracking key: user_id + schedule_id
            $trackingKey = $userId . '_' . $scheduleId;
            
            // Initialize tracking for this user+schedule if not exists
            if (!isset($userScheduleTracking[$trackingKey])) {
                $userScheduleTracking[$trackingKey] = [
                    'ronde' => 0,
                    'previousSubmitTime' => null,
                    'sessionIndex' => 0, // NEW: Track session restart count
                ];
            }

            $firstScan = Carbon::parse($verifications->first()->verified_at);
            $lastScan = Carbon::parse($verifications->last()->verified_at);
            $durasiSeconds = $firstScan->diffInSeconds($lastScan);

            // Calculate jarak waktu from THIS USER's previous submit ON SAME SCHEDULE
            $jarakWaktuSeconds = 0;
            if ($userScheduleTracking[$trackingKey]['previousSubmitTime']) {
                $jarakWaktuSeconds = $userScheduleTracking[$trackingKey]['previousSubmitTime']->diffInSeconds($firstScan);
                
                // SAFETY RULE: If gap > 7 hours, force reset to Putaran 1
                $maxGapSeconds = 7 * 60 * 60; // 7 hours
                if ($jarakWaktuSeconds > $maxGapSeconds) {
                    // Reset tracking for this user+schedule
                    $userScheduleTracking[$trackingKey] = [
                        'ronde' => 0,
                        'previousSubmitTime' => null,
                        'sessionIndex' => $userScheduleTracking[$trackingKey]['sessionIndex'] + 1, // Start new session
                    ];
                    $jarakWaktuSeconds = 0; // First scan of new session
                }
            }
            
            // Update this user+schedule's previous submit time for next iteration
            $userScheduleTracking[$trackingKey]['previousSubmitTime'] = $report->submitted_at 
                ? Carbon::parse($report->submitted_at) 
                : $lastScan;
            
            // Increment this user+schedule's ronde count
            $userScheduleTracking[$trackingKey]['ronde']++;

            // Determine status based on duration (30 min = 1800s, 45 min = 2700s)
            $status = 'normal';
            if ($durasiSeconds > 1800 && $durasiSeconds <= 2700) {
                $status = 'warning';
            } elseif ($durasiSeconds > 2700) {
                $status = 'danger';
            }

            // Build details array
            $details = [];
            foreach ($verifications as $verification) {
                $details[] = [
                    'nama_gerbong' => $verification->rangkaian->name ?? '-',
                    'waktu_scan' => Carbon::parse($verification->verified_at)->format('H:i:s'),
                ];
            }

            // Get user info
            $user = $report->user;
            $roleName = $user?->roles()?->first()?->name ?? 'Tidak Ada Jabatan';

            // Get schedule info
            $schedule = $report->schedule;
            $trainName = $schedule?->train?->name ?? $report->train?->name ?? '-';
            $scheduleInfo = $schedule ? ($schedule->no_ka . ' (' . $schedule->origin . ' → ' . $schedule->destination . ')') : '-';

            // Generate Session Color
            // Use a hash of UserID + ScheduleID + SessionIndex to pick a consistent color
            $sessionUniqueStr = $userId . '_' . $scheduleId . '_' . $userScheduleTracking[$trackingKey]['sessionIndex'];
            $colorIndex = crc32($sessionUniqueStr) % count($sessionColors);
            $sessionColor = $sessionColors[$colorIndex];

            $rekap[] = [
                'ronde' => $userScheduleTracking[$trackingKey]['ronde'],
                'tanggal' => $report->submitted_at ? Carbon::parse($report->submitted_at)->format('d/m/Y') : '-',
                'waktu_awal' => $firstScan->format('H:i:s'),
                'waktu_akhir' => $lastScan->format('H:i:s'),
                'durasi_detik' => $durasiSeconds,
                'durasi_formatted' => $this->formatDuration($durasiSeconds),
                'jarak_waktu_detik' => $jarakWaktuSeconds,
                'jarak_waktu_formatted' => $this->formatDuration($jarakWaktuSeconds),
                'status' => $status,
                'session_color' => $sessionColor, // Pass color to view
                'details' => $details,
                // User info
                'user_name' => $user?->name ?? '-',
                'user_id' => $user?->id,
                'user_nipp' => $user?->nipp ?? '-',
                'user_jabatan' => $roleName,
                'avatar_url' => $user?->avatar_url, // Add avatar URL
                'train_name' => $trainName,
                'no_ka' => $schedule?->no_ka ?? '-',
                'schedule_info' => $scheduleInfo,
                // Submit time
                'submitted_at' => $report->submitted_at ? Carbon::parse($report->submitted_at)->format('H:i:s') : '-',
            ];
        }

        // Calculate Rerata Jarak Waktu
        $userJarakWaktu = [];
        foreach ($rekap as $item) {
            $uid = $item['user_id'] ?? 'unknown';
            if (!isset($userJarakWaktu[$uid])) {
                $userJarakWaktu[$uid] = [
                    'total_detik' => 0,
                    'jumlah_ronde' => 0,
                    'jumlah_sesi' => 0, // Count of 0-second gaps (starts)
                    'user_name' => $item['user_name'],
                    'user_jabatan' => $item['user_jabatan'] ?? '-',
                    'schedules' => [], // Track per-schedule data
                ];
            }
            
            $jarak = $item['jarak_waktu_detik'];
            $scheduleKey = $item['train_name'] . ' (' . ($item['no_ka'] ?? '-') . ')';

            // Global User Stats
            $userJarakWaktu[$uid]['total_detik'] += $jarak;
            $userJarakWaktu[$uid]['jumlah_ronde']++;
            
            if ($jarak == 0) {
                $userJarakWaktu[$uid]['jumlah_sesi']++;
            }

            // Per-Schedule Stats
            if (!isset($userJarakWaktu[$uid]['schedules'][$scheduleKey])) {
                $userJarakWaktu[$uid]['schedules'][$scheduleKey] = [
                    'total_detik' => 0,
                    'jumlah_ronde' => 0,
                    'jumlah_sesi' => 0,
                ];
            }
            $userJarakWaktu[$uid]['schedules'][$scheduleKey]['total_detik'] += $jarak;
            $userJarakWaktu[$uid]['schedules'][$scheduleKey]['jumlah_ronde']++;
            if ($jarak == 0) {
                $userJarakWaktu[$uid]['schedules'][$scheduleKey]['jumlah_sesi']++;
            }
        }

        // Step 2: Calculate average per user and per schedule
        $userAverages = [];
        foreach ($userJarakWaktu as $uid => $data) {
            // Global Average
            $divisor = max(1, $data['jumlah_ronde'] - $data['jumlah_sesi']);
            $rataRata = ($data['jumlah_ronde'] > $data['jumlah_sesi']) 
                ? round($data['total_detik'] / $divisor) 
                : 0;

            // Per-Schedule Averages
            $scheduleAverages = [];
            foreach ($data['schedules'] as $key => $sData) {
                 $sDivisor = max(1, $sData['jumlah_ronde'] - $sData['jumlah_sesi']);
                 $sRata = ($sData['jumlah_ronde'] > $sData['jumlah_sesi'])
                    ? round($sData['total_detik'] / $sDivisor)
                    : 0;
                 
                 $scheduleAverages[$key] = [
                     'rerata_detik' => $sRata,
                     'rerata_formatted' => $this->formatDuration($sRata),
                     'jumlah_ronde' => $sData['jumlah_ronde'],
                 ];
            }

            $userAverages[$uid] = [
                'user_name' => $data['user_name'],
                'user_jabatan' => $data['user_jabatan'],
                'avatar_url' => $users->find($uid)->avatar_url, // Get avatar URL
                'rerata_detik' => $rataRata,
                'rerata_formatted' => $this->formatDuration($rataRata),
                'jumlah_ronde' => $data['jumlah_ronde'],
                'jumlah_sesi' => $data['jumlah_sesi'],
                'schedules' => $scheduleAverages, // Pass to view
            ];
        }

        // Step 3: Calculate overall average
        $totalUserAverages = 0;
        $validUsersCount = 0;
        foreach ($userAverages as $avg) {
            if ($avg['jumlah_ronde'] > $avg['jumlah_sesi']) {
                $totalUserAverages += $avg['rerata_detik'];
                $validUsersCount++;
            }
        }
        $rerataJarakWaktu = $validUsersCount > 0 ? round($totalUserAverages / $validUsersCount) : 0;
        $rerataJarakWaktuFormatted = $this->formatDuration($rerataJarakWaktu);

        // Handle AJAX Request for Realtime Polling
        if ($request->ajax()) {
            $htmlTable = view('pages.rekap.partials.table_rows', compact('rekap'))->render();
            $htmlSummary = view('pages.rekap.partials.summary_cards', compact('rekap', 'userAverages', 'rerataJarakWaktuFormatted', 'users'))->render();

            return response()->json([
                'html_table' => $htmlTable,
                'html_summary' => $htmlSummary,
            ]);
        }

        return view('pages.rekap.index', compact(
            'rekap', 
            'users', 
            'schedules', 
            'roles', 
            'dateFrom', 
            'dateTo', 
            'userId', 
            'scheduleId', 
            'roleId',
            'userAverages',
            'rerataJarakWaktu',
            'rerataJarakWaktuFormatted'
        ));
    }
}
