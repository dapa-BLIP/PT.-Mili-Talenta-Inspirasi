<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Trainer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JadwalController extends Controller
{
    /**
     * DISPLAY UTAMA (INDEX) + MULTI-FILTER SYNCHRONIZED (FULLY ROBUST)
     */
    public function index(Request $request)
    {
        $trainers = Trainer::orderBy('nama_mentor', 'asc')->get();
        $waktuSekarang = Carbon::now('Asia/Jakarta');

        $query = Jadwal::with('trainer');

        // 1. Ambil input filter date
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // 🛠️ FIX LOGIKA TANGGAL: Jika start_date dan end_date diisi hari yang sama, 
        // kita longgarkan query database agar jadwal yang selesai hari ini tetap lolos ditarik.
        if ($request->filled('start_date') && $request->filled('end_date') && $startDate === $endDate) {
            $query->where(function($q) use ($startDate) {
                $q->where('tanggal_mulai', '<=', $startDate)
                  ->where('tanggal_selesai', '>=', $startDate);
            });
        } else {
            // Logika filter rentang hari normal (jika berbeda hari)
            if ($request->filled('start_date')) {
                $query->where('tanggal_mulai', '>=', $startDate);
            }
            if ($request->filled('end_date')) {
                $query->where('tanggal_selesai', '<=', $endDate);
            }
        }

        if ($request->filled('trainer_id')) {
            $query->where('trainer_id', $request->trainer_id);
        }

        // Ambil semua data yang lolos sensor database awal
        $allJadwals = $query->orderBy('tanggal_mulai', 'desc')->get();

        // 2. Filter Status Real-time Menggunakan PHP Engine (Anti-Gagal Beda Jam)
        if ($request->filled('status')) {
            $statusFilter = strtolower($request->status);

            $jadwals = $allJadwals->filter(function ($j) use ($waktuSekarang, $statusFilter) {
                $tglSelesaiString = $j->tanggal_selesai instanceof \DateTimeInterface 
                    ? $j->tanggal_selesai->format('Y-m-d') 
                    : Carbon::parse($j->tanggal_selesai)->format('Y-m-d');
                
                // Gabungkan tanggal selesai dan jam selesai secara presisi
                $waktuSelesaiJadwal = Carbon::parse($tglSelesaiString . ' ' . $j->jam_selesai, 'Asia/Jakarta');

                // Tentukan status berdasarkan jam murni realtime
                $isComplete = in_array(strtolower($j->status), ['complete', 'selesai']) 
                    || $waktuSekarang->greaterThanOrEqualTo($waktuSelesaiJadwal);

                if ($statusFilter === 'complete') {
                    return $isComplete;
                } elseif ($statusFilter === 'on-going') {
                    return !$isComplete;
                }

                return true;
            });
        } else {
            $jadwals = $allJadwals;
        }

        return view('jadwal.index', compact('jadwals', 'trainers'));
    }
    /**
     * STORE JADWAL BARU
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_kegiatan'    => 'required|string|max:255',
            'trainer_id'        => 'required|exists:trainers,id',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai'         => 'required',
            'jam_selesai'       => 'required',
            'jenis_pelaksanaan' => 'required|in:offline,online',
            'description'       => 'nullable|string',
        ]);

        $validated['status'] = 'on-going'; 

        Jadwal::create($validated);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal baru berhasil ditambahkan!');
    }

    /**
     * UPDATE JADWAL
     */
    public function update(Request $request, $id)
    {
        $jadwal = Jadwal::findOrFail($id);

        $validated = $request->validate([
            'judul_kegiatan'    => 'required|string|max:255',
            'trainer_id'        => 'required|exists:trainers,id',
            'tanggal_mulai'     => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal_mulai',
            'jam_mulai'         => 'required',
            'jam_selesai'       => 'required',
            'jenis_pelaksanaan' => 'required|in:offline,online',
            'description'       => 'nullable|string',
        ]);

        $validated['status'] = $jadwal->getRawOriginal('status') ?? 'on-going';

        $jadwal->update($validated);

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui!');
    }

    /**
     * DELETE JADWAL
     */
    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $jadwal->delete();

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus!');
    }
}