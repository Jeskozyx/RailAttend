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

        $data = $query->select(
            'trains.name as train_name',
            'schedules.no_ka',
            'roles.name as role_name',
            'scan_reports.schedule_id',
            'scan_reports.user_id',
            DB::raw('DATE(scan_reports.created_at) as date'),
            DB::raw('TIMESTAMPDIFF(MINUTE, MIN(scan_reports.created_at), MAX(scan_reports.created_at)) as duration')
        )
        ->groupBy('trains.name', 'schedules.no_ka', 'roles.name', 'scan_reports.schedule_id', 'scan_reports.user_id', DB::raw('DATE(scan_reports.created_at)'))
        ->orderBy('trains.name')
        ->orderBy('schedules.no_ka')
        ->get();

        // Build unique train labels (X-axis)
        $trainLabels = [];
        foreach ($data as $row) {
            $label = $row->train_name . ' (' . $row->no_ka . ')';
            if (!in_array($label, $trainLabels)) {
                $trainLabels[] = $label;
            }
        }
        sort($trainLabels);

        // Get dynamic roles
        $roles = $this->getDynamicRoles();
        
        // Initialize data arrays per role
        $datasets = [];
        $durations = [];
        $roleInfo = [];
        foreach ($roles as $roleData) {
            $roleKey = Str::slug($roleData['name'], '_');
            $roleInfo[] = [
                'name' => $roleData['name'],
                'key' => $roleKey,
                'color' => $roleData['color']
            ];
            $durations[$roleKey] = array_fill(0, count($trainLabels), []);
            $datasets[$roleKey] = [
                'data' => array_fill(0, count($trainLabels), 0),
                'details' => array_fill(0, count($trainLabels), [])
            ];
        }

        // Populate data
        foreach ($data as $row) {
            $label = $row->train_name . ' (' . $row->no_ka . ')';
            $trainIndex = array_search($label, $trainLabels);

            if ($trainIndex === false) continue;

            $dateFormatted = \Carbon\Carbon::parse($row->date)->format('d M');
            $detailStr = $dateFormatted . ': ' . $row->duration . 'm';

            foreach ($roleInfo as $role) {
                if ($row->role_name === $role['name']) {
                    $roleKey = $role['key'];
                    $durations[$roleKey][$trainIndex][] = $row->duration;
                    $datasets[$roleKey]['details'][$trainIndex][] = $detailStr;
                    break;
                }
            }
        }

        // Calculate averages
        foreach ($roleInfo as $role) {
            $roleKey = $role['key'];
            $datasets[$roleKey]['data'] = array_map(function($arr) {
                return count($arr) > 0 ? round(array_sum($arr) / count($arr), 1) : 0;
            }, $durations[$roleKey]);
        }

        return [
            'labels' => $trainLabels,
            'roles' => $roleInfo,
            'datasets' => $datasets
        ];
    }
}
