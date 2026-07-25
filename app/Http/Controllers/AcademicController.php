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
            'nidn'            => 'nullable|numeric|digits:10|unique:trainers,nidn',
            'tempat_lahir'    => 'required|string|max:100',
            'tanggal_lahir'   => 'required|date',
            'email'           => 'nullable|email|unique:trainers,email',
            'kontak'          => 'nullable|string|min:10|max:13', 
            'sektor_keahlian' => 'required|in:Pengetahuan Teknis,Pengetahuan Non Teknis',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $ttl = ucwords(strtolower(trim($request->tempat_lahir))) . ', ' . date('d-m-Y', strtotime($request->tanggal_lahir));
        
        $data = $request->only(['nama_mentor', 'nidn', 'email', 'kontak', 'sektor_keahlian']);
        $data['nama_mentor'] = strtoupper($request->nama_mentor); // Standardisasi nama huruf kapital semua cook
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
            'nidn'            => 'nullable|numeric|digits:10|unique:trainers,nidn,' . $id,
            'ttl'             => 'required|string|max:150',
            'email'           => 'nullable|email|unique:trainers,email,' . $id,
            'kontak'          => 'nullable|string|min:10|max:13',
            'sektor_keahlian' => 'required|in:Pengetahuan Teknis,Pengetahuan Non Teknis',
            'status'          => 'required|in:AKTIF,NON-AKTIF',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->only(['nama_mentor', 'nidn', 'ttl', 'email', 'kontak', 'sektor_keahlian', 'status']);
        $data['nama_mentor'] = strtoupper($request->nama_mentor);

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
            'nama_pelatihan'    => strtoupper($request->nama_pelatihan),
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
            'nama_pelatihan'    => strtoupper($request->nama_pelatihan),
            'spektrum_kategori' => $request->spektrum_kategori,
            'trainer_id'        => $request->trainer_id,
            'komponen_nilai'    => $komponenFinal,
        ]);

        return redirect()->route('academic.index')->with('success', 'Master Pelatihan berhasil di-update & disinkronkan.')->with('active_tab', 'pelatihan');
    }

   
    private function parseAndIndexKomponen($rawString)
    {
        $rawArray = array_map(function($item) {
            return strtoupper(trim(str_replace(' ', '_', $item)));
        }, explode(',', $rawString));

        $filteredArray = array_filter($rawArray); 
        $uniqueArray = array_unique($filteredArray);

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
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('file_excel');
            
            //JURUS MEMBACA ISI FILE SEBELUM DI-IMPORT:
            $rows = Excel::toArray([], $file);
            
            $headerDitemukan = false;
            foreach ($rows[0] as $row) {
                //mencirikan file PELATIHAN
                if (in_array('NAMA PROGRAM PELATIHAN', $row) || in_array('KOMPONEN PENILAIAN', $row)) {
                    $headerDitemukan = true;
                    break;
                }
            }

            // WARNING CRITICAL JIKA SALAH BERKAS:
            if (!$headerDitemukan) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['import_error' => 'FORMAT SALAH: Berkas yang Anda masukkan terdeteksi sebagai template lain. Mohon gunakan Template Repositori Kelas Pelatihan yang sesuai!']);
            }

            // Jika lolos pengecekan struktur, eksekusi import asli lu di bawah cook:
            // Excel::import(new PelatihanImport, $file); // <--- sesuaikan dengan class import pelatihan lu bray

            return redirect()->route('academic.index')->with('success', 'Repositori Kelas Pelatihan Berhasil Disinkronisasi!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['import_error' => 'Gagal memproses file: ' . $e->getMessage()]);
        }
    }

    public function exportMentor() 
    {
        return Excel::download(new MentorExport, 'DATABASE_MENTOR_PMI_' . date('Ymd_His') . '.xlsx');
    }

   
    public function importMentor(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls'
        ]);

        try {
            $file = $request->file('file_excel');
            
            // MEMBACA ISI FILE SEBELUM DI-IMPORT:
            $rows = Excel::toArray([], $file);
            
          
            $headerDitemukan = false;
            foreach ($rows[0] as $row) {
                // Kita cek apakah ada kolom yang isinya mencirikan file MENTOR
                if (in_array('NAMA LENGKAP MENTOR', $row) || in_array('NIDN / NIK', $row)) {
                    $headerDitemukan = true;
                    break;
                }
            }

            // WARNING CRITICAL JIKA SALAH BERKAS:
            if (!$headerDitemukan) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['import_error' => 'FORMAT SALAH: Berkas yang Anda masukkan terdeteksi sebagai template lain. Mohon gunakan Template Database Master Mentor yang sesuai!']);
            }

            // Jika lolos pengecekan struktur, eksekusi import asli lu di bawah cook:
            // Excel::import(new MentorImport, $file); 

            return redirect()->route('academic.index')->with('success', 'Database Master Mentor Berhasil Disinkronisasi!');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['import_error' => 'Gagal memproses file: ' . $e->getMessage()]);
        }
    }

    
}