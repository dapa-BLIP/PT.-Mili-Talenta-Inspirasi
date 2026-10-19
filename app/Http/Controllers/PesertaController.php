<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Perusahaan;
use App\Models\Pelatihan;
use App\Models\Materi;
use App\Models\KategoriPelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PesertaExport;
use App\Imports\PesertaImport;

class PesertaController extends Controller
{
    /**
     * Helper Private Validasi Data Peserta
     */
    private function validatePeserta(Request $request, $id = null)
    {
        return $request->validate([
            'nama_lengkap'   => 'required|string|max:255',
            'gender'         => 'required|in:pria,wanita,p,l', 
            'email'          => 'required|email|unique:pesertas,email,' . $id,
            'pelatihan'      => 'required|string|max:255', 
            'perusahaan_id'  => 'nullable|exists:perusahaans,id',
            'tempat_lahir'   => 'nullable|string|max:100', 
            'tanggal_lahir'  => 'nullable|date',           
            'no_hp'          => 'nullable|string|min:8|max:15',
            'tempat_kerja'   => 'nullable|string|max:255', 
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'materi_ids'     => 'nullable|array',
            'materi_ids.*'   => 'integer',
        ], [
            'foto.max'       => 'Ukuran foto maksimal 2 MB.',
            'email.unique'   => 'Alamat email sudah terdaftar untuk peserta lain.',
        ]);
    }

    /**
     * Endpoint AJAX: Mengambil list materi per jenis pelatihan
     */
    public function getMateriByJenis(Request $request)
    {
        $jenis = $request->query('jenis');
        
        $materis = Materi::when($jenis, function ($q) use ($jenis) {
            $q->where('jenis_pelatihan', $jenis);
        })->orderBy('id', 'asc')->get(['id', 'nama_materi', 'jenis_pelatihan']);

        return response()->json($materis);
    }

    /**
     * Halaman Monitoring Data Peserta
     */
    public function index(Request $request)
    {
        $pesertas = Peserta::with(['perusahaan', 'nilais.pelatihan'])
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    $query->where('nama_lengkap', 'like', "%{$request->search}%")
                          ->orWhere('email', 'like', "%{$request->search}%")
                          ->orWhere('tempat_kerja', 'like', "%{$request->search}%");
                });
            })
            ->when($request->pelatihan, fn($q) => $q->where('pelatihan', $request->pelatihan))
            ->when($request->perusahaan_id, fn($q) => $q->where('perusahaan_id', $request->perusahaan_id))
            ->orderBy('id', 'asc')
            ->get();

        $perusahaans = Perusahaan::where('status', 'AKTIF')->orderBy('nama_perusahaan', 'asc')->get();

        $kategoriPrograms = KategoriPelatihan::orderBy('nama_kategori', 'asc')->pluck('nama_kategori')->toArray();
        $pelatihanPrograms = Pelatihan::whereNotNull('jenis_pelatihan')->where('jenis_pelatihan', '!=', '')->distinct()->pluck('jenis_pelatihan')->toArray();
        $programs = array_values(array_unique(array_filter(array_merge($kategoriPrograms, $pelatihanPrograms))));
        sort($programs);

        $pelatihans = Pelatihan::with('mentor')->orderBy('nama_pelatihan', 'asc')->get();

        return view('peserta.index', compact('pesertas', 'perusahaans', 'pelatihans', 'programs'));
    }

    /**
     * Form Tambah Peserta Manual
     */
    public function create()
    {
        $perusahaans = Perusahaan::where('status', 'AKTIF')->orderBy('nama_perusahaan', 'asc')->get();

        $kategoriPrograms = KategoriPelatihan::orderBy('nama_kategori', 'asc')->pluck('nama_kategori')->toArray();
        $pelatihanPrograms = Pelatihan::whereNotNull('jenis_pelatihan')->where('jenis_pelatihan', '!=', '')->distinct()->pluck('jenis_pelatihan')->toArray();
        $programs = array_values(array_unique(array_filter(array_merge($kategoriPrograms, $pelatihanPrograms))));
        sort($programs);

        $pelatihans = Pelatihan::with('mentor')->orderBy('nama_pelatihan', 'asc')->get();

        if (view()->exists('peserta.sub-data-peserta.create')) {
            return view('peserta.sub-data-peserta.create', compact('perusahaans', 'pelatihans', 'programs'));
        }

        return view('peserta.create', compact('perusahaans', 'pelatihans', 'programs'));
    }

    /**
     * Simpan Peserta Baru + Sinkronisasi Materi
     */
    public function store(Request $request)
    {
        $this->validatePeserta($request);
        
        $tempat = $request->input('tempat_lahir', 'Bekasi');
        $tanggal = $request->input('tanggal_lahir') ? date('d-m-Y', strtotime($request->tanggal_lahir)) : date('d-m-Y');
        $ttl = $tempat . ', ' . $tanggal;

        $data = $request->except(['tempat_lahir', 'tanggal_lahir', 'materi_ids']);
        $data['ttl'] = $ttl;
        $data['status'] = 'PENDING';

        if ($request->filled('no_hp')) {
            $rawPhone = preg_replace('/[^0-9]/', '', $request->no_hp);
            $rawPhone = preg_replace('/^(0|62)/', '', $rawPhone);
            $data['no_hp'] = '+62' . $rawPhone;
        }

        if ($request->filled('perusahaan_id')) {
            $pt = Perusahaan::find($request->perusahaan_id);
            $data['tempat_kerja'] = $pt ? $pt->nama_perusahaan : null;
        }

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('peserta', 'public');
        }

        $peserta = Peserta::create($data); 

        // Sinkronisasi materi checklist
        if ($request->has('materi_ids') && is_array($request->materi_ids)) {
            $peserta->materis()->sync($request->materi_ids);
        }
        
        return redirect()->route('peserta.index')->with('success', 'Data peserta & silabus materi berhasil didaftarkan.');
    }

    /**
     * Form Edit Data Peserta
     */
    public function edit($id)
    {
        $peserta = Peserta::with(['materis', 'nilais.pelatihan'])->findOrFail($id);
        $perusahaans = Perusahaan::where('status', 'AKTIF')->orderBy('nama_perusahaan', 'asc')->get();

        $kategoriPrograms = KategoriPelatihan::orderBy('nama_kategori', 'asc')->pluck('nama_kategori')->toArray();
        $pelatihanPrograms = Pelatihan::whereNotNull('jenis_pelatihan')->where('jenis_pelatihan', '!=', '')->distinct()->pluck('jenis_pelatihan')->toArray();
        $programs = array_values(array_unique(array_filter(array_merge($kategoriPrograms, $pelatihanPrograms))));
        sort($programs);

        $pelatihans = Pelatihan::with('mentor')->orderBy('nama_pelatihan', 'asc')->get();

        if (view()->exists('peserta.sub-data-peserta.edit')) {
            return view('peserta.sub-data-peserta.edit', compact('peserta', 'perusahaans', 'pelatihans', 'programs'));
        }

        return view('peserta.edit', compact('peserta', 'perusahaans', 'pelatihans', 'programs'));
    }

    /**
     * Update Data Peserta + Sinkronisasi Materi
     */
    public function update(Request $request, $id)
    {
        $peserta = Peserta::findOrFail($id);
        $this->validatePeserta($request, $peserta->id);

        // Validasi: Cek apakah program pelatihan diubah & apakah peserta sudah memiliki nilai di program sebelumnya
        $newProgram = trim((string)$request->pelatihan);
        $oldProgram = trim((string)$peserta->pelatihan);

        if (strcasecmp($newProgram, $oldProgram) !== 0) {
            if ($peserta->hasNilaiInProgram($oldProgram)) {
                $warningMsg = "Peserta memiliki nilai di program \"{$oldProgram}\".";

                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $warningMsg,
                        'errors'  => ['pelatihan' => [$warningMsg]]
                    ], 422);
                }

                return redirect()->back()
                    ->withInput()
                    ->withErrors(['pelatihan' => $warningMsg])
                    ->with('error', $warningMsg);
            }
        }

        $tempat = $request->input('tempat_lahir', 'Bekasi');
        $tanggal = $request->input('tanggal_lahir') ? date('d-m-Y', strtotime($request->tanggal_lahir)) : date('d-m-Y');
        $ttl = $tempat . ', ' . $tanggal;

        $data = $request->except(['tempat_lahir', 'tanggal_lahir', 'materi_ids']);
        $data['ttl'] = $ttl;

        if ($request->filled('no_hp')) {
            $rawPhone = preg_replace('/[^0-9]/', '', $request->no_hp);
            $rawPhone = preg_replace('/^(0|62)/', '', $rawPhone);
            $data['no_hp'] = '+62' . $rawPhone;
        }

        if ($request->filled('perusahaan_id')) {
            $pt = Perusahaan::find($request->perusahaan_id);
            $data['tempat_kerja'] = $pt ? $pt->nama_perusahaan : null;
        } else {
            $data['perusahaan_id'] = null;
            $data['tempat_kerja'] = null;
        }

        if ($request->hasFile('foto')) {
            if ($peserta->foto && Storage::disk('public')->exists($peserta->foto)) {
                Storage::disk('public')->delete($peserta->foto);
            }
            $data['foto'] = $request->file('foto')->store('peserta', 'public');
        }

        $peserta->update($data);

        // Sinkronisasi perubahan materi
        if ($request->has('materi_ids')) {
            $peserta->materis()->sync($request->materi_ids);
        } else {
            $peserta->materis()->detach();
        }

        return redirect()->route('peserta.index')->with('success', 'Perubahan data peserta & materi berhasil disimpan.');
    }

    /**
     * Endpoint AJAX: Cek apakah peserta memiliki nilai di program tertentu
     */
    public function checkNilaiPeserta(Request $request)
    {
        $pesertaId = $request->query('peserta_id');
        $peserta   = Peserta::find($pesertaId);

        if (!$peserta) {
            return response()->json([
                'has_nilai' => false,
                'program'   => null,
            ]);
        }

        $program = $request->query('program', $peserta->pelatihan);
        $hasNilai = $peserta->hasNilaiInProgram($program);

        return response()->json([
            'has_nilai' => $hasNilai,
            'program'   => $peserta->pelatihan,
        ]);
    }

    /**
     * Hapus Record Peserta
     */
    public function destroy($id)
    {
        $peserta = Peserta::find($id);

        if (!$peserta) {
            return redirect()->route('peserta.index')->with('warning', 'Data sudah tidak ada atau telah dihapus sebelumnya.');
        }

        if ($peserta->foto && Storage::disk('public')->exists($peserta->foto)) {
            Storage::disk('public')->delete($peserta->foto);
        }
        
        $peserta->delete();

        return redirect()->route('peserta.index')->with('success', 'Data record peserta telah berhasil dihapus dari database.');
    }

    /**
     * Update Status Verifikasi Peserta
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:AKTIF,PENDING,NON-AKTIF']);
        
        $peserta = Peserta::findOrFail($id);
        $peserta->update(['status' => $request->status]);
        
        return back()->with('success', 'Status verifikasi data peserta berhasil diperbarui.');
    }

    /**
     * Export Excel
     */
    public function export() 
    {
        $namaFile = 'Data_Peserta_CMS_' . now()->format('dmY') . '.xlsx';
        return Excel::download(new PesertaExport, $namaFile);
    }

    /**
     * Unduh Template Import Excel Peserta (Tanpa Kolom NO. & ID PESERTA)
     */
    public function template()
    {
        return Excel::download(new \App\Imports\Templates\PesertaImportTemplate, 'TEMPLATE_IMPORT_PESERTA.xlsx');
    }

    /**
     * Bulk Import System Peserta
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new PesertaImport, $request->file('file_excel'));
            return redirect()->route('peserta.index')->with('success', 'BULK IMPORT SYSTEM: Semua data peserta dari Excel berhasil diproses!');
        } catch (\Exception $e) {
            return redirect()->route('peserta.index')->withErrors(['import_error' => 'Gagal mengimport data. Rincian: ' . $e->getMessage()]);
        }
    }
}