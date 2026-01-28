<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Train extends Model
{
    protected $table = 'train';
    protected $fillable = ['name'];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function rangkaians()
    {
        return $this->hasMany(Rangkaian::class);
    }

    public function scan_reports()
    {
        return $this->hasMany(ScanReport::class);
    }
}
