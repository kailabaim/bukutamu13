<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TamuUmum extends Model
{
    use HasFactory;

    protected $table = 'tamu_umum'; // ganti dari 'tamu'

    protected $fillable = [
        'nama',
        'identitas',
        'keperluan',
        'guru_dituju',
        'kontak',
        'waktu_kunjungan',
        'tanggal_kunjungan',
        'foto',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'date',
    ];
}
