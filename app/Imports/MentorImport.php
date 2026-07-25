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
        return 8; // Membaca header dari baris ke-8
    }

    public function collection(Collection $rows)
    {
        $lineNumber = 8;

        foreach ($rows as $row) {
            $lineNumber++;

            // 🚀 SOLUSI SAKTI: Kita cek semua kemungkinan slug array yang dibentuk Laravel-Excel bray
            $idMentor       = $row['id_mentor'] ?? $row['idmentor'] ?? null;
            
            // Cek variasi key untuk Nama Mentor biar ga ketahan null kyk di image_9f2191.png cook
            $namaMentor     = $row['nama_lengkap_mentor'] ?? $row['namalengkapmentor'] ?? $row['nama_lengkap'] ?? $row['nama_mentor'] ?? null;
            
            $nidn           = $row['nidn_nik'] ?? $row['nidnnik'] ?? $row['nidn'] ?? null;
            $ttl            = $row['tempat_tanggal_lahir'] ?? $row['tempattanggallahir'] ?? $row['ttl'] ?? null;
            $email          = $row['email'] ?? null;
            $kontak         = $row['kontak_hp'] ?? $row['kontakhp'] ?? $row['kontak'] ?? $row['hp'] ?? null;
            $sektorKeahlian = $row['sektor_keahlian'] ?? $row['sektorkeahlian'] ?? null;
            $status         = $row['status'] ?? 'AKTIF';

            // Skip baris kosong gaib di paling bawah excel biar ga ikut ke-proses
            if (empty($namaMentor) && empty($email)) {
                continue;
            }

            // 🚀 BANNER WARNING HUMAN-FRIENDLY
            if (empty($namaMentor) || trim($namaMentor) == '') {
                throw new \Exception("Gagal Import! Data pada Baris ke-{$lineNumber} bermasalah: Kolom [NAMA LENGKAP MENTOR] tidak terbaca atau kosong bray.");
            }
            if (empty($email) || trim($email) == '') {
                throw new \Exception("Gagal Import! Data pada Baris ke-{$lineNumber} bermasalah: Kolom [EMAIL] wajib diisi untuk kuncian master.");
            }

            // Cleaning Email & Validasi Format
            $emailClean = strtolower(trim(str_replace(' ', '', preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $email))));
            if (!filter_var($emailClean, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Gagal Import! Baris ke-{$lineNumber}: Penulisan format alamat email [{$email}] salah cook.");
            }

            // Validasi Restriksi Digit NIDN (Murni 10 Angka)
            if ($nidn) {
                $nidnClean = trim(str_replace(["'", "`", " "], "", $nidn));
                if (!preg_match('/^\d{10}$/', $nidnClean)) {
                    throw new \Exception("Gagal Import! Baris ke-{$lineNumber}: Nomor NIDN/NIK [{$nidn}] ditolak! Aturan wajib murni berupa 10 digit angka cook.");
                }
                $nidn = $nidnClean;
            }

            // Cleaning Kontak HP
            if ($kontak) {
                $kontak = str_replace(["'", "`", " "], "", trim($kontak));
                if ($kontak && (strlen($kontak) < 10 || strlen($kontak) > 13)) {
                    throw new \Exception("Gagal Import! Baris ke-{$lineNumber}: Kontak HP [{$kontak}] tidak valid (Harus berukuran antara 10 sampai 13 digit).");
                }
            }

            // Standardisasi Teks Input
            $namaClean   = strtoupper(trim($namaMentor));
            $sektorClean = ucwords(strtolower(trim($sektorKeahlian)));
            $statusClean = strtoupper(trim($status));

            // SECURE RELATIONAL UPSERT ENGINE
            try {
                $idInt = $idMentor ? (int)str_replace(["'", "`", " "], "", trim($idMentor)) : null;
                $cekMentor = null;

                // Lock via email murni
                $cekMentor = DB::table('trainers')->where('email', $emailClean)->first();

                if (!$cekMentor && $idInt) {
                    $cekMentor = DB::table('trainers')->where('id', $idInt)->first();
                }

                // Tameng Duplikat NIDN
                if ($nidn) {
                    $nidnExist = DB::table('trainers')->where('nidn', $nidn)->first();
                    if ($nidnExist && (!$cekMentor || $cekMentor->id !== $nidnExist->id)) {
                        throw new \Exception("Gagal Menyimpan! Baris ke-{$lineNumber}: Kode NIDN/NIK [{$nidn}] sudah digunakan oleh mentor bernama: " . strtoupper($nidnExist->nama_mentor));
                    }
                }

                $dataArray = [
                    'nama_mentor'     => $namaClean,
                    'nidn'            => $nidn,
                    'ttl'             => $ttl ? strtoupper(trim($ttl)) : null,
                    'kontak'          => $kontak,
                    'sektor_keahlian' => in_array($sektorClean, ['Pengetahuan Teknis', 'Pengetahuan Non Teknis']) ? $sektorClean : 'Pengetahuan Teknis',
                    'status'          => in_array($statusClean, ['AKTIF', 'NON-AKTIF']) ? $statusClean : 'AKTIF',
                    'updated_at'      => now(),
                ];

                if ($cekMentor) {
                    DB::table('trainers')->where('id', $cekMentor->id)->update($dataArray);
                } else {
                    $dataArray['email']      = $emailClean;
                    $dataArray['created_at'] = now();
                    DB::table('trainers')->insert($dataArray);
                }

            } catch (\Exception $e) {
                if (str_contains($e->getMessage(), 'UNIQUE constraint failed: trainers.email')) {
                    throw new \Exception("Gagal Menyimpan! Baris ke-{$lineNumber}: Email [{$emailClean}] sudah terdaftar di database milik akun mentor lain.");
                }
                if (str_contains($e->getMessage(), 'UNIQUE constraint failed: trainers.nidn')) {
                    throw new \Exception("Gagal Menyimpan! Baris ke-{$lineNumber}: Nomor NIDN [{$nidn}] bentrok karena sudah dipakai bray.");
                }
                throw new \Exception($e->getMessage());
            }
        }
    }
}