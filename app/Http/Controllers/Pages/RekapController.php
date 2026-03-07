<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\RekapWaktuKereta; // Pakai Model Baru
use App\Models\User;
use App\Models\Schedule;
use App\Models\Train;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Services\TimeGapService;
// use Maatwebsite\Excel\Facades\Excel; // Removed
use App\Exports\RekapWaktuExport;

class RekapController extends Controller
{
    public function export(Request $request)
    {
        $dateStr = date('Y-m-d_H-i');
        // Instansiasi class export dan panggil method download
        return (new RekapWaktuExport($request))->download("rekap_waktu_{$dateStr}.xlsx");
    }

    public function index(Request $request)
    {
        // 1. Data Pendukung Filter
        $users = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'Admin'))->orderBy('name')->get();
        $roles = Role::where('name', '!=', 'Admin')->orderBy('name')->get();
        
        // Eager load schedules to build grouped dropdown
        $trains = Train::with(['schedules' => function($q) {
            $q->orderBy('no_ka');
        }])->orderBy('name')->get();
        
        // $schedules = Schedule::with('train')->orderBy('no_ka')->get(); // No longer needed as separate list

        // 2. Query ke Tabel Baru (Cepat!)
        $query = RekapWaktuKereta::with([
            'user.roles',
            'schedule.train',
            'scan_report',
            'scan_report.verifications:id,scan_report_id,rangkaian_id,verified_at',
            'scan_report.verifications.rangkaian:id,name'
        ])
        ->join('users','rekap_waktu_kereta.user_id','=','users.id')
        ->join('schedules','rekap_waktu_kereta.schedule_id','=','schedules.id')
        ->join('trains','schedules.train_id','=','trains.id')
        ->select('rekap_waktu_kereta.*');

        // Filter-filter
        // Filter Logic for Merged Dropdown (schedule_id can be "train_X" or "X")
        if ($request->schedule_id) {
            if (str_starts_with($request->schedule_id, 'train_')) {
                // Formatting "train_ID" -> Filter by Train
                $trainId = str_replace('train_', '', $request->schedule_id);
                $query->where('schedules.train_id', $trainId);
            } else {
                // Start with numeric -> Filter by specific Schedule
                $query->where('rekap_waktu_kereta.schedule_id', $request->schedule_id);
            }
        }

        // if ($request->train_id) $query->where('schedules.train_id', $request->train_id); // Removed separate filter
        
        // Filter by User
        if ($request->user_id) {
            $query->where('rekap_waktu_kereta.user_id', $request->user_id);
        }

        // Default Date Range: Today if not specified
        $dateFrom = $request->date_from ?? now()->format('Y-m-d');
        $dateTo = $request->date_to ?? now()->format('Y-m-d');

        // Batasi range tanggal max 31 hari untuk mencegah memory exhaustion
        $dateFromCarbon = \Carbon\Carbon::parse($dateFrom);
        $dateToCarbon = \Carbon\Carbon::parse($dateTo);
        if ($dateFromCarbon->diffInDays($dateToCarbon) > 31) {
            $dateTo = $dateFromCarbon->copy()->addDays(31)->format('Y-m-d');
        }
        
        $query->whereDate('rekap_waktu_kereta.tanggal', '>=', $dateFrom);
        $query->whereDate('rekap_waktu_kereta.tanggal', '<=', $dateTo);
        
        if ($request->role_id) {
            $query->whereHas('user.roles', function($q) use ($request) {
                $q->where('roles.id', $request->role_id);
            });
        }

        // Urutkan (Terlama di atas agar Putaran 1 duluan)
        // Safety limit: max 5000 records untuk mencegah crash
        $rekapModels = $query
        ->orderBy('users.name','asc')
        ->orderBy('rekap_waktu_kereta.waktu_awal', 'asc')
        ->limit(5000)
        ->get();

        // 3. Transform Data untuk View (Group by Session Sequential)
        // Gunakan chunkWhile agar sesi yang lintas hari atau jeda panjang terpisah dengan benar
        // Aturan: Sesi dianggap sama jika User, Schedule, SesiKe sama DAN jarak antar ronde < 7 jam
        $grouped = $rekapModels->chunkWhile(function ($curr, $key, $chunk) {
            $prev = $chunk->last();
            $sameUser = $curr->user_id === $prev->user_id;
            $sameSchedule = $curr->schedule_id === $prev->schedule_id;
            $sameSesi = $curr->sesi_ke === $prev->sesi_ke;
            
            
            // Gunakan Service untuk cek kontinuitas sesi (Logika 7 Jam)
            $calc = TimeGapService::calculateSessionGap($curr->waktu_awal, $prev->waktu_akhir);
            $isContinuous = !$calc['is_new_session']; 

            return $sameUser && $sameSchedule && $sameSesi && $isContinuous;
        });

        $rekap = $grouped->map(function ($items) {
            $firstItem = $items->first();
            $lastItem = $items->last(); // Assuming ordered by time

            // User & Schedule Info
            $user = $firstItem->user;
            $roleName = $user?->roles->first()?->name ?? '-';
            $schedule = $firstItem->schedule;
            $trainName = $schedule?->train?->name ?? '-';

            // Dynamic Color Generation (Moved here so it's available for rounds)
            $hue = ($firstItem->sesi_ke * 137.508) % 360; 
            $sessionColor = "hsl({$hue}, 70%, 45%)";

            // Process Rounds First (to get accurate computed gaps)
            // Use values() first to reset keys, then map over the NEW collection
            $roundsData = $items->values();
            $rounds = $roundsData->map(function ($item, $key) use ($roundsData, $sessionColor) {
                 // 1. Build details array for child rows with Gaps
                $details = [];
                $submitTimeCarbon = null;

                if ($item->scan_report && $item->scan_report->verifications) {
                    $verifications = $item->scan_report->verifications->sortBy('verified_at')->values();
                    
                    // --- REVISI LOGIKA SUBMIT TIME ---
                    // Prioritas 1: Ambil waktu user KLIK TOMBOL (dari database submitted_at atau updated_at)
                    if ($item->scan_report->submitted_at) {
                        $submitTimeCarbon = \Carbon\Carbon::parse($item->scan_report->submitted_at);
                    } 
                    elseif ($item->scan_report->updated_at) {
                        $submitTimeCarbon = $item->scan_report->updated_at;
                    }
                    // Prioritas 2: Jika data error/kosong, baru ambil scan terakhir (Fallback)
                    else {
                        $lastScan = $verifications->last();
                        if ($lastScan) {
                            $submitTimeCarbon = \Carbon\Carbon::parse($lastScan->verified_at);
                        }
                    }

                    foreach ($verifications as $idx => $verification) {
                        $nextVerif = $verifications->get($idx + 1);
                        $gapToNextFormatted = null;

                        if ($nextVerif) {
                            $currentScanTime = \Carbon\Carbon::parse($verification->verified_at);
                            $nextScanTime = \Carbon\Carbon::parse($nextVerif->verified_at);
                            // Ensure positive gap (Next - Current)
                            $gapSeconds = abs($nextScanTime->diffInSeconds($currentScanTime));
                            $gapToNextFormatted = $this->formatDuration($gapSeconds);
                        }

                        $details[] = [
                            'nama_gerbong' => $verification->rangkaian->name ?? '-',
                            'waktu_scan' => \Carbon\Carbon::parse($verification->verified_at)->format('H:i:s'),
                            'gap_to_next' => $gapToNextFormatted
                        ];
                    }
                }

                // Fallback for Submit Time if no scans or whatever
                if (!$submitTimeCarbon) {
                     // Try updated_at or fallback to waktu_akhir
                     $submitTimeCarbon = $item->scan_report ? $item->scan_report->updated_at : \Carbon\Carbon::parse($item->waktu_akhir);
                }

                // 2. Calculate Round Gap (Start Current - Submit Previous)
                $prevItem = $roundsData->get($key - 1);
                $gapSeconds = 0;
                $gapFormatted = '-';

                if ($prevItem) {
                    // --- PERBAIKAN LOGIKA GAP ---
                    // Ambil waktu Selesai (Submit) dari ronde sebelumnya dengan benar
                    $prevSubmitTime = null;

                    // Cek apakah previous item punya data scan report & submitted_at
                    if ($prevItem->scan_report) {
                        if ($prevItem->scan_report->submitted_at) {
                            // Prioritas 1: Waktu Klik Tombol Submit
                            $prevSubmitTime = \Carbon\Carbon::parse($prevItem->scan_report->submitted_at);
                        } elseif ($prevItem->scan_report->updated_at) {
                            // Prioritas 2: Waktu Update Terakhir
                            $prevSubmitTime = $prevItem->scan_report->updated_at;
                        }
                    }

                    // Fallback: Jika tidak ada data submit, baru pakai scan terakhir atau waktu akhir rekap
                    if (!$prevSubmitTime) {
                        $prevVerifs = $prevItem->scan_report ? $prevItem->scan_report->verifications : null;
                        if ($prevVerifs && $prevVerifs->count() > 0) {
                            $prevSubmitTime = \Carbon\Carbon::parse($prevVerifs->sortBy('verified_at')->last()->verified_at);
                        } else {
                            $prevSubmitTime = \Carbon\Carbon::parse($prevItem->waktu_akhir);
                        }
                    }
                    // -----------------------------

                    $currStart = \Carbon\Carbon::parse($item->waktu_awal);
                    
                    // Gap should be positive (Current Start > Prev Submit)
                    $gapSeconds = abs($currStart->diffInSeconds($prevSubmitTime));
                    $gapFormatted = $this->formatDuration($gapSeconds);
                } else {
                    $gapFormatted = 'Awal Putaran';
                }
                
                return [
                    'id' => $item->id,
                    'ronde' => $key + 1, // Force sequential 1-based index (Fixes "Putaran 9" issue)
                    'waktu_awal' => \Carbon\Carbon::parse($item->waktu_awal)->format('H:i:s'),
                    'waktu_akhir' => \Carbon\Carbon::parse($item->waktu_akhir)->format('H:i:s'),
                    'durasi_detik' => $item->durasi_detik,
                    'durasi_formatted' => $this->formatDuration($item->durasi_detik),
                    'jarak_waktu_detik' => $gapSeconds,
                    'jarak_waktu_formatted' => $gapFormatted,
                    'status' => $item->status,
                    'notes' => $item->scan_report->notes ?? null,
                    'details' => $details,
                    'waktu_submit' => $submitTimeCarbon->format('H:i:s'),
                    // Inherit session info for consistency/debugging
                    'train_name' => $item->schedule?->train?->name ?? '-',
                    'no_ka' => $item->schedule?->no_ka ?? '-',
                ];
            });

            // Session Aggregates (Recalculated from Processed Rounds)
            $waktuAwal = $items->min('waktu_awal');
            $waktuAkhir = $items->max('waktu_akhir');
            
            // "Duration of Session" = Start of First Round to End of Last Round
            $start = \Carbon\Carbon::parse($waktuAwal);
            $end = \Carbon\Carbon::parse($waktuAkhir);
            $durasiSesiSeconds = $end->diffInSeconds($start); 
            $totalActiveDuration = $items->sum('durasi_detik'); 
            
            // Average Gap Calculation (Using processed data)
            $validGaps = $rounds->where('jarak_waktu_detik', '>', 0);
            $avgGap = $validGaps->count() > 0 ? round($validGaps->avg('jarak_waktu_detik')) : 0;




            return [
                'session_id' => $firstItem->id, // Use first item ID as unique key for session row
                'sesi_ke' => $firstItem->sesi_ke,
                'tanggal' => \Carbon\Carbon::parse($firstItem->tanggal)->format('d/m/Y'),
                'waktu_awal' => \Carbon\Carbon::parse($waktuAwal)->format('H:i:s'),
                'waktu_akhir' => \Carbon\Carbon::parse($waktuAkhir)->format('H:i:s'),
                'durasi_sesi' => $this->formatDuration($totalActiveDuration), // Sum of rounds
                'rerata_gap' => $this->formatDuration($avgGap),
                'session_color' => $sessionColor,
                'rounds' => $rounds,
                'jumlah_putaran' => $items->count(),
                
                // User Info
                'user_name' => $user?->name ?? '-',
                'user_id' => $user?->id,
                'user_nipp' => $user?->nipp ?? '-',
                'user_jabatan' => $roleName,
                'avatar_url' => $user?->avatar_url,
                
                // Train Info
                'train_name' => $trainName,
                'no_ka' => $schedule?->no_ka ?? '-',
                'schedule_id' => $firstItem->schedule_id,
                'schedule_info' => $schedule ? ($schedule->no_ka . ' (' . $schedule->origin . ' → ' . $schedule->destination . ')') : '-',
            ];
        }); // End Map



        // 4. Hitung Summary (Statistik)
        $userAverages = [];
        $totalUserAverages = 0;
        $validUsersCount = 0;
        $totalRounds = $rekapModels->count();

        // Group RAW MODELS by User ID first (to get accurate round stats)
        $groupedByUser = $rekapModels->groupBy('user_id');

        foreach ($groupedByUser as $uid => $items) {
            // $items is Collection of RekapWaktuKereta Models
            
            // Logikanya sama dengan sebelumnya, tapi pakai model asli
            $validGaps = $items->where('jarak_waktu_detik', '>', 0);
            $countValid = $validGaps->count();
            $sumValid = $validGaps->sum('jarak_waktu_detik');
            
            $avgSeconds = $countValid > 0 ? round($sumValid / $countValid) : 0;
            
            // Build Schedule Summary
            $schedulesStats = [];
            $groupedBySchedule = $items->groupBy('schedule_id');
            
            foreach ($groupedBySchedule as $schId => $schItems) {
                $schValid = $schItems->where('jarak_waktu_detik', '>', 0);
                $schCount = $schValid->count();
                $schSum = $schValid->sum('jarak_waktu_detik');
                $schAvg = $schCount > 0 ? round($schSum / $schCount) : 0;
                
                // Tooltip text per Session (matches table rows)
                $schSessions = $schItems->chunkWhile(function ($curr, $key, $chunk) {
                    $prev = $chunk->last();
                    $sameUser = $curr->user_id === $prev->user_id;
                    $sameSchedule = $curr->schedule_id === $prev->schedule_id;
                    $sameSesi = $curr->sesi_ke === $prev->sesi_ke;
                    
                    $prevEnd = \Carbon\Carbon::parse($prev->waktu_akhir);
                    $currStart = \Carbon\Carbon::parse($curr->waktu_awal);
                    $gapSeconds = $currStart->diffInSeconds($prevEnd, false); 
                    $isContinuous = $gapSeconds <= (7 * 3600); 

                    return $sameUser && $sameSchedule && $sameSesi && $isContinuous;
                });

                $datesText = $schSessions->map(function($sessionItems) {
                     $firstItem = $sessionItems->first();
                     $date = \Carbon\Carbon::parse($firstItem->tanggal)->format('Y-m-d');
                     $sesiKe = $firstItem->sesi_ke;
                     
                     $dValid = $sessionItems->where('jarak_waktu_detik', '>', 0);
                     $dCount = $dValid->count();
                     $dSum = $dValid->sum('jarak_waktu_detik');
                     $dAvg = $dCount > 0 ? round($dSum / $dCount) : 0;
                     $dFormatted = $this->formatDuration($dAvg);
                     
                     // Show total rounds involved (including start) or just valid gaps?
                     // Usually "X Putaran" implies total rounds
                     $totalRounds = $sessionItems->count();
                     
                     return "Sesi $sesiKe ($date): $dFormatted ($totalRounds Putaran)";
                })->values()->implode(' &#013; ');

                $firstItem = $schItems->first();
                $schName = ($firstItem->schedule->train->name ?? '-') . ' (' . ($firstItem->schedule->no_ka ?? '-') . ')';

                $schedulesStats[$schName] = [
                    'rerata_formatted' => $this->formatDuration($schAvg),
                    'jumlah_ronde' => $schItems->count(),
                    'tooltip_text' => $datesText
                ];
            }

            $firstItem = $items->first();
            $user = $firstItem->user;
            
            // Hitung Jumlah Sesi Unik (User + Schedule + SesiKe + Sequential Gap)
            // Gunakan logika yang sama dengan transformasi view
            $uniqueSessions = $items->chunkWhile(function ($curr, $key, $chunk) {
                $prev = $chunk->last();
                $sameUser = $curr->user_id === $prev->user_id;
                $sameSchedule = $curr->schedule_id === $prev->schedule_id;
                $sameSesi = $curr->sesi_ke === $prev->sesi_ke;
                
                $prevEnd = \Carbon\Carbon::parse($prev->waktu_akhir);
                $currStart = \Carbon\Carbon::parse($curr->waktu_awal);
                $gapSeconds = $currStart->diffInSeconds($prevEnd, false); 
                $isContinuous = $gapSeconds <= (7 * 3600); 

                return $sameUser && $sameSchedule && $sameSesi && $isContinuous;
            })->count();

            $userAverages[$uid] = [
                'user_name' => $user->name,
                'user_jabatan' => $user->roles->first()?->name ?? '-',
                'avatar_url' => $user->avatar_url,
                'rerata_formatted' => $this->formatDuration($avgSeconds),
                'rerata_detik' => $avgSeconds,
                'jumlah_ronde' => $items->count(),
                'jumlah_sesi' => $uniqueSessions,
                'schedules' => $schedulesStats
            ];

            if ($avgSeconds > 0) {
                $totalUserAverages += $avgSeconds;
                $validUsersCount++;
            }
        }

        $rerataJarakWaktu = $validUsersCount > 0 ? round($totalUserAverages / $validUsersCount) : 0;
        $rerataJarakWaktuFormatted = $this->formatDuration($rerataJarakWaktu);

        // 5. Return View
        return view('pages.rekap.index', [
            'rekap' => $rekap, // Now strictly an array/collection of arrays (SESSIONS)
            'users' => $users,
            'roles' => $roles,
            'trains' => $trains, // Passing trains with eager loaded schedules
            // 'schedules' => $schedules, // Removed
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'userId' => $request->user_id,
            // 'trainId' => $request->train_id, // Removed
            'scheduleId' => $request->schedule_id,
            'roleId' => $request->role_id,
            'userAverages' => $userAverages,
            'rerataJarakWaktu' => $rerataJarakWaktu,
            'rerataJarakWaktuFormatted' => $rerataJarakWaktuFormatted,
            'totalRounds' => $totalRounds
        ]);
    }

    /**
     * Helper to format seconds into human readable string
     */
    private function formatDuration($seconds)
    {
        if ($seconds < 60) {
            return $seconds . ' Detik';
        } elseif ($seconds < 3600) {
            $menit = floor($seconds / 60);
            $detik = $seconds % 60;
            return $detik > 0 
                ? $menit . ' Menit ' . $detik . ' Detik'
                : $menit . ' Menit';
        } else {
            $jam = floor($seconds / 3600);
            $sisaDetik = $seconds % 3600;
            $menit = floor($sisaDetik / 60);
            $detik = $sisaDetik % 60;
            $result = $jam . ' Jam';
            if ($menit > 0) {
                $result .= ' ' . $menit . ' Menit';
            }
            if ($detik > 0) {
                $result .= ' ' . $detik . ' Detik';
            }
            return $result;
        }
    }
}