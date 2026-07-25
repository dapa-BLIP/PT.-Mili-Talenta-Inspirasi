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
            'NIDN / NIK', 
            'TEMPAT TANGGAL LAHIR', 
            'EMAIL', 
            'KONTAK HP', 
            'SEKTOR KEAHLIAN', 
            'STATUS'
        ];
    }

    public function map($row): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        return [
            sprintf('%03d', $rowNumber),
            strtoupper($row->nama_mentor),
            $row->nidn ?? '-',
            strtoupper($row->ttl ?? '-'),
            $row->email,
            "'" . ($row->kontak ?? '-'),
            strtoupper($row->sektor_keahlian),
            strtoupper($row->status ?? 'AKTIF')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        // --- Basa Basi Judul Dokumen (Sisi Atas) ---
        $sheet->getCell('A1')->setValue("PT MILI TALENTA INSPIRASI");
        $sheet->getStyle('A1')->getFont()->applyFromArray(['name' => 'Arial', 'size' => 16, 'bold' => true, 'color' => ['rgb' => '1E293B']]);
        
        $sheet->getCell('A2')->setValue("DATABASE INDEKS MASTER MENTOR PENGAMPU");
        $sheet->getStyle('A2')->getFont()->applyFromArray(['name' => 'Arial', 'size' => 11, 'bold' => true, 'color' => ['rgb' => '475569']]);

        $sheet->getCell('A4')->setValue("Kategori Berkas:");
        $sheet->getCell('B4')->setValue("DATA MASTER OPERASIONAL");
        $sheet->getCell('A5')->setValue("Tanggal Unduh:");
        $sheet->getCell('B5')->setValue(date('d F Y, H:i') . ' WIB');
        
        $sheet->getStyle('A4:A5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']]);
        $sheet->getStyle('B4:B5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => false, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']]);

        $sheet->getStyle('A4:B5')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

       
        $sheet->getStyle('A8:H8')->applyFromArray([
            'font' => ['name' => 'Arial', 'bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(8)->setRowHeight(25);

    
        for ($row = 9; $row <= $highestRow; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(20);
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // NO
            $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);   // NAMA
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // NIDN
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // TTL
            $sheet->getStyle('E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);   // EMAIL
            $sheet->getStyle('F' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // HP
            $sheet->getStyle('G' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // SEKTOR
            $sheet->getStyle('H' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // STATUS
        }

        
        $sheet->getStyle('A8:H' . $highestRow)->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 10],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

       
        $widths = ['A' => 16, 'B' => 30, 'C' => 18, 'D' => 25, 'E' => 25, 'F' => 22, 'G' => 27, 'H' => 15];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        return [];
    }
}