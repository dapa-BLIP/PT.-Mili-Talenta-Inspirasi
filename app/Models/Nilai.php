<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $fillable = [
        'peserta_id',
        'pelatihan_id',
        'trainer_id',
        'scores',
        'catatan'
    ];

    protected $casts = [
        'scores' => 'array' // 🚀 KUNCI PINTAR: Otomatis casting data JSON nilai bolak-balik
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class, 'peserta_id');
    }

    public function pelatihan()
    {
        return $this->belongsTo(Pelatihan::class, 'pelatihan_id');
    }

    public function mentor()
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }
}