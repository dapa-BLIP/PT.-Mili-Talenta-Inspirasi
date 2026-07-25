<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class PelatihanImport implements ToCollection, WithHeadingRow
{
    public function headingRow(): int
    {
        return 8; // Baris judul header
    }

    public function collection(Collection $rows)
    {
        $lineNumber = 8;

        foreach ($rows as $row) {
            $lineNumber++;

            // 🚀 REVISI: Pastikan key di bawah ini SAMA PERSIS dengan huruf kecil & underscore
            // dari judul kolom yang ada di file Excel lu (bisa dicek lewat dd($row) kalau mau yakin)
            $namaPelatihan    = $row['nama_program_pelatihan'] ?? null; // Pastikan header Excel lu "NAMA PROGRAM PELATIHAN"
            $namaMentorExcel  = $row['mentor_pengampu'] ?? null;        // Kalau di Excel judulnya "MENTOR PENGAMPU"
            
            // 💡 SOLUSI DARURAT: 
            // Kalau judul kolom di Excel lu adalah "NAMA LENGKAP MENTOR", 
            // maka ganti $row['mentor_pengampu'] menjadi $row['nama_lengkap_mentor']

            // Coba ganti baris $namaMentorExcel dengan ini biar aman:
            $namaMentorExcel = $row['nama_lengkap_mentor'] ?? $row['mentor_pengampu'] ?? null;

            $komponenNilaiRaw = $row['komponen_penilaian'] ?? null;
            $spektrumKategori = $row['kategori_spektrum'] ?? null;

            if (empty($namaPelatihan)) continue;

            $namaPelatihanClean = strtoupper(trim($namaPelatihan));
            $mentorNameClean    = strtoupper(trim($namaMentorExcel));
            $kategoriClean      = ucwords(strtolower(trim($spektrumKategori)));

            // Cari ID Mentor
            $mentor = DB::table('trainers')->where('nama_mentor', $mentorNameClean)->first();

            if (!$mentor) {
                throw new \Exception("Baris ke-{$lineNumber}: Mentor [{$namaMentorExcel}] tidak ditemukan di Database.");
            }

            $komponenArray = [];
            if ($komponenNilaiRaw) {
                $komponenArray = array_map(function($item) {
                    return strtoupper(trim(str_replace(' ', '_', $item)));
                }, explode(',', $komponenNilaiRaw));
            }

            try {
                $cekPelatihan = DB::table('pelatihans')->where('nama_pelatihan', $namaPelatihanClean)->first();

                $dataArray = [
                    'trainer_id'        => $mentor->id,
                    'komponen_nilai'    => json_encode(array_values(array_unique(array_filter($komponenArray)))),
                    'spektrum_kategori' => in_array($kategoriClean, ['Pengetahuan Teknis', 'Pengetahuan Non Teknis']) ? $kategoriClean : 'Pengetahuan Teknis',
                    'updated_at'        => now(),
                ];

                if ($cekPelatihan) {
                    DB::table('pelatihans')->where('id', $cekPelatihan->id)->update($dataArray);
                } else {
                    $dataArray['nama_pelatihan'] = $namaPelatihanClean;
                    $dataArray['created_at']     = now();
                    DB::table('pelatihans')->insert($dataArray);
                }
            } catch (\Exception $e) {
                throw new \Exception("Error Baris ke-{$lineNumber}: " . $e->getMessage());
            }
        }
    }
}