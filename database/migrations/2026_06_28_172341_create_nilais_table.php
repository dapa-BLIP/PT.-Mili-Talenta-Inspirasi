<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('pesertas')->onDelete('cascade');
            $table->foreignId('pelatihan_id')->constrained('pelatihans')->onDelete('cascade');
            $table->foreignId('trainer_id')->constrained('trainers')->onDelete('cascade'); // 🚀 DETAIL: Catat mentor pengampu kelas/penginput nilai
            $table->json('scores')->nullable(); // Menyimpan data dinamis seperti {"tugas": 85, "uts": 90, "praktik_lapangan": 95}
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Kunci unik gabungan: 1 peserta hanya punya 1 baris nilai per program pelatihan
            $table->unique(['peserta_id', 'pelatihan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};