<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class PesertaImport implements ToCollection, WithHeadingRow
{
    public function headingRow(): int
    {
        return 8;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            $lineNumber = 8;

            // 1. Ambil seluruh Program Pelatihan yang terdaftar di sistem (kategori_pelatihans & pelatihans)
            $registeredPrograms = [];
            $pelatihanList = DB::table('pelatihans')
                ->select('jenis_pelatihan', 'nama_pelatihan')
                ->get();
            foreach ($pelatihanList as $pel) {
                foreach ([$pel->jenis_pelatihan, $pel->nama_pelatihan] as $val) {
                    $cleanKey = strtoupper(trim(preg_replace('/\s+/', ' ', preg_replace('/[\x00-\x1F\x7F\xA0]/u', ' ', (string) $val))));
                    if ($cleanKey !== '') {
                        $registeredPrograms[$cleanKey] = trim((string) $val);
                    }
                }
            }
            foreach (DB::table('kategori_pelatihans')->whereNotNull('nama_kategori')->where('nama_kategori', '!=', '')->pluck('nama_kategori') as $kat) {
                $cleanKey = strtoupper(trim(preg_replace('/\s+/', ' ', preg_replace('/[\x00-\x1F\x7F\xA0]/u', ' ', (string) $kat))));
                if ($cleanKey !== '') {
                    $registeredPrograms[$cleanKey] = trim((string) $kat);
                }
            }

            // 2. Ambil seluruh Perusahaan Mitra yang sudah terdaftar di website (perusahaans)
            $registeredPerusahaans = [];
            foreach (DB::table('perusahaans')->get() as $pt) {
                $cleanPt = strtoupper(trim(preg_replace('/\s+/', ' ', preg_replace('/[\x00-\x1F\x7F\xA0]/u', ' ', (string) $pt->nama_perusahaan))));
                if ($cleanPt !== '') {
                    $registeredPerusahaans[$cleanPt] = $pt;
                }
            }

            foreach ($rows as $row) {
                $lineNumber++;

                // Primary Key (ID) otomatis dibuat oleh database, tidak diambil dari input user
                $namaLengkap = $row['nama_lengkap'] ?? $row['namalengkap'] ?? $row['nama_peserta'] ?? $row['nama'] ?? null;
                $gender      = $row['gender'] ?? $row['jenis_kelamin'] ?? null;
                $ttl         = $row['tempat_tanggal_lahir'] ?? $row['tempattanggallahir'] ?? $row['ttl'] ?? null;
                $email       = $row['email'] ?? null;
                $noHp        = $row['no_handphone'] ?? $row['nohandphone'] ?? $row['no_hp'] ?? $row['kontak'] ?? null;
                $tempatKerja = $row['tempat_kerja'] ?? $row['tempatkerja'] ?? $row['perusahaan'] ?? $row['nama_perusahaan'] ?? $row['perusahaan_mitra'] ?? null;
                $pelatihan   = $row['sektor_pelatihan'] ?? $row['sektorpelatihan'] ?? $row['program_pelatihan'] ?? $row['pelatihan'] ?? $row['program'] ?? '';
                $status      = $row['status_verifikasi'] ?? $row['status'] ?? 'PENDING';

                $isDashOrEmpty = fn($val) => empty($val) || trim((string) $val) === '' || trim((string) $val) === '-';

                if ($isDashOrEmpty($namaLengkap) && $isDashOrEmpty($email) && $isDashOrEmpty($noHp)) {
                    continue;
                }

                // 1. VALIDASI WAJIB ISI NAMA & EMAIL
                if ($isDashOrEmpty($namaLengkap)) {
                    throw new \Exception("Gagal Import! Data pada Baris ke-{$lineNumber} bermasalah: Kolom [NAMA LENGKAP] kosong.");
                }
                if ($isDashOrEmpty($email)) {
                    throw new \Exception("Gagal Import! Data pada Baris ke-{$lineNumber} bermasalah: Kolom [EMAIL] wajib diisi.");
                }

                $emailClean = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', (string) $email);
                $emailClean = strtolower(trim(str_replace(' ', '', $emailClean)));

                if (!filter_var($emailClean, FILTER_VALIDATE_EMAIL)) {
                    throw new \Exception("Gagal Import! Baris ke-{$lineNumber}: Format penulisan alamat email [{$email}] salah.");
                }

                // 2. VALIDASI PERUSAHAAN WAJIB TERDAFTAR DI WEBSITE
                if ($isDashOrEmpty($tempatKerja)) {
                    throw new \Exception("Gagal Import! Baris ke-{$lineNumber}: Kolom [TEMPAT KERJA / PERUSAHAAN] kosong! Wajib diisi perusahaan yang sudah terdaftar di website.");
                }

                $ptKey = strtoupper(trim(preg_replace('/\s+/', ' ', preg_replace('/[\x00-\x1F\x7F\xA0]/u', ' ', (string) $tempatKerja))));
                if (!isset($registeredPerusahaans[$ptKey])) {
                    throw new \Exception(
                        "Gagal Import! Baris ke-{$lineNumber}: Perusahaan [{$tempatKerja}] belum terdaftar di website! " .
                        "Silakan tambahkan perusahaan tersebut terlebih dahulu di menu Data Perusahaan."
                    );
                }
                $matchedPt = $registeredPerusahaans[$ptKey];

                // 3. VALIDASI PROGRAM PELATIHAN WAJIB TERDAFTAR DI WEBSITE
                if ($isDashOrEmpty($pelatihan)) {
                    throw new \Exception("Gagal Import! Baris ke-{$lineNumber}: Kolom [SEKTOR PELATIHAN] kosong! Wajib diisi program pelatihan yang sudah terdaftar di website.");
                }

                $progKey = strtoupper(trim(preg_replace('/\s+/', ' ', preg_replace('/[\x00-\x1F\x7F\xA0]/u', ' ', (string) $pelatihan))));
                if (!isset($registeredPrograms[$progKey])) {
                    throw new \Exception(
                        "Gagal Import! Baris ke-{$lineNumber}: Program Pelatihan [{$pelatihan}] belum terdaftar di website! " .
                        "Silakan tambahkan program pelatihan tersebut terlebih dahulu di menu Kelola Pelatihan."
                    );
                }
                $pelatihanClean = $registeredPrograms[$progKey];

                // 4. VALIDASI & NORMALISASI NO HP (+62)
                $noHpFormatted = null;
                if (!$isDashOrEmpty($noHp)) {
                    $noHpRaw = str_replace(["'", "`"], "", trim((string) $noHp));
                    $digits = preg_replace('/[^0-9]/', '', $noHpRaw);
                    if (strlen($digits) < 9 || strlen($digits) > 15) {
                        throw new \Exception("Gagal Import! Baris ke-{$lineNumber}: Nomor HP [{$noHpRaw}] tidak valid (10-15 digit).");
                    }
                    $localDigits = preg_replace('/^(0|62)/', '', $digits);
                    $noHpFormatted = '+62' . $localDigits;
                }

                $namaClean   = ucwords(strtolower(trim((string) $namaLengkap)));
                $statusClean = strtoupper(trim((string) $status));

                // Normalisasi Gender
                $genderClean = strtolower(trim((string) $gender));
                if (in_array($genderClean, ['pria', 'l', 'laki-laki', 'laki'])) {
                    $genderClean = 'pria';
                } elseif (in_array($genderClean, ['wanita', 'p', 'perempuan'])) {
                    $genderClean = 'wanita';
                } else {
                    $genderClean = 'pria';
                }

                // Normalisasi TTL
                $ttlClean = trim((string) $ttl);
                if (!empty($ttlClean) && $ttlClean !== '-') {
                    if (!str_contains($ttlClean, ',')) {
                        $parts = explode(' ', $ttlClean, 2);
                        $ttlClean = count($parts) == 2 ? ucwords(strtolower($parts[0])) . ', ' . trim($parts[1]) : ucwords(strtolower($ttlClean)) . ', ' . date('d-m-Y');
                    } else {
                        $parts = explode(',', $ttlClean, 2);
                        $ttlClean = ucwords(strtolower(trim($parts[0]))) . ', ' . trim($parts[1]);
                    }
                } else {
                    $ttlClean = 'Bekasi, ' . date('d-m-Y');
                }

                // 5. SIMPAN KE DATABASE (PK OTOMATIS DARI DATABASE)
                try {
                    $cekPeserta = DB::table('pesertas')->where('email', $emailClean)->first();

                    $dataArray = [
                        'nama_lengkap'  => $namaClean,
                        'gender'        => $genderClean,
                        'pelatihan'     => $pelatihanClean,
                        'ttl'           => $ttlClean,
                        'no_hp'         => $noHpFormatted,
                        'perusahaan_id' => $matchedPt->id,
                        'tempat_kerja'  => $matchedPt->nama_perusahaan,
                        'status'        => in_array($statusClean, ['AKTIF', 'PENDING', 'NON-AKTIF']) ? $statusClean : 'PENDING',
                        'updated_at'    => now(),
                    ];

                    if ($cekPeserta) {
                        DB::table('pesertas')->where('id', $cekPeserta->id)->update($dataArray);
                    } else {
                        $dataArray['email']      = $emailClean;
                        $dataArray['created_at'] = now();
                        DB::table('pesertas')->insert($dataArray);
                    }
                } catch (\Exception $e) {
                    if (str_contains($e->getMessage(), 'UNIQUE constraint failed')) {
                        throw new \Exception("Gagal Menyimpan! Data Baris ke-{$lineNumber}: Email [{$emailClean}] sudah terdaftar.");
                    }
                    throw new \Exception("Gagal Memproses Baris ke-{$lineNumber}: " . $e->getMessage());
                }
            }
        });
    }
}