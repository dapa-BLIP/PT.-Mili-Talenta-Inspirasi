<?php

namespace App\Exports;

use App\Models\Pelatihan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PelatihanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    public function collection()
    {
        return Pelatihan::with('mentor')->get();
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function headings(): array
    {
        return ['NO.', 'NAMA PROGRAM PELATIHAN', 'MENTOR PENGAMPU', 'KOMPONEN PENILAIAN', 'KATEGORI SPEKTRUM'];
    }

    public function map($p): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        $komponenString = is_array($p->komponen_nilai) ? implode(', ', $p->komponen_nilai) : '';
        return [
            sprintf('%03d', $rowNumber),
            strtoupper($p->nama_pelatihan),
            strtoupper($p->mentor->nama_mentor ?? 'BELUM DITUNJUK'),
            strtoupper($komponenString),
            strtoupper($p->spektrum_kategori),
        ];
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
        
        $sheet->getCell('A2')->setValue("REPOSITORI REKORD DATA MASTER KELAS PELATIHAN");
        $sheet->getStyle('A2')->getFont()->applyFromArray([
            'name' => 'Arial', 'size' => 11, 'bold' => true, 'color' => ['rgb' => '475569']
        ]);

        $sheet->getCell('A4')->setValue("Tipe Dokumen:");
        $sheet->getCell('B4')->setValue("MASTER ACADEMIC HUB CONFIG");
        $sheet->getCell('A5')->setValue("Tanggal Unduh:");
        $sheet->getCell('B5')->setValue(date('d F Y, H:i') . ' WIB');
        
        // 🎨 STYLING METADATA: Warna Ungu Solid dengan Teks Putih Kontras kyk Mockup
        $sheet->getStyle('A4:A5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']]);
        $sheet->getStyle('B4:B5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => false, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']]);

        $sheet->getStyle('A4:B5')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '16365C'], 
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // --- Header Styling (Ungu Spektrum Premium) ---
        $sheet->getStyle('A8:' . $highestColumn . '8')->applyFromArray([
            'font' => [
                'name' => 'Arial', 'bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16365C'],
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
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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

        // --- FIX LEBAR KOLOM MANUAL ---
        $widths = ['A' => 16, 'B' => 35, 'C' => 28, 'D' => 41, 'E' => 30];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        return [];
    }
}