<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_mentor', // 🚀 Update
        'nidn', 
        'ttl', 
        'email', 
        'kontak', 
        'sektor_keahlian', 
        'foto', // 🚀 Tambah
        'status'
    ];

    public function pelatihans()
    {
        return $this->hasMany(Pelatihan::class, 'trainer_id');
    }

    
}