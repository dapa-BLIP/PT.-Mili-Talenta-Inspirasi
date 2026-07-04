<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwals';

    protected $fillable = [
        'judul_kegiatan',
        'description',
        'trainer_id',
        'status', // Tetap di sini gak apa-apa buat amanin fillable lama
        'tanggal_mulai',
        'tanggal_selesai',
        'jam_mulai',
        'jam_selesai',
        'jenis_pelaksanaan',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * ACCESSOR STATUS OTOMATIS
     * Dipanggil otomatis lewat $jadwal->status di Blade
     */
    public function getStatusAttribute()
    {
        // 1. Pastikan data tanggal dan jam selesai ada
        if (!$this->tanggal_selesai || !$this->jam_selesai) {
            return 'on-going';
        }

        // 2. Gabungkan tanggal selesai dan jam selesai menjadi satu objek Datetime penuh
        // Contoh hasil: "2026-05-23 15:30:00"
        $waktuSelesaiFormat = $this->tanggal_selesai->format('Y-m-d') . ' ' . $this->jam_selesai;
        $waktuSelesai = Carbon::parse($waktuSelesaiFormat);

        // 3. Bandingkan dengan waktu server saat ini
        // Jika waktu sekarang sudah MELEWATI waktu selesai, otomatis 'complete'
        if (Carbon::now()->greaterThan($waktuSelesai)) {
            return 'complete';
        }

        return 'on-going';
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }
}