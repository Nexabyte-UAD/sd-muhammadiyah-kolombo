<?php

namespace Tests\Feature;

use App\Models\GuruStaff;
use App\Models\Kelas;
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

    public function test_guru_staff_form_persists_bidang_tugas_and_preserves_nip_as_text(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'pegawai@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);

        $this->actingAs($user)->post(route('admin.guru-staff.store'), [
            'tipe' => 'guru',
            'nama' => 'Guru Matematika',
            'jenis_kelamin' => 'laki_laki',
            'jabatan' => 'Guru Kelas',
            'bidang_tugas' => 'Matematika',
            'nip' => '001234567890123456',
            'status_kepegawaian' => 'PNS',
            'pendidikan_terakhir' => 'S1',
            'agama' => 'Islam',
        ])->assertRedirect(route('admin.guru-staff.index', ['tipe' => 'guru']));

        $this->assertDatabaseHas('guru_staffs', [
            'tipe' => 'guru',
            'nama' => 'Guru Matematika',
            'bidang_tugas' => 'Matematika',
            'nip' => '001234567890123456',
            'jenis_kelamin' => 'laki_laki',
            'status_kepegawaian' => 'PNS',
            'pendidikan_terakhir' => 'S1',
            'agama' => 'Islam',
        ]);
    }

    public function test_class_teacher_can_be_selected_from_guru_data(): void
    {
        $user = User::create([
            'name' => 'Admin Kelas',
            'email' => 'kelas@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);
        $guru = GuruStaff::create([
            'tipe' => 'guru',
            'nama' => 'Wali Kelas Pilihan',
            'jabatan' => 'Guru Kelas',
        ]);

        $this->actingAs($user)
            ->post(route('admin.kelas.store'), [
                'tingkat' => 'Kelas 3A',
                'jurusan' => 'Tahfiz',
                'wali_kelas_id' => $guru->id,
            ])
            ->assertRedirect(route('admin.kelas.index'));

        $this->assertSame(
            $guru->id,
            Kelas::where('tingkat', 'Kelas 3A')->value('wali_kelas_id')
        );
        $this->assertSame('Tahfiz', Kelas::where('tingkat', 'Kelas 3A')->value('jurusan'));
    }

    public function test_class_can_be_created_without_an_automatic_major(): void
    {
        $user = User::create([
            'name' => 'Admin Kelas Kosong',
            'email' => 'kelas-kosong@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);

        $this->actingAs($user)
            ->post(route('admin.kelas.store'), [
                'tingkat' => 'Kelas Percobaan',
                'jurusan' => '',
                'wali_kelas_id' => '',
            ])
            ->assertRedirect(route('admin.kelas.index'));

        $this->assertDatabaseHas('kelas', [
            'tingkat' => 'Kelas Percobaan',
            'jurusan' => null,
        ]);
    }

    public function test_student_form_uses_classes_created_by_admin(): void
    {
        $user = User::create([
            'name' => 'Admin Siswa',
            'email' => 'siswa-dinamis@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);
        Kelas::create(['tingkat' => 'Kelas Bintang']);

        $this->actingAs($user)
            ->get(route('admin.siswa.create'))
            ->assertOk()
            ->assertSee('Kelas Bintang');

        $this->actingAs($user)
            ->post(route('admin.siswa.store'), [
                'nama' => 'Siswa Dinamis',
                'jenis_kelamin' => 'L',
                'kelas' => 'Kelas Bintang',
                'status' => 'aktif',
                'tahun_masuk' => 2026,
            ])
            ->assertRedirect(route('admin.siswa.index', ['status' => 'aktif']));

        $this->assertDatabaseHas('siswas', [
            'nama' => 'Siswa Dinamis',
            'kelas' => 'Kelas Bintang',
        ]);
    }

    public function test_guru_staff_rejects_values_outside_the_allowed_biodata_enums(): void
    {
        $user = User::create([
            'name' => 'Admin Validasi',
            'email' => 'validasi@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);

        $this->actingAs($user)
            ->from(route('admin.guru-staff.create', ['tipe' => 'guru']))
            ->post(route('admin.guru-staff.store'), [
                'tipe' => 'guru',
                'nama' => 'Data Tidak Valid',
                'jenis_kelamin' => 'invalid',
                'jabatan' => 'Guru',
                'status_kepegawaian' => 'Tetap',
                'pendidikan_terakhir' => 'D4',
                'agama' => 'Lainnya',
            ])
            ->assertSessionHasErrors([
                'jenis_kelamin',
                'status_kepegawaian',
                'pendidikan_terakhir',
                'agama',
            ]);

        $this->assertDatabaseMissing('guru_staffs', ['nama' => 'Data Tidak Valid']);
    }
}
