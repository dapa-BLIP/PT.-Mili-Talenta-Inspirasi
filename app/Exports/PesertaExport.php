<?php

namespace App\Exports;

use App\Models\Peserta;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class PesertaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithCustomStartCell
{
    public function collection()
    {
        return Peserta::orderBy('nama_lengkap', 'asc')->get();
    }

    public function startCell(): string
    {
        return 'A8';
    }

    public function headings(): array
    {
        return [
            'NO.', 
            'ID PESERTA', 
            'NAMA LENGKAP', 
            'GENDER', 
            'TEMPAT, TANGGAL LAHIR', 
            'EMAIL', 
            'NO. HANDPHONE', 
            'TEMPAT KERJA', 
            'SEKTOR PELATIHAN', 
            'STATUS VERIFIKASI', 
            'TANGGAL DAFTAR'
        ];
    }

    public function map($peserta): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        return [
            sprintf('%03d', $rowNumber),            // Kolom A: NO. biasa (001, 002)
            
            // 🚀 FIX LOGIKAL: Ditambahkan tanda petik single di depan agar Excel membaca murni sebagai STRING TEXT 8 Digit, gak diringkas otomatis jadi angka satuan lagi cook!
            "'" . sprintf('%08d', $peserta->id),    // Kolom B: ID PESERTA (00000007, 00000008)
            
            strtoupper($peserta->nama_lengkap), 
            strtoupper($peserta->gender ?? 'PRIA'), 
            $peserta->ttl ?: 'Bekasi, ' . date('d-m-Y'), 
            $peserta->email,
            "'" . ($peserta->no_hp ?? '-'), 
            strtoupper($peserta->tempat_kerja ?? '-'), 
            strtoupper($peserta->pelatihan), 
            strtoupper($peserta->status ?? 'PENDING'), 
            $peserta->created_at ? $peserta->created_at->format('d-m-Y') : date('d-m-Y')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();

        $sheet->getCell('A1')->setValue("PT MILI TALENTA INSPIRASI");
        $sheet->getStyle('A1')->getFont()->applyFromArray(['name' => 'Arial', 'size' => 16, 'bold' => true, 'color' => ['rgb' => '1E293B']]);
        
        $sheet->getCell('A2')->setValue("DATA REPORT REGISTRASI PESERTA PELATIHAN TERPADU");
        $sheet->getStyle('A2')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => true, 'size' => 11, 'color' => ['rgb' => '475569']]);

        $sheet->getCell('A4')->setValue("Manajemen Sektor:");
        $sheet->getCell('B4')->setValue("PORTAL DATABASE UTAMA");
        $sheet->getCell('A5')->setValue("Tanggal Unduh:");
        $sheet->getCell('B5')->setValue(date('d F Y, H:i') . ' WIB');
        
        $sheet->getStyle('A4:A5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']]);
        $sheet->getStyle('B4:B5')->getFont()->applyFromArray(['name' => 'Arial', 'bold' => false, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']]);

        $sheet->getStyle('A4:B5')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '334155']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

        $sheet->getStyle('A8:K8')->applyFromArray([
            'font' => ['name' => 'Arial', 'bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '334155']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
        ]);
        $sheet->getRowDimension('8')->setRowHeight(25);

        for ($i = 9; $i <= $highestRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(20);
            $sheet->getStyle('A' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); 
            $sheet->getStyle('C' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('D' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('G' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle('I' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('K' . $i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->getStyle("A8:K{$highestRow}")->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 10],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

        $widths = ['A' => 16, 'B' => 18, 'C' => 25, 'D' => 12, 'E' => 25, 'F' => 26, 'G' => 18, 'H' => 22, 'I' => 27, 'J' => 20, 'K' => 19];
        foreach ($widths as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        return [];
    }
}