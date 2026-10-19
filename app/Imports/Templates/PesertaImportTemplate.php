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

class PesertaImportTemplate implements FromArray, WithHeadings, WithStyles, WithCustomStartCell
{
    public function startCell(): string
    {
        return 'A8';
    }

    public function headings(): array
    {
        return [
            'NO.',
            'NAMA LENGKAP',
            'GENDER',
            'TEMPAT, TANGGAL LAHIR',
            'EMAIL',
            'NO. HANDPHONE',
            'TEMPAT KERJA',
            'SEKTOR PELATIHAN',
            'STATUS VERIFIKASI',
            'TANGGAL DAFTAR',
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
                '-',
                '-',
                '-',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        $sheet->getCell('A1')->setValue("PT MILI TALENTA INSPIRASI");
        $sheet->getStyle('A1')->getFont()->applyFromArray([
            'name' => 'Arial', 'size' => 16, 'bold' => true, 'color' => ['rgb' => '1E293B']
        ]);

        $sheet->getCell('A2')->setValue("TEMPLATE IMPORT REGISTRASI PESERTA PELATIHAN");
        $sheet->getStyle('A2')->getFont()->applyFromArray([
            'name' => 'Arial', 'bold' => true, 'size' => 11, 'color' => ['rgb' => '475569']
        ]);

        $sheet->getCell('A4')->setValue("Kategori Berkas:");
        $sheet->getCell('B4')->setValue("TEMPLATE PESERTA");
        $sheet->getCell('A5')->setValue("Tanggal Unduh:");
        $sheet->getCell('B5')->setValue(date('d F Y, H:i') . ' WIB');

        $sheet->getStyle('A4:A5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']]);
        $sheet->getStyle('B4:B5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => false, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']]);

        $sheet->getStyle('A4:B5')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

        $sheet->getStyle('A8:' . $highestColumn . '8')->applyFromArray([
            'font' => ['name' => 'Arial', 'bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $sheet->getRowDimension(8)->setRowHeight(25);

        for ($i = 9; $i <= $highestRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
            $sheet->getStyle('A' . $i . ':' . $highestColumn . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->getStyle("A8:{$highestColumn}{$highestRow}")->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 10],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

        $widths = ['A' => 12, 'B' => 28, 'C' => 14, 'D' => 25, 'E' => 26, 'F' => 18, 'G' => 24, 'H' => 27, 'I' => 20, 'J' => 19];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        return [];
    }
}

