<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PesertaExport;
use App\Imports\PesertaImport;

class PesertaController extends Controller
{
    private function validatePeserta(Request $request, $id = null)
    {
        return $request->validate([
            'nama_lengkap'  => 'required|string|max:255',
            'gender'        => 'required|in:pria,wanita', 
            'email'         => 'required|email|unique:pesertas,email,' . $id,
            'pelatihan'     => 'required|string', // CMS Concept: Menyimpan data nama Batch / Angkatan
            'tempat_lahir'  => 'nullable|string|max:100', 
            'tanggal_lahir' => 'nullable|date',           
            'no_hp'         => 'nullable|string|max:20',
            'tempat_kerja'  => 'nullable|string|max:255', // CMS Concept: Menyimpan nama PT (PT MILI, dll)
            'foto'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    }

    public function index(Request $request)
    {
        // Tetap diurutkan berdasarkan ID dari urutan awal ter-import agar tidak melompat acak
        $pesertas = Peserta::when($request->search, fn($q) => $q->where('nama_lengkap', 'like', "%{$request->search}%"))
            ->when($request->pelatihan, fn($q) => $q->where('pelatihan', $request->pelatihan))
            ->orderBy('id', 'asc')
            ->get();

        return view('peserta.index', compact('pesertas'));
    }

    public function create()
    {
        return view('peserta.create');
    }

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

        // Menyimpan data murni ke tabel mandiri tanpa relasi statis lama
        Peserta::create($data); 
        
        return redirect()->to('/peserta')->with('success', 'Hore! Data peserta berhasil didaftarkan.');
    }

    public function edit($id)
    {
        $peserta = Peserta::findOrFail($id);
        return view('peserta.edit', compact('peserta'));
    }

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

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:AKTIF,PENDING,NON-AKTIF']);
        
        $peserta = Peserta::findOrFail($id);
        $peserta->update(['status' => $request->status]);
        
        return back()->with('success', 'Status verifikasi data peserta berhasil diperbarui.');
    }

    /**
     * PROSES EXPORT DATA PESERTA
     */
    public function export() 
    {
        $namaFile = 'Template_Peserta_CMS_' . now()->format('dmY') . '.xlsx';
        
        // 🚀 FIX ABSOLUT MUTLAK: Panggil dengan full path namespace langsung biar ga nyasar ke folder controller
        return Excel::download(new \App\Exports\PesertaExport, $namaFile);
    }

    /**
     * PROSES IMPORT DATA PESERTA
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            // 🚀 FIX ABSOLUT: Tembak ke path import class aslinya langsung tanpa keyword return di depan
            Excel::import(new \App\Imports\PesertaImport, $request->file('file_excel'));
            return redirect()->to('/peserta')->with('success', 'BULK IMPORT SYSTEM: Semua data peserta dari Excel berhasil diproses!');
        } catch (\Exception $e) {
            return redirect()->to('/peserta')->withErrors(['import_error' => 'Gagal mengimport data. Rincian: ' . $e->getMessage()]);
        }
    }

    // =========================================================================
    // EXECUTIVE MONTHLY REPORT - REDIRECT TO DASHBOARD.INDEX (FIXED SQLITE)
    // =========================================================================
    public function report(Request $request)
    {
        $bulanAktif = $request->input('bulan', date('m'));
        $tahunAktif = $request->input('tahun', date('Y'));

        // 1. Total Registrasi Berdasarkan Filter Bulan & Tahun
        $totalRegistrasi = Peserta::whereMonth('created_at', $bulanAktif)
            ->whereYear('created_at', $tahunAktif)
            ->count();

        // 2. Komposisi Program Pelatihan Dinamis Berdasarkan Bulan & Tahun (Membaca Batch Angkatan)
        $komposisiProgram = Peserta::select('pelatihan', DB::raw('count(*) as total'))
            ->whereMonth('created_at', $bulanAktif)
            ->whereYear('created_at', $tahunAktif)
            ->groupBy('pelatihan')
            ->orderBy('total', 'desc')
            ->get();

        $programTerpopuler = $komposisiProgram->first() ? $komposisiProgram->first()->pelatihan : 'Belum Ada';
        $totalProgramTerpopuler = $komposisiProgram->first() ? $komposisiProgram->first()->total : 0;
        $jumlahJenisProgram = $komposisiProgram->count();

        // 3. Komposisi Gender Berdasarkan Filter Bulan & Tahun
        $priaTotal = Peserta::whereMonth('created_at', $bulanAktif)
            ->whereYear('created_at', $tahunAktif)
            ->where(DB::raw('LOWER(gender)'), 'pria') 
            ->count();

        $wanitaTotal = Peserta::whereMonth('created_at', $bulanAktif)
            ->whereYear('created_at', $tahunAktif)
            ->where(DB::raw('LOWER(gender)'), 'wanita') 
            ->count();

        $totalGender = $priaTotal + $wanitaTotal;
        $priaPercent = $totalGender > 0 ? round(($priaTotal / $totalGender) * 100) : 0;
        $wanitaPercent = $totalGender > 0 ? round(($wanitaTotal / $totalGender) * 100) : 0;

        // 4. Tren Aktivitas Mingguan menggunakan strftime('%w')
        $weeklyTrendRaw = Peserta::select(DB::raw("strftime('%w', created_at) as day_index"), DB::raw('count(*) as total'))
            ->whereMonth('created_at', $bulanAktif)
            ->whereYear('created_at', $tahunAktif)
            ->groupBy('day_index')
            ->pluck('total', 'day_index')
            ->toArray();

        $mapIndexToDay = [
            '1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', 
            '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '0' => 'Sunday'
        ];

        $daysOfWeek = ['Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0, 'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0];
        
        foreach ($weeklyTrendRaw as $dayIndex => $count) {
            if (isset($mapIndexToDay[$dayIndex])) {
                $dayName = $mapIndexToDay[$dayIndex];
                $daysOfWeek[$dayName] = $count;
            }
        }

        // 5. Hitung Bulan Terbaik sepanjang tahun terpilih
        $bulanTerbaikRaw = Peserta::select(DB::raw("strftime('%m', created_at) as bulan"), DB::raw('count(*) as total'))
            ->whereYear('created_at', $tahunAktif)
            ->groupBy('bulan')
            ->orderBy('total', 'desc')
            ->first();

        $namaBulanTerbaik = "-";
        $totalBulanTerbaik = 0;
        if ($bulanTerbaikRaw) {
            $namaBulanTerbaik = date('F', mktime(0, 0, 0, (int)$bulanTerbaikRaw->bulan, 1));
            $totalBulanTerbaik = $bulanTerbaikRaw->total;
        }

        return view('dashboard.index', compact(
            'bulanAktif', 'tahunAktif', 'totalRegistrasi', 'komposisiProgram', 
            'daysOfWeek', 'namaBulanTerbaik', 'totalBulanTerbaik',
            'programTerpopuler', 'totalProgramTerpopuler', 'jumlahJenisProgram',
            'priaTotal', 'wanitaTotal', 'priaPercent', 'wanitaPercent'
        ));
    }
}