<?php

namespace App\Imports;

use App\Models\Trainer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MentorImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Trainer([
            'nama_mentor'     => strtoupper(trim($row['nama_lengkap_mentor'])),
            'nidn'            => trim($row['nidn_nik']),
            'ttl'             => strtoupper(trim($row['tempat_tanggal_lahir'])),
            'email'           => trim($row['email']),
            'kontak'          => trim($row['kontak_hp']),
            'sektor_keahlian' => trim($row['sektor_keahlian']),
            'status'          => strtoupper(trim($row['status'] ?? 'AKTIF')),
        ]);
    }
}