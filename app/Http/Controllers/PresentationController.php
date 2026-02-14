<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB; // Tambahan penting untuk DB::statement
use App\Models\User;
use App\Models\ScanReport;
use App\Models\Verification;
use App\Models\Schedule;
use App\Models\Rangkaian;
use App\Models\Train;
use App\Models\RekapWaktuKereta;

class PresentationController extends Controller
{
    // 1. TAMPILKAN HALAMAN
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'scans' => ScanReport::count(),
        ];

        return view('presentasi', compact('stats'));
    }

    // 2. GENERATE DATA (TOMBOL INITIATE)
    public function generate(Request $request)
    {
        set_time_limit(300); // 5 Menit max execution

        try {
            Artisan::call('db:seed', [
                '--class' => 'PresentationSeeder'
            ]);

            return redirect()->back()->with('success', 'Data berhasil digenerate! Sistem siap untuk demo.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan generate: ' . $e->getMessage());
        }
    }

    // 3. HAPUS DATA (TOMBOL PURGE) - INI YANG TADI HILANG
    public function reset(Request $request)
    {
        set_time_limit(300);

        try {
            // Matikan Foreign Key Check biar bisa truncate paksa
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            
            // Bersihkan semua tabel terkait
            RekapWaktuKereta::truncate();
            Verification::truncate();
            ScanReport::truncate();
            Schedule::truncate();
            Rangkaian::truncate();
            Train::truncate();
            
            // Hapus user dummy saja (sisakan admin asli jika ada)
            User::where('email', 'like', '%@railattend.simulasi')->delete();
            
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            return redirect()->back()->with('success', 'SUKSES! Semua data dummy telah dimusnahkan.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal reset data: ' . $e->getMessage());
        }
    }
}