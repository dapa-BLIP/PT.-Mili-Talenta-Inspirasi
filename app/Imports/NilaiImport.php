<?php

namespace App\Imports;

use App\Models\Nilai;
use App\Models\Pelatihan;
use App\Models\Peserta;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class NilaiImport implements ToCollection, WithHeadingRow
{
    protected $pelatihanId;
    protected $trainerId;

    public function __construct($pelatihanId, $trainerId)
    {
        $this->pelatihanId = $pelatihanId;
        $this->trainerId = $trainerId;
    }

    public function headingRow(): int
    {
        return 8;
    }

    public function collection(Collection $rows)
    {
        $pelatihan = Pelatihan::find($this->pelatihanId);
        $komponenNilai = ($pelatihan && is_array($pelatihan->komponen_nilai)) ? $pelatihan->komponen_nilai : [];

        foreach ($rows as $row) {
            $pesertaId = $row['id_peserta'] ?? null;
            if (!$pesertaId) {
                continue;
            }

            $idClean = (int) preg_replace('/[^0-9]/', '', (string) $pesertaId);
            if (!$idClean || !Peserta::where('id', $idClean)->exists()) {
                continue;
            }

            $scores = [];
            foreach ($komponenNilai as $komponen) {
                $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '_', trim($komponen)));
                if (isset($row[$slug]) && $row[$slug] !== '' && $row[$slug] !== null) {
                    $val = is_numeric($row[$slug]) ? max(0, min(100, (float) $row[$slug])) : 0;
                    $scores[strtolower($komponen)] = $val;
                }
            }

            $catatan = isset($row['catatan']) ? trim((string) $row['catatan']) : null;

            Nilai::updateOrCreate(
                [
                    'peserta_id'   => $idClean,
                    'pelatihan_id' => $this->pelatihanId,
                ],
                [
                    'trainer_id' => $this->trainerId,
                    'scores'     => $scores,
                    'catatan'    => $catatan ?: null,
                ]
            );
        }
    }
}

