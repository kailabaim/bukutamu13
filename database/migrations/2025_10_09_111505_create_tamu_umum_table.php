<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tamu_umum', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nik')->nullable(); // Nomor KTP
            $table->string('no_telepon')->nullable();
            $table->text('alamat')->nullable();

            // Bisa referensi ke instansi, atau isi manual
            $table->foreignId('instansi_id')->nullable()->constrained('instansi')->onDelete('set null');
            $table->string('instansi_lain')->nullable(); // jika instansi tidak terdaftar

            $table->text('keperluan');
            $table->timestamp('waktu_datang')->nullable();
            $table->timestamp('waktu_pulang')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tamu_umum');
    }
};