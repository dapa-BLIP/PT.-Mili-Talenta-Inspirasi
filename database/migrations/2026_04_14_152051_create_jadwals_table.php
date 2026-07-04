<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->string('judul_kegiatan');
            $table->text('description')->nullable();
            
            // Kolom Foreign Key ke tabel trainers (Wajib ada!)
            $table->foreignId('trainer_id')->constrained('trainers')->onDelete('cascade');
            
            $table->string('status'); // 'on-going' atau 'complete'
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('jenis_pelaksanaan'); // 'offline' atau 'online'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};