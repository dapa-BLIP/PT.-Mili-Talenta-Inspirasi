<?php

namespace App\Imports;

use App\Models\Peserta;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PesertaImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // 🚀 KEY SYNC: Menyelaraskan rujukan key array dengan heading string bawaan template export lu
        $namaLengkap = $row['nama_lengkap'] ?? null;
        $gender      = $row['gender'] ?? null; 
        $ttl         = $row['tempat_tanggal_lahir'] ?? $row['ttl'] ?? null;
        $email       = $row['email'] ?? null;
        $noHp        = $row['no_handphone'] ?? $row['no_hp'] ?? null;
        $tempatKerja = $row['tempat_kerja'] ?? null;
        $pelatihan   = $row['sektor_pelatihan'] ?? $row['batch_pelatihan'] ?? '';
        $status      = $row['status_verifikasi'] ?? $row['status'] ?? 'PENDING';

        // 1. VALIDASI UTAMA
        if (empty($namaLengkap) || trim($namaLengkap) == '' || empty($email) || trim($email) == '') {
            return null;
        }

        // 2. CLEANING DATA SPASI DAN GAIB
        $emailClean = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $email);
        $emailClean = strtolower(trim(str_replace(' ', '', $emailClean)));

        if ($noHp) {
            $noHp = ltrim(trim($noHp), "'");
            if($noHp == '-') $noHp = null;
        }

        $namaClean      = ucwords(strtolower(trim($namaLengkap))); 
        $pelatihanClean = ucwords(strtolower(trim($pelatihan)));   
        $statusClean    = strtoupper(trim($status)); 
        
        // 3. NORMALISASI GENDER FIELD
        $genderClean = strtolower(trim($gender));
        if (in_array($genderClean, ['pria', 'l', 'laki-laki', 'laki'])) {
            $genderClean = 'pria';
        } elseif (in_array($genderClean, ['wanita', 'p', 'perempuan'])) {
            $genderClean = 'wanita';
        } else {
            $genderClean = 'pria'; 
        }

        // 4. NORMALISASI FIELD TTL
        $ttlClean = trim($ttl);
        if (!empty($ttlClean) && $ttlClean != '-') {
            if (!str_contains($ttlClean, ',')) {
                $parts = explode(' ', $ttlClean, 2);
                if (count($parts) == 2) {
                    $ttlClean = ucwords(strtolower($parts[0])) . ', ' . trim($parts[1]);
                } else {
                    $ttlClean = ucwords(strtolower($ttlClean)) . ', ' . date('d-m-Y');
                }
            } else {
                $parts = explode(',', $ttlClean, 2);
                $ttlClean = ucwords(strtolower(trim($parts[0]))) . ', ' . trim($parts[1]);
            }
        } else {
            $ttlClean = 'Bekasi, ' . date('d-m-Y');
        }

        // 5. UPSERT TRANSACTION DIRECT TO DATABASE (CMS HUB ALGORITHM)
        try {
            $cekPeserta = DB::table('pesertas')->where('email', $emailClean)->first();

            $dataArray = [
                'nama_lengkap' => $namaClean,
                'gender'       => $genderClean,
                'pelatihan'    => $pelatihanClean ?: 'Batch Kelapa Sawit', // Langsung simpan string text dinamis
                'ttl'          => $ttlClean,
                'no_hp'        => $noHp,
                'tempat_kerja' => $tempatKerja && trim($tempatKerja) != '-' ? ucwords(strtolower(trim($tempatKerja))) : 'PT MILI',
                'status'       => in_array($statusClean, ['AKTIF', 'PENDING', 'NON-AKTIF']) ? $statusClean : 'PENDING',
                'updated_at'   => now(),
            ];

            if ($cekPeserta) {
                DB::table('pesertas')->where('email', $emailClean)->update($dataArray);
            } else {
                $dataArray['email']      = $emailClean;
                $dataArray['created_at'] = now();
                DB::table('pesertas')->insert($dataArray);
            }

        } catch (\Exception $e) {
            Log::error("Gagal melakukan import engine CMS: {$namaClean}. Error: " . $e->getMessage());
        }

        return null;
    }
}