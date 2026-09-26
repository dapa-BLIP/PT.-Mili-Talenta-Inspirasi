<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelatihan extends Model
{
    use HasFactory;

    protected $table = 'pelatihans';

    protected $fillable = [
    'nama_pelatihan',
    'jenis_pelatihan',
    'trainer_id',
    'komponen_nilai',
    'materi_ids',
    ];

protected $casts = [
    'komponen_nilai' => 'array',
    'materi_ids' => 'array',
    ];

    public function mentor()
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class, 'pelatihan_id');
    }
}