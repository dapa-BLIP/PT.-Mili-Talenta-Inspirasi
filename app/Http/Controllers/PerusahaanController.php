<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use Illuminate\Http\Request;

class PerusahaanController extends Controller
{
    public function index()
    {
        $perusahaans = Perusahaan::with('pesertas')
            ->withCount('pesertas')
            ->latest()
            ->get()
            ->map(function ($pt) {
                // Grouping berdasarkan teks kolom 'pelatihan' di tabel pesertas
                $grouped = $pt->pesertas->groupBy(function ($item) {
                    return !empty($item->pelatihan) ? strtoupper($item->pelatihan) : 'BELUM DITETAPKAN';
                })->map->count();

                $pt->breakdown_program = $grouped;
                $pt->total_program_count = $grouped->count();
                return $pt;
            });

        return view('company.perusahaan', compact('perusahaans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_perusahaan'  => 'required|string|max:255|unique:perusahaans,nama_perusahaan',
            'penanggung_jawab' => 'nullable|string|max:150',
            'kontak_pic'       => 'nullable|string|max:20',
            'email_perusahaan' => 'nullable|email|max:150',
            'alamat'           => 'nullable|string|max:500',
        ]);

        $kontak = $request->kontak_pic;
        if ($kontak) {
            $raw = preg_replace('/[^0-9]/', '', $kontak);
            $raw = preg_replace('/^(0|62)/', '', $raw);
            $kontak = '+62' . $raw;
        }

        Perusahaan::create([
            'nama_perusahaan'  => strtoupper(trim($request->nama_perusahaan)),
            'penanggung_jawab' => strtoupper(trim($request->penanggung_jawab ?? '')),
            'kontak_pic'       => $kontak,
            'email_perusahaan' => strtolower(trim($request->email_perusahaan ?? '')),
            'alamat'           => trim($request->alamat ?? ''),
            'status'           => 'AKTIF',
        ]);

        return redirect()->route('perusahaan.index')->with('success', 'Data Perusahaan Mitra berhasil didaftarkan.');
    }

    public function update(Request $request, $id)
    {
        $perusahaan = Perusahaan::findOrFail($id);

        $request->validate([
            'nama_perusahaan'  => 'required|string|max:255|unique:perusahaans,nama_perusahaan,' . $id,
            'penanggung_jawab' => 'nullable|string|max:150',
            'kontak_pic'       => 'nullable|string|max:20',
            'email_perusahaan' => 'nullable|email|max:150',
            'alamat'           => 'nullable|string|max:500',
            'status'           => 'required|in:AKTIF,PENDING,NON-AKTIF',
        ]);

        $kontak = $request->kontak_pic;
        if ($kontak) {
            $raw = preg_replace('/[^0-9]/', '', $kontak);
            $raw = preg_replace('/^(0|62)/', '', $raw);
            $kontak = '+62' . $raw;
        }

        $perusahaan->update([
            'nama_perusahaan'  => strtoupper(trim($request->nama_perusahaan)),
            'penanggung_jawab' => strtoupper(trim($request->penanggung_jawab ?? '')),
            'kontak_pic'       => $kontak,
            'email_perusahaan' => strtolower(trim($request->email_perusahaan ?? '')),
            'alamat'           => trim($request->alamat ?? ''),
            'status'           => $request->status,
        ]);

        return redirect()->route('perusahaan.index')->with('success', 'Data Perusahaan Mitra berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $perusahaan = Perusahaan::withCount('pesertas')->findOrFail($id);

        if ($perusahaan->pesertas_count > 0) {
            return redirect()->route('perusahaan.index')->withErrors([
                'delete_error' => "Gagal menghapus! Perusahaan ini masih memiliki {$perusahaan->pesertas_count} peserta aktif terhubung."
            ]);
        }

        $perusahaan->delete();
        return redirect()->route('perusahaan.index')->with('success', 'Data Perusahaan berhasil dihapus.');
    }
}