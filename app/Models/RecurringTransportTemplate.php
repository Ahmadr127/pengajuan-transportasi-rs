<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringTransportTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis',
        'keperluan',
        'prioritas',
        'pemohon_nama',
        'pemohon_unit',
        'jumlah_penumpang',
        'jam',
        'jam_sampai',
        'hari',
        'start_date',
        'end_date',
        'alamat_asal',
        'alamat_tujuan',
        'keterangan',
        'pasien_nama',
        'pasien_no_rm',
        'is_active',
    ];

    protected $casts = [
        'hari' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
