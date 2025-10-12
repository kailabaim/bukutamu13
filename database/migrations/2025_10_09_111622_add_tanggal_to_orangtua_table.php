<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orangtua', function (Blueprint $table) {
            $table->date('tanggal')->nullable(); // atau ->default(now()) jika selalu ada
        });
    }

    public function down(): void
    {
        Schema::table('orangtua', function (Blueprint $table) {
            $table->dropColumn('tanggal');
        });
    }
};