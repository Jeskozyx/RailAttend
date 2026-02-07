<?php

namespace App\Http\Controllers\Pages\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\DashboardTrait;
use App\Models\Train;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PeriodeKelilingController extends Controller
{
    use DashboardTrait;

    /**
     * Display Periode Keliling page
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

        return view('pages.dashboard.periodekeliling', compact('trains', 'chartData', 'startDate', 'endDate', 'trainId', 'allRoles', 'roleColors'));
    }

    /**
     * API endpoint for Periode Keliling chart data (AJAX Polling)
     */
    public function getStats(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        return response()->json($this->buildChartData($startDate, $endDate, $trainId));
    }

    /**
     * Build chart data for Periode Keliling
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
            DB::raw('DATE(scan_reports.created_at) as date'),
            DB::raw('count(*) as total')
        )
        ->groupBy('trains.name', 'schedules.no_ka', 'roles.name', DB::raw('DATE(scan_reports.created_at)'))
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

        // Populate data
        foreach ($data as $row) {
            $label = $row->train_name . ' (' . $row->no_ka . ')';
            $trainIndex = array_search($label, $trainLabels);

            if ($trainIndex === false) continue;

            $dateFormatted = \Carbon\Carbon::parse($row->date)->format('d M');
            $detailStr = $dateFormatted . ': ' . $row->total;

            foreach ($roleInfo as $role) {
                if ($row->role_name === $role['name']) {
                    $roleKey = $role['key'];
                    $datasets[$roleKey]['data'][$trainIndex] += $row->total;
                    $datasets[$roleKey]['details'][$trainIndex][] = $detailStr;
                    break;
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
