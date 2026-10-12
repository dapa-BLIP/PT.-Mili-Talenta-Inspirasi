<?php

use App\Models\User;

test('authenticated user can access all main dashboard pages', function () {
    $user = User::factory()->create();

    $routes = [
        '/dashboard',
        '/profile',
        '/peserta',
        '/peserta/create',
        '/manajemen-peserta/perusahaan',
        '/manajemen-peserta/nilai',
        '/jadwal',
        '/academic-master/pelatihan',
        '/academic-master/mentor',
        '/feedback',
    ];

    foreach ($routes as $route) {
        $response = $this->actingAs($user)->get($route);
        $response->assertStatus(200);
    }
});
