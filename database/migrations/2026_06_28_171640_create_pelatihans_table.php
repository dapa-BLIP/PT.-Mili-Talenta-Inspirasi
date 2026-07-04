<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pelatihans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelatihan');
            $table->string('spektrum_kategori'); // Pengetahuan Teknis / Pengetahuan Non Teknis
            $table->foreignId('trainer_id')->constrained('trainers')->onDelete('cascade');
            $table->json('komponen_nilai')->nullable(); // Tempat nampung array ["TUGAS","UTS"]
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pelatihans');
    }
};