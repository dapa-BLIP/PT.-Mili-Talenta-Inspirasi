<?php

namespace App\Http\Controllers;

use App\Models\KategoriPelatihan;
use App\Models\Pelatihan;
use App\Models\Materi;
use Illuminate\Http\Request;

class KategoriPelatihanController extends Controller
{
    public function store(Request $request)
    {
        $request->merge([
            'nama_kategori' => strtoupper(trim((string)$request->nama_kategori))
        ]);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori_pelatihans,nama_kategori',
        ], [
            'nama_kategori.required' => 'Nama modul pelatihan wajib diisi.',
            'nama_kategori.unique'   => 'Nama modul program pelatihan ini sudah terdaftar di sistem.',
        ]);

        KategoriPelatihan::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        return redirect()->route('academic.pelatihan.index')->with('success', 'Modul Program Pelatihan baru berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $kategori = KategoriPelatihan::findOrFail($id);

        $terpakaiDiPelatihan = Pelatihan::where('jenis_pelatihan', $kategori->nama_kategori)->exists();
        $terpakaiDiMateri    = Materi::where('jenis_pelatihan', $kategori->nama_kategori)->exists();

        if ($terpakaiDiPelatihan || $terpakaiDiMateri) {
            return redirect()->route('academic.pelatihan.index')->withErrors([
                'delete_error' => "Modul '{$kategori->nama_kategori}' tidak dapat dihapus karena masih digunakan oleh kelas pelatihan atau bank materi aktif."
            ]);
        }

        $kategori->delete();
        return redirect()->route('academic.pelatihan.index')->with('success', 'Modul Program Pelatihan berhasil dihapus.');
    }
}