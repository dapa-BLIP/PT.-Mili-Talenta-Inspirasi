<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\Pelatihan;
use App\Models\Materi;
use App\Models\KategoriPelatihan;
use Illuminate\Http\Request;
use App\Exports\PelatihanExport;
use App\Imports\PelatihanImport;
use App\Imports\Templates\PelatihanImportTemplate;
use Maatwebsite\Excel\Facades\Excel;

class PelatihanController extends Controller
{
    public function index()
    {
        $pelatihans = Pelatihan::with('mentor')->latest()->get();
        $materis = Materi::with('trainer')->latest()->get(); 
        $kategoriPelatihans = KategoriPelatihan::orderBy('nama_kategori', 'asc')->get();
        $trainers = Trainer::where('status', 'AKTIF')->orderBy('nama_mentor', 'asc')->get();

        return view('academic.pelatihan', compact('pelatihans', 'materis', 'kategoriPelatihans', 'trainers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelatihan'  => 'required|string|max:255',
            'jenis_pelatihan' => 'required|string',
            'trainer_id'      => 'required|exists:trainers,id',
            'raw_komponen'    => 'required|string',
            'materi_ids'      => 'nullable|array', 
        ]);

        $namaClean = strtoupper(trim((string)$request->nama_pelatihan));

        $sudahAda = Pelatihan::where('nama_pelatihan', $namaClean)
            ->where('trainer_id', $request->trainer_id)
            ->first();

        if ($sudahAda) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'duplikat' => "Program '{$namaClean}' dengan mentor yang dipilih sudah terdaftar di sistem! Silakan gunakan tombol Edit pada tabel jika ingin mengubahnya."
                ]);
        }

        $komponenFinal = $this->parseAndIndexKomponen($request->raw_komponen);

        Pelatihan::create([
            'nama_pelatihan'  => $namaClean,
            'jenis_pelatihan' => strtoupper(trim((string)$request->jenis_pelatihan)),
            'trainer_id'      => $request->trainer_id,
            'komponen_nilai'  => $komponenFinal,
            'materi_ids'      => $request->materi_ids ?? [], 
        ]);

        return redirect()->route('academic.pelatihan.index')->with('success', 'Master Pelatihan & Kurikulum berhasil dibukukan.');
    }

    public function update(Request $request, $id)
    {
        $pelatihan = Pelatihan::findOrFail($id);

        $request->validate([
            'nama_pelatihan'  => 'required|string|max:255',
            'jenis_pelatihan' => 'required|string',
            'trainer_id'      => 'required|exists:trainers,id',
            'raw_komponen'    => 'required|string',
            'materi_ids'      => 'nullable|array', 
        ]);

        $namaClean = strtoupper(trim((string)$request->nama_pelatihan));

        $sudahAda = Pelatihan::where('nama_pelatihan', $namaClean)
            ->where('trainer_id', $request->trainer_id)
            ->where('id', '!=', $id)
            ->first();

        if ($sudahAda) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'duplikat' => "Kombinasi program '{$namaClean}' dan mentor tersebut sudah digunakan oleh kelas lain."
                ]);
        }

        $komponenFinal = $this->parseAndIndexKomponen($request->raw_komponen);

        $pelatihan->update([
            'nama_pelatihan'  => $namaClean,
            'jenis_pelatihan' => strtoupper(trim((string)$request->jenis_pelatihan)),
            'trainer_id'      => $request->trainer_id,
            'komponen_nilai'  => $komponenFinal,
            'materi_ids'      => $request->materi_ids ?? [], 
        ]);

        return redirect()->route('academic.pelatihan.index')->with('success', 'Master Pelatihan berhasil di-update & disinkronkan.');
    }

    public function destroy($id)
    {
        Pelatihan::findOrFail($id)->delete();
        return redirect()->route('academic.pelatihan.index')->with('success', 'Data Master Pelatihan berhasil dihapus.');
    }

    public function export()
    {
        return Excel::download(new PelatihanExport, 'MASTER_PELATIHAN_PMI_' . date('Ymd_His') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            $import = new PelatihanImport();
            Excel::import($import, $request->file('file_excel'));

            $rincian = [];
            if ($import->createdCount > 0) {
                $rincian[] = "{$import->createdCount} kelas baru ditambahkan";
            }
            if ($import->updatedCount > 0) {
                $rincian[] = "{$import->updatedCount} kelas diperluas komponen nilainya";
            }
            if ($import->skippedCount > 0) {
                $rincian[] = "{$import->skippedCount} baris dilewati (identik)";
            }

            $pesan = !empty($rincian) 
                ? 'Import selesai: ' . implode(', ', $rincian) . '.' 
                : 'Repositori Kelas Pelatihan Berhasil Disinkronisasi!';

            return redirect()->route('academic.pelatihan.index')->with('success', $pesan);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withErrors(['import_error' => $e->getMessage()]);
        }
    }

    public function template()
    {
        return Excel::download(new PelatihanImportTemplate, 'TEMPLATE_IMPORT_PELATIHAN.xlsx');
    }

    private function parseAndIndexKomponen($rawString)
    {
        $rawArray = array_map(function($item) {
            return strtoupper(trim((string)$item));
        }, explode(',', (string)$rawString));

        $filteredArray = array_filter($rawArray, fn($item) => $item !== '');
        $uniqueArray = array_unique($filteredArray);

        return array_values($uniqueArray);
    }
}