<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\MentorExport;
use App\Imports\MentorImport;
use App\Imports\Templates\MentorImportTemplate;
use Maatwebsite\Excel\Facades\Excel;

class MentorController extends Controller
{
    public function index()
    {
        $trainers = Trainer::latest()->get();
        return view('academic.mentor', compact('trainers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_mentor'       => 'required|string|max:255',
            'nidn'              => 'nullable|numeric|digits_between:8,16|unique:trainers,nidn',
            'tempat_lahir'      => 'required|string|max:100',
            'tanggal_lahir'     => 'required|date',
            'email'             => 'nullable|email|unique:trainers,email',
            'kontak'            => [
                'required',
                function ($attribute, $value, $fail) {
                    $digits = preg_replace('/[^0-9]/', '', (string)$value);
                    if (str_starts_with($digits, '62')) {
                        $digits = '0' . substr($digits, 2);
                    }
                    if (!preg_match('/^08[1-9][0-9]{7,10}$/', $digits)) {
                        $fail('Nomor WhatsApp harus tepat 10 sampai 13 digit angka seluler yang valid (contoh: 0812-3456-7890).');
                    }
                },
            ],
            'program_pelatihan' => 'required|string',
            'foto'              => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $ttl = ucwords(strtolower(trim((string)$request->tempat_lahir))) . ', ' . date('d-m-Y', strtotime($request->tanggal_lahir));
        $kontakFormatted = $this->formatNomorTelepon($request->kontak);

        $data = [
            'nama_mentor'       => strtoupper(trim((string)$request->nama_mentor)),
            'nidn'              => $request->nidn,
            'ttl'               => $ttl,
            'email'             => $request->email,
            'kontak'            => $kontakFormatted,
            'program_pelatihan' => $request->program_pelatihan,
            'status'            => 'AKTIF',
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('mentor', 'public');
        }

        Trainer::create($data);
        return redirect()->route('academic.mentor.index')->with('success', 'Data Mentor baru berhasil didaftarkan.');
    }

    public function update(Request $request, $id)
    {
        $trainer = Trainer::findOrFail($id);

        $request->validate([
            'nama_mentor'       => 'required|string|max:255',
            'nidn'              => 'nullable|numeric|digits_between:8,16|unique:trainers,nidn,' . $id,
            'tempat_lahir'      => 'nullable|string|max:100',
            'tanggal_lahir'     => 'nullable|date',
            'email'             => 'nullable|email|unique:trainers,email,' . $id,
            'kontak'            => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (!$value) return;
                    $digits = preg_replace('/[^0-9]/', '', (string)$value);
                    if (str_starts_with($digits, '62')) {
                        $digits = '0' . substr($digits, 2);
                    }
                    if (!preg_match('/^08[1-9][0-9]{7,10}$/', $digits)) {
                        $fail('Nomor WhatsApp harus tepat 10 sampai 13 digit angka seluler yang valid (contoh: 0812-3456-7890).');
                    }
                },
            ],
            'program_pelatihan' => 'required|string',
            'status'            => 'required|in:AKTIF,PENDING,NON-AKTIF',
            'foto'              => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->filled('tempat_lahir') && $request->filled('tanggal_lahir')) {
            $ttl = ucwords(strtolower(trim((string)$request->tempat_lahir))) . ', ' . date('d-m-Y', strtotime($request->tanggal_lahir));
        } elseif ($request->filled('tempat_lahir')) {
            $ttl = ucwords(strtolower(trim((string)$request->tempat_lahir)));
        } else {
            $ttl = $trainer->ttl;
        }

        $kontakFormatted = $request->filled('kontak') 
            ? $this->formatNomorTelepon($request->kontak) 
            : $trainer->kontak;

        $data = [
            'nama_mentor'       => strtoupper(trim((string)$request->nama_mentor)),
            'nidn'              => $request->nidn,
            'ttl'               => $ttl,
            'email'             => $request->email,
            'kontak'            => $kontakFormatted,
            'program_pelatihan' => $request->program_pelatihan,
            'status'            => $request->status,
        ];

        if ($request->hasFile('foto')) {
            if ($trainer->foto && Storage::disk('public')->exists($trainer->foto)) {
                Storage::disk('public')->delete($trainer->foto);
            }
            $data['foto'] = $request->file('foto')->store('mentor', 'public');
        }

        $trainer->update($data);
        return redirect()->route('academic.mentor.index')->with('success', 'Data profil mentor berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $trainer = Trainer::findOrFail($id);
        if ($trainer->foto && Storage::disk('public')->exists($trainer->foto)) {
            Storage::disk('public')->delete($trainer->foto);
        }
        $trainer->delete();
        return redirect()->route('academic.mentor.index')->with('success', 'Data Mentor berhasil dihapus.');
    }

    public function export()
    {
        return Excel::download(new MentorExport, 'DATABASE_MENTOR_PMI_' . date('Ymd_His') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            Excel::import(new MentorImport, $request->file('file_excel'));
            return redirect()->route('academic.mentor.index')->with('success', 'Database Master Mentor Berhasil Disinkronisasi!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['import_error' => 'Gagal memproses file: ' . $e->getMessage()]);
        }
    }

    public function template()
    {
        return Excel::download(new MentorImportTemplate, 'TEMPLATE_IMPORT_MENTOR.xlsx');
    }

    private function formatNomorTelepon($nomor)
    {
        if (!$nomor) return null;

        $digits = preg_replace('/[^0-9]/', '', (string)$nomor);

        if (str_starts_with($digits, '62')) {
            $digits = '0' . substr($digits, 2);
        } elseif (!str_starts_with($digits, '0') && strlen($digits) > 0) {
            $digits = '0' . $digits;
        }

        $len = strlen($digits);

        if ($len === 10 || $len === 11) {
            return substr($digits, 0, 4) . '-' . substr($digits, 4, 3) . '-' . substr($digits, 7);
        } elseif ($len === 12 || $len === 13) {
            return substr($digits, 0, 4) . '-' . substr($digits, 4, 4) . '-' . substr($digits, 8);
        }

        return $digits;
    }
}