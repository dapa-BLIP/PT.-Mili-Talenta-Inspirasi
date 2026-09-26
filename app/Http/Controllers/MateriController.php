<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Trainer;
use App\Models\Pelatihan;
use App\Models\KategoriPelatihan;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    public function index()
    {
        return redirect()->route('academic.pelatihan.index');
    }

    public function store(Request $request)
    {
        $request->merge([
            'nama_materi'     => strtoupper(trim((string)$request->nama_materi)),
            'jenis_pelatihan' => strtoupper(trim((string)$request->jenis_pelatihan)),
        ]);

        $request->validate([
            'nama_materi'     => 'required|string|max:255',
            'jenis_pelatihan' => 'required|string|max:255',
            'trainer_id'      => 'nullable|exists:trainers,id',
        ], [
            'nama_materi.required'     => 'Judul materi pembelajaran wajib diisi.',
            'jenis_pelatihan.required' => 'Modul pelatihan induk wajib dipilih.',
            'trainer_id.exists'        => 'Mentor pengampu yang dipilih tidak valid.',
        ]);

        Materi::create([
            'nama_materi'     => $request->nama_materi,
            'jenis_pelatihan' => $request->jenis_pelatihan,
            'trainer_id'      => $request->trainer_id,
        ]);

        return redirect()->route('academic.pelatihan.index')->with('success', 'Materi pembelajaran baru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $materi = Materi::findOrFail($id);

        $request->merge([
            'nama_materi'     => strtoupper(trim((string)$request->nama_materi)),
            'jenis_pelatihan' => strtoupper(trim((string)$request->jenis_pelatihan)),
        ]);

        $request->validate([
            'nama_materi'     => 'required|string|max:255',
            'jenis_pelatihan' => 'required|string|max:255',
            'trainer_id'      => 'nullable|exists:trainers,id',
        ], [
            'nama_materi.required'     => 'Judul materi pembelajaran wajib diisi.',
            'jenis_pelatihan.required' => 'Modul pelatihan induk wajib dipilih.',
            'trainer_id.exists'        => 'Mentor pengampu yang dipilih tidak valid.',
        ]);

        $materi->update([
            'nama_materi'     => $request->nama_materi,
            'jenis_pelatihan' => $request->jenis_pelatihan,
            'trainer_id'      => $request->trainer_id,
        ]);

        return redirect()->route('academic.pelatihan.index')->with('success', 'Materi pembelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $materi = Materi::findOrFail($id);
        $materi->delete();

        return redirect()->route('academic.pelatihan.index')->with('success', 'Materi pembelajaran berhasil dihapus.');
    }
}