<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_materi');
            $table->foreignId('trainer_id')->nullable()->constrained('trainers')->onDelete('set null');
            $table->string('jenis_pelatihan'); // 'Pelatihan Teknis' atau 'Pelatihan Non Teknis'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materis');
    }
};