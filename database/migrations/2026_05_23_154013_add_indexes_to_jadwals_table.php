<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            // Menambahkan index pada kolom pencarian tanggal agar query filter ngebut
            $table->index('tanggal_mulai');
            $table->index('tanggal_selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwals', function (Blueprint $table) {
            // Drop index jika migration di-rollback
            $table->dropIndex(['tanggal_mulai']);
            $table->dropIndex(['tanggal_selesai']);
        });
    }
};