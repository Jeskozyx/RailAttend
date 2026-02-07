<?php

namespace App\Http\Controllers\Pages\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Train;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanPerDinasController extends Controller
{
    /**
     * Display Scan Per Dinas page
     */
    public function index(Request $request)
    {
        $trains = Train::orderBy('name', 'ASC')->get();
        
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        $chartData = $this->buildChartData($startDate, $endDate, $trainId);

        return view('pages.dashboard.scanpPERDINAS', compact('trains', 'chartData', 'startDate', 'endDate', 'trainId'));
    }

    /**
     * API endpoint for Scan Per Dinas chart data (AJAX Polling)
     */
    public function getStats(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $trainId = $request->input('train_id');

        return response()->json($this->buildChartData($startDate, $endDate, $trainId));
    }

    /**
     * Build chart data for Scan Per Dinas
     */
    private function buildChartData($startDate, $endDate, $trainId = null)
    {
        $query = DB::table('scan_reports')
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

        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }
}
