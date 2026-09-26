<?php

namespace App\Imports;

use App\Models\Pelatihan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PelatihanImport implements ToCollection, WithHeadingRow
{
    public $createdCount = 0;
    public $updatedCount = 0;
    public $skippedCount = 0;

    public function headingRow(): int
    {
        return 8;
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            $lineNumber = 8;

            foreach ($rows as $row) {
                $lineNumber++;

                $namaPelatihanRaw = $row['nama_program_pelatihan'] ?? $row['program_pelatihan'] ?? $row['nama_pelatihan'] ?? null;
                $namaMentorRaw    = $row['mentor_pengampu'] ?? $row['mentor_pelatihan'] ?? $row['mentor'] ?? null;
                $komponenNilaiRaw = $row['komponen_penilaian'] ?? $row['penilaian'] ?? null;

                // Lewati baris kosong total
                if (empty($namaPelatihanRaw) && empty($namaMentorRaw) && empty($komponenNilaiRaw)) {
                    continue;
                }

                $namaPelatihanClean = $this->cleanString($namaPelatihanRaw);
                $mentorNameClean    = $this->cleanString($namaMentorRaw);

                // 1. Validasi Program Wajib Terdaftar di Master Modul (kategori_pelatihans)
                if (empty($namaPelatihanClean)) {
                    throw new \Exception("⚠️ Baris ke-{$lineNumber}: Kolom [NAMA PROGRAM PELATIHAN] tidak boleh kosong.");
                }

                $masterProgram = DB::table('kategori_pelatihans')
                    ->whereRaw('UPPER(TRIM(nama_kategori)) = ?', [$namaPelatihanClean])
                    ->first();

                if (!$masterProgram) {
                    throw new \Exception(
                        "⚠️ Baris ke-{$lineNumber}: Program '{$namaPelatihanRaw}' BELUM TERDAFTAR di sistem! " .
                        "Daftarkan terlebih dahulu di menu [Kelola Modul Pelatihan]."
                    );
                }

                // Tentukan Jenis Pelatihan dari Master
                $jenisPelatihan = $masterProgram->jenis_pelatihan ?? 'PELATIHAN TEKNIS';

                // 2. Validasi Mentor Wajib Terdaftar & Status AKTIF
                if (empty($mentorNameClean) || $mentorNameClean === 'BELUM DITUNJUK' || $mentorNameClean === '-') {
                    throw new \Exception("⚠️ Baris ke-{$lineNumber}: Kolom [MENTOR PENGAMPU] wajib diisi nama mentor aktif.");
                }

                $mentor = DB::table('trainers')
                    ->where('status', 'AKTIF')
                    ->whereRaw('UPPER(TRIM(nama_mentor)) = ?', [$mentorNameClean])
                    ->first();

                if (!$mentor) {
                    throw new \Exception(
                        "⚠️ Baris ke-{$lineNumber}: Mentor '{$namaMentorRaw}' TIDAK DITEMUKAN atau NON-AKTIF! " .
                        "Pastikan mentor sudah terdaftar dan berstatus aktif di menu [Kelola Mentor]."
                    );
                }

                // 3. Parsing Komponen Penilaian Bebas Dinamis
                $komponenBaruDariExcel = [];
                if (!empty($komponenNilaiRaw) && trim((string)$komponenNilaiRaw) !== '-') {
                    foreach (explode(',', (string)$komponenNilaiRaw) as $item) {
                        $clean = $this->cleanString($item);
                        if ($clean !== '') {
                            $komponenBaruDariExcel[] = $clean;
                        }
                    }
                    $komponenBaruDariExcel = array_values(array_unique($komponenBaruDariExcel));
                }

                // 4. Cek Keberadaan Data di Database
                $pelatihan = Pelatihan::where('nama_pelatihan', $masterProgram->nama_kategori)
                    ->where('trainer_id', $mentor->id)
                    ->first();

                if ($pelatihan) {
                    // SKENARIO A: UPDATE (Smart-Append Komponen Baru tanpa hapus nilai lama)
                    $komponenLama = is_array($pelatihan->komponen_nilai) 
                        ? $pelatihan->komponen_nilai 
                        : json_decode($pelatihan->komponen_nilai, true) ?? [];

                    $yangBenarBenarBaru = array_diff($komponenBaruDariExcel, $komponenLama);

                    if (!empty($yangBenarBenarBaru)) {
                        $komponenGabungan = array_values(array_unique(array_merge($komponenLama, $komponenBaruDariExcel)));

                        $pelatihan->update([
                            'komponen_nilai' => $komponenGabungan,
                        ]);

                        $this->updatedCount++;
                    } else {
                        $this->skippedCount++;
                    }
                } else {
                    // SKENARIO B: KELAS BARU (Insert Baru + Auto-Assign Seluruh Materi)
                    if (empty($komponenBaruDariExcel)) {
                        throw new \Exception(
                            "⚠️ Baris ke-{$lineNumber}: Kelas baru '{$masterProgram->nama_kategori}' wajib memiliki minimal 1 komponen penilaian di Excel."
                        );
                    }

                    // Otomatis tarik semua ID materi sesuai jenis pelatihan agar centang aktif semua
                    $autoMateriIds = DB::table('materis')
                        ->where('jenis_pelatihan', $jenisPelatihan)
                        ->pluck('id')
                        ->map(fn($id) => (string)$id)
                        ->toArray();

                    $payload = [
                        'nama_pelatihan'  => $masterProgram->nama_kategori,
                        'trainer_id'      => $mentor->id,
                        'komponen_nilai'  => $komponenBaruDariExcel,
                        'materi_ids'      => $autoMateriIds,
                        'jenis_pelatihan' => $jenisPelatihan,
                    ];

                    if (Schema::hasColumn('pelatihans', 'kategori_pelatihan_id')) {
                        $payload['kategori_pelatihan_id'] = $masterProgram->id;
                    }

                    Pelatihan::create($payload);
                    $this->createdCount++;
                }
            }
        });
    }

    /**
     * Pembersih teks dari karakter non-breaking space (0xA0) dan spasi ganda
     */
    private function cleanString(?string $str): string
    {
        if (!$str) return '';
        $cleaned = preg_replace('/[\x00-\x1F\x7F\xA0]/u', ' ', $str);
        return strtoupper(trim(preg_replace('/\s+/', ' ', $cleaned)));
    }
}