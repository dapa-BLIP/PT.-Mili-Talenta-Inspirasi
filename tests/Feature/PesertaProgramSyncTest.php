<?php

use App\Models\User;
use App\Models\KategoriPelatihan;
use App\Models\Pelatihan;
use App\Models\Trainer;
use App\Models\Peserta;

test('peserta create modal dropdown only renders program pelatihan without class optgroups', function () {
    $user = User::factory()->create();

    KategoriPelatihan::create(['nama_kategori' => 'PROGRAM WEB DEV']);
    $trainer = Trainer::create([
        'nama_mentor' => 'Trainer Sync',
        'program_pelatihan' => 'PROGRAM WEB DEV',
        'status' => 'AKTIF',
    ]);
    Pelatihan::create([
        'nama_pelatihan' => 'KELAS LARAVEL ADVANCED',
        'jenis_pelatihan' => 'PROGRAM WEB DEV',
        'trainer_id' => $trainer->id,
    ]);

    $response = $this->actingAs($user)->get('/peserta');

    $response->assertStatus(200);
    $response->assertSee('PROGRAM WEB DEV');
    $response->assertDontSee('<optgroup', false);
});

test('nilai page has export excel button and warning modal for cascading filter', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/manajemen-peserta/nilai');

    $response->assertStatus(200);
    $response->assertSee('Export Excel');
    $response->assertDontSee('btnExportNilai');
    $response->assertSee('matrix-warning-modal');
    $response->assertDontSee('modalImportNilai');
    $response->assertDontSee('IMPORT MATRIX NILAI KELAS');
});

test('peserta detail modal renders 3d flip landscape card with all participant fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/peserta');

    $response->assertStatus(200);
    $response->assertSee('modalDetailPeserta');
    $response->assertSee('backdrop-blur-sm');
    $response->assertSee('cardFlipperPeserta');
    $response->assertSee('detail_peserta_gender');
    $response->assertSee('detail_peserta_wa_link');
    $response->assertSee('detail_peserta_email');
    $response->assertSee('detail_peserta_ttl');
    $response->assertSee('detail_peserta_kerja');
    $response->assertSee('PARTICIPANT CARD');
    $response->assertSee('PT MILI TALENTA INSPIRASI');
});

test('user cannot change participant program if participant already has grades in current program', function () {
    $user = User::factory()->create();

    $trainer = Trainer::create([
        'nama_mentor' => 'Trainer Grade Test',
        'program_pelatihan' => 'PROGRAM WEB DEV',
        'status' => 'AKTIF',
    ]);

    $pelatihanLama = Pelatihan::create([
        'nama_pelatihan' => 'KELAS WEB BASIC',
        'jenis_pelatihan' => 'PROGRAM WEB DEV',
        'trainer_id' => $trainer->id,
        'komponen_nilai' => ['tugas', 'uts'],
    ]);

    $pelatihanBaru = Pelatihan::create([
        'nama_pelatihan' => 'KELAS MOBILE FLUTTER',
        'jenis_pelatihan' => 'PROGRAM MOBILE DEV',
        'trainer_id' => $trainer->id,
        'komponen_nilai' => ['tugas', 'uts'],
    ]);

    $peserta = Peserta::create([
        'nama_lengkap' => 'Ahmad Fikri',
        'gender' => 'pria',
        'email' => 'ahmad.fikri@example.com',
        'pelatihan' => 'PROGRAM WEB DEV',
        'status' => 'AKTIF',
    ]);

    \App\Models\Nilai::create([
        'peserta_id' => $peserta->id,
        'pelatihan_id' => $pelatihanLama->id,
        'trainer_id' => $trainer->id,
        'scores' => ['tugas' => 88, 'uts' => 90],
        'catatan' => 'Lulus baik',
    ]);

    $response = $this->actingAs($user)->put(route('peserta.update', $peserta->id), [
        'nama_lengkap' => 'Ahmad Fikri',
        'gender' => 'pria',
        'email' => 'ahmad.fikri@example.com',
        'pelatihan' => 'PROGRAM MOBILE DEV',
    ]);

    $response->assertSessionHasErrors('pelatihan');
    $this->assertStringContainsString('Peserta memiliki nilai di program "PROGRAM WEB DEV"', session('errors')->first('pelatihan'));

    $peserta->refresh();
    $this->assertEquals('PROGRAM WEB DEV', $peserta->pelatihan);
});

test('user can change participant program if participant has no grades in current program', function () {
    $user = User::factory()->create();

    $peserta = Peserta::create([
        'nama_lengkap' => 'Siti Aminah',
        'gender' => 'wanita',
        'email' => 'siti.aminah@example.com',
        'pelatihan' => 'PROGRAM WEB DEV',
        'status' => 'AKTIF',
    ]);

    $response = $this->actingAs($user)->put(route('peserta.update', $peserta->id), [
        'nama_lengkap' => 'Siti Aminah',
        'gender' => 'wanita',
        'email' => 'siti.aminah@example.com',
        'pelatihan' => 'PROGRAM MOBILE DEV',
    ]);

    $response->assertSessionHasNoErrors();
    $peserta->refresh();
    $this->assertEquals('PROGRAM MOBILE DEV', $peserta->pelatihan);
});

test('peserta edit modal renders program change warning alert component and warning modal', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/peserta');

    $response->assertStatus(200);
    $response->assertSee('edit_pelatihan_warning');
    $response->assertSee('edit_pelatihan_warning_prog');
    $response->assertSee('modalWarningProgramPeserta');
    $response->assertSee('modal_warning_program_name');
    $response->assertSee('closeModalWarningProgram');
});
