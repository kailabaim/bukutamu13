<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    use HasFactory;

    protected $table = 'orangtua';

    protected $fillable = [
        'nama_orangtua',
        'nama_siswa',
        'kelas',
        'alamat',
        'keperluan',
        'kontak',
        'guru_dituju',
        'waktu_kunjungan',
        'tanggal',
        'foto',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
} 