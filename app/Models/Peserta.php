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
        'tempat_kerja',
        'pelatihan',
        'foto',
        'status'
    ];

    public function nilai()
    {
        return $this->hasOne(Nilai::class);
    }
    
    public function materis()
    {
        return $this->belongsToMany(Materi::class, 'peserta_materi');
    }
}