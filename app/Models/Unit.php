<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'simutu_id',
        'kode_unit',
        'nama_unit',
        'deskripsi_unit',
        'status_unit',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status_unit', 'aktif');
    }
}
