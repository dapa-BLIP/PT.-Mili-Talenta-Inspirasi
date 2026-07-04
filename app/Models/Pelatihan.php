<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelatihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_pelatihan', 
        'spektrum_kategori', 
        'trainer_id', 
        'komponen_nilai'
    ];

    protected $casts = [
        'komponen_nilai' => 'array' // Otomatis mengubah JSON di DB menjadi array PHP
    ];

    /**
     * 🚀 SINKRONISASI: Relasi ke Master Mentor
     * Menggunakan model Trainer (tabel trainers) namun dengan nama method 'mentor'
     */
    public function mentor()
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }
}