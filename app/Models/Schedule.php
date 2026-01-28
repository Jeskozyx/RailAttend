<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'train_id',
        'user_id',
        'no_ka',
        'origin',
        'destination',
        'departure_time',
        'arrival_time',
        'date',
    ];


    public function scan_reports()
    {
        return $this->hasMany(ScanReport::class);
    }


    public function active_report()
    {
        return $this->hasMany(ScanReport::class)->where('status', 'process')->latest();
    }

    public function train()
    {
        return $this->belongsTo(Train::class, 'train_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
