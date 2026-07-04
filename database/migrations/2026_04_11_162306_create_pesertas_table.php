<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
 public function up(): void
{
    Schema::create('pesertas', function (Blueprint $table) {
        $table->id();
        $table->string('nama_lengkap');
        $table->string('gender')->nullable(); // <-- TAMBAHIN BARIS INI (Gua set nullable dulu biar data lama ga bentrok)
        $table->string('ttl')->nullable();
        $table->string('email')->unique();
        $table->string('no_hp')->nullable();
        $table->string('tempat_kerja')->nullable();
        $table->string('pelatihan');
        $table->string('foto')->nullable();
        $table->string('status')->default('PENDING');
        $table->timestamps();
    });

}

    public function down(): void
    {
        Schema::dropIfExists('pesertas');
    }
};