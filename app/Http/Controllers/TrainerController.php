<?php

namespace App\Http\Controllers;

use App\Models\Trainer; // Pastikan Model Trainer sudah ada
use Illuminate\Http\Request;

class TrainerController extends Controller
{
    public function index()
    {
        $trainers = Trainer::orderBy('nama', 'asc')->get();
        return view('trainer.index', compact('trainers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
        ]);

        Trainer::create([
            'nama' => strtoupper($request->nama) // Biar auto-uppercase konsisten dengan UI industrial
        ]);

        return redirect()->route('trainer.index')->with('success', 'Trainer successfully registered.');
    }
}