<?php

use App\Models\User;
use App\Models\Pelatihan;
use App\Models\Trainer;
use App\Models\Peserta;
use App\Models\Nilai;

test('authenticated user can view nilai matrix index page with 3-step filter', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/manajemen-peserta/nilai');

    $response->assertStatus(200);
    $response->assertSee('Kelola Nilai Kolektif');
    $response->assertSee('Program Pelatihan');
    $response->assertSee('Mentor Pengampu');
    $response->assertSee('Kelas Pelatihan');
    $response->assertDontSee('Kategori Spektrum');
});

test('user can fetch existing nilai via ajax', function () {
    $user = User::factory()->create();

    $trainer = Trainer::create([
        'nama_mentor' => 'Trainer Test',
        'program_pelatihan' => 'Web Dev',
        'status' => 'AKTIF',
    ]);

    $pelatihan = Pelatihan::create([
        'nama_pelatihan' => 'TEST KELAS PELATIHAN',
        'jenis_pelatihan' => 'PELATIHAN TEKNIS',
        'trainer_id' => $trainer->id,
        'komponen_nilai' => ['tugas', 'uts', 'uas'],
    ]);

    $peserta = Peserta::create([
        'nama_lengkap' => 'Budi Santoso',
        'gender' => 'pria',
        'email' => 'budi.santoso@example.com',
        'pelatihan' => 'TEST KELAS PELATIHAN',
        'status' => 'AKTIF',
    ]);

    Nilai::create([
        'peserta_id' => $peserta->id,
        'pelatihan_id' => $pelatihan->id,
        'trainer_id' => $trainer->id,
        'scores' => ['tugas' => 90, 'uts' => 85, 'uas' => 92],
        'catatan' => 'Sangat baik',
    ]);

    $response = $this->actingAs($user)->getJson('/ajax/get-nilai-existing?pelatihan_id=' . $pelatihan->id);

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'peserta_id' => $peserta->id,
        'catatan' => 'Sangat baik',
    ]);
});

test('user can store matrix scores via ajax', function () {
    $user = User::factory()->create();

    $trainer = Trainer::create([
        'nama_mentor' => 'Trainer Store Test',
        'program_pelatihan' => 'Data Science',
        'status' => 'AKTIF',
    ]);

    $pelatihan = Pelatihan::create([
        'nama_pelatihan' => 'KELAS DATA SCIENCE',
        'jenis_pelatihan' => 'PELATIHAN TEKNIS',
        'trainer_id' => $trainer->id,
        'komponen_nilai' => ['praktik', 'teori'],
    ]);

    $peserta = Peserta::create([
        'nama_lengkap' => 'Siti Rahma',
        'gender' => 'wanita',
        'email' => 'siti.rahma@example.com',
        'pelatihan' => 'KELAS DATA SCIENCE',
        'status' => 'AKTIF',
    ]);

    $payload = [
        'pelatihan_id' => $pelatihan->id,
        'trainer_id' => $trainer->id,
        'nilai' => [
            [
                'peserta_id' => $peserta->id,
                'scores' => [
                    'praktik' => 88,
                    'teori' => 94,
                ],
                'catatan' => 'Lulus memuaskan',
            ],
        ],
    ];

    $response = $this->actingAs($user)->postJson('/manajemen-peserta/nilai/store', $payload);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    $this->assertDatabaseHas('nilais', [
        'peserta_id' => $peserta->id,
        'pelatihan_id' => $pelatihan->id,
        'catatan' => 'Lulus memuaskan',
    ]);
});

test('user can download export excel for selected pelatihan', function () {
    $user = User::factory()->create();

    $trainer = Trainer::create([
        'nama_mentor' => 'Trainer Export',
        'program_pelatihan' => 'Cybersecurity',
        'status' => 'AKTIF',
    ]);

    $pelatihan = Pelatihan::create([
        'nama_pelatihan' => 'KELAS CYBERSECURITY',
        'jenis_pelatihan' => 'PELATIHAN TEKNIS',
        'trainer_id' => $trainer->id,
        'komponen_nilai' => ['tugas', 'ujian'],
    ]);

    $response = $this->actingAs($user)->get('/manajemen-peserta/nilai/export?pelatihan_id=' . $pelatihan->id);

    $response->assertStatus(200);
    $response->assertHeader('content-disposition');
});

test('user can download export excel without pelatihan_id parameter defaulting gracefully', function () {
    $user = User::factory()->create();

    $trainer = Trainer::create([
        'nama_mentor' => 'Trainer Default Export',
        'program_pelatihan' => 'Cybersecurity',
        'status' => 'AKTIF',
    ]);

    $pelatihan = Pelatihan::create([
        'nama_pelatihan' => 'KELAS DEFAULT EXPORT',
        'jenis_pelatihan' => 'PELATIHAN TEKNIS',
        'trainer_id' => $trainer->id,
        'komponen_nilai' => ['tugas'],
    ]);

    $response = $this->actingAs($user)->get('/manajemen-peserta/nilai/export');

    $response->assertStatus(200);
    $response->assertHeader('content-disposition');
});

