<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'trainers';

    // Kolom yang dapat diisi secara massal
    protected $guarded = [];

    // Relasi balik ke Pelatihan
    public function pelatihans()
    {
        return $this->hasMany(Pelatihan::class, 'trainer_id');
    }
}