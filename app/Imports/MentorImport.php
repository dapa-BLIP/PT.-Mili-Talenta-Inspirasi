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
        DB::transaction(function () use ($rows) {
            $lineNumber = 8;

            // Ambil seluruh daftar Program Pelatihan yang terdaftar langsung dari relasi database Pelatihan (kategori_pelatihans & pelatihans)
            $registeredPrograms = [];

            $pelatihanList = DB::table('pelatihans')
                ->select('jenis_pelatihan', 'nama_pelatihan')
                ->get();
            foreach ($pelatihanList as $pel) {
                foreach ([$pel->jenis_pelatihan, $pel->nama_pelatihan] as $val) {
                    $cleanKey = strtoupper(trim(preg_replace('/\s+/', ' ', preg_replace('/[\x00-\x1F\x7F\xA0]/u', ' ', (string) $val))));
                    if ($cleanKey !== '') {
                        $registeredPrograms[$cleanKey] = $cleanKey;
                    }
                }
            }

            // Prioritaskan dari Master Modul Pelatihan (kategori_pelatihans)
            $kategoriList = DB::table('kategori_pelatihans')
                ->whereNotNull('nama_kategori')
                ->where('nama_kategori', '!=', '')
                ->pluck('nama_kategori');
            foreach ($kategoriList as $kat) {
                $cleanKey = strtoupper(trim(preg_replace('/\s+/', ' ', preg_replace('/[\x00-\x1F\x7F\xA0]/u', ' ', (string) $kat))));
                if ($cleanKey !== '') {
                    $registeredPrograms[$cleanKey] = $cleanKey;
                }
            }

            $seenNidns = [];
            $seenEmails = [];
            $seenKontaks = [];

            $isEmptyField = fn($v) => $v === null || trim((string) $v) === '' || trim((string) $v) === '-';
            $isRecordIncomplete = fn($m) => $isEmptyField($m->nidn) || $isEmptyField($m->ttl) || $isEmptyField($m->email) || $isEmptyField($m->kontak);

            $insertedCount = 0;
            $completedIncompleteCount = 0;
            $skippedAlreadyCompleteRows = [];

            foreach ($rows as $row) {
                $lineNumber++;

                $namaMentor = $row['nama_lengkap_mentor'] ?? $row['namalengkapmentor'] ?? $row['nama_mentor'] ?? $row['nama'] ?? null;
                $nidn       = $row['nik'] ?? $row['nidn_nik'] ?? $row['nidnnik'] ?? $row['nidn'] ?? null;
                $ttl        = $row['tempat_tanggal_lahir'] ?? $row['tempattanggallahir'] ?? $row['ttl'] ?? null;
                $email      = $row['email'] ?? null;
                $kontak     = $row['kontak'] ?? $row['kontak_wa'] ?? $row['kontakwa'] ?? $row['kontak_hp'] ?? $row['no_hp'] ?? null;
                $program    = $row['program_pelatihan'] ?? $row['programpelatihan'] ?? $row['jenis_pelatihan'] ?? $row['pelatihan'] ?? $row['kategori_pelatihan'] ?? $row['program'] ?? $row['program_pengampu'] ?? $row['sektor_keahlian'] ?? null;
                $status     = $row['status'] ?? null;

                $isDashOrEmpty = fn($val) => empty($val) || trim((string) $val) === '' || trim((string) $val) === '-';

                // Lewati baris kosong total atau baris contoh template ('-')
                if ($isDashOrEmpty($namaMentor) && $isDashOrEmpty($email) && $isDashOrEmpty($kontak) && $isDashOrEmpty($nidn)) {
                    continue;
                }

                // 1. Validasi Nama Mentor
                if ($isDashOrEmpty($namaMentor)) {
                    throw new \Exception("⚠️ Baris ke-{$lineNumber}: Kolom [NAMA LENGKAP MENTOR] kosong! Wajib diisi nama mentor.");
                }

                $namaClean = strtoupper(trim(preg_replace('/\s+/', ' ', (string) $namaMentor)));

                // 2. Validasi Format NIK / NIDN (8 - 16 Digit Angka + Cek Duplikat di File Excel)
                $nidnClean = null;
                if (!$isDashOrEmpty($nidn)) {
                    $nidnClean = trim(str_replace(["'", "`", " ", "\t"], "", (string) $nidn));
                    if ($nidnClean !== '' && $nidnClean !== '-') {
                        if (!preg_match('/^[0-9]+$/', $nidnClean)) {
                            throw new \Exception("⚠️ Baris ke-{$lineNumber}: NIK/NIDN [{$nidn}] tidak valid! Hanya boleh berisi angka tanpa huruf/simbol.");
                        }
                        $digitCount = strlen($nidnClean);
                        if ($digitCount < 8 || $digitCount > 16) {
                            throw new \Exception("⚠️ Baris ke-{$lineNumber}: Jumlah digit NIK/NIDN [{$nidn}] salah ({$digitCount} digit)! Wajib antara 8 sampai 16 digit angka.");
                        }

                        if (isset($seenNidns[$nidnClean])) {
                            throw new \Exception("⚠️ Peringatan Baris ke-{$lineNumber}: NIK/NIDN [{$nidnClean}] sama dengan baris ke-{$seenNidns[$nidnClean]} di file Excel! NIK/NIDN setiap mentor tidak boleh sama.");
                        }
                        $seenNidns[$nidnClean] = $lineNumber;
                    } else {
                        $nidnClean = null;
                    }
                }

                // 3. Validasi & Normalisasi Nomor Telepon / WA (+62 tanpa tanda '-')
                $kontakFormatted = null;
                if (!$isDashOrEmpty($kontak)) {
                    $rawDigits = preg_replace('/[^0-9]/', '', (string) $kontak);
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

                    if (isset($seenKontaks[$kontakFormatted])) {
                        throw new \Exception("⚠️ Peringatan Baris ke-{$lineNumber}: Nomor kontak [{$kontakFormatted}] sama dengan baris ke-{$seenKontaks[$kontakFormatted]} di file Excel! Nomor telepon setiap mentor harus berbeda.");
                    }
                    $seenKontaks[$kontakFormatted] = $lineNumber;
                }

                // 4. Validasi Program Pelatihan (Wajib Terdaftar di Database Pelatihan / KategoriPelatihan)
                $daftarProgramTersedia = !empty($registeredPrograms)
                    ? implode(', ', array_map(fn($p) => "'{$p}'", array_values(array_unique($registeredPrograms))))
                    : 'Belum ada program pelatihan yang terdaftar';

                if ($isDashOrEmpty($program)) {
                    throw new \Exception("⚠️ Baris ke-{$lineNumber}: Kolom [PROGRAM PELATIHAN] kosong! Pilihan program yang terdaftar di database Pelatihan: {$daftarProgramTersedia}.");
                }

                $progUpper = strtoupper(trim(preg_replace('/\s+/', ' ', preg_replace('/[\x00-\x1F\x7F\xA0]/u', ' ', (string) $program))));

                if (isset($registeredPrograms[$progUpper])) {
                    $programClean = $registeredPrograms[$progUpper];
                } elseif (isset($registeredPrograms['PELATIHAN ' . $progUpper])) {
                    $programClean = $registeredPrograms['PELATIHAN ' . $progUpper];
                } else {
                    throw new \Exception(
                        "⚠️ Baris ke-{$lineNumber}: Program Pelatihan [{$program}] belum terdaftar di database Pelatihan! " .
                        "Pilihan yang tersedia saat ini: {$daftarProgramTersedia}. " .
                        "(Silakan tambahkan program pelatihan tersebut terlebih dahulu di menu Kelola Pelatihan)."
                    );
                }

                // 5. Validasi Email (Opsional)
                $emailClean = null;
                if (!$isDashOrEmpty($email)) {
                    $emailClean = strtolower(trim(str_replace(' ', '', preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', (string) $email))));
                    if (!filter_var($emailClean, FILTER_VALIDATE_EMAIL)) {
                        throw new \Exception("⚠️ Baris ke-{$lineNumber}: Penulisan format email [{$email}] tidak valid!");
                    }
                    if (isset($seenEmails[$emailClean])) {
                        throw new \Exception("⚠️ Peringatan Baris ke-{$lineNumber}: Email [{$emailClean}] duplikat dengan baris ke-{$seenEmails[$emailClean]} di file Excel!");
                    }
                    $seenEmails[$emailClean] = $lineNumber;
                }

                // Normalisasi TTL (Opsional)
                $ttlClean = !$isDashOrEmpty($ttl) ? strtoupper(trim((string) $ttl)) : null;

                // 6. Validasi Status Mentor (Wajib 'AKTIF', 'PENDING', atau 'NON-AKTIF')
                if (empty($status) || trim((string) $status) === '') {
                    throw new \Exception("⚠️ Baris ke-{$lineNumber}: Kolom [STATUS] kosong! Wajib diisi 'AKTIF', 'PENDING', atau 'NON-AKTIF'.");
                }

                $statusUpper = strtoupper(trim(str_replace([' ', '_'], '-', (string) $status)));

                if ($statusUpper === 'AKTIF') {
                    $statusClean = 'AKTIF';
                } elseif ($statusUpper === 'PENDING') {
                    $statusClean = 'PENDING';
                } elseif ($statusUpper === 'NON-AKTIF' || $statusUpper === 'NONAKTIF') {
                    $statusClean = 'NON-AKTIF';
                } else {
                    throw new \Exception("⚠️ Baris ke-{$lineNumber}: Status [{$status}] ditolak! Status yang didukung hanya: 'AKTIF', 'PENDING', atau 'NON-AKTIF'.");
                }

                // 7. Cek apakah baris ini merupakan kelanjutan / pelengkap data mentor yang sebelumnya belum lengkap
                $existingSameMentor = null;

                if ($nidnClean !== null) {
                    $cekNidnDb = DB::table('trainers')->where('nidn', $nidnClean)->first();
                    if ($cekNidnDb) {
                        $dbNama = strtoupper(trim(preg_replace('/\s+/', ' ', (string) $cekNidnDb->nama_mentor)));
                        $kontakConflict = !$isEmptyField($cekNidnDb->kontak) && !$isEmptyField($kontakFormatted) && trim((string) $cekNidnDb->kontak) !== $kontakFormatted;
                        $emailConflict  = !$isEmptyField($cekNidnDb->email) && !$isEmptyField($emailClean) && strtolower(trim((string) $cekNidnDb->email)) !== $emailClean;

                        if ($dbNama === $namaClean && !$kontakConflict && !$emailConflict) {
                            $existingSameMentor = $cekNidnDb;
                        } else {
                            throw new \Exception("⚠️ Peringatan Baris ke-{$lineNumber}: NIK/NIDN [{$nidnClean}] sudah terdaftar atas nama mentor '{$cekNidnDb->nama_mentor}'! Tidak boleh menggunakan NIK/NIDN yang sama.");
                        }
                    }
                }

                // Jika belum ketemu via NIDN (misal import sebelumnya NIDN masih kosong), cek apakah ada record mentor yang sama yang datanya belum lengkap
                if (!$existingSameMentor) {
                    $candidatesByName = DB::table('trainers')->where('nama_mentor', $namaClean)->get();
                    foreach ($candidatesByName as $cand) {
                        $nidnConflict   = !$isEmptyField($cand->nidn) && !$isEmptyField($nidnClean) && trim((string) $cand->nidn) !== $nidnClean;
                        $kontakConflict = !$isEmptyField($cand->kontak) && !$isEmptyField($kontakFormatted) && trim((string) $cand->kontak) !== $kontakFormatted;
                        $emailConflict  = !$isEmptyField($cand->email) && !$isEmptyField($emailClean) && strtolower(trim((string) $cand->email)) !== $emailClean;

                        if (!$nidnConflict && !$kontakConflict && !$emailConflict) {
                            $sameKontak = !$isEmptyField($cand->kontak) && !$isEmptyField($kontakFormatted) && trim((string) $cand->kontak) === $kontakFormatted;
                            $sameEmail  = !$isEmptyField($cand->email) && !$isEmptyField($emailClean) && strtolower(trim((string) $cand->email)) === $emailClean;
                            if ($isRecordIncomplete($cand) || $sameKontak || $sameEmail) {
                                $existingSameMentor = $cand;
                                break;
                            }
                        }
                    }
                }

                // 8. Simpan ke Database (Lengkapi field yang kosong saja untuk mentor yang belum lengkap, atau Insert baru tanpa menimpa mentor lain)
                try {
                    if ($existingSameMentor) {
                        // Pastikan data baru yang dilengkapi tidak bentrok dengan mentor LAIN di database
                        if ($nidnClean) {
                            $otherNidn = DB::table('trainers')->where('nidn', $nidnClean)->where('id', '!=', $existingSameMentor->id)->first();
                            if ($otherNidn) {
                                throw new \Exception("⚠️ Peringatan Baris ke-{$lineNumber}: NIK/NIDN [{$nidnClean}] sudah terdaftar atas nama mentor '{$otherNidn->nama_mentor}'! Tidak boleh menggunakan NIK/NIDN yang sama.");
                            }
                        }
                        if ($emailClean) {
                            $otherEmail = DB::table('trainers')->where('email', $emailClean)->where('id', '!=', $existingSameMentor->id)->first();
                            if ($otherEmail) {
                                throw new \Exception("⚠️ Peringatan Baris ke-{$lineNumber}: Email [{$emailClean}] sudah digunakan oleh mentor '{$otherEmail->nama_mentor}'.");
                            }
                        }
                        if ($kontakFormatted) {
                            $otherKontak = DB::table('trainers')->where('kontak', $kontakFormatted)->where('id', '!=', $existingSameMentor->id)->first();
                            if ($otherKontak) {
                                throw new \Exception("⚠️ Peringatan Baris ke-{$lineNumber}: Nomor kontak [{$kontakFormatted}] sudah terdaftar atas nama mentor '{$otherKontak->nama_mentor}'! Nomor telepon tidak boleh sama.");
                            }
                        }

                        // Hanya ambil / isi data yang sebelumnya belum lengkap (kosong / null / '-')
                        $updateFields = [];
                        if ($isEmptyField($existingSameMentor->nidn) && !$isEmptyField($nidnClean)) {
                            $updateFields['nidn'] = $nidnClean;
                        }
                        if ($isEmptyField($existingSameMentor->ttl) && !$isEmptyField($ttlClean)) {
                            $updateFields['ttl'] = $ttlClean;
                        }
                        if ($isEmptyField($existingSameMentor->email) && !$isEmptyField($emailClean)) {
                            $updateFields['email'] = $emailClean;
                        }
                        if ($isEmptyField($existingSameMentor->kontak) && !$isEmptyField($kontakFormatted)) {
                            $updateFields['kontak'] = $kontakFormatted;
                        }
                        if ($isEmptyField($existingSameMentor->program_pelatihan) && !$isEmptyField($programClean)) {
                            $updateFields['program_pelatihan'] = $programClean;
                        }
                        if ($isEmptyField($existingSameMentor->status) && !$isEmptyField($statusClean)) {
                            $updateFields['status'] = $statusClean;
                        }

                        if (!empty($updateFields)) {
                            $updateFields['updated_at'] = now();
                            DB::table('trainers')->where('id', $existingSameMentor->id)->update($updateFields);
                            $completedIncompleteCount++;
                        } else {
                            $skippedAlreadyCompleteRows[] = [
                                'line' => $lineNumber,
                                'nidn' => $nidnClean ?: ($existingSameMentor->nidn ?? '-'),
                                'nama' => $existingSameMentor->nama_mentor,
                            ];
                        }
                    } else {
                        // Mentor Baru: Pastikan NIK/NIDN, Email, dan Nomor Kontak belum dipakai mentor lain
                        if ($nidnClean) {
                            $cekNidnDb = DB::table('trainers')->where('nidn', $nidnClean)->first();
                            if ($cekNidnDb) {
                                throw new \Exception("⚠️ Peringatan Baris ke-{$lineNumber}: NIK/NIDN [{$nidnClean}] sudah terdaftar atas nama mentor '{$cekNidnDb->nama_mentor}'! Tidak boleh menggunakan NIK/NIDN yang sama.");
                            }
                        }
                        if ($emailClean) {
                            $cekEmailDb = DB::table('trainers')->where('email', $emailClean)->first();
                            if ($cekEmailDb) {
                                throw new \Exception("⚠️ Peringatan Baris ke-{$lineNumber}: Email [{$emailClean}] sudah digunakan oleh mentor '{$cekEmailDb->nama_mentor}'.");
                            }
                        }
                        if ($kontakFormatted) {
                            $cekKontakDb = DB::table('trainers')->where('kontak', $kontakFormatted)->first();
                            if ($cekKontakDb) {
                                throw new \Exception("⚠️ Peringatan Baris ke-{$lineNumber}: Nomor kontak [{$kontakFormatted}] sudah terdaftar atas nama mentor '{$cekKontakDb->nama_mentor}'! Nomor telepon tidak boleh sama.");
                            }
                        }

                        // Insert sebagai data mentor baru (TIDAK menimpa mentor yang namanya sama)
                        DB::table('trainers')->insert([
                            'nama_mentor'       => $namaClean,
                            'nidn'              => $nidnClean,
                            'ttl'               => $ttlClean,
                            'email'             => $emailClean,
                            'kontak'            => $kontakFormatted,
                            'program_pelatihan' => $programClean,
                            'status'            => $statusClean,
                            'created_at'        => now(),
                            'updated_at'        => now(),
                        ]);
                        $insertedCount++;
                    }
                } catch (\Exception $e) {
                    if (str_contains($e->getMessage(), 'trainers.nidn') || str_contains($e->getMessage(), 'UNIQUE constraint failed: trainers.nidn')) {
                        throw new \Exception("⚠️ Peringatan Baris ke-{$lineNumber}: NIK/NIDN [{$nidnClean}] sudah terdaftar pada mentor lain di sistem!");
                    }
                    if (str_contains($e->getMessage(), 'trainers.email') || str_contains($e->getMessage(), 'UNIQUE constraint failed: trainers.email')) {
                        throw new \Exception("⚠️ Peringatan Baris ke-{$lineNumber}: Email [{$emailClean}] sudah digunakan oleh mentor lain.");
                    }
                    throw new \Exception($e->getMessage());
                }
            }

            if ($insertedCount === 0 && $completedIncompleteCount === 0 && !empty($skippedAlreadyCompleteRows)) {
                $firstDup = $skippedAlreadyCompleteRows[0];
                throw new \Exception("⚠️ Peringatan Baris ke-{$firstDup['line']}: NIK/NIDN [{$firstDup['nidn']}] sudah terdaftar atas nama mentor '{$firstDup['nama']}'! Tidak boleh menggunakan NIK/NIDN yang sama.");
            }
        });
    }
}