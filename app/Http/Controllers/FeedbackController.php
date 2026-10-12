<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FeedbackController extends Controller
{
    /**
     * Menampilkan daftar feedback evaluasi materi offline dengan filter & metrik
     */
    public function index(Request $request)
    {
        $query = Feedback::latest();

        // Filter jika ada kategori
        if ($request->filled('category') && strtolower($request->category) !== 'all') {
            $query->whereRaw('LOWER(kategori) = ?', [strtolower($request->category)]);
        }

        $feedbacks = $query->get();
        $selectedCategory = $request->get('category', 'all');

        // URL Google Form yang sedang aktif (bisa diubah dari UI & disimpan di cache)
        $googleFormUrl = Cache::get('feedback_google_form_url', config('services.feedback.google_form_url', env('FEEDBACK_GOOGLE_FORM_URL', 'https://docs.google.com/forms/')));

        // Metrik hitungan untuk badge tombol filter
        $totalSemua = Feedback::count();
        $totalBerat = Feedback::whereRaw('LOWER(kategori) = ?', ['berat'])->count();
        $totalSedang = Feedback::whereRaw('LOWER(kategori) = ?', ['sedang'])->count();
        $totalUmum = Feedback::whereRaw('LOWER(kategori) = ?', ['umum'])->count();

        return view('feedback.index', compact(
            'feedbacks', 
            'selectedCategory', 
            'googleFormUrl',
            'totalSemua', 
            'totalBerat', 
            'totalSedang', 
            'totalUmum'
        ));
    }

    /**
     * Memperbarui URL Google Form Evaluasi Materi Offline
     */
    public function updateFormUrl(Request $request)
    {
        $request->validate([
            'google_form_url' => 'required|url|max:500',
        ]);

        Cache::forever('feedback_google_form_url', trim($request->google_form_url));

        return redirect()->route('feedback.index')->with('success', 'Link Google Form evaluasi materi offline berhasil diperbarui!');
    }

    /**
     * Simpan masukan / feedback baru (Internal App Form)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_peserta' => 'nullable|string|max:255',
            'kegiatan'     => 'nullable|string|max:255',
            'kategori'     => 'required|string|in:Berat,Sedang,Umum,berat,sedang,umum',
            'pesan'        => 'required|string|max:2000',
        ]);

        Feedback::create([
            'nama_peserta' => $request->filled('nama_peserta') ? trim($request->nama_peserta) : 'ANONYMOUS',
            'kegiatan'     => $request->filled('kegiatan') ? trim($request->kegiatan) : 'Materi Offline',
            'kategori'     => ucfirst(strtolower($validated['kategori'])),
            'pesan'        => trim($validated['pesan']),
            'status'       => 'pending',
        ]);

        return redirect()->route('feedback.index')->with('success', 'Masukan / keluhan peserta berhasil dicatat!');
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
            'solusi' => trim($request->solusi),
        ]);

        return redirect()->back()->with('success', 'Masukan peserta berhasil ditandai selesai ditindaklanjuti!');
    }

    /**
     * Hapus Feedback dari Sistem
     */
    public function destroy($id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();

        return redirect()->route('feedback.index')->with('success', 'Data feedback berhasil dihapus!');
    }

    /**
     * Endpoint Webhook dari Google Form (Google Apps Script / Webhook Eksternal)
     * Peserta yang kurang puas setelah materi offline mengisi Google Form dan hasilnya langsung masuk ke sini
     */
    public function webhookStore(Request $request)
    {
        try {
            $namaPeserta = $request->input('nama_peserta') ?? $request->input('nama') ?? $request->input('name') ?? $request->input('email') ?? 'ANONYMOUS';
            $kegiatan = $request->input('kegiatan') ?? $request->input('materi') ?? $request->input('session') ?? 'Materi Offline';
            
            $kategoriRaw = $request->input('kategori') ?? $request->input('category') ?? 'Umum';
            $pesanRaw = $request->input('pesan') ?? $request->input('message') ?? $request->input('feedback') ?? $request->input('masukan') ?? $request->input('keluhan') ?? '-';

            // Normalisasi kategori
            $katLower = strtolower(trim((string)$kategoriRaw));
            if (in_array($katLower, ['berat', 'high', 'critical', 'urgent', 'bug', 'tidak puas', 'sangat kurang puas'])) {
                $kategori = 'Berat';
            } elseif (in_array($katLower, ['sedang', 'medium', 'performa', 'warning', 'kurang puas', 'cukup'])) {
                $kategori = 'Sedang';
            } else {
                $kategori = 'Umum';
            }

            Feedback::create([
                'nama_peserta' => trim((string)$namaPeserta),
                'kegiatan'     => trim((string)$kegiatan),
                'kategori'     => $kategori,
                'pesan'        => trim((string)$pesanRaw),
                'status'       => 'pending',
            ]);

            return response()->json([
                'status'  => 'success', 
                'message' => 'Feedback peserta berhasil diterima dan dicatat.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error', 
                'message' => $e->getMessage()
            ], 500);
        }
    }
}