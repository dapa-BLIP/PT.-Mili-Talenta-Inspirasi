<?php

namespace App\Http\Controllers;

use App\Models\Peserta;
use App\Models\Trainer;
use App\Models\Pelatihan;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $bulanAktif = $request->input('bulan', date('m'));
        $tahunAktif = $request->input('tahun', date('Y'));

        $totalMentor = Trainer::count();
        $totalPelatihan = Pelatihan::count();
        $trainers = Trainer::all();
        $pelatihans = Pelatihan::all();

        $totalRegistrasi = Peserta::whereMonth('created_at', $bulanAktif)
            ->whereYear('created_at', $tahunAktif)
            ->count();

        $komposisiProgram = Peserta::select('pelatihan', DB::raw('count(*) as total'))
            ->whereMonth('created_at', $bulanAktif)
            ->whereYear('created_at', $tahunAktif)
            ->groupBy('pelatihan')
            ->orderBy('total', 'desc')
            ->get();

        $programTerpopuler = $komposisiProgram->first() ? $komposisiProgram->first()->pelatihan : 'Belum Ada';
        $totalProgramTerpopuler = $komposisiProgram->first() ? $komposisiProgram->first()->total : 0;
        $jumlahJenisProgram = $komposisiProgram->count();

        $priaTotal = Peserta::whereMonth('created_at', $bulanAktif)
            ->whereYear('created_at', $tahunAktif)
            ->where(DB::raw('LOWER(gender)'), 'pria') 
            ->count();

        $wanitaTotal = Peserta::whereMonth('created_at', $bulanAktif)
            ->whereYear('created_at', $tahunAktif)
            ->where(DB::raw('LOWER(gender)'), 'wanita') 
            ->count();

        $totalGender = $priaTotal + $wanitaTotal;
        $priaPercent = $totalGender > 0 ? round(($priaTotal / $totalGender) * 100) : 0;
        $wanitaPercent = $totalGender > 0 ? round(($wanitaTotal / $totalGender) * 100) : 0;

        $driver = DB::getDriverName();
        $dayExpression = $driver === 'sqlite'
            ? "strftime('%w', created_at)"
            : "(DAYOFWEEK(created_at) - 1)";

        $weeklyTrendRaw = Peserta::select(DB::raw("{$dayExpression} as day_index"), DB::raw('count(*) as total'))
            ->whereMonth('created_at', $bulanAktif)
            ->whereYear('created_at', $tahunAktif)
            ->groupBy('day_index')
            ->pluck('total', 'day_index')
            ->toArray();

        $mapIndexToDay = [
            '1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', 
            '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '0' => 'Sunday'
        ];

        $daysOfWeek = ['Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0, 'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0];

        foreach ($weeklyTrendRaw as $dayIndex => $count) {
            if (isset($mapIndexToDay[$dayIndex])) {
                $dayName = $mapIndexToDay[$dayIndex];
                $daysOfWeek[$dayName] = $count;
            }
        }

        // ================= METRIK ANALISIS FEEDBACK =================
        $totalFeedback = Feedback::whereMonth('created_at', $bulanAktif)
            ->whereYear('created_at', $tahunAktif)
            ->count();

        $resolvedFeedback = Feedback::whereMonth('created_at', $bulanAktif)
            ->whereYear('created_at', $tahunAktif)
            ->where('status', 'resolved')
            ->count();

        $pendingFeedback = $totalFeedback - $resolvedFeedback;
        $resolveRate = $totalFeedback > 0 ? round(($resolvedFeedback / $totalFeedback) * 100) : 100;

        return view('dashboard.index', compact(
            'bulanAktif', 'tahunAktif', 'totalRegistrasi', 'komposisiProgram', 
            'daysOfWeek', 'programTerpopuler', 'totalProgramTerpopuler', 'jumlahJenisProgram',
            'priaTotal', 'wanitaTotal', 'priaPercent', 'wanitaPercent',
            'totalMentor', 'totalPelatihan', 'trainers', 'pelatihans',
            'totalFeedback', 'resolvedFeedback', 'pendingFeedback', 'resolveRate'
        ));
    }
}