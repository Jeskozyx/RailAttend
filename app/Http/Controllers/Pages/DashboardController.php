<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Train;
use App\Models\ScanReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Rangkaian;
use Illuminate\Support\Facades\DB;

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
            // Group ScanReport by Train Name AND Schedule No (No KA)
            $trainScanStats = DB::table('scan_reports')
                ->join('schedules', 'scan_reports.schedule_id', '=', 'schedules.id')
                ->join('trains', 'schedules.train_id', '=', 'trains.id')
                ->select('trains.name', 'schedules.no_ka', DB::raw('count(*) as total'))
                ->groupBy('trains.name', 'schedules.no_ka')
                ->orderBy('total', 'desc')
                ->limit(7) // Top 7 Active Schedules
                ->get();

            // Sort for display: Group by Name, then by No KA
            $trainScanStats = $trainScanStats->sort(function($a, $b) {
                if ($a->name === $b->name) {
                    return $a->no_ka <=> $b->no_ka;
                }
                return strcmp($a->name, $b->name);
            });
            
            $chartTrainTypeLabels = $trainScanStats->map(function($item) {
                return $item->name . ' (' . $item->no_ka . ')';
            })->values();
            $chartTrainTypeValues = $trainScanStats->pluck('total')->values();

            // 3. Scan Frequency Chart Data (By Role)
            // Join scan_reports -> users -> roles
            $scanStats = DB::table('scan_reports')
                ->join('users', 'scan_reports.user_id', '=', 'users.id')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('roles.name', DB::raw('count(*) as total'))
                ->groupBy('roles.name')
                ->pluck('total', 'name');
            
            // dd($scanStats);

            // Map DB role names to Chart Labels
            $chartPieLabels = ['Polsuska', 'Kondektur', 'TKA'];
            $counts = ['Polsuska' => 0, 'Kondektur' => 0, 'TKA' => 0];

            // \Illuminate\Support\Facades\Log::info('ScanStats Raw:', $scanStats->toArray());

            foreach($scanStats as $roleName => $count) {
                if (stripos($roleName, 'Polsuska') !== false || stripos($roleName, 'Polisi') !== false) {
                    $counts['Polsuska'] += $count;
                } elseif (stripos($roleName, 'Kondektur') !== false) {
                    $counts['Kondektur'] += $count;
                } elseif (stripos($roleName, 'TKA') !== false || stripos($roleName, 'Teknisi') !== false) {
                    $counts['TKA'] += $count;
                }
            }
            
            // \Illuminate\Support\Facades\Log::info('Calculated Counts:', $counts);

            $chartPieValues = array_values($counts);
            
            // Calculate percentages for the legend
            $totalScans = array_sum($chartPieValues);
            $chartPiePercentages = array_map(function($val) use ($totalScans) {
                return $totalScans > 0 ? round(($val / $totalScans) * 100) : 0;
            }, $chartPieValues);

            return view("pages.dashboard", compact(
                "trains", 
                "totalAdmin", 
                "totalUsers", 
                "totalSarana",
                "chartTrainTypeLabels",
                "chartTrainTypeValues",
                "chartPieLabels",
                "chartPieValues",
                "chartPiePercentages"
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

        // Filter by Date
        $query->whereDate('scan_reports.created_at', '>=', $startDate)
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

        // 2. Initialize Role Data Arrays & Detail Maps
        $polsuskaData = array_fill(0, count($dates), 0);
        $kondekturData = array_fill(0, count($dates), 0);
        $tkaData = array_fill(0, count($dates), 0);

        // Arrays to hold the string details for the tooltip
        // access via $polsuskaDetails[$dateIndex] => "TrainA(10): 5, TrainB(20): 3"
        $polsuskaDetails = array_fill(0, count($dates), []);
        $kondekturDetails = array_fill(0, count($dates), []);
        $tkaDetails = array_fill(0, count($dates), []);

        // 3. Map Query Result to Arrays
        foreach ($data as $row) {
            $dateIndex = array_search($row->date, $dates);
            if ($dateIndex !== false) {
                // Build Detail String: "TrainName(NoKA): Total"
                $detailStr = $row->train_name . '(' . $row->no_ka . '): ' . $row->total;

                if (stripos($row->role_name, 'Polsuska') !== false || stripos($row->role_name, 'Polisi') !== false) {
                    $polsuskaData[$dateIndex] += $row->total;
                    $polsuskaDetails[$dateIndex][] = $detailStr;
                } elseif (stripos($row->role_name, 'Kondektur') !== false) {
                    $kondekturData[$dateIndex] += $row->total;
                    $kondekturDetails[$dateIndex][] = $detailStr;
                } elseif (stripos($row->role_name, 'TKA') !== false || stripos($row->role_name, 'Teknisi') !== false) {
                    $tkaData[$dateIndex] += $row->total;
                    $tkaDetails[$dateIndex][] = $detailStr;
                }
            }
        }

        $chartData = [
            'labels' => $labels,
            'polsuska' => $polsuskaData,
            'polsuskaDetails' => $polsuskaDetails, 
            'kondektur' => $kondekturData,
            'kondekturDetails' => $kondekturDetails,
            'tka' => $tkaData,
            'tkaDetails' => $tkaDetails
        ];

        return view('pages.dashboard.scanKA', compact('trains', 'chartData', 'startDate', 'endDate', 'trainId'));
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

        // Filter by Date
        $query->whereDate('scan_reports.created_at', '>=', $startDate)
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
}
