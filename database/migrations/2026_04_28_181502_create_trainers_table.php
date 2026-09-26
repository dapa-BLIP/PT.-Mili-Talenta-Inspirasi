<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->string('nidn', 50)->unique()->nullable(); 
            $table->string('nama_mentor'); 
            $table->string('ttl')->nullable(); 
            $table->string('email')->unique()->nullable(); 
            $table->string('kontak', 20)->nullable();
            $table->string('program_pelatihan');
            $table->string('foto')->nullable(); 
            $table->string('status', 20)->default('AKTIF'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};