<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekapWaktuKereta extends Model
{
    // Arahkan ke nama tabel yang benar
    protected $table = 'rekap_waktu_kereta';
    
    protected $guarded = ['id'];

    // Relasi
    public function user() { return $this->belongsTo(User::class); }
    public function schedule() { return $this->belongsTo(Schedule::class); }
    public function scan_report() { return $this->belongsTo(ScanReport::class); }

    // Helper untuk Blade / Excel (Format Jam:Menit:Detik)
    public function getDurasiFormattedAttribute()
    {
        return gmdate('H:i:s', $this->durasi_detik);
    }

    public function getJarakFormattedAttribute()
    {
        return gmdate('H:i:s', $this->jarak_waktu_detik);
    }
}