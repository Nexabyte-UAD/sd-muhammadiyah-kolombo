<?php

namespace Tests\Feature;

use App\Models\GuruStaff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_guaranteed_admin_resource_routes_do_not_include_unimplemented_show_actions(): void
    {
        foreach (['berita', 'guru-staff', 'prestasi', 'ekstrakurikuler', 'users', 'siswa'] as $resource) {
            $this->assertFalse(app('router')->getRoutes()->hasNamedRoute("admin.{$resource}.show"));
        }
    }

    public function test_guru_staff_edit_route_binds_the_model(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);
        $guru = GuruStaff::create([
            'nama' => 'Guru Test',
            'jabatan' => 'Guru',
            'tipe' => 'guru',
        ]);

        $this->actingAs($user)
            ->get(route('admin.guru-staff.edit', $guru))
            ->assertOk()
            ->assertSee('Guru Test');
    }

    public function test_berita_routes_use_the_controller_parameter_name(): void
    {
        $route = app('router')->getRoutes()->getByName('admin.berita.edit');

        $this->assertSame(['berita'], $route->parameterNames());
    }
}
