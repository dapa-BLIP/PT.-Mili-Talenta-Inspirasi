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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->string('kategori'); // Menampung kategori: Bug, Saran, Performa
            $table->text('pesan');      // Menampung isi feedback log
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // FIX: Diubah menjadi 'feedbacks' jamak agar sinkron saat rollback
        Schema::dropIfExists('feedbacks'); 
    }
};