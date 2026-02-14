<?php

namespace App\Http\Controllers\Pages\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DashboardTrait;
use App\Models\Train;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Services\TimeGapService;

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
    /**
     * Build chart data for Rerata Keliling using RekapWaktuKereta (Single Source of Truth)
     */
    private function buildChartData($startDate, $endDate, $trainId = null)
    {
        // 1. Fetch Pre-calculated Rekap Data
        $query = DB::table('rekap_waktu_kereta')
            ->join('users', 'rekap_waktu_kereta.user_id', '=', 'users.id')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->leftJoin('schedules', 'rekap_waktu_kereta.schedule_id', '=', 'schedules.id')
            ->leftJoin('trains', 'schedules.train_id', '=', 'trains.id')
            ->whereDate('rekap_waktu_kereta.tanggal', '>=', $startDate)
            ->whereDate('rekap_waktu_kereta.tanggal', '<=', $endDate);

        if ($trainId) {
            $query->where('schedules.train_id', $trainId);
        }

        $results = $query->select(
            'trains.name as train_name',
            'schedules.no_ka',
            'roles.name as role_name',
            'rekap_waktu_kereta.user_id',
            'rekap_waktu_kereta.jarak_waktu_detik'
        )
        ->orderBy('trains.name')
        ->orderBy('schedules.no_ka')
        ->get();

        // 2. Prepare Data Structure
        $trainLabels = [];
        // Structure: $trainStats[TrainLabel][RoleKey][UserId] = ['sum' => 0, 'count' => 0]
        $trainStats = []; 

        foreach ($results as $row) {
            $trainLabel = $row->train_name . ' (' . $row->no_ka . ')';
            if (!in_array($trainLabel, $trainLabels)) {
                $trainLabels[] = $trainLabel;
            }

            $roleKey = Str::slug($row->role_name, '_');
            $userId = $row->user_id;

            // Initialize stats bucket
            if (!isset($trainStats[$trainLabel][$roleKey][$userId])) {
                $trainStats[$trainLabel][$roleKey][$userId] = [
                    'sum_seconds' => 0,
                    'valid_count' => 0
                ];
            }

            // Only count valid gaps (> 0)
            if ($row->jarak_waktu_detik > 0) {
                $trainStats[$trainLabel][$roleKey][$userId]['sum_seconds'] += $row->jarak_waktu_detik;
                $trainStats[$trainLabel][$roleKey][$userId]['valid_count']++;
            }
        }

        sort($trainLabels);

        // 3. Final Aggregation & Averages
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
            
            $datasets[$roleKey] = [
                'data' => array_fill(0, count($trainLabels), 0),
                'details' => array_fill(0, count($trainLabels), [])
            ];
        }

        foreach ($trainLabels as $index => $label) {
            foreach ($roleInfo as $role) {
                $roleKey = $role['key'];
                
                if (isset($trainStats[$label][$roleKey])) {
                    $usersData = $trainStats[$label][$roleKey];
                    $validUserAverages = []; 

                    foreach ($usersData as $uid => $stats) {
                        if ($stats['valid_count'] > 0) {
                            $avgSeconds = $stats['sum_seconds'] / $stats['valid_count'];
                            $avgMinutes = round($avgSeconds / 60, 1); // Convert to Minutes
                            $validUserAverages[] = $avgMinutes;
                        }
                    }

                    // Global Average for this Train & Role (Average of User Averages)
                    if (count($validUserAverages) > 0) {
                        $globalAvg = array_sum($validUserAverages) / count($validUserAverages);
                        $datasets[$roleKey]['data'][$index] = round($globalAvg, 1);
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
