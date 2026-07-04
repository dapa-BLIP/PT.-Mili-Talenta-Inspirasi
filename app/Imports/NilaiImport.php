<?php

namespace App\Imports;

use App\Models\Nilai;
use App\Models\Pelatihan;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class NilaiImport implements ToCollection, WithHeadingRow
{
    protected $pelatihanId;
    protected $trainerId;
    protected $komponenNilai = [];

    public function __construct($pelatihanId, $trainerId)
    {
        $this->pelatihanId = $pelatihanId;
        $this->trainerId = $trainerId;

        // Ambil template acuan komponen nilai terdaftar untuk validasi key impor
        $pelatihan = Pelatihan::find($pelatihanId);
        if ($pelatihan && is_array($pelatihan->komponen_nilai)) {
            $this->komponenNilai = $pelatihan->komponen_nilai;
        }
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // 1. Tangkap ID Peserta
            $pesertaId = isset($row['id_peserta']) ? trim($row['id_peserta']) : null;

            if (empty($pesertaId) || !is_numeric($pesertaId)) {
                continue; // Skip baris header atau kosong
            }

            // 2. Kumpulkan sub-scores nilai dinamis berdasarkan heading Excel
            $scoresPayload = [];
            $hasScoreInput = false;

            foreach ($this->komponenNilai as $komponen) {
                // Maatwebsite WithHeadingRow otomatis mengubah "UTS" atau "NILAI TUGAS" jadi "uts" atau "nilai_tugas"
                $excelKey = strtolower(str_replace(' ', '_', $komponen));
                
                if (isset($row[$excelKey]) && trim($row[$excelKey]) !== '') {
                    $scoresPayload[strtolower($komponen)] = (int)$row[$excelKey];
                    $hasScoreInput = true;
                }
            }

            // 3. Tangkap kolom catatan evaluasi paling ujung
            $catatan = isset($row['catatan']) && trim($row['catatan']) !== '' ? trim($row['catatan']) : null;

            // Jika baris excel kosong tanpa isi nilai / catatan, lewati
            if (!$hasScoreInput && empty($catatan)) {
                continue;
            }

            // 4. Lakukan Upsert (Update or Insert) aman ke tabel nilais terpusat
            Nilai::updateOrCreate(
                [
                    'peserta_id'   => $pesertaId,
                    'pelatihan_id' => $this->pelatihanId,
                ],
                [
                    'trainer_id' => $this->trainerId,
                    'scores'     => $scoresPayload, // Masuk otomatis sebagai format JSON bersih
                    'catatan'    => $catatan,
                ]
            );
        }
    }
}