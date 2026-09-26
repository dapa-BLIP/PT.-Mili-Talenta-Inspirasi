<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perusahaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan')->unique();
            $table->string('penanggung_jawab')->nullable(); // PIC / HRD
            $table->string('kontak_pic')->nullable();
            $table->string('email_perusahaan')->nullable();
            $table->text('alamat')->nullable();
            $table->enum('status', ['AKTIF', 'PENDING', 'NON-AKTIF'])->default('AKTIF');
            $table->timestamps();
        });

        if (Schema::hasTable('pesertas') && !Schema::hasColumn('pesertas', 'perusahaan_id')) {
            Schema::table('pesertas', function (Blueprint $table) {
                $table->foreignId('perusahaan_id')->nullable()->constrained('perusahaans')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pesertas') && Schema::hasColumn('pesertas', 'perusahaan_id')) {
            Schema::table('pesertas', function (Blueprint $table) {
                $table->dropForeign(['perusahaan_id']);
                $table->dropColumn('perusahaan_id');
            });
        }
        Schema::dropIfExists('perusahaans');
    }
};