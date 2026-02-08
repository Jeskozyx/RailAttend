<?php

namespace App\Http\Controllers\Pages\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DashboardTrait;
use App\Models\Train;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RerataKelilingController extends Controller
{
    use DashboardTrait;

    /**
     * Display Rerata Keliling page
     */
    public function index(Request $request)
    {
        $trains = Train::orderBy('name', 'ASC')->get();
        
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        $chartData = $this->buildChartData($startDate, $endDate, $trainId);

        // Pass roles to view for dynamic legend
        $allRoles = Role::where('name', '!=', 'Admin')->orderBy('name')->get();
        $roleColors = [];
        foreach ($allRoles as $role) {
            $roleColors[$role->name] = $this->generateRoleColor($role->name);
        }

        return view('pages.dashboard.reratakeliling', compact('trains', 'chartData', 'startDate', 'endDate', 'trainId', 'allRoles', 'roleColors'));
    }

    /**
     * API endpoint for Rerata Keliling chart data (AJAX Polling)
     */
    public function getStats(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        return response()->json($this->buildChartData($startDate, $endDate, $trainId));
    }

    /**
     * Build chart data for Rerata Keliling
     * Duration = MAX(created_at) - MIN(created_at) per schedule per user
     */
    private function buildChartData($startDate, $endDate, $trainId = null)
    {
        // 1. Fetch Raw Scans
        $query = DB::table('scan_reports')
            ->join('users', 'scan_reports.user_id', '=', 'users.id')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->leftJoin('schedules', 'scan_reports.schedule_id', '=', 'schedules.id')
            ->leftJoin('trains', 'schedules.train_id', '=', 'trains.id')
            ->where('scan_reports.status', 'completed')
            ->whereDate('scan_reports.created_at', '>=', $startDate)
            ->whereDate('scan_reports.created_at', '<=', $endDate);

        if ($trainId) {
            $query->where('schedules.train_id', $trainId);
        }

        $rawScans = $query->select(
            'trains.name as train_name',
            'schedules.no_ka',
            'roles.name as role_name',
            'scan_reports.id as report_id',
            'scan_reports.schedule_id',
            'scan_reports.user_id',
            'scan_reports.created_at' // We need this to order, but we need verifications for exact time
        )
        ->orderBy('trains.name')
        ->orderBy('schedules.no_ka')
        ->orderBy('scan_reports.created_at', 'ASC')
        ->get();

        // 2. Prepare Data Structure
        $trainLabels = [];
        // Structure: $trainStats[TrainLabel][RoleKey][UserId] = ['total_gap' => 0, 'rounds' => 0, 'starts' => 0]
        $trainStats = []; 

        // 3. Process Scans to Calculate Gaps (Mirrors RekapController Logic)
        // We need to fetch verifications for these reports to get exact scan times if needed, 
        // but scan_reports.created_at (or submitted_at) is usually the summary.
        // RekapController uses verifications. Let's stick to report times for performance 
        // OR fetch verifications if we want per-scan precision. 
        // *Correction*: Rekap logic tracks gap between *reports* (previousSubmitTime).
        // So we can use scan_reports data directly.

        // Tracking state: $userScheduleTracking[UserId_ScheduleId] = previousSubmitTime
        $userScheduleTracking = [];
        $maxGapSeconds = 7 * 60 * 60; // 7 hours safety rule

        foreach ($rawScans as $scan) {
            $trainLabel = $scan->train_name . ' (' . $scan->no_ka . ')';
            if (!in_array($trainLabel, $trainLabels)) {
                $trainLabels[] = $trainLabel;
            }

            $roleKey = Str::slug($scan->role_name, '_');
            $userId = $scan->user_id;
            $scheduleId = $scan->schedule_id;
            $trackingKey = $userId . '_' . $scheduleId;
            $scanTime = \Carbon\Carbon::parse($scan->created_at);

            // Initialize stats bucket
            if (!isset($trainStats[$trainLabel][$roleKey][$userId])) {
                $trainStats[$trainLabel][$roleKey][$userId] = [
                    'total_gap' => 0,
                    'rounds' => 0,
                    'starts' => 0
                ];
            }

            // Calculate Gap
            $gapSeconds = 0;
            if (isset($userScheduleTracking[$trackingKey])) {
                $previousTime = $userScheduleTracking[$trackingKey];
                $gapSeconds = $previousTime->diffInSeconds($scanTime);

                // Safety Rule
                if ($gapSeconds > $maxGapSeconds) {
                    $gapSeconds = 0; // Reset / Start new session
                    // Update tracking time is handled below
                }
            } else {
                // First scan for this schedule -> Gap 0
                $gapSeconds = 0; 
            }

            // Update Stats
            $trainStats[$trainLabel][$roleKey][$userId]['rounds']++;
            $trainStats[$trainLabel][$roleKey][$userId]['total_gap'] += $gapSeconds;
            
            if ($gapSeconds == 0) {
                $trainStats[$trainLabel][$roleKey][$userId]['starts']++;
            }

            // Update Tracking
            $userScheduleTracking[$trackingKey] = $scanTime;
        }

        sort($trainLabels);

        // 4. Final Aggregation
        $roles = $this->getDynamicRoles();
        $datasets = [];
        $roleInfo = [];

        foreach ($roles as $roleData) {
            $roleKey = Str::slug($roleData['name'], '_');
            $roleInfo[] = [
                'name' => $roleData['name'],
                'key' => $roleKey,
                'color' => $roleData['color']
            ];
            
            // Initialize dataset for this role
            $datasets[$roleKey] = [
                'data' => array_fill(0, count($trainLabels), 0),
                'details' => array_fill(0, count($trainLabels), [])
            ];
        }

        // 5. Calculate Averages
        foreach ($trainLabels as $index => $label) {
            foreach ($roleInfo as $role) {
                $roleKey = $role['key'];
                
                if (isset($trainStats[$label][$roleKey])) {
                    $usersData = $trainStats[$label][$roleKey];
                    $validUserAverages = []; // Store averages of users who have valid gaps
                    $detailsText = [];

                    foreach ($usersData as $uid => $stats) {
                        // Formula: Total Gap / (Rounds - Starts)
                        $divisor = max(1, $stats['rounds'] - $stats['starts']);
                        
                        // Only calculate if user has actual gaps (rounds > starts)
                        if ($stats['rounds'] > $stats['starts']) {
                            $avgSeconds = $stats['total_gap'] / $divisor;
                            $avgMinutes = round($avgSeconds / 60, 1);
                            $validUserAverages[] = $avgMinutes;
                            
                            // Optional: details per user
                            // $detailsText[] = "User $uid: $avgMinutes m"; 
                        }
                    }

                    // Global Average for this Train & Role
                    if (count($validUserAverages) > 0) {
                        $globalAvg = array_sum($validUserAverages) / count($validUserAverages);
                        $datasets[$roleKey]['data'][$index] = round($globalAvg, 1);
                        
                        // Detail text can be list of user averages or just summary
                        $datasets[$roleKey]['details'][$index] = [count($validUserAverages) . " User Valid"];
                    }
                }
            }
        }

        return [
            'labels' => $trainLabels,
            'roles' => $roleInfo,
            'datasets' => $datasets
        ];
    }
}
