<?php

namespace App\Exports;

use App\Models\Trainer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MentorExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    public function collection()
    {
        return Trainer::orderBy('nama_mentor', 'asc')->get();
    }

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

    public function map($row): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        // Normalisasi format +62 dan pembatasan maksimal digit
        $kontakDisplay = '-';
        if ($row->kontak) {
            $digitsOnly = preg_replace('/[^0-9]/', '', (string)$row->kontak);
            $localDigits = preg_replace('/^(0|62)/', '', $digitsOnly);
            
            // Batasi maksimal 13 digit nomor lokal (standar operator Indonesia)
            $localDigits = substr($localDigits, 0, 11);
            
            if (!empty($localDigits)) {
                $kontakDisplay = "'+62" . $localDigits;
            }
        }

        return [
            sprintf('%03d', $rowNumber),
            strtoupper($row->nama_mentor),
            $row->nidn ? "'" . $row->nidn : '-',
            strtoupper($row->ttl ?? '-'),
            $row->email ?? '-',
            $kontakDisplay,
            strtoupper($row->program_pelatihan ?? '-'),
            strtoupper($row->status ?? 'AKTIF')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // --- Header Judul Dokumen ---
        $sheet->getCell('A1')->setValue("PT MILI TALENTA INSPIRASI");
        $sheet->getStyle('A1')->getFont()->applyFromArray([
            'name' => 'Arial', 'size' => 16, 'bold' => true, 'color' => ['rgb' => '1E293B']
        ]);
        
        $sheet->getCell('A2')->setValue("DATABASE MENTOR PELATIHAN");
        $sheet->getStyle('A2')->getFont()->applyFromArray([
            'name' => 'Arial', 'size' => 11, 'bold' => true, 'color' => ['rgb' => '475569']
        ]);

        // --- Metadata Dokumen ---
        $sheet->getCell('A4')->setValue("Kategori Berkas:");
        $sheet->getCell('B4')->setValue("DATA MENTOR");
        $sheet->getCell('A5')->setValue("Tanggal Unduh:");
        $sheet->getCell('B5')->setValue(date('d F Y, H:i') . ' WIB');
        
        $sheet->getStyle('A4:A5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']]);
        $sheet->getStyle('B4:B5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => false, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']]);

        $sheet->getStyle('A4:B5')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

        // --- Header Tabel (Emerald Premium) ---
        $sheet->getStyle('A8:H8')->applyFromArray([
            'font' => ['name' => 'Arial', 'bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(8)->setRowHeight(25);

        // --- Alignment Tiap Kolom Data ---
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

        // --- Border Grid Tabel ---
        $sheet->getStyle('A8:H' . $highestRow)->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 10],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

        // --- Lebar Kolom Ergonomis ---
        $widths = ['A' => 15, 'B' => 32, 'C' => 18, 'D' => 28, 'E' => 28, 'F' => 22, 'G' => 26, 'H' => 15];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        return [];
    }
}