<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AcademicController; 
use Illuminate\Support\Facades\Route;

// Akses utama langsung dilempar ke halaman login
Route::get('/', function () {
    return redirect()->route('login');
});

// Kelompokkan semua rute yang butuh LOGIN & VERIFIKASI
Route::middleware(['auth', 'verified'])->group(function () {
    
    // 1. DASHBOARD
    Route::get('/dashboard', [PesertaController::class, 'report'])->name('dashboard');

    // 2. PROFILE USER
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // =========================================================================
    // JALUR AJAX UTAMA (Matrix Grading System Auto-Adapt)
    // =========================================================================
    Route::get('/ajax/get-materi', [NilaiController::class, 'getMateriBySektor'])->name('nilai.getMateri');
    Route::get('/ajax/get-nilai-existing', [NilaiController::class, 'getNilaiExisting'])->name('nilai.getNilaiExisting');

    // =========================================================================
    // 3. MANAJEMEN PESERTA (CRUD & REPORT)
    // =========================================================================
    Route::get('peserta/report', [PesertaController::class, 'report'])->name('peserta.report');
    Route::get('peserta/export', [PesertaController::class, 'export'])->name('peserta.export');
    Route::post('peserta/import', [PesertaController::class, 'import'])->name('peserta.import');
    Route::patch('peserta/{id}/status', [PesertaController::class, 'updateStatus'])->name('peserta.updateStatus');
    Route::resource('peserta', PesertaController::class);
    
    // =========================================================================
    // 4. KELOLA NILAI MATRIX SYSTEM (PREFIXED)
    // =========================================================================
    Route::prefix('manajemen-peserta')->group(function () {
        Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
        Route::post('/nilai/store', [NilaiController::class, 'store'])->name('nilai.store');
        Route::get('/nilai/export', [NilaiController::class, 'export'])->name('nilai.export');
        Route::post('/nilai/import', [NilaiController::class, 'import'])->name('nilai.import');
    });

    // =========================================================================
    // 5. JADWAL MANAGEMENT
    // =========================================================================
    Route::resource('jadwal', JadwalController::class);

    // =========================================================================
    // 6. ACADEMIC MASTER HUB (CMS SINGLE PAGE HUB) 
    // =========================================================================
    Route::get('academic-master', [AcademicController::class, 'index'])->name('academic.index');
    
    // Mentor Actions (Store, Update, Delete)
    Route::post('academic-master/mentor', [AcademicController::class, 'storeTrainer'])->name('academic.storeTrainer');
    Route::put('academic-master/mentor/{id}', [AcademicController::class, 'updateTrainer'])->name('academic.updateTrainer'); 
    Route::delete('academic-master/mentor/{id}', [AcademicController::class, 'destroyTrainer'])->name('academic.destroyTrainer');
    
    // Pelatihan Actions (Store, Update, Delete)
    Route::post('academic-master/pelatihan', [AcademicController::class, 'storePelatihan'])->name('academic.storePelatihan');
    Route::put('academic-master/pelatihan/{id}', [AcademicController::class, 'updatePelatihan'])->name('academic.updatePelatihan');
    Route::delete('academic-master/pelatihan/{id}', [AcademicController::class, 'destroyPelatihan'])->name('academic.destroyPelatihan');


    Route::prefix('academic-master')->name('academic.')->group(function () {
    // Routes Export
    Route::get('/export-pelatihan', [AcademicController::class, 'exportPelatihan'])->name('exportPelatihan');
    Route::get('/export-mentor', [AcademicController::class, 'exportMentor'])->name('exportMentor');

    // Routes Import
    Route::post('/import-pelatihan', [AcademicController::class, 'importPelatihan'])->name('importPelatihan');
    Route::post('/import-mentor', [AcademicController::class, 'importMentor'])->name('importMentor');
    });

    // =========================================================================
    // 7. FEEDBACK MANAGEMENT
    // =========================================================================
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

    
});

require __DIR__.'/auth.php';