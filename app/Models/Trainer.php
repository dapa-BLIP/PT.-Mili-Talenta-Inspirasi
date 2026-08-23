<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_mentor', 
        'nidn', 
        'ttl', 
        'email', 
        'kontak', 
        'sektor_keahlian', 
        'foto',
        'status'
    ];

    public function pelatihans()
    {
        return $this->hasMany(Pelatihan::class, 'trainer_id');
    }

    
}