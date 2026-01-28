<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Train;
use App\Models\ScanReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request) 
    {
        $roles = Auth::user()->getRoleNames()[0];
        
        if ($roles != "Admin") {
            $train = Train::orderBy('id', 'DESC')->get();
            return view("pages.dashboard", compact("train"));
        } else {
            // Ambil data kereta dengan laporan scan (grouped by train)
            $search = $request->get('search');
            
            $trains = Train::with(['scan_reports' => function($q) {
                $q->with(['user', 'schedule', 'verifications'])
                  ->orderBy('created_at', 'DESC');
            }])->orderBy('name', 'ASC')->get();
            
            // Filter jika ada search
            if ($search) {
                $trains = $trains->filter(function($train) use ($search) {
                    return stripos($train->name, $search) !== false;
                });
            }
                
            return view("pages.dashboard", compact("trains"));
        }
    }
}
