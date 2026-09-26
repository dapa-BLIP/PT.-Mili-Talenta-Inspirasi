<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class MentorImport implements ToCollection, WithHeadingRow
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

            $namaMentor = $row['nama_lengkap_mentor'] ?? $row['namalengkapmentor'] ?? $row['nama_mentor'] ?? $row['nama'] ?? null;
            $nidn       = $row['nik'] ?? $row['nidn_nik'] ?? $row['nidnnik'] ?? $row['nidn'] ?? null;
            $ttl        = $row['tempat_tanggal_lahir'] ?? $row['tempattanggallahir'] ?? $row['ttl'] ?? null;
            $email      = $row['email'] ?? null;
            $kontak     = $row['kontak'] ?? $row['kontak_wa'] ?? $row['kontakwa'] ?? $row['kontak_hp'] ?? $row['no_hp'] ?? null;
            $program    = $row['program_pelatihan'] ?? $row['programpelatihan'] ?? $row['program_pengampu'] ?? $row['sektor_keahlian'] ?? null;
            $status     = $row['status'] ?? null;

            // Lewati baris kosong total
            if (empty($namaMentor) && empty($email) && empty($kontak) && empty($nidn)) {
                continue;
            }

            // 1. Validasi Nama Mentor
            if (empty($namaMentor) || trim((string)$namaMentor) === '') {
                throw new \Exception("⚠️ Baris ke-{$lineNumber}: Kolom [NAMA LENGKAP MENTOR] kosong! Wajib diisi nama mentor.");
            }

            // 2. Validasi NIK / NIDN (8 - 16 Digit Angka)
            $nidnClean = null;
            if (!empty($nidn)) {
                $nidnClean = trim(str_replace(["'", "`", " ", "\t"], "", (string)$nidn));
                if (!preg_match('/^[0-9]+$/', $nidnClean)) {
                    throw new \Exception("⚠️ Baris ke-{$lineNumber}: NIK/NIDN [{$nidn}] tidak valid! Hanya boleh berisi angka tanpa huruf/simbol.");
                }
                $digitCount = strlen($nidnClean);
                if ($digitCount < 8 || $digitCount > 16) {
                    throw new \Exception("⚠️ Baris ke-{$lineNumber}: Jumlah digit NIK/NIDN [{$nidn}] salah ({$digitCount} digit)! Wajib antara 8 sampai 16 digit angka.");
                }
            }

            // 3. Validasi & Normalisasi Nomor Telepon / WA
            $kontakFormatted = null;
            if (!empty($kontak)) {
                $rawDigits = preg_replace('/[^0-9]/', '', (string)$kontak);
                $localDigits = preg_replace('/^(0|62)/', '', $rawDigits);

                if (empty($localDigits)) {
                    throw new \Exception("⚠️ Baris ke-{$lineNumber}: Nomor kontak [{$kontak}] tidak valid!");
                }

                if (!str_starts_with($localDigits, '8')) {
                    throw new \Exception("⚠️ Baris ke-{$lineNumber}: Nomor kontak [{$kontak}] salah! Nomor seluler wajib diawali angka 8 (contoh: 81234567890).");
                }

                $panjangNomor = strlen($localDigits);
                if ($panjangNomor < 9 || $panjangNomor > 13) {
                    throw new \Exception("⚠️ Baris ke-{$lineNumber}: Jumlah digit nomor kontak [{$kontak}] tidak sesuai ({$panjangNomor} digit)! Panjang nomor seluler harus antara 9 sampai 13 digit.");
                }

                $kontakFormatted = '+62' . $localDigits;
            }

            // 4. Validasi Program Pelatihan
            if (empty($program) || trim((string)$program) === '') {
                throw new \Exception("⚠️ Baris ke-{$lineNumber}: Kolom [PROGRAM PELATIHAN] kosong! Pilih 'PELATIHAN TEKNIS' atau 'PELATIHAN NON TEKNIS'.");
            }

            $progUpper = strtoupper(trim((string)$program));
            if ($progUpper === 'PELATIHAN TEKNIS' || $progUpper === 'TEKNIS') {
                $programClean = 'Pelatihan Teknis';
            } elseif ($progUpper === 'PELATIHAN NON TEKNIS' || $progUpper === 'NON TEKNIS' || str_contains($progUpper, 'NON')) {
                $programClean = 'Pelatihan Non Teknis';
            } else {
                throw new \Exception("⚠️ Baris ke-{$lineNumber}: Pilihan Program Pelatihan [{$program}] tidak dikenali! Pilihan yang tersedia hanya: 'Pelatihan Teknis' atau 'Pelatihan Non Teknis'.");
            }

            // 5. Validasi Email
            $emailClean = null;
            if (!empty($email)) {
                $emailClean = strtolower(trim(str_replace(' ', '', preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', (string)$email))));
                if (!filter_var($emailClean, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("⚠️ Baris ke-{$lineNumber}: Penulisan format email [{$email}] tidak valid!");
                }
            }

            // 6. Validasi Status Mentor (Wajib 'AKTIF', 'PENDING', atau 'NON-AKTIF')
            if (empty($status) || trim((string)$status) === '') {
                throw new \Exception("⚠️ Baris ke-{$lineNumber}: Kolom [STATUS] kosong! Wajib diisi 'AKTIF', 'PENDING', atau 'NON-AKTIF'.");
            }

            $statusUpper = strtoupper(trim(str_replace([' ', '_'], '-', (string)$status)));
            
            if ($statusUpper === 'AKTIF') {
                $statusClean = 'AKTIF';
            } elseif ($statusUpper === 'PENDING') {
                $statusClean = 'PENDING';
            } elseif ($statusUpper === 'NON-AKTIF' || $statusUpper === 'NONAKTIF') {
                $statusClean = 'NON-AKTIF';
            } else {
                throw new \Exception("⚠️ Baris ke-{$lineNumber}: Status [{$status}] ditolak! Status yang didukung hanya: 'AKTIF', 'PENDING', atau 'NON-AKTIF'.");
            }

            $namaClean = strtoupper(trim((string)$namaMentor));

            // Upsert ke Database
            try {
                $cekMentor = null;

                if ($emailClean) {
                    $cekMentor = DB::table('trainers')->where('email', $emailClean)->first();
                }

                if (!$cekMentor && $nidnClean) {
                    $cekMentor = DB::table('trainers')->where('nidn', $nidnClean)->first();
                }

                if (!$cekMentor) {
                    $cekMentor = DB::table('trainers')->where('nama_mentor', $namaClean)->first();
                }

                // Cek jika NIK bentrok dengan ID mentor yang berbeda
                if ($nidnClean) {
                    $cekNidn = DB::table('trainers')->where('nidn', $nidnClean)->first();
                    if ($cekNidn && (!$cekMentor || $cekMentor->id !== $cekNidn->id)) {
                        throw new \Exception("⚠️ Baris ke-{$lineNumber}: NIK/NIDN [{$nidnClean}] sudah terdaftar atas nama mentor '{$cekNidn->nama_mentor}'.");
                    }
                }

                $dataArray = [
                    'nama_mentor'       => $namaClean,
                    'nidn'              => $nidnClean,
                    'ttl'               => $ttl ? strtoupper(trim((string)$ttl)) : null,
                    'email'             => $emailClean,
                    'kontak'            => $kontakFormatted,
                    'program_pelatihan' => $programClean,
                    'status'            => $statusClean,
                    'updated_at'        => now(),
                ];

                if ($cekMentor) {
                    DB::table('trainers')->where('id', $cekMentor->id)->update($dataArray);
                } else {
                    $dataArray['created_at'] = now();
                    DB::table('trainers')->insert($dataArray);
                }

            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'trainers.email') || str_contains($e->getMessage(), 'UNIQUE constraint failed: trainers.email')) {
                    throw new \Exception("⚠️ Baris ke-{$lineNumber}: Email [{$emailClean}] sudah digunakan oleh mentor lain.");
                }
                throw new \Exception($e->getMessage());
            }
        }
    }
}