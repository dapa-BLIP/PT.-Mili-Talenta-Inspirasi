<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Materi extends Model
{
    // Daftarin kolom database yang boleh diisi massal
    protected $fillable = [
        'nama_materi',
        'trainer_id',
        'sektor', // Sektor penentu filter interaktif
    ];

    /**
     * Relasi balik ke Trainer/Dosen
     * Menghubungkan tabel materis ke tabel trainers melalui trainer_id
     */
    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }
}