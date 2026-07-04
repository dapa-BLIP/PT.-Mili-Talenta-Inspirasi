<?php

namespace App\Exports;

use App\Models\Peserta;
use App\Models\Pelatihan;
use App\Models\Nilai;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// 🛠️ FIX KRUSIAL: Menghapus ShouldAutoSize biar lebar kolom A (NO.) tidak dipaksa melar oleh teks judul dokumen
class NilaiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    protected $pelatihanId;
    protected $sektor;
    protected $komponenNilai = [];

    public function __construct($pelatihanId, $sektor)
    {
        $this->pelatihanId = $pelatihanId;
        $this->sektor = $sektor;

        $pelatihan = Pelatihan::find($pelatihanId);
        if ($pelatihan && is_array($pelatihan->komponen_nilai)) {
            $this->komponenNilai = $pelatihan->komponen_nilai;
        }
    }

    public function collection()
    {
        return Peserta::where('pelatihan', $this->sektor)->orderBy('nama_lengkap', 'asc')->get();
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function headings(): array
    {
        $headers = ['NO.', 'NAMA PESERTA'];
        foreach ($this->komponenNilai as $komponen) {
            $headers[] = strtoupper(str_replace('_', ' ', $komponen));
        }
        $headers[] = 'CATATAN';
        return $headers;
    }

    public function map($row): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        
        $nilaiExisting = Nilai::where('peserta_id', $row->id)->where('pelatihan_id', $this->pelatihanId)->first();
        $subScores = $nilaiExisting && is_array($nilaiExisting->scores) ? $nilaiExisting->scores : [];

        $output = [
            sprintf('%03d', $rowNumber),
            strtoupper($row->nama_lengkap)
        ];

        foreach ($this->komponenNilai as $komponen) {
            $keyLower = strtolower($komponen);
            $output[] = isset($subScores[$keyLower]) ? $subScores[$keyLower] : '';
        }

        $output[] = $nilaiExisting ? $nilaiExisting->catatan : '';
        return $output;
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // --- Basa Basi Judul Dokumen (Sisi Atas) ---
        $sheet->getCell('A1')->setValue("PT MILI TALENTA INSPIRASI");
        $sheet->getStyle('A1')->getFont()->applyFromArray([
            'name' => 'Arial', 'size' => 16, 'bold' => true, 'color' => ['rgb' => '1E293B']
        ]);
        
        $sheet->getCell('A2')->setValue("LAPORAN MATRIKS KOMPETENSI PENILAIAN MAHASISWA");
        $sheet->getStyle('A2')->getFont()->applyFromArray([
            'name' => 'Arial', 'size' => 11, 'bold' => true, 'color' => ['rgb' => '475569']
        ]);

        $sheet->getCell('A4')->setValue("Sektor Klaster:");
        $sheet->getCell('B4')->setValue(strtoupper($this->sektor));
        $sheet->getCell('A5')->setValue("Tanggal Unduh:");
        $sheet->getCell('B5')->setValue(date('d F Y, H:i') . ' WIB');
        
        $sheet->getStyle('A4:A5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']]);
        $sheet->getStyle('B4:B5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => false, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']]);

        // 🎨 STYLING METADATA ATAS: Solid Biru PT MILI dengan Teks Putih Kontras
        $sheet->getStyle('A4:B5')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '22A0F6'], 
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // --- Header Styling (Biru PT MILI) ---
        $sheet->getStyle('A8:' . $highestColumn . '8')->applyFromArray([
            'font' => [
                'name' => 'Arial', 'bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '22A0F6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);
        $sheet->getRowDimension(8)->setRowHeight(25);

        // --- Body Alignment ---
        for ($row = 9; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('C' . $row . ':' . $highestColumn . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($highestColumn . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        // --- Solid Hitam Border Tabel ---
        $sheet->getStyle('A8:' . $highestColumn . $highestRow)->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 10],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // --- 🛠️ LOCK UKURAN LEBAR KOLOM SECARA MANUAL (ANTI MELAR GAIB) ---
        $sheet->getColumnDimension('A')->setWidth(16);   // NO kembali kecil singset premium
        $sheet->getColumnDimension('B')->setWidth(35);  // Nama Peserta dapet space lega rapi
        
        // Loop manual untuk set lebar kolom nilai dinamis (C, D, E dst) sampai kolom Catatan paling akhir
        for ($col = 'C'; $col <= $highestColumn; $col++) {
            $sheet->getColumnDimension($col)->setWidth(18);
        }

        return [];
    }
}