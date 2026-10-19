<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController; 
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\NilaiController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\MentorController;
use App\Http\Controllers\MateriController; 
use App\Http\Controllers\KategoriPelatihanController;
use App\Http\Controllers\PerusahaanController;

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

    // AJAX GRADING MATRIX & CHECKLIST PESERTA
    Route::get('/ajax/get-materi', [NilaiController::class, 'getMateriByJenis'])->name('nilai.getMateri');
    Route::get('/ajax/get-nilai-existing', [NilaiController::class, 'getNilaiExisting'])->name('nilai.getNilaiExisting');
    Route::get('/ajax/peserta-get-materi', [PesertaController::class, 'getMateriByJenis'])->name('peserta.getMateri');
    Route::get('/ajax/check-peserta-nilai', [PesertaController::class, 'checkNilaiPeserta'])->name('peserta.checkNilai');

    // 3. PESERTA (ROOT RESOURCE)
    Route::get('peserta/export', [PesertaController::class, 'export'])->name('peserta.export');
    Route::get('peserta/template', [PesertaController::class, 'template'])->name('peserta.template');
    Route::post('peserta/import', [PesertaController::class, 'import'])->name('peserta.import');
    Route::patch('peserta/{id}/status', [PesertaController::class, 'updateStatus'])->name('peserta.updateStatus');
    Route::resource('peserta', PesertaController::class);
    
    // 4. MANAJEMEN PESERTA (PERUSAHAAN & NILAI)
    Route::prefix('manajemen-peserta')->group(function () {
        Route::resource('perusahaan', PerusahaanController::class);

        Route::get('/nilai', [NilaiController::class, 'index'])->name('nilai.index');
        Route::post('/nilai/store', [NilaiController::class, 'store'])->name('nilai.store');
        Route::get('/nilai/export', [NilaiController::class, 'export'])->name('nilai.export');
        Route::post('/nilai/import', [NilaiController::class, 'import'])->name('nilai.import');
    });

    // 5. JADWAL
    Route::resource('jadwal', JadwalController::class);

    // 6. MASTER AKADEMIK (MODULAR CONTROLLERS)
    Route::prefix('academic-master')->name('academic.')->group(function () {
        Route::get('/', function() { return redirect()->route('academic.pelatihan.index'); })->name('index');

        // Submenu: Kelola Kelas Pelatihan (PelatihanController)
        Route::get('/pelatihan', [PelatihanController::class, 'index'])->name('pelatihan.index');
        Route::post('/pelatihan', [PelatihanController::class, 'store'])->name('storePelatihan');
        Route::put('/pelatihan/{id}', [PelatihanController::class, 'update'])->name('updatePelatihan');
        Route::delete('/pelatihan/{id}', [PelatihanController::class, 'destroy'])->name('destroyPelatihan');
        Route::get('/export-pelatihan', [PelatihanController::class, 'export'])->name('exportPelatihan');
        Route::post('/import-pelatihan', [PelatihanController::class, 'import'])->name('importPelatihan');
        Route::get('/template-pelatihan', [PelatihanController::class, 'template'])->name('templatePelatihan');

        // Submenu: Kelola Mentor / Trainer (MentorController)
        Route::get('/mentor', [MentorController::class, 'index'])->name('mentor.index');
        Route::post('/mentor', [MentorController::class, 'store'])->name('storeTrainer');
        Route::put('/mentor/{id}', [MentorController::class, 'update'])->name('updateTrainer'); 
        Route::delete('/mentor/{id}', [MentorController::class, 'destroy'])->name('destroyTrainer');
        Route::get('/export-mentor', [MentorController::class, 'export'])->name('exportMentor');
        Route::post('/import-mentor', [MentorController::class, 'import'])->name('importMentor');
        Route::get('/template-mentor', [MentorController::class, 'template'])->name('templateMentor');

        // Submenu: Bank Materi (MateriController)
        Route::get('/materi', [MateriController::class, 'index'])->name('materi.index');
        Route::post('/materi', [MateriController::class, 'store'])->name('storeMateri');
        Route::put('/materi/{id}', [MateriController::class, 'update'])->name('updateMateri');
        Route::delete('/materi/{id}', [MateriController::class, 'destroy'])->name('destroyMateri');
        // Alias route agar tidak bentrok jika view memakai format materi.*
        Route::post('/materi/save', [MateriController::class, 'store'])->name('materi.store');
        Route::delete('/materi/{id}/delete', [MateriController::class, 'destroy'])->name('materi.destroy');

        // Submenu: Modul / Program Pelatihan (KategoriPelatihanController)
        Route::post('/kategori-pelatihan', [KategoriPelatihanController::class, 'store'])->name('kategori.store');
        Route::delete('/kategori-pelatihan/{id}', [KategoriPelatihanController::class, 'destroy'])->name('kategori.destroy');
        // Alias route untuk kompatibilitas form view
        Route::post('/kategori-pelatihan/simpan', [KategoriPelatihanController::class, 'store'])->name('kategori-pelatihans.store');
        Route::delete('/kategori-pelatihan/{id}/hapus', [KategoriPelatihanController::class, 'destroy'])->name('kategori-pelatihans.destroy');
    });

    // 7. FEEDBACK (EVALUASI PASCA MATERI OFFLINE)
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
    Route::post('/feedback/update-form-url', [FeedbackController::class, 'updateFormUrl'])->name('feedback.updateFormUrl');
    Route::put('/feedback/{id}/resolve', [FeedbackController::class, 'resolve'])->name('feedback.resolve');
    Route::delete('/feedback/{id}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');
});

require __DIR__.'/auth.php';