<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\Pelatihan;
use App\Models\Materi;
use App\Models\KategoriPelatihan;
use App\Models\Peserta;
use App\Models\Nilai;
use Illuminate\Http\Request;
use App\Exports\PelatihanExport;
use App\Imports\PelatihanImport;
use App\Imports\Templates\PelatihanImportTemplate;
use Maatwebsite\Excel\Facades\Excel;

class PelatihanController extends Controller
{
    public function index()
    {
        $pelatihans = Pelatihan::with('mentor')->withCount('nilais')->latest()->get();
        $materis = Materi::with('trainer')->latest()->get(); 
        $kategoriPelatihans = KategoriPelatihan::orderBy('nama_kategori', 'asc')->get();
        $trainers = Trainer::where('status', 'AKTIF')->orderBy('nama_mentor', 'asc')->get();

        // Hitung peserta yang terdaftar pada setiap pelatihan
        $allPesertas = Peserta::select('pelatihan')->get();
        foreach ($pelatihans as $p) {
            $namaClean = strtolower(trim((string)$p->nama_pelatihan));
            $jenisClean = strtolower(trim((string)$p->jenis_pelatihan));
            $p->peserta_count = $allPesertas->filter(function($item) use ($namaClean, $jenisClean) {
                $pesertaProg = strtolower(trim((string)$item->pelatihan));
                return ($namaClean !== '' && $pesertaProg === $namaClean) ||
                       ($jenisClean !== '' && $pesertaProg === $jenisClean);
            })->count();
        }

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

        $namaClean  = strtoupper(trim((string) $request->nama_pelatihan));
        $jenisClean = strtoupper(trim((string) $request->jenis_pelatihan));

        $trainer = Trainer::findOrFail($request->trainer_id);
        $trainerProg = strtoupper(trim((string) $trainer->program_pelatihan));

        if ($trainerProg !== $jenisClean && $trainerProg !== $namaClean) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'trainer_id' => "Mentor '{$trainer->nama_mentor}' terdaftar pada program '{$trainer->program_pelatihan}', bukan pada program '{$jenisClean}'! Pilih mentor yang sesuai dengan program pelatihan."
                ]);
        }

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
            'jenis_pelatihan' => $jenisClean,
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

        $namaClean  = strtoupper(trim((string) $request->nama_pelatihan));
        $jenisClean = strtoupper(trim((string) $request->jenis_pelatihan));

        $trainer = Trainer::findOrFail($request->trainer_id);
        $trainerProg = strtoupper(trim((string) $trainer->program_pelatihan));

        if ($trainerProg !== $jenisClean && $trainerProg !== $namaClean) {
            return redirect()->back()
                ->withInput()
                ->withErrors([
                    'trainer_id' => "Mentor '{$trainer->nama_mentor}' terdaftar pada program '{$trainer->program_pelatihan}', bukan pada program '{$jenisClean}'! Pilih mentor yang sesuai dengan program pelatihan."
                ]);
        }

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
            'jenis_pelatihan' => $jenisClean,
            'trainer_id'      => $request->trainer_id,
            'komponen_nilai'  => $komponenFinal,
            'materi_ids'      => $request->materi_ids ?? [], 
        ]);

        return redirect()->route('academic.pelatihan.index')->with('success', 'Master Pelatihan berhasil di-update & disinkronkan.');
    }

    public function destroy($id)
    {
        $pelatihan = Pelatihan::findOrFail($id);

        $nilaiCount = Nilai::where('pelatihan_id', $pelatihan->id)->count();

        $namaLower = strtolower(trim((string)$pelatihan->nama_pelatihan));
        $jenisLower = strtolower(trim((string)$pelatihan->jenis_pelatihan));

        $pesertaCount = Peserta::where(function($q) use ($namaLower, $jenisLower) {
            if ($namaLower !== '') {
                $q->whereRaw('LOWER(TRIM(pelatihan)) = ?', [$namaLower]);
            }
            if ($jenisLower !== '' && $jenisLower !== $namaLower) {
                $q->orWhereRaw('LOWER(TRIM(pelatihan)) = ?', [$jenisLower]);
            }
        })->count();

        if ($pesertaCount > 0 || $nilaiCount > 0) {
            $alasan = [];
            if ($pesertaCount > 0) {
                $alasan[] = "{$pesertaCount} peserta terdaftar";
            }
            if ($nilaiCount > 0) {
                $alasan[] = "{$nilaiCount} data nilai yang telah diinput";
            }

            $pesanWarning = "Kelas Pelatihan '{$pelatihan->nama_pelatihan}' tidak dapat dihapus karena masih memiliki " . implode(' dan ', $alasan) . " di sistem!";

            return redirect()->route('academic.pelatihan.index')
                ->with('error', $pesanWarning)
                ->withErrors(['hapus_error' => $pesanWarning]);
        }

        $pelatihan->delete();

        return redirect()->route('academic.pelatihan.index')->with('success', "Data Master Pelatihan '{$pelatihan->nama_pelatihan}' berhasil dihapus.");
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