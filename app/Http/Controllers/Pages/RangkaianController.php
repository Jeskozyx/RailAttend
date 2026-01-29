<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rangkaian;
use App\Models\Train;
use Illuminate\Support\Str;

class RangkaianController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'train_id' => 'required|exists:trains,id',
            'type' => 'required|string',
            'jumlah' => 'required|numeric|min:1',
            'start_number' => 'required|numeric'
        ]);

        $train = Train::findOrFail($request->train_id);
        $jumlah = $request->jumlah;
        $startUrutan = $request->start_number;

        Rangkaian::where('train_id', $request->train_id)
            ->where('urutan', '>=', $startUrutan)
            ->orderBy('urutan', 'desc')
            ->each(function ($rangkaian) use ($jumlah) {
                $rangkaian->urutan += $jumlah;
                $rangkaian->save();
            });

        for ($i = 0; $i < $jumlah; $i++) {
            $urutanBaru = $startUrutan + $i;
            $nomorGerbong = $startUrutan + $i;

            $labelTipe = $request->nama_tipe;
            if (empty($labelTipe)) {
                $labelTipe = match($request->type) {
                    'EKS' => 'Eksekutif',
                    'EKO' => 'Ekonomi',
                    'LUX' => 'Luxury',
                    'KMP' => 'Kereta Makan',
                    'BP' => 'Pembangkit',
                    default => $request->type,
                };
            }

            if ($request->type == 'KMP' || $request->type == 'BP') {
                $finalName = $labelTipe;
            } else {
                $finalName = $labelTipe . ' ' . $nomorGerbong;
            }

            $qrRaw = 'KAI-' . Str::upper(Str::random(6));

            Rangkaian::create([
                'train_id' => $request->train_id,
                'name' => $finalName,
                'type' => $request->type,
                'urutan' => $urutanBaru,
                'qr_code' => $qrRaw
            ]);
        }

        return back()->with('success', 'Berhasil menambahkan ' . $request->jumlah . ' rangkaian (+QR Code)!');
    }

    public function destroy($id)
    {
        $rangkaian = Rangkaian::findOrFail($id);
        
        // Hapus file QR Code jika ada
        if ($rangkaian->qr_code) {
            $filename = $rangkaian->qr_code . '.svg';
            $path = public_path('assets/images/qr/' . $filename);
            
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $rangkaian->delete();
        return back()->with('success', 'Rangkaian berhasil dihapus (QR Code juga dihapus)!');
    }
    
    public function printQr($train_id)
    {
        $train = Train::with(['rangkaians' => function($q) {
            $q->orderBy('urutan', 'asc');
        }])->findOrFail($train_id);

        foreach ($train->rangkaians as $rangkaian) {
            
            if (empty($rangkaian->qr_code)) {
                $qrRaw = 'KAI-' . Str::upper(Str::random(6));
                $rangkaian->qr_code = $qrRaw;
                $rangkaian->save();
            }

            if ($rangkaian->qr_code) {
                $filename = $rangkaian->qr_code . '.svg';
                $path = public_path('assets/images/qr/' . $filename);
                
                if (!file_exists($path)) {
                    $qrImage = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                        ->size(300)
                        ->margin(2)
                        ->merge(public_path('assets/images/kai_logo.png'), 0.3, true)
                        ->generate($rangkaian->qr_code);
                    
                    file_put_contents($path, $qrImage);
                }
            }
        }

        return view('pages.kereta.goscan', compact('train'));
    }
}