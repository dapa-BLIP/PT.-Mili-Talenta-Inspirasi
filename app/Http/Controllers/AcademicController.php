<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\Pelatihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\PelatihanExport;
use App\Imports\PelatihanImport;
use App\Exports\MentorExport;
use App\Imports\MentorImport;
use Maatwebsite\Excel\Facades\Excel;

class AcademicController extends Controller
{
    public function index()
    {
        $trainers = Trainer::orderBy('nama_mentor', 'asc')->get();
        $pelatihans = Pelatihan::with('mentor')->orderBy('id', 'desc')->get();

        return view('academic.index', compact('trainers', 'pelatihans'));
    }

    public function storeTrainer(Request $request)
    {
        $request->validate([
            'nama_mentor'     => 'required|string|max:255',
            'nidn'            => 'nullable|string|max:50|unique:trainers,nidn',
            'tempat_lahir'    => 'required|string|max:100',
            'tanggal_lahir'   => 'required|date',
            'email'           => 'nullable|email|unique:trainers,email',
            'kontak'          => 'nullable|string|max:20',
            'sektor_keahlian' => 'required|in:Pengetahuan Teknis,Pengetahuan Non Teknis',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $ttl = ucwords(strtolower(trim($request->tempat_lahir))) . ', ' . date('d-m-Y', strtotime($request->tanggal_lahir));
        $data = $request->only(['nama_mentor', 'nidn', 'email', 'kontak', 'sektor_keahlian']);
        $data['ttl'] = $ttl;

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('mentor', 'public');
        }

        Trainer::create($data);
        return redirect()->route('academic.index')->with('success', 'Data Mentor baru berhasil didaftarkan.')->with('active_tab', 'mentor');
    }

    public function updateTrainer(Request $request, $id)
    {
        $trainer = Trainer::findOrFail($id);

        $request->validate([
            'nama_mentor'     => 'required|string|max:255',
            'nidn'            => 'nullable|string|max:50|unique:trainers,nidn,' . $id,
            'ttl'             => 'required|string|max:150',
            'email'           => 'nullable|email|unique:trainers,email,' . $id,
            'kontak'          => 'nullable|string|max:20',
            'sektor_keahlian' => 'required|in:Pengetahuan Teknis,Pengetahuan Non Teknis',
            'status'          => 'required|in:AKTIF,NON-AKTIF',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['nama_mentor', 'nidn', 'ttl', 'email', 'kontak', 'sektor_keahlian', 'status']);

        if ($request->hasFile('foto')) {
            if ($trainer->foto && Storage::disk('public')->exists($trainer->foto)) {
                Storage::disk('public')->delete($trainer->foto);
            }
            $data['foto'] = $request->file('foto')->store('mentor', 'public');
        }

        $trainer->update($data);
        return redirect()->route('academic.index')->with('success', 'Data rekord profil mentor berhasil diperbarui.')->with('active_tab', 'mentor');
    }

    public function storePelatihan(Request $request)
    {
        $request->validate([
            'nama_pelatihan'     => 'required|string|max:255',
            'spektrum_kategori'  => 'required|in:Pengetahuan Teknis,Pengetahuan Non Teknis',
            'trainer_id'         => 'required|exists:trainers,id',
            'raw_komponen'       => 'required|string', 
        ]);

        $komponenFinal = $this->parseAndIndexKomponen($request->raw_komponen);

        Pelatihan::create([
            'nama_pelatihan'    => $request->nama_pelatihan,
            'spektrum_kategori' => $request->spektrum_kategori,
            'trainer_id'        => $request->trainer_id,
            'komponen_nilai'    => $komponenFinal,
        ]);

        return redirect()->route('academic.index')->with('success', 'Master Pelatihan & Komponen Nilai berhasil dibukukan.')->with('active_tab', 'pelatihan');
    }

    public function updatePelatihan(Request $request, $id)
    {
        $pelatihan = Pelatihan::findOrFail($id);

        $request->validate([
            'nama_pelatihan'     => 'required|string|max:255',
            'spektrum_kategori'  => 'required|in:Pengetahuan Teknis,Pengetahuan Non Teknis',
            'trainer_id'         => 'required|exists:trainers,id',
            'raw_komponen'       => 'required|string', 
        ]);

        $komponenFinal = $this->parseAndIndexKomponen($request->raw_komponen);

        $pelatihan->update([
            'nama_pelatihan'    => $request->nama_pelatihan,
            'spektrum_kategori' => $request->spektrum_kategori,
            'trainer_id'        => $request->trainer_id,
            'komponen_nilai'    => $komponenFinal,
        ]);

        return redirect()->route('academic.index')->with('success', 'Master Pelatihan berhasil di-update & disinkronkan.')->with('active_tab', 'pelatihan');
    }

    /**
     * 🛡️ PERBAIKAN LOGIKA: Saring dan buang mutlak data komponen yang duplikat (Anti UTS double)
     */
    private function parseAndIndexKomponen($rawString)
    {
        // 1. Pecah string berdasarkan koma, bersihkan spasi, ganti spasi tengah jadi underscore, dan uppercase-kan
        $rawArray = array_map(function($item) {
            return strtoupper(trim(str_replace(' ', '_', $item)));
        }, explode(',', $rawString));

        // 2. Buang elemen kosong akibat user typo ketik double koma (misal: UTS,,UAS)
        $filteredArray = array_filter($rawArray); 
        
        // 3. MATIKAN DUPLIKAT: Ambil murni nilai yang unik saja
        $uniqueArray = array_unique($filteredArray);

        // 4. Reset index array biar berurutan rapi dari 0 kembali saat disimpan ke JSON
        return array_values($uniqueArray);
    }

    public function destroyTrainer($id)
    {
        $trainer = Trainer::findOrFail($id);
        if ($trainer->foto && Storage::disk('public')->exists($trainer->foto)) {
            Storage::disk('public')->delete($trainer->foto);
        }
        $trainer->delete();
        return redirect()->route('academic.index')->with('success', 'Data Mentor berhasil dihapus.')->with('active_tab', 'mentor');
    }

    public function destroyPelatihan($id)
    {
         Pelatihan::findOrFail($id)->delete();
        return redirect()->route('academic.index')->with('success', 'Data Master Pelatihan berhasil dihapus.')->with('active_tab', 'pelatihan');
    }

    public function exportPelatihan() 
    {
        return Excel::download(new PelatihanExport, 'MASTER_PELATIHAN_PMI_' . date('Ymd_His') . '.xlsx');
    }

    public function importPelatihan(Request $request) 
    {
        $request->validate(['file_excel' => 'required|mimes:xlsx,xls']);
        Excel::import(new PelatihanImport, $request->file('file_excel'));
        return redirect()->route('academic.index')->with('success', 'Bulk Data Repositori Pelatihan Berhasil Di-import!')->with('active_tab', 'pelatihan');
    }

    public function exportMentor() 
    {
        return Excel::download(new MentorExport, 'DATABASE_MENTOR_PMI_' . date('Ymd_His') . '.xlsx');
    }

    public function importMentor(Request $request) 
    {
        $request->validate(['file_excel' => 'required|mimes:xlsx,xls']);
        Excel::import(new MentorImport, $request->file('file_excel'));
        return redirect()->route('academic.index')->with('success', 'Bulk Database Master Mentor Berhasil Di-import!')->with('active_tab', 'mentor');
    }
}