<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rangkaian extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'train_id',
        'name',
        'type',
        'urutan',
        'qr_code',
        'is_verified',
        'verified_at',
    ];

    public function train()
    {
        return $this->belongsTo(Train::class, 'train_id');
    }

    public function verifications()
    {
        return $this->hasMany(Verification::class);
    }
}
