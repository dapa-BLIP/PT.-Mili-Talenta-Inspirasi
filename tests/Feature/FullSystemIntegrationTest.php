<?php

use App\Models\User;
use App\Models\KategoriPelatihan;
use App\Models\Pelatihan;
use App\Models\Materi;
use App\Models\Trainer;
use App\Models\Perusahaan;
use App\Models\Peserta;
use App\Models\Jadwal;
use App\Models\Nilai;

test('all core pages load without errors and logout modal uses landscape layout with hover white border button', function () {
    $user = User::factory()->create();

    $routes = [
        route('dashboard'),
        route('peserta.index'),
        route('perusahaan.index'),
        route('academic.mentor.index'),
        route('academic.pelatihan.index'),
        route('nilai.index'),
        route('jadwal.index'),
        route('feedback.index'),
    ];

    foreach ($routes as $uri) {
        $response = $this->actingAs($user)->get($uri);
        $response->assertStatus(200);
    }

    $dashboardResponse = $this->actingAs($user)->get(route('dashboard'));
    $dashboardResponse->assertSee('border-transparent hover:border-white', false);
    $dashboardResponse->assertSee('max-width: 540px;', false);
});

test('ajax peserta get materi endpoint queries jenis_pelatihan without sql error', function () {
    $user = User::factory()->create();

    $trainer = Trainer::create([
        'nama_mentor' => 'Mentor Materi Test',
        'program_pelatihan' => 'PELATIHAN TEKNIS',
        'status' => 'AKTIF',
    ]);

    Materi::create([
        'nama_materi' => 'DASAR PEMROGRAMAN WEB',
        'jenis_pelatihan' => 'PELATIHAN TEKNIS',
        'trainer_id' => $trainer->id,
    ]);

    $response = $this->actingAs($user)->getJson('/ajax/peserta-get-materi?jenis=PELATIHAN+TEKNIS');

    $response->assertStatus(200);
    $response->assertJsonFragment([
        'nama_materi' => 'DASAR PEMROGRAMAN WEB',
        'jenis_pelatihan' => 'PELATIHAN TEKNIS',
    ]);
});

test('materi actively used in pelatihan class cannot be deleted', function () {
    $user = User::factory()->create();

    $trainer = Trainer::create([
        'nama_mentor' => 'Mentor Proteksi Materi',
        'program_pelatihan' => 'PELATIHAN TEKNIS',
        'status' => 'AKTIF',
    ]);

    $materiAktif = Materi::create([
        'nama_materi' => 'MATERI AKTIF DIGUNAKAN',
        'jenis_pelatihan' => 'PELATIHAN TEKNIS',
        'trainer_id' => $trainer->id,
    ]);

    $materiBebas = Materi::create([
        'nama_materi' => 'MATERI TIDAK DIGUNAKAN',
        'jenis_pelatihan' => 'PELATIHAN TEKNIS',
        'trainer_id' => $trainer->id,
    ]);

    Pelatihan::create([
        'nama_pelatihan' => 'KELAS AKTIF 1',
        'jenis_pelatihan' => 'PELATIHAN TEKNIS',
        'trainer_id' => $trainer->id,
        'materi_ids' => [$materiAktif->id],
    ]);

    // Coba hapus materi aktif -> harus ditolak
    $resBlocked = $this->actingAs($user)->delete(route('academic.materi.destroy', $materiAktif->id));
    $resBlocked->assertSessionHasErrors('error');
    $this->assertDatabaseHas('materis', ['id' => $materiAktif->id]);

    // Coba hapus materi bebas -> harus berhasil
    $resAllowed = $this->actingAs($user)->delete(route('academic.materi.destroy', $materiBebas->id));
    $resAllowed->assertSessionHasNoErrors();
    $this->assertDatabaseMissing('materis', ['id' => $materiBebas->id]);
});

test('pelatihan page has no tailwind class typos', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('academic.pelatihan.index'));

    $response->assertStatus(200);
    $response->assertDontSee('9git statuspx', false);
    $response->assertDontSee('overflow-y-aut ', false);
});

test('mentor import accepts custom user-created program pelatihan from kategori_pelatihans and handles dash placeholders', function () {
    KategoriPelatihan::create(['nama_kategori' => 'TESTING']);
    KategoriPelatihan::create(['nama_kategori' => 'CLOUD COMPUTING']);

    $import = new \App\Imports\MentorImport();

    $rows = collect([
        // Baris contoh template dengan '-' harus dilewati otomatis
        collect([
            'nama_lengkap_mentor'  => '-',
            'nik'                  => '-',
            'tempat_tanggal_lahir' => '-',
            'email'                => '-',
            'kontak'               => '-',
            'program_pelatihan'    => '-',
            'status'               => '-',
        ]),
        // Baris mentor baru dengan program custom 'TESTING' (case-insensitive)
        collect([
            'nama_lengkap_mentor'  => 'Dr. Hendra Wijaya',
            'nik'                  => '123456789012',
            'tempat_tanggal_lahir' => 'Jakarta, 10-05-1985',
            'email'                => 'hendra.wijaya@example.com',
            'kontak'               => '081234567890',
            'program_pelatihan'    => 'testing',
            'status'               => 'AKTIF',
        ]),
        // Baris mentor baru dengan program custom 'CLOUD COMPUTING' dan kolom opsional '-'
        collect([
            'nama_lengkap_mentor'  => 'Ir. Rina Kartika',
            'nik'                  => '-',
            'tempat_tanggal_lahir' => '-',
            'email'                => '-',
            'kontak'               => '+6281398765432',
            'program_pelatihan'    => 'CLOUD COMPUTING',
            'status'               => 'AKTIF',
        ]),
    ]);

    $import->collection($rows);

    $this->assertDatabaseHas('trainers', [
        'nama_mentor'       => 'DR. HENDRA WIJAYA',
        'program_pelatihan' => 'TESTING',
        'status'            => 'AKTIF',
    ]);

    $this->assertDatabaseHas('trainers', [
        'nama_mentor'       => 'IR. RINA KARTIKA',
        'program_pelatihan' => 'CLOUD COMPUTING',
        'nidn'              => null,
        'email'             => null,
        'status'            => 'AKTIF',
    ]);
});

test('mentor import rejects unregistered program pelatihan including unseeded defaults', function () {
    KategoriPelatihan::create(['nama_kategori' => 'TESTING']);

    $import = new \App\Imports\MentorImport();

    $rows = collect([
        collect([
            'nama_lengkap_mentor'  => 'Mentor Program Asing',
            'nik'                  => '123456789011',
            'tempat_tanggal_lahir' => 'Bandung, 01-01-1990',
            'email'                => 'asing@example.com',
            'kontak'               => '081234567890',
            'program_pelatihan'    => 'PROGRAM BELUM ADA',
            'status'               => 'AKTIF',
        ]),
    ]);

    expect(fn () => $import->collection($rows))
        ->toThrow(\Exception::class, 'belum terdaftar di database Pelatihan');
});

test('mentor kontak format is identical (+62 without hyphens) between manual button input and excel import', function () {
    $user = User::factory()->create();
    KategoriPelatihan::create(['nama_kategori' => 'TESTING']);

    // 1. Input via tombol Tambah Mentor (storeTrainer)
    $this->actingAs($user)->post(route('academic.storeTrainer'), [
        'nama_mentor'       => 'Mentor Tombol Manual',
        'nidn'              => '111122223333',
        'tempat_lahir'      => 'Bekasi',
        'tanggal_lahir'     => '1990-05-10',
        'email'             => 'manual@example.com',
        'kontak'            => '0857-7668-5706',
        'program_pelatihan' => 'TESTING',
    ])->assertRedirect(route('academic.mentor.index'));

    // 2. Input via Import Excel
    $import = new \App\Imports\MentorImport();
    $import->collection(collect([
        collect([
            'nama_lengkap_mentor'  => 'Mentor Import Excel',
            'nik'                  => '444455556666',
            'tempat_tanggal_lahir' => 'Bekasi, 10-05-1990',
            'email'                => 'import@example.com',
            'kontak'               => '085776685707',
            'program_pelatihan'    => 'TESTING',
            'status'               => 'AKTIF',
        ]),
    ]));

    $manualTrainer = Trainer::where('email', 'manual@example.com')->firstOrFail();
    $importTrainer = Trainer::where('email', 'import@example.com')->firstOrFail();

    expect($manualTrainer->kontak)->toBe('+6285776685706');
    expect($importTrainer->kontak)->toBe('+6285776685707');
});

test('re-importing mentor with previously incomplete data fills only missing fields without duplicate nik nidn error', function () {
    KategoriPelatihan::create(['nama_kategori' => 'TESTING']);

    $import = new \App\Imports\MentorImport();

    // Import pertama: data belum lengkap (TTL dan Email masih '-')
    $import->collection(collect([
        collect([
            'nama_lengkap_mentor'  => 'Dr. Budi Santoso',
            'nik'                  => '123456789012',
            'tempat_tanggal_lahir' => '-',
            'email'                => '-',
            'kontak'               => '081234567890',
            'program_pelatihan'    => 'TESTING',
            'status'               => 'AKTIF',
        ]),
    ]));

    $mentorBefore = Trainer::where('nidn', '123456789012')->firstOrFail();
    expect($mentorBefore->ttl)->toBeNull();
    expect($mentorBefore->email)->toBeNull();
    expect($mentorBefore->kontak)->toBe('+6281234567890');

    // Import kedua: data lengkap dengan NIK/NIDN yang sama -> harus melengkapi data yang belum lengkap saja tanpa error
    $import->collection(collect([
        collect([
            'nama_lengkap_mentor'  => 'Dr. Budi Santoso',
            'nik'                  => '123456789012',
            'tempat_tanggal_lahir' => 'Bandung, 10-05-1985',
            'email'                => 'budi.santoso@example.com',
            'kontak'               => '081234567890',
            'program_pelatihan'    => 'TESTING',
            'status'               => 'AKTIF',
        ]),
    ]));

    $mentorAfter = Trainer::where('nidn', '123456789012')->firstOrFail();
    expect($mentorAfter->id)->toBe($mentorBefore->id);
    expect($mentorAfter->ttl)->toBe('BANDUNG, 10-05-1985');
    expect($mentorAfter->email)->toBe('budi.santoso@example.com');
    expect($mentorAfter->kontak)->toBe('+6281234567890');
});

test('new mentor with same name is inserted as separate record without overwriting existing mentor as long as nik and phone differ', function () {
    KategoriPelatihan::create(['nama_kategori' => 'TESTING']);

    $existing = Trainer::create([
        'nama_mentor'       => 'ANDI PRATAMA',
        'nidn'              => '111122223333',
        'ttl'               => 'JAKARTA, 01-01-1985',
        'email'             => 'andi1@example.com',
        'kontak'            => '+628111111111',
        'program_pelatihan' => 'TESTING',
        'status'            => 'AKTIF',
    ]);

    // Import mentor baru dengan nama yang sama tetapi NIK/NIDN, no telpon, TTL, dan email berbeda
    $import = new \App\Imports\MentorImport();
    $import->collection(collect([
        collect([
            'nama_lengkap_mentor'  => 'ANDI PRATAMA',
            'nik'                  => '999988887777',
            'tempat_tanggal_lahir' => 'SURABAYA, 02-02-1992',
            'email'                => 'andi2@example.com',
            'kontak'               => '082222222222',
            'program_pelatihan'    => 'TESTING',
            'status'               => 'AKTIF',
        ]),
    ]));

    // Pastikan data mentor pertama TIDAK tertimpa
    $existingFresh = Trainer::findOrFail($existing->id);
    expect($existingFresh->nidn)->toBe('111122223333');
    expect($existingFresh->kontak)->toBe('+628111111111');
    expect($existingFresh->email)->toBe('andi1@example.com');
    expect($existingFresh->ttl)->toBe('JAKARTA, 01-01-1985');

    // Pastikan mentor kedua masuk sebagai record baru
    $newMentor = Trainer::where('nidn', '999988887777')->firstOrFail();
    expect($newMentor->id)->not->toBe($existing->id);
    expect($newMentor->nama_mentor)->toBe('ANDI PRATAMA');
    expect($newMentor->kontak)->toBe('+6282222222222');
    expect($newMentor->email)->toBe('andi2@example.com');
});

test('duplicate nik nidn on mentor triggers warning on manual input and excel import', function () {
    $user = User::factory()->create();
    KategoriPelatihan::create(['nama_kategori' => 'TESTING']);

    Trainer::create([
        'nama_mentor'       => 'MENTOR PERTAMA',
        'nidn'              => '1234567890',
        'kontak'            => '+6281234567890',
        'program_pelatihan' => 'TESTING',
        'status'            => 'AKTIF',
    ]);

    // 1. Duplikat via manual form
    $res = $this->actingAs($user)->post(route('academic.storeTrainer'), [
        'nama_mentor'       => 'MENTOR KEDUA',
        'nidn'              => '1234567890',
        'tempat_lahir'      => 'Jakarta',
        'tanggal_lahir'     => '1992-01-01',
        'email'             => 'kedua@example.com',
        'kontak'            => '081299998888',
        'program_pelatihan' => 'TESTING',
    ]);
    $res->assertSessionHasErrors('nidn');
    expect(session('errors')->first('nidn'))->toContain('Peringatan: NIK / NIDN');

    // 2. Duplikat via Import Excel
    $import = new \App\Imports\MentorImport();
    $rows = collect([
        collect([
            'nama_lengkap_mentor'  => 'MENTOR KETIGA',
            'nik'                  => '1234567890',
            'tempat_tanggal_lahir' => 'Jakarta, 01-01-1992',
            'email'                => 'ketiga@example.com',
            'kontak'               => '081299997777',
            'program_pelatihan'    => 'TESTING',
            'status'               => 'AKTIF',
        ]),
    ]);

    expect(fn () => $import->collection($rows))
        ->toThrow(\Exception::class, 'sudah terdaftar atas nama mentor');
});

test('pelatihan class creation validates mentor matches selected program pelatihan', function () {
    $user = User::factory()->create();
    KategoriPelatihan::create(['nama_kategori' => 'TESTING']);
    KategoriPelatihan::create(['nama_kategori' => 'KELISTRIKAN']);

    $mentorTesting = Trainer::create([
        'nama_mentor'       => 'MENTOR TESTING',
        'program_pelatihan' => 'TESTING',
        'status'            => 'AKTIF',
    ]);

    $mentorListrik = Trainer::create([
        'nama_mentor'       => 'MENTOR LISTRIK',
        'program_pelatihan' => 'KELISTRIKAN',
        'status'            => 'AKTIF',
    ]);

    // Gagal jika memilih mentor KELISTRIKAN untuk program TESTING
    $resFail = $this->actingAs($user)->post(route('academic.storePelatihan'), [
        'nama_pelatihan'  => 'TESTING',
        'jenis_pelatihan' => 'TESTING',
        'trainer_id'      => $mentorListrik->id,
        'raw_komponen'    => 'TUGAS, UJIAN',
    ]);
    $resFail->assertSessionHasErrors('trainer_id');

    // Berhasil jika memilih mentor TESTING untuk program TESTING
    $resOk = $this->actingAs($user)->post(route('academic.storePelatihan'), [
        'nama_pelatihan'  => 'TESTING',
        'jenis_pelatihan' => 'TESTING',
        'trainer_id'      => $mentorTesting->id,
        'raw_komponen'    => 'TUGAS, UJIAN',
    ]);
    $resOk->assertSessionHasNoErrors();
    $this->assertDatabaseHas('pelatihans', [
        'jenis_pelatihan' => 'TESTING',
        'trainer_id'      => $mentorTesting->id,
    ]);
});

test('peserta export and template include NO column for neat numbering while omitting ID PESERTA and peserta import strictly validates perusahaan and program pelatihan', function () {
    $export = new \App\Exports\PesertaExport();
    $template = new \App\Imports\Templates\PesertaImportTemplate();

    expect($export->headings()[0])->toBe('NO.');
    expect($export->headings())->not->toContain('ID PESERTA');
    expect($template->headings()[0])->toBe('NO.');
    expect($template->headings())->not->toContain('ID PESERTA');

    KategoriPelatihan::create(['nama_kategori' => 'TESTING']);
    $pt = Perusahaan::create([
        'nama_perusahaan' => 'PT MAJU BERSAMA',
        'status'          => 'AKTIF',
    ]);

    $import = new \App\Imports\PesertaImport();

    // 1. Gagal jika Perusahaan belum terdaftar
    expect(fn () => $import->collection(collect([
        collect([
            'no'                   => '01',
            'nama_lengkap'         => 'Budi Peserta',
            'gender'               => 'PRIA',
            'tempat_tanggal_lahir' => 'Bekasi, 01-01-2000',
            'email'                => 'budi@example.com',
            'no_handphone'         => '081234567890',
            'tempat_kerja'         => 'PT BELUM TERDAFTAR',
            'sektor_pelatihan'     => 'TESTING',
            'status_verifikasi'    => 'AKTIF',
        ]),
    ])))->toThrow(\Exception::class, 'Perusahaan [PT BELUM TERDAFTAR] belum terdaftar di website');

    // 2. Gagal jika Program Pelatihan belum terdaftar
    expect(fn () => $import->collection(collect([
        collect([
            'no'                   => '01',
            'nama_lengkap'         => 'Budi Peserta',
            'gender'               => 'PRIA',
            'tempat_tanggal_lahir' => 'Bekasi, 01-01-2000',
            'email'                => 'budi@example.com',
            'no_handphone'         => '081234567890',
            'tempat_kerja'         => 'PT MAJU BERSAMA',
            'sektor_pelatihan'     => 'PROGRAM TIDAK ADA',
            'status_verifikasi'    => 'AKTIF',
        ]),
    ])))->toThrow(\Exception::class, 'Program Pelatihan [PROGRAM TIDAK ADA] belum terdaftar di website');

    // 3. Berhasil jika Perusahaan dan Program Pelatihan sudah terdaftar, PK otomatis
    $import->collection(collect([
        collect([
            'no'                   => '01',
            'nama_lengkap'         => 'Budi Peserta',
            'gender'               => 'PRIA',
            'tempat_tanggal_lahir' => 'Bekasi, 01-01-2000',
            'email'                => 'budi@example.com',
            'no_handphone'         => '081234567890',
            'tempat_kerja'         => 'PT MAJU BERSAMA',
            'sektor_pelatihan'     => 'TESTING',
            'status_verifikasi'    => 'AKTIF',
        ]),
    ]));

    $peserta = Peserta::where('email', 'budi@example.com')->firstOrFail();
    expect($peserta->id)->toBeGreaterThan(0);
    expect($peserta->perusahaan_id)->toBe($pt->id);
    expect($peserta->tempat_kerja)->toBe('PT MAJU BERSAMA');
    expect($peserta->pelatihan)->toBe('TESTING');
    expect($peserta->no_hp)->toBe('+6281234567890');
});



