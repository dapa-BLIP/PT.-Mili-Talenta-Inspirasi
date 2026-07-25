<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\Pelatihan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MentorImport;
use App\Exports\MentorExport;

class TrainerController extends Controller
{
    /**
     * Tampilan Hub Master Academic digabung di view tunggal dashboard
     */
    public function index()
    {
        $trainers = Trainer::orderBy('nama_mentor', 'asc')->get();
        $pelatihans = Pelatihan::with('mentor')->orderBy('nama_pelatihan', 'asc')->get();
        
        return view('academic.index', compact('trainers', 'pelatihans'));
    }

    /**
     * REGISTRASI MANUAL DARI FORM DI VIEW MENTOR MODAL
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_mentor'     => 'required|string|max:255',
            'email'           => 'required|email|unique:trainers,email',
            
            // 🚀 REVISI VALIDASI NIDN: Wajib murni angka sepanjang 10 digit & anti-kembar di DB cook!
            'nidn'            => 'nullable|numeric|digits:10|unique:trainers,nidn',
            
            'tempat_lahir'    => 'required|string|max:100',
            'tanggal_lahir'   => 'required|date',
            'kontak'          => 'nullable|string|min:10|max:13',
            'sektor_keahlian' => 'required|string',
            'foto'            => 'nullable|image|mimes:jpeg,jpg,png|max:2048'
        ]);

        $ttl = strtoupper($request->tempat_lahir) . ', ' . date('d-m-Y', strtotime($request->tanggal_lahir));
        
        $data = $request->only(['nama_mentor', 'nidn', 'email', 'kontak', 'sektor_keahlian']);
        $data['nama_mentor'] = strtoupper($request->nama_mentor);
        $data['ttl'] = $ttl;
        $data['status'] = 'AKTIF';

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('mentor', 'public');
        }

        Trainer::create($data);

        return redirect()->back()->with(['success' => 'Mantap! Profil data master Mentor baru berhasil diregistrasikan.', 'active_tab' => 'mentor']);
    }

    /**
     * PROSES EXPORT DATA MASTER MENTOR
     */
    public function exportMentor()
    {
        $namaFile = 'Master_Database_Mentor_MILI_' . now()->format('dmY') . '.xlsx';
        return Excel::download(new MentorExport, $namaFile);
    }

    /**
     * PROSES IMPORT DATA EXCEL DENGAN FRIENDLY EXCEPTION CATCHER bray
     */
    public function importMentor(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new MentorImport, $request->file('file_excel'));
            
            return redirect()->back()->with(['success' => 'BULK IMPORT SYSTEM: Data Master Mentor dari Excel berhasil disinkronkan bray!', 'active_tab' => 'mentor']);
        } catch (\Exception $e) {
            // Tangkap pesan friendly exception dari engine import bray!
            return redirect()->back()->withInput()->withErrors(['import_error' => $e->getMessage()]);
        }
    }
}