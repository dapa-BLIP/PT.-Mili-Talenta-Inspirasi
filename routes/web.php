<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController; 
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AcademicController; 
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::post('/feedback/webhook', [FeedbackController::class, 'webhookStore']);

Route::middleware(['auth', 'verified'])->group(function () {
    
    // 1. DASHBOARD
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 2. PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // JALUR AJAX UTAMA MATRIX GRADING
    Route::get('/ajax/get-materi', [NilaiController::class, 'getMateriBySektor'])->name('nilai.getMateri');
    Route::get('/ajax/get-nilai-existing', [NilaiController::class, 'getNilaiExisting'])->name('nilai.getNilaiExisting');

    // 3. PESERTA
    Route::get('peserta/export', [PesertaController::class, 'export'])->name('peserta.export');
    Route::post('peserta/import', [PesertaController::class, 'import'])->name('peserta.import');
    Route::patch('peserta/{id}/status', [PesertaController::class, 'updateStatus'])->name('peserta.updateStatus');
    Route::resource('peserta', PesertaController::class);
    
    // 4. KELOLA NILAI MATRIX
    Route::prefix('manajemen-peserta')->group(function () {
        Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
        Route::post('/nilai/store', [NilaiController::class, 'store'])->name('nilai.store');
        Route::get('/nilai/export', [NilaiController::class, 'export'])->name('nilai.export');
        Route::post('/nilai/import', [NilaiController::class, 'import'])->name('nilai.import');
    });

    // 5. JADWAL
    Route::resource('jadwal', JadwalController::class);

    // 6. ACADEMIC MASTER HUB
    Route::get('academic-master', [AcademicController::class, 'index'])->name('academic.index');
    Route::prefix('academic-master')->name('academic.')->group(function () {
        Route::post('/mentor', [AcademicController::class, 'storeTrainer'])->name('storeTrainer');
        Route::put('/mentor/{id}', [AcademicController::class, 'updateTrainer'])->name('updateTrainer'); 
        Route::delete('/mentor/{id}', [AcademicController::class, 'destroyTrainer'])->name('destroyTrainer');
        
        Route::post('/pelatihan', [AcademicController::class, 'storePelatihan'])->name('storePelatihan');
        Route::put('/pelatihan/{id}', [AcademicController::class, 'updatePelatihan'])->name('updatePelatihan');
        Route::delete('/pelatihan/{id}', [AcademicController::class, 'destroyPelatihan'])->name('destroyPelatihan');

        Route::get('/export-pelatihan', [AcademicController::class, 'exportPelatihan'])->name('exportPelatihan');
        Route::get('/export-mentor', [AcademicController::class, 'exportMentor'])->name('exportMentor');
        Route::post('/import-pelatihan', [AcademicController::class, 'importPelatihan'])->name('importPelatihan');
        Route::post('/import-mentor', [AcademicController::class, 'importMentor'])->name('importMentor');
    });

    // 7. FEEDBACK
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::put('/feedback/{id}/resolve', [FeedbackController::class, 'resolve'])->name('feedback.resolve');
});

require __DIR__.'/auth.php';