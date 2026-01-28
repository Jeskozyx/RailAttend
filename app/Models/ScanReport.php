<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScanReport extends Model
{
    protected $fillable = [
        'schedule_id',
        'train_id',
        'user_id',
        'status',
        'notes',
        'submitted_at',
    ];

    // Relasi ke Kereta
    public function train()
    {
        return $this->belongsTo(Train::class);
    }

    // Relasi balik ke Jadwal
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    // Relasi balik ke Kondektur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke detail scan gerbong
    public function verifications()
    {
        return $this->hasMany(Verification::class);
    }
}