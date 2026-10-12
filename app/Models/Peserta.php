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

    public function nilais()
    {
        return $this->hasMany(Nilai::class, 'peserta_id');
    }

    public function hasNilaiInProgram($programName = null)
    {
        $program = strtolower(trim((string)($programName ?: $this->pelatihan)));
        if ($program === '') {
            return false;
        }

        $cleanProgram = preg_replace('/[^a-z0-9]/', '', str_replace(['kelas', 'pelatihan', 'program'], '', $program));

        $nilais = $this->relationLoaded('nilais') ? $this->nilais : $this->nilais()->with('pelatihan')->get();

        foreach ($nilais as $n) {
            if (!$n->pelatihan) continue;

            $jenis = strtolower(trim((string)$n->pelatihan->jenis_pelatihan));
            $nama  = strtolower(trim((string)$n->pelatihan->nama_pelatihan));

            $cleanJenis = preg_replace('/[^a-z0-9]/', '', str_replace(['kelas', 'pelatihan', 'program'], '', $jenis));
            $cleanNama  = preg_replace('/[^a-z0-9]/', '', str_replace(['kelas', 'pelatihan', 'program'], '', $nama));

            $isMatch = ($jenis === $program) 
                    || ($nama === $program)
                    || ($cleanProgram !== '' && ($cleanJenis === $cleanProgram || $cleanNama === $cleanProgram))
                    || ($cleanProgram !== '' && ($cleanJenis !== '' && (str_contains($cleanJenis, $cleanProgram) || str_contains($cleanProgram, $cleanJenis))));

            if ($isMatch) {
                $scores = is_string($n->scores) ? json_decode($n->scores, true) : $n->scores;
                if (is_array($scores)) {
                    foreach ($scores as $s) {
                        if ($s !== null && trim((string)$s) !== '') {
                            return true;
                        }
                    }
                }
                if (!empty($n->catatan)) {
                    return true;
                }
            }
        }

        return false;
    }
    
    public function materis()
    {
        return $this->belongsToMany(Materi::class, 'peserta_materi');
    }
}