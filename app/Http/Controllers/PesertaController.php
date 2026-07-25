<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PesertaExport;
use App\Imports\PesertaImport;

class PesertaController extends Controller
{
    /**
     * Helper Private untuk Validasi Data Peserta
     */
    private function validatePeserta(Request $request, $id = null)
    {
        return $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'gender'        => 'required|in:pria,wanita', 
            'email'         => 'required|email|unique:pesertas,email,' . $id,
            'pelatihan'     => 'required|string', 
            'tempat_lahir'  => 'nullable|string|max:100', 
            'tanggal_lahir' => 'nullable|date',           
            'no_hp'         => 'nullable|string|min:10|max:13',
            'tempat_kerja'  => 'nullable|string|max:255', 
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    }

    /**
     * Menampilkan Tabel Master Peserta
     */
    public function index(Request $request)
    {
        $pesertas = Peserta::when($request->search, fn($q) => $q->where('nama_lengkap', 'like', "%{$request->search}%"))
            ->when($request->pelatihan, fn($q) => $q->where('pelatihan', $request->pelatihan))
            ->orderBy('id', 'asc')
            ->get();

        return view('peserta.index', compact('pesertas'));
    }

    /**
     * Form Tambah Peserta Manual
     */
    public function create()
    {
        return view('peserta.create');
    }

    /**
     * Proses Simpan Peserta Baru
     */
    public function store(Request $request)
    {
        $this->validatePeserta($request);
        
        $tempat = $request->input('tempat_lahir', 'Bekasi');
        $tanggal = $request->input('tanggal_lahir') ? date('d-m-Y', strtotime($request->tanggal_lahir)) : date('d-m-Y');
        $ttl = $tempat . ', ' . $tanggal;

        $data = $request->except(['tempat_lahir', 'tanggal_lahir']);
        $data['ttl'] = $ttl;
        $data['status'] = 'PENDING'; 

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('peserta', 'public');
        }

        Peserta::create($data); 
        
        return redirect()->to('/peserta')->with('success', 'Hore! Data peserta berhasil didaftarkan.');
    }

    /**
     * Form Edit Data Peserta
     */
    public function edit($id)
    {
        $peserta = Peserta::findOrFail($id);
        return view('peserta.edit', compact('peserta'));
    }

    /**
     * Proses Update Data Peserta
     */
    public function update(Request $request, $id)
    {
        $peserta = Peserta::findOrFail($id);
        $this->validatePeserta($request, $peserta->id);

        $tempat = $request->input('tempat_lahir', 'Bekasi');
        $tanggal = $request->input('tanggal_lahir') ? date('d-m-Y', strtotime($request->tanggal_lahir)) : date('d-m-Y');
        $ttl = $tempat . ', ' . $tanggal;

        $data = $request->except(['tempat_lahir', 'tanggal_lahir']);
        $data['ttl'] = $ttl;

        if ($request->hasFile('foto')) {
            if ($peserta->foto && Storage::disk('public')->exists($peserta->foto)) {
                Storage::disk('public')->delete($peserta->foto);
            }
            $data['foto'] = $request->file('foto')->store('peserta', 'public');
        }

        $peserta->update($data);

        return redirect()->to('/peserta')->with('success', 'Mantap! Perubahan data peserta berhasil disimpan.');
    }

    /**
     * Hapus Record Peserta
     */
    public function destroy($id)
    {
        $peserta = Peserta::find($id);

        if (!$peserta) {
            return redirect()->to('/peserta')->with('warning', 'Data sudah tidak ada atau telah dihapus sebelumnya.');
        }

        if ($peserta->foto && Storage::disk('public')->exists($peserta->foto)) {
            Storage::disk('public')->delete($peserta->foto);
        }
        
        $peserta->delete();

        return redirect()->to('/peserta')->with('success', 'Data record peserta telah berhasil dihapus dari database.');
    }

    /**
     * Toggle/Update Status Verifikasi Peserta
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:AKTIF,PENDING,NON-AKTIF']);
        
        $peserta = Peserta::findOrFail($id);
        $peserta->update(['status' => $request->status]);
        
        return back()->with('success', 'Status verifikasi data peserta berhasil diperbarui.');
    }

    /**
     * Export Excel Template / Data
     */
    public function export() 
    {
        $namaFile = 'Template_Peserta_CMS_' . now()->format('dmY') . '.xlsx';
        return Excel::download(new PesertaExport, $namaFile);
    }

    /**
     * Bulk Import System Peserta dari Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new PesertaImport, $request->file('file_excel'));
            return redirect()->to('/peserta')->with('success', 'BULK IMPORT SYSTEM: Semua data peserta dari Excel berhasil diproses!');
        } catch (\Exception $e) {
            return redirect()->to('/peserta')->withErrors(['import_error' => 'Gagal mengimport data. Rincian: ' . $e->getMessage()]);
        }
    }
}