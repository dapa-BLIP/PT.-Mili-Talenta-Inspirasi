<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    // Menampilkan halaman feedback anonim (Index)
    public function index()
    {
        // Mengambil log data feedback terbaru dari tabel feedbacks
        $feedbacks = Feedback::latest()->get();
        return view('feedback.index', compact('feedbacks'));
    }

    // Menyimpan input feedback (Jika nanti dibutuhkan integrasi webhook/API Google Form)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori' => 'required|string',
            'pesan' => 'required|string',
        ]);

        Feedback::create([
            'kategori' => $validated['kategori'],
            'pesan' => $validated['pesan'],
        ]);

        return redirect()->route('feedback.index')
                         ->with('success', 'Transmission secure. Feedback logged.');
    }
}