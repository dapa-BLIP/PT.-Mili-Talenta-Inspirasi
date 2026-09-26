<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peserta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_lengkap',
        'gender',
        'ttl', 
        'email',
        'no_hp',
        'perusahaan_id',
        'tempat_kerja',
        'pelatihan',
        'foto',
        'status'
    ];

    public function perusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id');
    }

    public function nilai()
    {
        return $this->hasOne(Nilai::class);
    }
    
    public function materis()
    {
        return $this->belongsToMany(Materi::class, 'peserta_materi');
    }
}