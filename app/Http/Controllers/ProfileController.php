<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    // Menampilkan daftar peserta
    public function index()
    {
        // Pake latest() biar data terbaru muncul paling atas (Standar Industrial)
        $pesertas = Peserta::latest()->get();
        return view('peserta.index', compact('pesertas'));
    }

    // Menampilkan form tambah
    public function create()
    {
        return view('peserta.create');
    }

    // Menyimpan data ke database
    public function store(Request $request)
    {
        // 1. Validasi dengan pesan error yang rapi
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email'        => 'required|email|unique:pesertas,email',
            'pelatihan'    => 'required|string',
        ]);

        // 2. Gunakan $validated, bukan $request->all() 
        // Ini biar user gak bisa masukin data 'admin=1' lewat inspect element
        Peserta::create($validated);

        return redirect()->route('peserta.index')
                         ->with('success', 'Peserta berhasil didaftarkan ke sistem.');
    }
}