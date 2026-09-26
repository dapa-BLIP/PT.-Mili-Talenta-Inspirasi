<?php

namespace App\Imports\Templates;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PelatihanImportTemplate implements FromArray, WithHeadings, WithStyles, WithCustomStartCell
{
    public function startCell(): string
    {
        return 'A8';
    }

    public function headings(): array
    {
        return [
            'NO.', 
            'NAMA PROGRAM PELATIHAN', 
            'MENTOR PENGAMPU', 
            'KOMPONEN PENILAIAN'
        ];
    }

    public function array(): array
    {
        return [
            [
                '001',
                'PEMROGRAMAN WEB LARAVEL',
                'AMI',
                'TUGAS, PRAKTIK, PROYEK AKHIR'
            ],
            [
                '002',
                'COMMUNICATION & LEADERSHIP',
                'KAMAL',
                'KEAKTIFAN, PRESENTASI, EVALUASI'
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // 1. Kop Surat
        $sheet->getCell('A1')->setValue("PT MILI TALENTA INSPIRASI");
        $sheet->getStyle('A1')->getFont()->applyFromArray([
            'name' => 'Arial', 'size' => 15, 'bold' => true, 'color' => ['rgb' => '1E293B']
        ]);

        $sheet->getCell('A2')->setValue("MASTER DATA PROGRAM PELATIHAN");
        $sheet->getStyle('A2')->getFont()->applyFromArray([
            'name' => 'Arial', 'size' => 11, 'bold' => true, 'color' => ['rgb' => '1E293B']
        ]);

        // 2. Metadata Box
        $sheet->getCell('A4')->setValue("Kategori Berkas:");
        $sheet->getCell('B4')->setValue("TEMPLATE IMPORT KELAS PELATIHAN");
        $sheet->getCell('A5')->setValue("Tanggal Unduh:");
        $sheet->getCell('B5')->setValue(date('d F Y, H:i') . ' WIB');

        $sheet->getStyle('A4:A5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']]);
        $sheet->getStyle('B4:B5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => false, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']]);

        $sheet->getStyle('A4:B5')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16365C']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

        // 3. Header Tabel (Baris 8)
        $sheet->getStyle('A8:' . $highestColumn . '8')->applyFromArray([
            'font' => ['name' => 'Arial', 'bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9.5],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16365C']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(8)->setRowHeight(25);

        // 4. Baris Isi Data
        for ($row = 9; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        }

        // 5. Border Hitam
        $sheet->getStyle('A8:' . $highestColumn . $highestRow)->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 9.5],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

        // 6. Lebar Kolom
        $widths = ['A' => 15, 'B' => 42, 'C' => 32, 'D' => 48];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        return [];
    }
}