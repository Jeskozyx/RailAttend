<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Train;
use App\Models\User;
use App\Models\Rangkaian;
use App\Models\Verification;
use App\Models\ScanReport; // Pastikan Model ini sudah dibuat
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    /**
     * ==========================================
     * BAGIAN ADMIN (MANAJEMEN JADWAL)
     * ==========================================
     */

    public function index(Request $request)
    {
        $query = Train::with(['schedules' => function($q) {
            $q->orderBy('date', 'desc')->orderBy('departure_time', 'asc');
        }]);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('schedules', function($subQ) use ($search) {
                      $subQ->where('no_ka', 'like', "%{$search}%")
                           ->orWhere('origin', 'like', "%{$search}%")
                           ->orWhere('destination', 'like', "%{$search}%");
                  });
            });
        }

        $sort = $request->get('sort', 10);
        $trains = $query->paginate($sort);

        return view('pages.jadwal.index', compact('trains'));
    }

    public function create()
    {
        $trains = Train::all();
        $kondekturs = User::role('Kondektur')->get();
        return view('pages.jadwal.create', compact('trains', 'kondekturs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'train_id' => 'required|exists:trains,id',
            'no_ka' => 'required|string',
            'origin' => 'required|string',
            'destination' => 'required|string',
            'date' => 'required|date',
            'departure_time' => 'required',
            'arrival_time' => 'required',
        ]);

        Schedule::create($request->all());

        return redirect()->route('schedule.index')->with('success', 'Jadwal berhasil ditambahkan');
    }

    public function edit(string $id)
    {
        $schedule = Schedule::findOrFail($id);
        $trains = Train::all();
        $kondekturs = User::role('Kondektur')->get();
        return view('pages.jadwal.edit', compact('schedule', 'trains', 'kondekturs'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'train_id' => 'required|exists:trains,id',
            'no_ka' => 'required|string',
            'origin' => 'required|string',
            'destination' => 'required|string',
            'date' => 'required|date',
            'departure_time' => 'required',
            'arrival_time' => 'required',
        ]);

        $schedule = Schedule::findOrFail($id);
        $schedule->update($request->all());

        return redirect()->route('schedule.index')->with('success', 'Jadwal berhasil diperbarui');
    }

    public function destroy(string $id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('schedule.index')->with('success', 'Jadwal berhasil dihapus');
    }

    /**
     * ==========================================
     * BAGIAN KONDEKTUR (DINASAN & SCANNING)
     * ==========================================
     */

    // 1. Menampilkan Daftar Jadwal Dinas Kondektur
    public function jadwalKondektur(Request $request)
    {
        $trainId = $request->query('train_id');

        $query = Schedule::with('train')
            ->orderBy('date', 'desc')
            ->orderBy('departure_time', 'asc');

        // Filter jika diperlukan (opsional)
        if ($trainId) {
            $query->where('train_id', $trainId);
        }

        $jadwalDinas = $query->get();
        
        $title = 'Jadwal Dinasan';
        if ($trainId && $jadwalDinas->isNotEmpty()) {
             $title = $jadwalDinas->first()->train->name;
        }

        return view('pages.schedule.jadwal', compact('jadwalDinas', 'title'));
    }

    // 2. Menampilkan Daftar Gerbong dalam satu Jadwal
    public function showGerbong($id)
    {
        $schedule = Schedule::with(['train.rangkaians' => function($q) {
            $q->orderBy('urutan', 'asc');
        }])->findOrFail($id);
        
        // Cari Laporan Aktif (Status: Process). 
        // Jika tidak ada (misal baru mulai atau sudah submit sebelumnya), UI akan bersih/reset.
        $report = ScanReport::where('schedule_id', $id)
                            ->where('user_id', Auth::id())
                            ->where('status', 'process')
                            ->first();

        // Loop gerbong untuk menandai mana yang sudah discan DI SESI INI
        foreach($schedule->train->rangkaians as $rangkaian) {
            $verification = null;
            
            if ($report) {
                // Hanya cari verifikasi yang terikat ID Laporan ini
                $verification = Verification::where('scan_report_id', $report->id)
                                    ->where('rangkaian_id', $rangkaian->id)
                                    ->first();
            }

            $rangkaian->is_verified = $verification ? true : false;
            $rangkaian->verified_at = $verification ? $verification->verified_at : null;
        }

        return view('pages.schedule.gerbong', compact('schedule', 'report'));
    }

    // 3. Menampilkan Halaman Kamera/Scanner
    public function verifikasi($schedule_id, $rangkaian_id)
    {
        $schedule = Schedule::with(['train.rangkaians' => function($q) {
            $q->orderBy('urutan', 'asc');
        }])->findOrFail($schedule_id);

        // LOGIKA PENTING: Start Session
        // Cek apakah ada sesi laporan aktif? Jika tidak ada, buat baru (Reset otomatis terjadi di sini)
        $report = ScanReport::firstOrCreate(
            [
                'schedule_id' => $schedule_id,
                'train_id' => $schedule->train_id,
                'user_id' => Auth::id(),
                'status' => 'process'
            ]
        );

        $selectedGerbong = Rangkaian::findOrFail($rangkaian_id);
        
        // Cek status verifikasi semua gerbong terhadap REPORT ID yang aktif
        foreach($schedule->train->rangkaians as $rangkaian) {
            $verification = Verification::where('scan_report_id', $report->id)
                                ->where('rangkaian_id', $rangkaian->id)
                                ->first();

            $rangkaian->is_verified = $verification ? true : false;
            $rangkaian->verified_at = $verification ? $verification->verified_at : null;
        }

        return view('pages.schedule.verifikasi', compact('schedule', 'selectedGerbong', 'report'));
    }

    // 4. Proses AJAX Scan dari Kamera
    public function processScan(Request $request)
    {
        $request->validate([
            'qr_code' => 'required',
            'train_id' => 'required',
            'rangkaian_id' => 'required',
            'schedule_id' => 'required'
        ]);

        // A. Cari Sesi Laporan Aktif
        $report = ScanReport::where('schedule_id', $request->schedule_id)
                            ->where('user_id', Auth::id())
                            ->where('status', 'process')
                            ->first();

        if (!$report) {
            return response()->json(['status' => 'error', 'message' => 'Sesi laporan tidak aktif! Silakan refresh halaman.'], 404);
        }

        // B. Validasi QR Code
        $gerbong = Rangkaian::where('qr_code', $request->qr_code)->first();

        if (!$gerbong) {
            return response()->json(['status' => 'error', 'message' => 'QR Code tidak dikenali!'], 404);
        }

        // C. Validasi Kesesuaian Kereta
        if ($gerbong->train_id != $request->train_id) {
             return response()->json(['status' => 'error', 'message' => 'QR Code ini bukan milik kereta ini!'], 400);
        }

        // D. Validasi Gerbong Target
        if ($gerbong->id != $request->rangkaian_id) {
            return response()->json(['status' => 'error', 'message' => 'Salah Gerbong! Anda sedang memilih ' . Rangkaian::find($request->rangkaian_id)->name], 400);
        }

        // E. Cek Duplikasi di Sesi Ini
        $existing = Verification::where('scan_report_id', $report->id)
                                ->where('rangkaian_id', $gerbong->id)
                                ->exists();

        if ($existing) {
            return response()->json([
                'status' => 'warning',
                'message' => 'Gerbong ini sudah terverifikasi di sesi ini.'
            ]);
        }

        // F. Simpan Verifikasi Terikat ke Report ID
        Verification::create([
            'scan_report_id' => $report->id, // Kunci utama reset
            'schedule_id' => $request->schedule_id,
            'rangkaian_id' => $gerbong->id,
            'user_id' => Auth::id(),
            'verified_at' => Carbon::now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil! ' . $gerbong->name . ' terverifikasi.',
            'gerbong_id' => $gerbong->id
        ]);
    }

    // 5. Submit Laporan Akhir (Finalisasi & Reset)
    public function submitReport(Request $request)
    {
        // Cari laporan aktif yang mau disubmit
        $report = ScanReport::where('schedule_id', $request->schedule_id)
                            ->where('user_id', Auth::id())
                            ->where('status', 'process')
                            ->firstOrFail();

        // Hitung Kelengkapan
        $totalGerbong = $report->schedule->train->rangkaians->count();
        $totalScan = $report->verifications->count();

        // Validasi: Wajib Scan Semua
        if ($totalScan < $totalGerbong) {
            $sisa = $totalGerbong - $totalScan;
            return back()->with('error', "Gagal Submit! Masih ada $sisa gerbong yang belum discan.");
        }

        // Finalisasi Laporan
        $report->update([
            'status' => 'completed',
            'submitted_at' => Carbon::now()
        ]);

        // Karena status sudah 'completed', saat user masuk lagi, 
        // 'firstOrCreate' akan membuat ID baru -> Tampilan jadi RESET/BERSIH.

        // Ambil train_id untuk redirect dengan filter
        $trainId = $report->schedule->train_id;

        return redirect()->route('jadwal.view', ['train_id' => $trainId])->with('success', 'Laporan Dinasan Berhasil Dikirim & Diarsipkan.');
    }
}