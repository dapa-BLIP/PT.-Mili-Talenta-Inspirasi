<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriPelatihan extends Model
{
    protected $table = 'kategori_pelatihans';
    
    protected $fillable = [
        'nama_kategori',
    ];

    public function trainers()
    {
        return $this->hasMany(Trainer::class, 'program_pelatihan', 'nama_kategori');
    }

    public function pelatihans()
    {
        return $this->hasMany(Pelatihan::class, 'jenis_pelatihan', 'nama_kategori');
    }
}