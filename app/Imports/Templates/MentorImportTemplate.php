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

class MentorImportTemplate implements FromArray, WithHeadings, WithStyles, WithCustomStartCell
{
    public function startCell(): string
    {
        return 'A8';
    }

    public function headings(): array
    {
        return [
            'NO.', 
            'NAMA LENGKAP MENTOR', 
            'NIK', 
            'TEMPAT TANGGAL LAHIR', 
            'EMAIL', 
            'KONTAK', 
            'PROGRAM PELATIHAN', 
            'STATUS'
        ];
    }

    public function array(): array
    {
        return [
            [
                '01',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-',
                '-'
            ],
            
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        // 1. Judul Kop Surat
        $sheet->getCell('A1')->setValue("PT MILI TALENTA INSPIRASI");
        $sheet->getStyle('A1')->getFont()->applyFromArray([
            'name' => 'Arial', 'size' => 15, 'bold' => true, 'color' => ['rgb' => '1E293B']
        ]);
        
        $sheet->getCell('A2')->setValue("DATABASE MENTOR PELATIHAN");
        $sheet->getStyle('A2')->getFont()->applyFromArray([
            'name' => 'Arial', 'size' => 11, 'bold' => true, 'color' => ['rgb' => '1E293B']
        ]);

        // 2. Metadata Box Hijau Baris 4-5
        $sheet->getCell('A4')->setValue("Kategori Berkas:");
        $sheet->getCell('B4')->setValue("TEMPLATE");
        $sheet->getCell('A5')->setValue("Tanggal Unduh:");
        $sheet->getCell('B5')->setValue(date('d F Y, H:i') . ' WIB');
        
        $sheet->getStyle('A4:A5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']]);
        $sheet->getStyle('B4:B5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => false, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']]);

        $sheet->getStyle('A4:B5')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00704A']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

        // 3. Header Tabel (Baris 8)
        $sheet->getStyle('A8:' . $highestColumn . '8')->applyFromArray([
            'font' => ['name' => 'Arial', 'bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 9.5],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '00704A']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(8)->setRowHeight(25);

        // 4. Baris Data Alignment
        for ($row = 9; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // 5. Border Kotak Penuh Hitam
        $sheet->getStyle('A8:' . $highestColumn . $highestRow)->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 9.5],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // 6. Lebar Kolom
        $widths = ['A' => 15, 'B' => 30, 'C' => 18, 'D' => 28, 'E' => 28, 'F' => 22, 'G' => 26, 'H' => 15];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        return [];
    }
}