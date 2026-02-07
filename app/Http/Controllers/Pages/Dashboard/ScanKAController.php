<?php

namespace App\Http\Controllers\Pages\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DashboardTrait;
use App\Models\Train;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ScanKAController extends Controller
{
    use DashboardTrait;

    /**
     * Display Scan KA page
     */
    public function index(Request $request) 
    {
        $trains = Train::orderBy('name', 'ASC')->get();
        
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        $chartData = $this->buildChartData($startDate, $endDate, $trainId);

        // Pass roles to view for dynamic legend rendering
        $allRoles = Role::where('name', '!=', 'Admin')->orderBy('name')->get();
        $roleColors = [];
        foreach ($allRoles as $role) {
            $roleColors[$role->name] = $this->generateRoleColor($role->name);
        }

        return view('pages.dashboard.scanKA', compact('trains', 'chartData', 'startDate', 'endDate', 'trainId', 'allRoles', 'roleColors'));
    }

    /**
     * API endpoint for Scan KA chart data (AJAX Polling)
     */
    public function getStats(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        return response()->json($this->buildChartData($startDate, $endDate, $trainId));
    }

    /**
     * Build chart data for Scan KA
     */
    private function buildChartData($startDate, $endDate, $trainId = null)
    {
        $query = DB::table('scan_reports')
            ->join('users', 'scan_reports.user_id', '=', 'users.id')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->leftJoin('schedules', 'scan_reports.schedule_id', '=', 'schedules.id')
            ->leftJoin('trains', 'schedules.train_id', '=', 'trains.id');

        $query->where('scan_reports.status', 'completed')
              ->whereDate('scan_reports.created_at', '>=', $startDate)
              ->whereDate('scan_reports.created_at', '<=', $endDate);

        if ($trainId) {
            $query->where('schedules.train_id', $trainId);
        }

        $data = $query->select(
            DB::raw('DATE(scan_reports.created_at) as date'),
            'roles.name as role_name',
            'trains.name as train_name',
            'schedules.no_ka',
            DB::raw('count(*) as total')
        )
        ->groupBy(DB::raw('DATE(scan_reports.created_at)'), 'roles.name', 'trains.name', 'schedules.no_ka')
        ->orderBy(DB::raw('DATE(scan_reports.created_at)'), 'asc')
        ->get();

        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $labels = [];
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
            $labels[] = $date->format('d M Y');
        }

        $roles = $this->getDynamicRoles();
        $roleChartData = $this->buildDynamicRoleChartData($data, $dates, $roles);

        return [
            'labels' => $labels,
            'roles' => $roleChartData['roles'],
            'datasets' => $roleChartData['datasets']
        ];
    }
}
