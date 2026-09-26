<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_perusahaan',
        'penanggung_jawab',
        'kontak_pic',
        'email_perusahaan',
        'alamat',
        'status',
    ];

    public function pesertas()
    {
        return $this->hasMany(Peserta::class, 'perusahaan_id');
    }

    // Mengambil daftar program unik yang diikuti oleh peserta dari PT ini beserta jumlahnya
    public function getRekapProgramAttribute()
    {
        return $this->pesertas()
            ->select('pelatihan_id', \DB::raw('count(*) as total_peserta'))
            ->with('pelatihan:id,nama_pelatihan,jenis_pelatihan')
            ->groupBy('pelatihan_id')
            ->get();
    }
}