<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\Pelatihan;
use App\Models\Peserta;
use App\Models\Nilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\NilaiExport;
use App\Imports\NilaiImport;

class NilaiController extends Controller
{
    public function index()
    {
        $sektors = Pelatihan::whereNotNull('spektrum_kategori')
            ->where('spektrum_kategori', '!=', '')
            ->distinct()
            ->pluck('spektrum_kategori')
            ->toArray();

        $pesertas = Peserta::select('id', 'nama_lengkap', 'pelatihan', 'tempat_kerja', 'gender', 'status')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        return view('nilai.index', compact('sektors', 'pesertas'));
    }

    public function getNilaiExisting(Request $request)
    {
        $pelatihanId = $request->get('pelatihan_id');

        if (!$pelatihanId) {
            return response()->json([]);
        }

        $data = Nilai::where('pelatihan_id', $pelatihanId)
            ->get(['peserta_id', 'scores', 'catatan']);

        return response()->json($data);
    }

    /**
     * 🚀 AJAX: Load pelatihan beserta komponen_nilai yang valid dari database
     */
    public function getMateriBySektor(Request $request)
    {
        try {
            $sektorVal = trim($request->get('sektor', ''));

            if (!$sektorVal) {
                return response()->json([]);
            }

            // Normalisasi pemanggilan dengan mengambil model lengkap agar accessor komponen_nilai terbaca
            $pelatihans = Pelatihan::with('mentor')
                ->where(function ($q) use ($sektorVal) {
                    $q->where('spektrum_kategori', $sektorVal)
                      ->orWhereRaw('LOWER(TRIM(spektrum_kategori)) = ?', [strtolower($sektorVal)]);
                })
                ->orderBy('nama_pelatihan', 'asc')
                ->get();

            // Transformasikan agar komponen_nilai dipastikan berupa array bersih
            $result = $pelatihans->map(function ($item) {
                $komponen = $item->komponen_nilai ?? $item->komponen_penilaian ?? [];
                
                if (is_string($komponen)) {
                    $decoded = json_decode($komponen, true);
                    $komponen = is_array($decoded) ? $decoded : explode(',', $komponen);
                }

                return [
                    'id'              => $item->id,
                    'nama_pelatihan'  => $item->nama_pelatihan,
                    'spektrum_kategori' => $item->spektrum_kategori,
                    'komponen_nilai'  => array_values(array_filter(array_map('trim', (array)$komponen))),
                    'trainer_id'      => $item->trainer_id,
                    'mentor'          => $item->mentor,
                ];
            });

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'error'   => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

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
                $activePesertaIds = Peserta::where(function($q) {
                    $q->whereNull('status')
                      ->orWhereRaw("UPPER(TRIM(status)) IN ('AKTIF', 'ACTIVE', '')");
                })->pluck('id')->toArray();

                foreach ($request->nilai as $data) {
                    if (!isset($data['peserta_id']) || $data['peserta_id'] === null) {
                        continue;
                    }

                    if (!in_array($data['peserta_id'], $activePesertaIds)) {
                        continue;
                    }

                    $scoresPayload = isset($data['scores']) ? $data['scores'] : [];
                    $catatanText   = isset($data['catatan']) ? trim($data['catatan']) : null;

                    $isKosong = true;
                    foreach ($scoresPayload as $val) {
                        if (trim((string)$val) !== '') {
                            $isKosong = false;
                            break;
                        }
                    }

                    if ($isKosong && empty($catatanText)) {
                        continue;
                    }

                    $cleanScores = [];
                    foreach ($scoresPayload as $key => $val) {
                        if (trim((string)$val) !== '') {
                            $cleanScores[$key] = (int)$val;
                        }
                    }

                    Nilai::updateOrCreate(
                        [
                            'peserta_id'   => $data['peserta_id'],
                            'pelatihan_id' => $pelatihanId,
                        ],
                        [
                            'trainer_id' => $trainerId,
                            'scores'     => $cleanScores,
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

    public function export(Request $request)
    {
        $request->validate([
            'spektrum_kategori' => 'required',
            'pelatihan_id'      => 'required',
        ]);

        $sektor      = $request->get('spektrum_kategori');
        $pelatihanId = $request->get('pelatihan_id');
        $namaFile    = 'Template_Matrix_Nilai_' . str_replace(' ', '_', strtoupper($sektor)) . '_' . now()->format('dmY_His') . '.xlsx';

        return Excel::download(new NilaiExport($pelatihanId, $sektor), $namaFile);
    }
    
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

            Excel::import(new NilaiImport($pelatihanId, $trainerId), $request->file('file_excel'));

            return redirect()->back()->with('success', 'EXCEL ENGINE SUCCESS: Berkas nilai excel berhasil di-import masuk ke kelas.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal membaca berkas Excel. Detail: ' . $e->getMessage()]);
        }
    }
}