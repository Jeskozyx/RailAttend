<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verification extends Model
{
    protected $fillable = [
        'schedule_id',
        'scan_report_id',
        'rangkaian_id',
        'user_id',
        'verified_at'
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function scan_report()
    {
        return $this->belongsTo(ScanReport::class);
    }

    public function rangkaian()
    {
        return $this->belongsTo(Rangkaian::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
