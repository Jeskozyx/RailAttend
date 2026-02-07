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
        
        // Track per-user AND per-schedule: previousSubmitTime and rondeCount
        // This handles midnight-crossing shifts correctly
        $userScheduleTracking = [];

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
                ];
            }

            $firstScan = Carbon::parse($verifications->first()->verified_at);
            $lastScan = Carbon::parse($verifications->last()->verified_at);
            $durasiSeconds = $firstScan->diffInSeconds($lastScan);

            // Calculate jarak waktu from THIS USER's previous submit ON SAME SCHEDULE
            $jarakWaktuSeconds = 0;
            if ($userScheduleTracking[$trackingKey]['previousSubmitTime']) {
                $jarakWaktuSeconds = $userScheduleTracking[$trackingKey]['previousSubmitTime']->diffInSeconds($firstScan);
                
                // SAFETY RULE: If gap > 8 hours (28800 seconds), force reset to Putaran 1
                // This handles edge cases like using same schedule days apart
                $maxGapSeconds = 8 * 60 * 60; // 8 hours
                if ($jarakWaktuSeconds > $maxGapSeconds) {
                    // Reset tracking for this user+schedule
                    $userScheduleTracking[$trackingKey] = [
                        'ronde' => 0,
                        'previousSubmitTime' => null,
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

            $rekap[] = [
                'ronde' => $userScheduleTracking[$trackingKey]['ronde'],
                'tanggal' => $report->submitted_at ? Carbon::parse($report->submitted_at)->format('d/m/Y') : '-',
                'waktu_awal' => $firstScan->format('H:i:s'),
                'waktu_akhir' => $lastScan->format('H:i:s'),
                'durasi_detik' => $durasiSeconds,
                'jarak_waktu_detik' => $jarakWaktuSeconds,
                'status' => $status,
                'details' => $details,
                // User info
                'user_name' => $user?->name ?? '-',
                'user_nipp' => $user?->nipp ?? '-',
                'user_jabatan' => $roleName,
                'train_name' => $trainName,
                'no_ka' => $schedule?->no_ka ?? '-',
                'schedule_info' => $scheduleInfo,
                // Submit time
                'submitted_at' => $report->submitted_at ? Carbon::parse($report->submitted_at)->format('H:i:s') : '-',
            ];
        }

        return view('pages.rekap.index', compact('rekap', 'users', 'schedules', 'roles', 'dateFrom', 'dateTo', 'userId', 'scheduleId', 'roleId'));
    }
}
