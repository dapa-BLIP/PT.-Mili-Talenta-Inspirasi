<?php

use App\Models\User;
use App\Models\Trainer;
use App\Models\Jadwal;

test('authenticated user can access jadwal index page', function () {
    $user = User::factory()->create();

    $responseJadwal = $this->actingAs($user)->get('/jadwal');
    $responseJadwal->assertStatus(200);
});

test('user can store, update, and delete jadwal', function () {
    $user = User::factory()->create();
    $trainer = Trainer::create([
        'nama_mentor' => 'Master Mentor Jadwal',
        'program_pelatihan' => 'PROGRAM WEB DEV',
        'status' => 'AKTIF',
    ]);

    // Store
    $storeResponse = $this->actingAs($user)->post(route('jadwal.store'), [
        'judul_kegiatan' => 'Workshop Laravel 12',
        'trainer_id' => $trainer->id,
        'tanggal_mulai' => '2026-10-12',
        'tanggal_selesai' => '2026-10-14',
        'jam_mulai' => '08:30',
        'jam_selesai' => '12:00',
        'jenis_pelaksanaan' => 'offline',
        'description' => 'Sesi hands-on coding framework Laravel.',
    ]);

    $storeResponse->assertRedirect(route('jadwal.index'));
    $this->assertDatabaseHas('jadwals', [
        'judul_kegiatan' => 'Workshop Laravel 12',
        'trainer_id' => $trainer->id,
    ]);

    $jadwal = Jadwal::where('judul_kegiatan', 'Workshop Laravel 12')->first();

    // Update
    $updateResponse = $this->actingAs($user)->put(route('jadwal.update', $jadwal->id), [
        'judul_kegiatan' => 'Workshop Laravel 12 – Advanced',
        'trainer_id' => $trainer->id,
        'tanggal_mulai' => '2026-10-12',
        'tanggal_selesai' => '2026-10-14',
        'jam_mulai' => '09:00',
        'jam_selesai' => '13:00',
        'jenis_pelaksanaan' => 'online',
        'description' => 'Updated description.',
    ]);

    $updateResponse->assertRedirect(route('jadwal.index'));
    $this->assertDatabaseHas('jadwals', [
        'id' => $jadwal->id,
        'judul_kegiatan' => 'Workshop Laravel 12 – Advanced',
        'jenis_pelaksanaan' => 'online',
    ]);

    // Destroy
    $deleteResponse = $this->actingAs($user)->delete(route('jadwal.destroy', $jadwal->id));
    $deleteResponse->assertRedirect(route('jadwal.index'));
    $this->assertDatabaseMissing('jadwals', ['id' => $jadwal->id]);
});

test('jadwal date range query correctly finds overlapping ongoing schedules', function () {
    $user = User::factory()->create();
    $trainer = Trainer::create([
        'nama_mentor' => 'Trainer Timeline',
        'program_pelatihan' => 'PROGRAM WEB DEV',
        'status' => 'AKTIF',
    ]);

    $jadwal = Jadwal::create([
        'judul_kegiatan' => 'Bootcamp Fullstack 2 Minggu',
        'trainer_id' => $trainer->id,
        'tanggal_mulai' => '2026-10-05',
        'tanggal_selesai' => '2026-10-18',
        'jam_mulai' => '09:00',
        'jam_selesai' => '16:00',
        'jenis_pelaksanaan' => 'offline',
        'status' => 'on-going',
    ]);

    // Query in the middle of the bootcamp
    $response = $this->actingAs($user)->get('/jadwal?start_date=2026-10-10&end_date=2026-10-12');
    $response->assertStatus(200);
    $response->assertSee('Bootcamp Fullstack 2 Minggu');
});

