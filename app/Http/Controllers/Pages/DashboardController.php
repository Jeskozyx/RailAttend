<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Train;
use App\Models\ScanReport;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Rangkaian;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index(Request $request) 
    {
        $roles = Auth::user()->getRoleNames()[0];
        
        if ($roles != "Admin") {
            $train = Train::orderBy('id', 'DESC')->get();
            return view("pages.dashboard", compact("train"));
        } else {
            // 1. Statistics Cards
            $totalAdmin = User::role('Admin')->count();
            $totalUsers = User::count();
            $totaljadwal = Schedule::count();
            
            // Trains (Existing logic + count)
            $search = $request->get('search');
            $trainsQuery = Train::with(['scan_reports' => function($q) {
                $q->with(['user', 'schedule', 'verifications'])
                  ->orderBy('created_at', 'DESC');
            }])->orderBy('name', 'ASC');

            $trains = $trainsQuery->get();
            
            // Filter search manually as per original logic (since relation constraints are complex)
            if ($search) {
                $trains = $trains->filter(function($train) use ($search) {
                    return stripos($train->name, $search) !== false;
                });
            }

            $totalSarana = Rangkaian::count();

            // 2. Scan per KA Chart Data (Group by Schedule/No KA)
            $trainScanStats = $this->getTrainScanStats($request->get('sort', 'name'));
            
            $chartTrainTypeLabels = $trainScanStats->map(function($item) {
                return $item->name . ' (' . $item->no_ka . ')';
            })->values();
            $chartTrainTypeValues = $trainScanStats->pluck('total')->values();

            // 3. Scan Frequency Chart Data (By Role) - DYNAMIC
            // Get all non-Admin roles from database
            $allRoles = Role::where('name', '!=', 'Admin')->orderBy('name')->get();
            
            // Get scan stats per role
            $scanStats = DB::table('scan_reports')
                ->join('users', 'scan_reports.user_id', '=', 'users.id')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('scan_reports.status', 'completed')
                ->select('roles.name', DB::raw('count(*) as total'))
                ->groupBy('roles.name')
                ->pluck('total', 'name');

            // Build dynamic chart data
            $chartPieLabels = [];
            $chartPieValues = [];
            $chartPieColors = [];
            
            foreach ($allRoles as $role) {
                $chartPieLabels[] = $role->name; // Full name, no abbreviation
                $chartPieValues[] = $scanStats[$role->name] ?? 0;
                $chartPieColors[] = $this->generateRoleColor($role->name);
            }
            
            // Calculate percentages for the legend
            $totalScans = array_sum($chartPieValues);
            $chartPiePercentages = array_map(function($val) use ($totalScans) {
                return $totalScans > 0 ? round(($val / $totalScans) * 100) : 0;
            }, $chartPieValues);

            return view("pages.dashboard", compact(
                "trains", 
                "totalAdmin", 
                "totalUsers", 
                "totaljadwal",
                "totalSarana",
                "chartTrainTypeLabels",
                "chartTrainTypeValues",
                "chartPieLabels",
                "chartPieValues",
                "chartPiePercentages",
                "chartPieColors",
                "allRoles"
            ));
        }
    }

    public function scanKA(Request $request) 
    {
        $trains = Train::orderBy('name', 'ASC')->get();
        
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        // Query Scan Reports
        $query = DB::table('scan_reports')
            ->join('users', 'scan_reports.user_id', '=', 'users.id')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->leftJoin('schedules', 'scan_reports.schedule_id', '=', 'schedules.id') // Join schedules to filter by train
            ->leftJoin('trains', 'schedules.train_id', '=', 'trains.id'); // Join trains to get train name

        // Filter by Date + Only Completed Reports
        $query->where('scan_reports.status', 'completed') // Only count submitted reports
              ->whereDate('scan_reports.created_at', '>=', $startDate)
              ->whereDate('scan_reports.created_at', '<=', $endDate);

        // Filter by Train if selected
        if ($trainId) {
            $query->where('schedules.train_id', $trainId);
        }

        // Select Data with Detail for Tooltip
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

        // Process Data for Chart
        // 1. Generate Date Range Labels
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $labels = [];
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
            $labels[] = $date->format('d M Y');
        }

        // 2. Get dynamic roles and build chart data
        $roles = $this->getDynamicRoles();
        $roleChartData = $this->buildDynamicRoleChartData($data, $dates, $roles);

        $chartData = [
            'labels' => $labels,
            'roles' => $roleChartData['roles'],
            'datasets' => $roleChartData['datasets']
        ];

        // Pass roles to view for dynamic legend rendering
        $allRoles = Role::where('name', '!=', 'Admin')->orderBy('name')->get();
        $roleColors = [];
        foreach ($allRoles as $role) {
            $roleColors[$role->name] = $this->generateRoleColor($role->name);
        }

        return view('pages.dashboard.scanKA', compact('trains', 'chartData', 'startDate', 'endDate', 'trainId', 'allRoles', 'roleColors'));
    }

    public function scanPerDinas(Request $request)
    {
        $trains = Train::orderBy('name', 'ASC')->get();
        
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        // Query Scan Reports
        $query = DB::table('scan_reports')
            ->leftJoin('schedules', 'scan_reports.schedule_id', '=', 'schedules.id') 
            ->leftJoin('trains', 'schedules.train_id', '=', 'trains.id');

        // Filter by Date + Only Completed Reports
        $query->where('scan_reports.status', 'completed') // Only count submitted reports
              ->whereDate('scan_reports.created_at', '>=', $startDate)
              ->whereDate('scan_reports.created_at', '<=', $endDate);

        // Filter by Train if selected
        if ($trainId) {
            $query->where('schedules.train_id', $trainId);
        }

        // Select Data: Group by Date and Schedule (Train Name + No KA)
        $data = $query->select(
            DB::raw('DATE(scan_reports.created_at) as date'),
            'trains.name as train_name',
            'schedules.no_ka',
            DB::raw('count(*) as total')
        )
        ->groupBy(DB::raw('DATE(scan_reports.created_at)'), 'trains.name', 'schedules.no_ka')
        ->orderBy(DB::raw('DATE(scan_reports.created_at)'), 'asc')
        ->get();

        // Process Data for Chart
        // 1. Generate Date Range Labels (X-Axis)
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $labels = [];
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
            $labels[] = $date->format('d M Y');
        }

        // 2. Identify Unique Schedules (Datasets)
        $uniqueSchedules = [];
        foreach ($data as $row) {
            $scheduleName = $row->train_name . ' (' . $row->no_ka . ')';
            if (!in_array($scheduleName, $uniqueSchedules)) {
                $uniqueSchedules[] = $scheduleName;
            }
        }
        sort($uniqueSchedules);

        // 3. Build Datasets
        $datasets = [];
        foreach ($uniqueSchedules as $schedule) {
            $dataPoints = array_fill(0, count($dates), 0);
            
            // Generate a consistent color based on the schedule name
            $hash = md5($schedule);
            $r = hexdec(substr($hash, 0, 2));
            $g = hexdec(substr($hash, 2, 2));
            $b = hexdec(substr($hash, 4, 2));
            $color = "rgb($r, $g, $b)";

            $datasets[] = [
                'label' => $schedule,
                'data' => $dataPoints,
                'backgroundColor' => $color,
                'borderColor' => $color,
                'borderWidth' => 1,
                'borderRadius' => 4,
                'borderSkipped' => false
            ];
        }

        // 4. Populate Data Points
        foreach ($data as $row) {
            $dateIndex = array_search($row->date, $dates);
            $scheduleName = $row->train_name . ' (' . $row->no_ka . ')';
            
            // Find the dataset index for this schedule
            $datasetIndex = array_search($scheduleName, $uniqueSchedules);

            if ($dateIndex !== false && $datasetIndex !== false) {
                $datasets[$datasetIndex]['data'][$dateIndex] = $row->total;
            }
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => $datasets
        ];

        return view('pages.dashboard.scanpPERDINAS', compact('trains', 'chartData', 'startDate', 'endDate', 'trainId'));
    }

    // ==========================================
    // TAMBAHAN: FUNGSI API UNTUK AJAX POLLING
    // ==========================================
    public function getStats(Request $request)
    {
        // 1. Hitung Statistik Kartu
        $totalUsers = User::count();
        $totalSarana = Rangkaian::count();

        // 2. Hitung Grafik Kereta
        $sort = request()->get('sort', 'name');
        $trainScanStats = $this->getTrainScanStats($sort);

        $chartTrainLabels = $trainScanStats->map(fn($i) => $i->name . ' (' . $i->no_ka . ')')->values();
        $chartTrainValues = $trainScanStats->pluck('total')->values();

        // 3. Hitung Pie Chart - DYNAMIC
        $allRoles = Role::where('name', '!=', 'Admin')->orderBy('name')->get();
        
        $scanStats = DB::table('scan_reports')
            ->join('users', 'scan_reports.user_id', '=', 'users.id')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('scan_reports.status', 'completed')
            ->select('roles.name', DB::raw('count(*) as total'))
            ->groupBy('roles.name')
            ->pluck('total', 'name');

        $chartPieLabels = [];
        $chartPieValues = [];
        $chartPieColors = [];
        
        foreach ($allRoles as $role) {
            $chartPieLabels[] = $role->name;
            $chartPieValues[] = $scanStats[$role->name] ?? 0;
            $chartPieColors[] = $this->generateRoleColor($role->name);
        }

        $totalScans = array_sum($chartPieValues);
        $chartPiePercentages = array_map(fn($val) => $totalScans > 0 ? round(($val / $totalScans) * 100) : 0, $chartPieValues);

        return response()->json([
            'totalUsers' => $totalUsers,
            'totalSarana' => $totalSarana,
            'chartTrainLabels' => $chartTrainLabels,
            'chartTrainValues' => $chartTrainValues,
            'chartPieLabels' => $chartPieLabels,
            'chartPieValues' => $chartPieValues,
            'chartPieColors' => $chartPieColors,
            'chartPiePercentages' => $chartPiePercentages
        ]);
    } /**
     * API endpoint for Scan KA chart data (AJAX Polling)
     */
    public function getScanKAStats(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        $query = DB::table('scan_reports')
            ->join('users', 'scan_reports.user_id', '=', 'users.id')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->leftJoin('schedules', 'scan_reports.schedule_id', '=', 'schedules.id')
            ->leftJoin('trains', 'schedules.train_id', '=', 'trains.id');

        // Filter by Date + Only Completed Reports
        $query->where('scan_reports.status', 'completed') // Only count submitted reports
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

        // Get dynamic roles and build chart data
        $roles = $this->getDynamicRoles();
        $roleChartData = $this->buildDynamicRoleChartData($data, $dates, $roles);

        return response()->json([
            'labels' => $labels,
            'roles' => $roleChartData['roles'],
            'datasets' => $roleChartData['datasets']
        ]);
    }

    /**
     * API endpoint for Scan Per Dinas chart data (AJAX Polling)
     */
    public function getScanPerDinasStats(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        $query = DB::table('scan_reports')
            ->leftJoin('schedules', 'scan_reports.schedule_id', '=', 'schedules.id')
            ->leftJoin('trains', 'schedules.train_id', '=', 'trains.id');

        // Filter by Date + Only Completed Reports
        $query->where('scan_reports.status', 'completed') // Only count submitted reports
              ->whereDate('scan_reports.created_at', '>=', $startDate)
              ->whereDate('scan_reports.created_at', '<=', $endDate);

        if ($trainId) {
            $query->where('schedules.train_id', $trainId);
        }

        $data = $query->select(
            DB::raw('DATE(scan_reports.created_at) as date'),
            'trains.name as train_name',
            'schedules.no_ka',
            DB::raw('count(*) as total')
        )
        ->groupBy(DB::raw('DATE(scan_reports.created_at)'), 'trains.name', 'schedules.no_ka')
        ->orderBy(DB::raw('DATE(scan_reports.created_at)'), 'asc')
        ->get();

        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $labels = [];
        $dates = [];
        foreach ($period as $date) {
            $dates[] = $date->format('Y-m-d');
            $labels[] = $date->format('d M Y');
        }

        $uniqueSchedules = [];
        foreach ($data as $row) {
            $scheduleName = $row->train_name . ' (' . $row->no_ka . ')';
            if (!in_array($scheduleName, $uniqueSchedules)) {
                $uniqueSchedules[] = $scheduleName;
            }
        }
        sort($uniqueSchedules);

        $datasets = [];
        foreach ($uniqueSchedules as $schedule) {
            $hash = md5($schedule);
            $r = hexdec(substr($hash, 0, 2));
            $g = hexdec(substr($hash, 2, 2));
            $b = hexdec(substr($hash, 4, 2));
            $color = "rgb($r, $g, $b)";

            $datasets[] = [
                'label' => $schedule,
                'data' => array_fill(0, count($dates), 0),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'borderWidth' => 1,
                'borderRadius' => 4,
                'borderSkipped' => false
            ];
        }

        foreach ($data as $row) {
            $dateIndex = array_search($row->date, $dates);
            $scheduleName = $row->train_name . ' (' . $row->no_ka . ')';
            $datasetIndex = array_search($scheduleName, $uniqueSchedules);

            if ($dateIndex !== false && $datasetIndex !== false) {
                $datasets[$datasetIndex]['data'][$dateIndex] = $row->total;
            }
        }

        return response()->json([
            'labels' => $labels,
            'datasets' => $datasets
        ]);
    }

    /**
     * Periode Keliling Page
     */
    public function periodeKeliling(Request $request)
    {
        $trains = Train::orderBy('name', 'ASC')->get();
        
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        $chartData = $this->buildPeriodeKelilingData($startDate, $endDate, $trainId);

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
    public function getPeriodeKelilingStats(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        return response()->json($this->buildPeriodeKelilingData($startDate, $endDate, $trainId));
    }

    /**
     * Build chart data for Periode Keliling - DYNAMIC ROLES
     * X-axis: Train Name (No KA)
     * Datasets: Dynamic per role (stacked)
     * Tooltip details: Date-level breakdown
     */
    private function buildPeriodeKelilingData($startDate, $endDate, $trainId = null)
    {
        // Query: Group by Train, No KA, Role, Date
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
            $roleKey = \Illuminate\Support\Str::slug($roleData['name'], '_');
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

            // Find matching role (exact match)
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

    /**
     * Rerata Keliling Page - Average scan duration per train per role
     */
    public function rerataKeliling(Request $request)
    {
        $trains = Train::orderBy('name', 'ASC')->get();
        
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        $chartData = $this->buildRerataKelilingData($startDate, $endDate, $trainId);

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
    public function getRerataKelilingStats(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        return response()->json($this->buildRerataKelilingData($startDate, $endDate, $trainId));
    }

    /**
     * Build chart data for Rerata Keliling - DYNAMIC ROLES
     * Duration = MAX(created_at) - MIN(created_at) per schedule per user
     */
    private function buildRerataKelilingData($startDate, $endDate, $trainId = null)
    {
        // Query: Calculate duration per schedule, user, role
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
            $roleKey = \Illuminate\Support\Str::slug($roleData['name'], '_');
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

            // Find matching role (exact match)
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

    private function getTrainScanStats($sort = 'name')
    {
        // Get Top 7 Active Schedules First
        $data = DB::table('scan_reports')
            ->join('schedules', 'scan_reports.schedule_id', '=', 'schedules.id')
            ->join('trains', 'schedules.train_id', '=', 'trains.id')
            ->where('scan_reports.status', 'completed')
            ->select('trains.name', 'schedules.no_ka', DB::raw('count(*) as total'))
            ->groupBy('trains.name', 'schedules.no_ka')
            ->orderBy('total', 'desc')
            ->limit(7)
            ->get();

        // Then apply display sorting
        if ($sort === 'most') {
            return $data->sortByDesc('total');
        } elseif ($sort === 'least') {
            return $data->sortBy('total');
        } else {
            // Default: Name
            return $data->sort(function($a, $b) {
                if ($a->name === $b->name) {
                    return $a->no_ka <=> $b->no_ka;
                }
                return strcmp($a->name, $b->name);
            });
        }
    }

    /**
     * Generate role color - static for predefined roles, dynamic for new ones
     */
    private function generateRoleColor(string $roleName): string
    {
        // Static colors for predefined roles
        $staticColors = [
            'Polsuska' => '#F75026',
            'Kondektur' => '#00078A',
            'Teknisi Kereta Api' => '#1CC8CE',
        ];

        // Return static color if exists
        if (isset($staticColors[$roleName])) {
            return $staticColors[$roleName];
        }

        // Generate dynamic color for new roles
        $hash = md5($roleName);
        $h = hexdec(substr($hash, 0, 2)) % 360;
        $s = 65 + (hexdec(substr($hash, 2, 2)) % 20); // 65-85%
        $l = 40 + (hexdec(substr($hash, 4, 2)) % 15); // 40-55%
        return "hsl($h, {$s}%, {$l}%)";
    }

    /**
     * Get all non-Admin roles with their colors
     */
    private function getDynamicRoles()
    {
        $roles = Role::where('name', '!=', 'Admin')->orderBy('name')->get();
        $rolesData = [];
        
        foreach ($roles as $role) {
            $rolesData[] = [
                'name' => $role->name,
                'color' => $this->generateRoleColor($role->name)
            ];
        }
        
        return $rolesData;
    }

    /**
     * Build dynamic chart data by role (used by multiple methods)
     */
    private function buildDynamicRoleChartData($data, $dates, $roles)
    {
        $result = [
            'roles' => [],
            'datasets' => []
        ];

        foreach ($roles as $roleData) {
            $roleName = $roleData['name'];
            $roleKey = \Illuminate\Support\Str::slug($roleName, '_');
            
            $result['roles'][] = [
                'name' => $roleName,
                'key' => $roleKey,
                'color' => $roleData['color']
            ];
            
            $result['datasets'][$roleKey] = [
                'data' => array_fill(0, count($dates), 0),
                'details' => array_fill(0, count($dates), [])
            ];
        }

        foreach ($data as $row) {
            $dateIndex = array_search($row->date, $dates);
            if ($dateIndex === false) continue;

            // Find matching role
            foreach ($result['roles'] as $roleInfo) {
                if ($row->role_name === $roleInfo['name']) {
                    $roleKey = $roleInfo['key'];
                    $detailStr = $row->train_name . '(' . $row->no_ka . '): ' . $row->total;
                    $result['datasets'][$roleKey]['data'][$dateIndex] += $row->total;
                    $result['datasets'][$roleKey]['details'][$dateIndex][] = $detailStr;
                    break;
                }
            }
        }

        return $result;
    }
}