<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PesertaImport implements ToCollection, WithHeadingRow
{
    public function headingRow(): int
    {
        return 8;
    }

    public function collection(Collection $rows)
    {
        $lineNumber = 8; 

        foreach ($rows as $row) {
            $lineNumber++;

            $idPeserta   = $row['id_peserta'] ?? null; 
            $namaLengkap = $row['nama_lengkap'] ?? null;
            $gender      = $row['gender'] ?? null; 
            $ttl         = $row['tempat_tanggal_lahir'] ?? $row['ttl'] ?? null;
            $email       = $row['email'] ?? null;
            $noHp        = $row['no_handphone'] ?? $row['no_hp'] ?? null;
            $tempatKerja = $row['tempat_kerja'] ?? null;
            $pelatihan   = $row['sektor_pelatihan'] ?? $row['batch_pelatihan'] ?? '';
            $status      = $row['status_verifikasi'] ?? $row['status'] ?? 'PENDING';

            // 1. VALIDASI DATA HARUS ISI
            if (empty($namaLengkap) || trim($namaLengkap) == '') {
                throw new \Exception("Gagal Import! Data pada Baris ke-{$lineNumber} bermasalah: Kolom [NAMA LENGKAP] kosong.");
            }
            if (empty($email) || trim($email) == '') {
                throw new \Exception("Gagal Import! Data pada Baris ke-{$lineNumber} bermasalah: Kolom [EMAIL] wajib diisi.");
            }

            $emailClean = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $email);
            $emailClean = strtolower(trim(str_replace(' ', '', $emailClean)));

            if (!filter_var($emailClean, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Gagal Import! Baris ke-{$lineNumber}: Format penulisan alamat email [{$email}] salah.");
            }

            if ($noHp) {
                $noHp = str_replace(["'", "`"], "", trim($noHp));
                if ($noHp == '-') $noHp = null;
                
                if ($noHp && (strlen($noHp) < 10 || strlen($noHp) > 13)) {
                    throw new \Exception("Gagal Import! Baris ke-{$lineNumber}: Nomor HP [{$noHp}] tidak valid (Harus berukuran 10 sampai 13 digit angka).");
                }
            }

            $namaClean      = ucwords(strtolower(trim($namaLengkap))); 
            $pelatihanClean = ucwords(strtolower(trim($pelatihan)));   
            $statusClean    = strtoupper(trim($status)); 
            
            // Normalisasi Gender
            $genderClean = strtolower(trim($gender));
            if (in_array($genderClean, ['pria', 'l', 'laki-laki', 'laki'])) { $genderClean = 'pria'; }
            elseif (in_array($genderClean, ['wanita', 'p', 'perempuan'])) { $genderClean = 'wanita'; }
            else { $genderClean = 'pria'; }

            // Normalisasi TTL
            $ttlClean = trim($ttl);
            if (!empty($ttlClean) && $ttlClean != '-') {
                if (!str_contains($ttlClean, ',')) {
                    $parts = explode(' ', $ttlClean, 2);
                    $ttlClean = count($parts) == 2 ? ucwords(strtolower($parts[0])) . ', ' . trim($parts[1]) : ucwords(strtolower($ttlClean)) . ', ' . date('d-m-Y');
                } else {
                    $parts = explode(',', $ttlClean, 2);
                    $ttlClean = ucwords(strtolower(trim($parts[0]))) . ', ' . trim($parts[1]);
                }
            } else { $ttlClean = 'Bekasi, ' . date('d-m-Y'); }

            // 2. ENGINE AUTOMATIC UPSERT DENGAN FRIENDLY WARNING HANDLER
            try {
                // Konversi string "00000007" jadi integer angka 7 murni buat dibaca DB cook
                $idInt = $idPeserta ? (int)str_replace(["'", "`"], "", trim($idPeserta)) : null;
                $cekPeserta = null;

                // Selalu utamakan cek email_clean biar tameng databasenya kokoh anti-duplikat
                $cekPeserta = \Illuminate\Support\Facades\DB::table('pesertas')->where('email', $emailClean)->first();

                if (!$cekPeserta && $idInt) {
                    $cekPeserta = \Illuminate\Support\Facades\DB::table('pesertas')->where('id', $idInt)->first();
                }

                $dataArray = [
                    'nama_lengkap' => $namaClean,
                    'gender'       => $genderClean,
                    'pelatihan'    => $pelatihanClean ?: 'Batch Kelapa Sawit',
                    'ttl'          => $ttlClean,
                    'no_hp'        => $noHp,
                    'tempat_kerja' => $tempatKerja && trim($tempatKerja) != '-' ? ucwords(strtolower(trim($tempatKerja))) : 'PT MILI',
                    'status'       => in_array($statusClean, ['AKTIF', 'PENDING', 'NON-AKTIF']) ? $statusClean : 'PENDING',
                    'updated_at'   => now(),
                ];

                if ($cekPeserta) {
                    \Illuminate\Support\Facades\DB::table('pesertas')->where('id', $cekPeserta->id)->update($dataArray);
                } else {
                    $dataArray['email']      = $emailClean;
                    $dataArray['created_at'] = now();
                    \Illuminate\Support\Facades\DB::table('pesertas')->insert($dataArray);
                }

            } catch (\Exception $e) {
                // 🚀 UPDATE CERDAS: Bungkus SQL State error kaku menjadi bahasa friendly biar lu ga malu-maluin pas demo bray!
                if (str_contains($e->getMessage(), 'UNIQUE constraint failed')) {
                    throw new \Exception("Gagal Menyimpan! Data pada Baris ke-{$lineNumber} terdeteksi ganda: Alamat Email [{$emailClean}] sudah terdaftar di sistem sebelumnya.");
                }
                throw new \Exception("Gagal Memproses Baris ke-{$lineNumber}: " . $e->getMessage());
            }
        }
    }
}