<?php

use App\Models\User;
use App\Models\Feedback;

test('authenticated user can access feedback index page', function () {
    $user = User::factory()->create();

    $responseFeedback = $this->actingAs($user)->get('/feedback');
    $responseFeedback->assertStatus(200);
    $responseFeedback->assertSee('System Feedback');
    $responseFeedback->assertSee('Open Google Form');
});

test('user can resolve and delete feedback', function () {
    $user = User::factory()->create();

    $feedback = Feedback::create([
        'nama_peserta' => 'Dwi Prasetyo',
        'kegiatan' => 'Praktikum Web Offline',
        'kategori' => 'Berat',
        'pesan' => 'Proyektor di ruangan sempat mati dan koneksi wifi lambat.',
        'status' => 'pending',
    ]);

    // 1. Resolve
    $resolveResponse = $this->actingAs($user)->put(route('feedback.resolve', $feedback->id), [
        'solusi' => 'Kabel HDMI proyektor telah diganti baru dan router wifi sudah di-restart.',
    ]);

    $resolveResponse->assertSessionHas('success');
    $feedback->refresh();
    $this->assertEquals('resolved', $feedback->status);
    $this->assertEquals('Kabel HDMI proyektor telah diganti baru dan router wifi sudah di-restart.', $feedback->solusi);

    // 2. Delete
    $deleteResponse = $this->actingAs($user)->delete(route('feedback.destroy', $feedback->id));
    $deleteResponse->assertRedirect(route('feedback.index'));
    $this->assertDatabaseMissing('feedbacks', ['id' => $feedback->id]);
});

test('feedback category filter works seamlessly case-insensitively', function () {
    $user = User::factory()->create();

    Feedback::create(['kategori' => 'Berat', 'pesan' => 'Error 500 saat simpan data', 'status' => 'pending']);
    Feedback::create(['kategori' => 'Umum', 'pesan' => 'Usulan warna tema dark mode', 'status' => 'pending']);

    $responseBerat = $this->actingAs($user)->get('/feedback?category=berat');
    $responseBerat->assertStatus(200);
    $responseBerat->assertSee('Error 500 saat simpan data');
    $responseBerat->assertDontSee('Usulan warna tema dark mode');

    $responseUmum = $this->actingAs($user)->get('/feedback?category=Umum');
    $responseUmum->assertStatus(200);
    $responseUmum->assertSee('Usulan warna tema dark mode');
    $responseUmum->assertDontSee('Error 500 saat simpan data');
});

test('webhook can store feedback with flexible payload keys, participant, and activity', function () {
    $response = $this->postJson('/feedback/webhook', [
        'nama' => 'Budi Sudarsono',
        'materi' => 'Workshop Git Offline',
        'category' => 'tidak puas',
        'message' => 'Penjelasan merge conflict terlalu cepat, butuh modul tambahan.',
    ]);

    $response->assertStatus(200);
    $response->assertJson(['status' => 'success']);

    $this->assertDatabaseHas('feedbacks', [
        'nama_peserta' => 'Budi Sudarsono',
        'kegiatan' => 'Workshop Git Offline',
        'kategori' => 'Berat',
        'pesan' => 'Penjelasan merge conflict terlalu cepat, butuh modul tambahan.',
        'status' => 'pending',
    ]);
});

