<?php

namespace App\Imports;

use App\Models\Pelatihan;
use App\Models\Trainer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PelatihanImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cari ID Mentor berdasarkan nama dari Excel (Case Insensitive)
        $mentorNama = trim($row['mentor_pengampu']);
        $mentor = Trainer::where('nama_mentor', 'like', "%{$mentorNama}%")->first();

        // Olah string komponen nilai: bersihkan spasi, uppercase, matikan duplikat
        $rawKomponen = explode(',', $row['komponen_penilaian']);
        $cleaned = array_map(function($item) {
            return strtoupper(trim(str_replace(' ', '_', $item)));
        }, $rawKomponen);
        
        $finalKomponen = array_values(array_unique(array_filter($cleaned)));

        return new Pelatihan([
            'nama_pelatihan'    => strtoupper(trim($row['nama_program_pelatihan'])),
            'spektrum_kategori' => trim($row['kategori_spektrum']),
            'trainer_id'        => $mentor ? $mentor->id : null,
            'komponen_nilai'    => $finalKomponen, // Cast otomatis ke JSON di Model
        ]);
    }
}