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

    /**
     * API endpoint for main dashboard stats (AJAX Polling)
     */
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
    }

    /**
     * Get train scan statistics for top 7 schedules
     */
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
        $staticColors = [
            'Polsuska' => '#F75026',
            'Kondektur' => '#00078A',
            'Teknisi Kereta Api' => '#1CC8CE',
        ];

        if (isset($staticColors[$roleName])) {
            return $staticColors[$roleName];
        }

        $hash = md5($roleName);
        $h = hexdec(substr($hash, 0, 2)) % 360;
        $s = 65 + (hexdec(substr($hash, 2, 2)) % 20);
        $l = 40 + (hexdec(substr($hash, 4, 2)) % 15);
        return "hsl($h, {$s}%, {$l}%)";
    }
}