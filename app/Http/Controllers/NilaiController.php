<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\Pelatihan;
use App\Models\Peserta;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NilaiController extends Controller
{
    public function index()
    {
        // 1. Ambil spektrum kategori unik dari master pelatihan CMS
        $sektors = Pelatihan::whereNotNull('spektrum_kategori')
            ->where('spektrum_kategori', '!=', '')
            ->distinct()
            ->pluck('spektrum_kategori')
            ->toArray();

        // 2. Ambil semua peserta, urutkan berdasarkan nama biar rapi
        $pesertas = Peserta::orderBy('nama_lengkap', 'asc')->get();

        return view('nilai.index', compact('sektors', 'pesertas'));
    }

    /**
     * 🚀 ENDPOINT AJAX: Menarik rekam nilai JSON lama dari tabel nilais
     */
    public function getNilaiExisting(Request $request)
    {
        $pelatihanId = $request->get('pelatihan_id');

        if (!$pelatihanId) {
            return response()->json([]);
        }

        // Ambil data nilai berdasarkan pelatihan yang dikunci
        $data = Nilai::where('pelatihan_id', $pelatihanId)
            ->get(['peserta_id', 'scores', 'catatan']);

        return response()->json($data);
    }

    /**
     * 🚀 ENDPOINT AJAX: Dipanggil oleh filter Langkah 1 untuk load kelas pelatihan sawit
     */
    public function getMateriBySektor(Request $request)
    {
        try {
            $sektorVal = $request->get('sektor');

            if (!$sektorVal) {
                return response()->json([]);
            }

            // Ambil kelas pelatihan beserta data mentor pengampunya
            $pelatihans = Pelatihan::with('mentor')
                ->where('spektrum_kategori', trim($sektorVal))
                ->get();

            return response()->json($pelatihans);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * 🚀 STEP 2 ACTION: Simpan / Upsert data Matrix Nilai Kolektif format JSON
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pelatihan_id' => 'required|exists:pelatihans,id',
            'trainer_id'   => 'required|exists:trainers,id', 
            'nilai'        => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter input matrix tidak lengkap atau tidak valid!'
            ], 422);
        }

        $pelatihanId = $request->pelatihan_id;
        $trainerId   = $request->trainer_id; 

        try {
            DB::transaction(function () use ($request, $pelatihanId, $trainerId) {
                foreach ($request->nilai as $data) {
                    if (!isset($data['peserta_id']) || $data['peserta_id'] === null) {
                        continue;
                    }

                    // Ambil sub-array scores (Contoh: ["tugas" => 90, "uts" => 80])
                    $scoresPayload = isset($data['scores']) ? $data['scores'] : [];
                    $catatanText   = isset($data['catatan']) ? trim($data['catatan']) : null;

                    // Filter apakah ada nilai yang di-input, jika kosong semua skip baris ini
                    $isKosong = true;
                    foreach ($scoresPayload as $val) {
                        if (trim($val) !== '') {
                            $isKosong = false;
                            break;
                        }
                    }

                    if ($isKosong && empty($catatanText)) {
                        continue; // Lewati jika tidak ada inputan nilai maupun catatan
                    }

                    // Bersihkan dan bersihkan nilai integer data sub-scores
                    $cleanScores = [];
                    foreach ($scoresPayload as $key => $val) {
                        if (trim($val) !== '') {
                            $cleanScores[$key] = (int)$val;
                        }
                    }

                    // Lakukan operasi Pintar: Update data jika eksis, Insert jika belum ada (Upsert)
                    Nilai::updateOrCreate(
                        [
                            'peserta_id'   => $data['peserta_id'],
                            'pelatihan_id' => $pelatihanId,
                        ],
                        [
                            'trainer_id' => $trainerId,
                            'scores'     => $cleanScores, // Disimpan otomatis jadi JSON via Eloquent Casts
                            'catatan'    => $catatanText,
                        ]
                    );
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Luar biasa! Semua data nilai matriks dinamis berhasil dibukukan.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Kesalahan Sistem SQL: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PROSES EXPORT TEMPLATE EXCEL DYNAMIC MATRIX
     */
    public function export(Request $request)
    {
        $request->validate([
            'spektrum_kategori' => 'required',
            'pelatihan_id'      => 'required',
        ]);

        $sektor      = $request->get('spektrum_kategori');
        $pelatihanId = $request->get('pelatihan_id');

        $namaFile = 'Template_Matrix_Nilai_' . str_replace(' ', '_', strtoupper($sektor)) . '_' . now()->format('dmY') . '.xlsx';

        // Lempar variabel parameter baru ke constructor file export lu
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\NilaiExport($pelatihanId, $sektor), $namaFile);
    }
    
    /**
     * PROSES IMPORT EXCEL MATRIX GRADING SYSTEM
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_excel'   => 'required|mimes:xlsx,xls,csv|max:10240',
            'pelatihan_id' => 'required',
            'trainer_id'   => 'required',
        ]);

        try {
            $pelatihanId = $request->pelatihan_id;
            $trainerId   = $request->trainer_id;

            // Masukkan data import mengarah ke parameter ID master terpusat yang baru
            \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\NilaiImport($pelatihanId, $trainerId), $request->file('file_excel'));

            return redirect()->back()->with('success', 'EXCEL ENGINE SUCCESS: Berkas nilai excel berhasil di-import masuk ke kelas.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal membaca berkas Excel. Detail: ' . $e->getMessage()]);
        }
    }
}