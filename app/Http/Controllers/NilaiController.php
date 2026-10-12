<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\Pelatihan;
use App\Models\Peserta;
use App\Models\Nilai;
use App\Models\KategoriPelatihan;
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
        $kategoriPrograms = KategoriPelatihan::orderBy('nama_kategori', 'asc')->pluck('nama_kategori')->toArray();
        $pelatihanPrograms = Pelatihan::whereNotNull('jenis_pelatihan')->where('jenis_pelatihan', '!=', '')->distinct()->pluck('jenis_pelatihan')->toArray();
        $programs = array_values(array_unique(array_filter(array_merge($kategoriPrograms, $pelatihanPrograms))));
        sort($programs);

        $mentors = Trainer::where('status', 'AKTIF')
            ->orderBy('nama_mentor', 'asc')
            ->get();

        $pelatihans = Pelatihan::with('mentor')
            ->orderBy('nama_pelatihan', 'asc')
            ->get();

        $pesertas = Peserta::select('id', 'nama_lengkap', 'pelatihan', 'tempat_kerja', 'gender', 'status')
            ->orderBy('nama_lengkap', 'asc')
            ->get();

        return view('nilai.index', compact('programs', 'mentors', 'pelatihans', 'pesertas'));
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
     * 🚀 AJAX: Load materi pelatihan berdasarkan jenis_pelatihan
     */
    public function getMateriByJenis(Request $request)
    {
        try {
            $jenisVal = trim($request->get('jenis_pelatihan', $request->get('sektor', '')));

            if (!$jenisVal) {
                return response()->json([]);
            }

            $pelatihans = Pelatihan::with('mentor')
                ->where(function ($q) use ($jenisVal) {
                    $q->where('jenis_pelatihan', $jenisVal)
                      ->orWhereRaw('LOWER(TRIM(jenis_pelatihan)) = ?', [strtolower($jenisVal)]);
                })
                ->orderBy('nama_pelatihan', 'asc')
                ->get();

            $result = $pelatihans->map(function ($item) {
                $komponen = $item->komponen_nilai ?? [];
                
                if (is_string($komponen)) {
                    $decoded = json_decode($komponen, true);
                    $komponen = is_array($decoded) ? $decoded : explode(',', $komponen);
                }

                return [
                    'id'              => $item->id,
                    'nama_pelatihan'  => $item->nama_pelatihan,
                    'jenis_pelatihan' => $item->jenis_pelatihan,
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
                'message' => 'Kolom penilaian tidak lengkap atau tidak valid!'
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
                'message' => 'Semua data nilai berhasil disimpan.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data gagal di simpan.' . $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $pelatihanId = $request->get('pelatihan_id');

        if ($pelatihanId) {
            $pelatihan = Pelatihan::find($pelatihanId);
        } else {
            $pelatihan = Pelatihan::first();
        }

        if (!$pelatihan) {
            return redirect()->back()->with('error', 'Data kelas pelatihan belum tersedia untuk di-export.');
        }

        $namaClean = str_replace(' ', '_', strtoupper($pelatihan->nama_pelatihan));
        $namaFile  = 'Template_Matrix_Nilai_' . $namaClean . '_' . now()->format('dmY_His') . '.xlsx';

        return Excel::download(new NilaiExport($pelatihan->id, $pelatihan->nama_pelatihan), $namaFile);
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

            return redirect()->back()->with('success', 'EXCEL ENGINE SUCCESS: Berkas nilai excel berhasil di-import.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal membaca berkas Excel. Detail: ' . $e->getMessage()]);
        }
    }
}