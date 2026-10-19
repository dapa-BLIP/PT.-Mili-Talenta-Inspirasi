<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\Pelatihan;
use App\Models\KategoriPelatihan;
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

        // Sinkronisasi format kontak lama (yang masih memakai '-' atau '08...') agar seragam menggunakan '+62' tanpa '-'
        foreach ($trainers as $t) {
            if (!empty($t->kontak)) {
                $normalized = $this->formatNomorTelepon($t->kontak);
                if ($normalized && $t->kontak !== $normalized) {
                    $t->update(['kontak' => $normalized]);
                    $t->kontak = $normalized;
                }
            }
        }

        $kategoriPrograms = KategoriPelatihan::orderBy('nama_kategori', 'asc')->pluck('nama_kategori')->toArray();
        $pelatihanPrograms = Pelatihan::whereNotNull('jenis_pelatihan')->where('jenis_pelatihan', '!=', '')->distinct()->pluck('jenis_pelatihan')->toArray();
        $programs = array_values(array_unique(array_filter(array_map(
            fn($p) => strtoupper(trim((string) $p)),
            array_merge($kategoriPrograms, $pelatihanPrograms)
        ))));
        sort($programs);

        return view('academic.mentor', compact('trainers', 'programs'));
    }

    public function store(Request $request)
    {
        $nidnInput = $request->filled('nidn') ? preg_replace('/[^0-9]/', '', (string) $request->nidn) : null;
        $request->merge([
            'nidn'  => $nidnInput !== '' ? $nidnInput : null,
            'email' => $request->filled('email') ? strtolower(trim((string) $request->email)) : null,
        ]);

        $existingByNidn = $request->nidn ? Trainer::where('nidn', $request->nidn)->first() : null;
        $nidnUniqueMsg = $existingByNidn
            ? "Peringatan: NIK / NIDN [{$request->nidn}] sudah terdaftar atas nama mentor '{$existingByNidn->nama_mentor}'! Gunakan NIK / NIDN yang berbeda."
            : "Peringatan: NIK / NIDN [{$request->nidn}] sudah terdaftar pada mentor lain di sistem!";

        $request->validate([
            'nama_mentor'       => 'required|string|max:255',
            'nidn'              => 'nullable|numeric|digits_between:8,16|unique:trainers,nidn',
            'tempat_lahir'      => 'required|string|max:100',
            'tanggal_lahir'     => 'required|date',
            'email'             => 'nullable|email|unique:trainers,email',
            'kontak'            => [
                'required',
                function ($attribute, $value, $fail) {
                    $digits = preg_replace('/[^0-9]/', '', (string) $value);
                    $localDigits = preg_replace('/^(0|62)/', '', $digits);
                    if (!preg_match('/^8[0-9]{8,12}$/', $localDigits)) {
                        $fail('Nomor WhatsApp harus berupa nomor seluler yang valid (contoh: +6281234567890 atau 081234567890, 10-15 digit).');
                        return;
                    }
                    $formatted = '+62' . $localDigits;
                    $existingKontak = Trainer::where('kontak', $formatted)->first();
                    if ($existingKontak) {
                        $fail("Peringatan: Nomor WhatsApp [{$formatted}] sudah terdaftar atas nama mentor '{$existingKontak->nama_mentor}'! Gunakan nomor telepon yang berbeda.");
                    }
                },
            ],
            'program_pelatihan' => 'required|string',
            'foto'              => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'nidn.unique'         => $nidnUniqueMsg,
            'nidn.digits_between' => 'Peringatan: NIK / NIDN harus terdiri dari 8 sampai 16 digit angka.',
            'nidn.numeric'        => 'Peringatan: NIK / NIDN hanya boleh berisi angka.',
            'email.unique'        => "Peringatan: Email [{$request->email}] sudah terdaftar pada mentor lain di sistem!",
        ]);

        $ttl = ucwords(strtolower(trim((string) $request->tempat_lahir))) . ', ' . date('d-m-Y', strtotime($request->tanggal_lahir));
        $kontakFormatted = $this->formatNomorTelepon($request->kontak);

        $data = [
            'nama_mentor'       => strtoupper(trim((string) $request->nama_mentor)),
            'nidn'              => $request->nidn,
            'ttl'               => $ttl,
            'email'             => $request->email,
            'kontak'            => $kontakFormatted,
            'program_pelatihan' => strtoupper(trim((string) $request->program_pelatihan)),
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

        $nidnInput = $request->filled('nidn') ? preg_replace('/[^0-9]/', '', (string) $request->nidn) : null;
        $request->merge([
            'nidn'  => $nidnInput !== '' ? $nidnInput : null,
            'email' => $request->filled('email') ? strtolower(trim((string) $request->email)) : null,
        ]);

        $existingByNidn = $request->nidn
            ? Trainer::where('nidn', $request->nidn)->where('id', '!=', $id)->first()
            : null;
        $nidnUniqueMsg = $existingByNidn
            ? "Peringatan: NIK / NIDN [{$request->nidn}] sudah terdaftar atas nama mentor '{$existingByNidn->nama_mentor}'! Gunakan NIK / NIDN yang berbeda."
            : "Peringatan: NIK / NIDN [{$request->nidn}] sudah terdaftar pada mentor lain di sistem!";

        $request->validate([
            'nama_mentor'       => 'required|string|max:255',
            'nidn'              => 'nullable|numeric|digits_between:8,16|unique:trainers,nidn,' . $id,
            'tempat_lahir'      => 'nullable|string|max:100',
            'tanggal_lahir'     => 'nullable|date',
            'email'             => 'nullable|email|unique:trainers,email,' . $id,
            'kontak'            => [
                'nullable',
                function ($attribute, $value, $fail) use ($id) {
                    if (!$value) return;
                    $digits = preg_replace('/[^0-9]/', '', (string) $value);
                    $localDigits = preg_replace('/^(0|62)/', '', $digits);
                    if (!preg_match('/^8[0-9]{8,12}$/', $localDigits)) {
                        $fail('Nomor WhatsApp harus berupa nomor seluler yang valid (contoh: +6281234567890 atau 081234567890, 10-15 digit).');
                        return;
                    }
                    $formatted = '+62' . $localDigits;
                    $existingKontak = Trainer::where('kontak', $formatted)->where('id', '!=', $id)->first();
                    if ($existingKontak) {
                        $fail("Peringatan: Nomor WhatsApp [{$formatted}] sudah terdaftar atas nama mentor '{$existingKontak->nama_mentor}'! Gunakan nomor telepon yang berbeda.");
                    }
                },
            ],
            'program_pelatihan' => 'required|string',
            'status'            => 'required|in:AKTIF,PENDING,NON-AKTIF',
            'foto'              => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'nidn.unique'         => $nidnUniqueMsg,
            'nidn.digits_between' => 'Peringatan: NIK / NIDN harus terdiri dari 8 sampai 16 digit angka.',
            'nidn.numeric'        => 'Peringatan: NIK / NIDN hanya boleh berisi angka.',
            'email.unique'        => "Peringatan: Email [{$request->email}] sudah terdaftar pada mentor lain di sistem!",
        ]);

        if ($request->filled('tempat_lahir') && $request->filled('tanggal_lahir')) {
            $ttl = ucwords(strtolower(trim((string) $request->tempat_lahir))) . ', ' . date('d-m-Y', strtotime($request->tanggal_lahir));
        } elseif ($request->filled('tempat_lahir')) {
            $ttl = ucwords(strtolower(trim((string) $request->tempat_lahir)));
        } else {
            $ttl = $trainer->ttl;
        }

        $kontakFormatted = $request->filled('kontak') 
            ? $this->formatNomorTelepon($request->kontak) 
            : $trainer->kontak;

        $data = [
            'nama_mentor'       => strtoupper(trim((string) $request->nama_mentor)),
            'nidn'              => $request->nidn,
            'ttl'               => $ttl,
            'email'             => $request->email,
            'kontak'            => $kontakFormatted,
            'program_pelatihan' => strtoupper(trim((string) $request->program_pelatihan)),
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

        $digits = preg_replace('/[^0-9]/', '', (string) $nomor);
        if ($digits === '') {
            return null;
        }

        $localDigits = preg_replace('/^(0|62)/', '', $digits);
        return '+62' . $localDigits;
    }
}