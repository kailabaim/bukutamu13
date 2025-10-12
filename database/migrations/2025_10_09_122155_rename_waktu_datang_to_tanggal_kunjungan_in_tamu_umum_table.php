<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tamu_umum', function (Blueprint $table) {
            // Ganti nama kolom 'waktu_datang' menjadi 'tanggal_kunjungan'
            $table->renameColumn('waktu_datang', 'tanggal_kunjungan');
        });
    }

    public function down(): void
    {
        Schema::table('tamu_umum', function (Blueprint $table) {
            $table->renameColumn('tanggal_kunjungan', 'waktu_datang');
        });
    }
};