<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = Feedback::latest();

        // Filter jika ada kategori
        if ($request->filled('category') && $request->category != 'all') {
            $query->where('kategori', $request->category);
        }

        $feedbacks = $query->get();
        $selectedCategory = $request->get('category', 'all');

        return view('feedback.index', compact('feedbacks', 'selectedCategory'));
    }

    /**
     * Simpan Solusi & Tandai Feedback Selesai Perbaikan
     */
    public function resolve(Request $request, $id)
    {
        $request->validate([
            'solusi' => 'required|string|max:1000',
        ]);

        $feedback = Feedback::findOrFail($id);
        $feedback->update([
            'status' => 'resolved',
            'solusi' => $request->solusi,
        ]);

        return redirect()->back()->with('success', 'Masalah feedback berhasil ditandai selesai!');
    }

    public function webhookStore(Request $request)
    {
        try {
            Feedback::create([
                'kategori' => $request->input('kategori', 'Umum'),
                'pesan'    => $request->input('pesan', '-'),
                'status'   => 'pending',
            ]);

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}