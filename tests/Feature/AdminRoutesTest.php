<?php

namespace Tests\Feature;

use App\Models\Berita;
use App\Models\GuruStaff;
use App\Models\Kelas;
use App\Models\Ekstrakurikuler;
use App\Models\RiwayatAkademik;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_uses_the_custom_admin_design(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Panel Administrasi')
            ->assertSee('Masuk ke Panel Admin')
            ->assertDontSee('adminlte', false);
    }

    public function test_admin_dashboard_uses_the_custom_panel_layout(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Admin Sekolah')
            ->assertSee('Ringkasan hari ini')
            ->assertSee('Akses Cepat')
            ->assertSee(asset('css/admin-panel.css'), false);
    }

    public function test_admin_can_update_own_account_without_changing_role(): void
    {
        $user = User::create([
            'name' => 'Admin Lama',
            'email' => 'admin-lama@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);

        $this->actingAs($user)
            ->get(route('admin.account.edit'))
            ->assertOk()
            ->assertSee('Akun Admin')
            ->assertDontSee('name="role"', false);

        $this->put(route('admin.account.update'), [
            'name' => 'Admin Baru',
            'email' => 'admin-baru@example.test',
            'password' => '',
            'password_confirmation' => '',
        ])->assertRedirect(route('admin.account.edit'));

        $user->refresh();

        $this->assertSame('Admin Baru', $user->name);
        $this->assertSame('admin-baru@example.test', $user->email);
        $this->assertSame('Admin', $user->role);
    }

    public function test_guaranteed_admin_resource_routes_do_not_include_unimplemented_show_actions(): void
    {
        foreach (['berita', 'guru-staff', 'prestasi', 'ekstrakurikuler', 'users', 'siswa'] as $resource) {
            $this->assertFalse(app('router')->getRoutes()->hasNamedRoute("admin.{$resource}.show"));
        }
    }

    public function test_berita_admin_pages_use_the_custom_panel_layout(): void
    {
        $user = User::create([
            'name' => 'Admin Berita',
            'email' => 'admin-berita@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);
        $berita = Berita::create([
            'judul' => 'Berita Uji',
            'isi' => 'Isi berita untuk pengujian.',
            'tanggal' => now()->toDateString(),
            'status' => 'published',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('admin.berita.index'))
            ->assertOk()
            ->assertSee('Daftar Berita')
            ->assertSee('Berita Uji');

        $this->get(route('admin.berita.create'))
            ->assertOk()
            ->assertSee('Informasi Berita');

        $this->get(route('admin.berita.edit', $berita))
            ->assertOk()
            ->assertSee('Berita Uji');
    }

    public function test_migrated_admin_pages_keep_their_header_actions(): void
    {
        $user = User::create([
            'name' => 'Admin Navigasi',
            'email' => 'admin-navigasi@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);

        $this->actingAs($user)
            ->get(route('admin.guru-staff.index', ['tipe' => 'guru']))
            ->assertOk()
            ->assertSee('Admin Sekolah')
            ->assertSee('Tambah Guru')
            ->assertSee(asset('css/admin-panel.css'), false);
    }

    public function test_obsolete_short_profile_admin_page_cannot_be_recreated(): void
    {
        $user = User::create([
            'name' => 'Admin Profil',
            'email' => 'admin-profil@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);

        $this->actingAs($user)
            ->get(route('admin.profil-sekolah.editType', 'profil_singkat'))
            ->assertNotFound();

        $this->assertDatabaseMissing('profil_sekolahs', ['type' => 'profil_singkat']);
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

    public function test_class_name_and_major_are_capitalized_when_saved(): void
    {
        $kelas = Kelas::create([
            'tingkat' => '  kelas   1a ',
            'jurusan' => 'program tahfiz',
        ]);

        $this->assertSame('Kelas 1A', $kelas->tingkat);
        $this->assertSame('Program Tahfiz', $kelas->jurusan);
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
                'agama' => 'Islam',
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

    public function test_student_can_be_connected_to_extracurricular_activities(): void
    {
        $user = User::create([
            'name' => 'Admin Ekstra Siswa',
            'email' => 'ekstra-siswa@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);
        $kelas = Kelas::create(['tingkat' => 'Kelas Ekstra']);
        $ekstrakurikuler = Ekstrakurikuler::create(['nama' => 'Robotika']);

        $this->actingAs($user)->post(route('admin.siswa.store'), [
            'nama' => 'Siswa Robotika',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'kelas' => $kelas->tingkat,
            'status' => 'aktif',
            'tahun_masuk' => 2026,
            'ekstrakurikuler_ids' => [$ekstrakurikuler->id],
        ])->assertSessionHasNoErrors();

        $siswa = \App\Models\Siswa::where('nama', 'Siswa Robotika')->firstOrFail();
        $this->assertTrue($siswa->ekstrakurikulers->contains($ekstrakurikuler));
    }

    public function test_admin_can_set_individual_end_of_year_decisions(): void
    {
        $user = User::create([
            'name' => 'Admin Akademik',
            'email' => 'akademik@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);
        Kelas::create(['tingkat' => 'Kelas 1A']);
        Kelas::create(['tingkat' => 'Kelas 2A']);

        $naik = Siswa::create([
            'nama' => 'Siswa Naik',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'kelas' => 'Kelas 1A',
            'status' => 'aktif',
            'tahun_masuk' => 2025,
        ]);
        $tinggal = Siswa::create([
            'nama' => 'Siswa Tinggal',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'kelas' => 'Kelas 1A',
            'status' => 'aktif',
            'tahun_masuk' => 2025,
        ]);

        $this->actingAs($user)->post(route('admin.siswa.promote'), [
            'kelas_asal' => 'Kelas 1A',
            'tahun_ajaran' => '2025/2026',
            'keputusan' => [
                $naik->id => [
                    'status' => 'naik',
                    'kelas_tujuan' => 'Kelas 2A',
                    'catatan' => 'Naik dengan baik',
                ],
                $tinggal->id => [
                    'status' => 'tinggal',
                    'kelas_tujuan' => '',
                    'catatan' => 'Perlu pendampingan',
                ],
            ],
        ])->assertSessionHasNoErrors();

        $this->assertSame('Kelas 2A', $naik->fresh()->kelas);
        $this->assertSame('Kelas 1A', $tinggal->fresh()->kelas);
        $this->assertDatabaseHas('riwayat_akademik', [
            'siswa_id' => $naik->id,
            'tahun_ajaran' => '2025/2026',
            'kelas_asal' => 'Kelas 1A',
            'kelas_tujuan' => 'Kelas 2A',
            'keputusan' => 'naik',
            'diproses_oleh' => $user->id,
        ]);
        $this->assertSame(2, RiwayatAkademik::count());
    }

    public function test_student_nis_must_be_unique_but_can_be_kept_when_editing(): void
    {
        $user = User::create([
            'name' => 'Admin NIS',
            'email' => 'nis@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);
        $kelas = Kelas::create(['tingkat' => 'Kelas NIS']);
        $siswa = Siswa::create([
            'nama' => 'Pemilik NIS',
            'nis' => '001234',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'kelas' => $kelas->tingkat,
            'status' => 'aktif',
            'tahun_masuk' => 2026,
        ]);

        $this->actingAs($user)->post(route('admin.siswa.store'), [
            'nama' => 'Duplikat NIS',
            'nis' => '001234',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'kelas' => $kelas->tingkat,
            'status' => 'aktif',
            'tahun_masuk' => 2026,
        ])->assertSessionHasErrors('nis');

        $this->actingAs($user)->put(route('admin.siswa.update', $siswa), [
            'nama' => 'Pemilik NIS Diperbarui',
            'nis' => '001234',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'kelas' => $kelas->tingkat,
            'status' => 'aktif',
            'tahun_masuk' => 2026,
        ])->assertSessionHasNoErrors();
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

    public function test_guru_staff_nip_must_be_unique_but_can_be_kept_when_editing(): void
    {
        $user = User::create([
            'name' => 'Admin NIP',
            'email' => 'nip@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);
        $guru = GuruStaff::create([
            'tipe' => 'guru',
            'nama' => 'Pemilik NIP',
            'jenis_kelamin' => 'laki_laki',
            'jabatan' => 'Guru',
            'nip' => '1987654321',
            'status_kepegawaian' => 'PNS',
            'pendidikan_terakhir' => 'S1',
            'agama' => 'Islam',
        ]);

        $this->actingAs($user)->post(route('admin.guru-staff.store'), [
            'tipe' => 'staf',
            'nama' => 'Duplikat NIP',
            'jenis_kelamin' => 'perempuan',
            'jabatan' => 'Staf',
            'nip' => '1987654321',
            'status_kepegawaian' => 'PNS',
            'pendidikan_terakhir' => 'S1',
            'agama' => 'Islam',
        ])->assertSessionHasErrors('nip');

        $this->actingAs($user)->put(route('admin.guru-staff.update', $guru), [
            'tipe' => 'guru',
            'nama' => 'Pemilik NIP Diperbarui',
            'jenis_kelamin' => 'laki_laki',
            'jabatan' => 'Guru',
            'nip' => '1987654321',
            'status_kepegawaian' => 'PNS',
            'pendidikan_terakhir' => 'S1',
            'agama' => 'Islam',
        ])->assertSessionHasNoErrors();
    }

    public function test_non_admin_cannot_access_admin_pages(): void
    {
        $user = User::create([
            'name' => 'Bukan Admin',
            'email' => 'user@example.test',
            'password' => 'password',
            'role' => 'User',
        ]);

        $this->actingAs($user)->get(route('admin.siswa.index'))->assertForbidden();
    }

    public function test_class_capacity_is_enforced(): void
    {
        $user = User::create([
            'name' => 'Admin Kapasitas',
            'email' => 'kapasitas@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);
        $kelas = Kelas::create(['tingkat' => 'Kelas Penuh', 'kapasitas' => 1]);
        Siswa::create([
            'nama' => 'Pengisi Kelas',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'kelas' => $kelas->tingkat,
            'kelas_id' => $kelas->id,
            'status' => 'aktif',
            'tahun_masuk' => 2026,
        ]);

        $this->actingAs($user)->post(route('admin.siswa.store'), [
            'nama' => 'Siswa Kedua',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'kelas' => $kelas->tingkat,
            'status' => 'aktif',
            'tahun_masuk' => 2026,
        ])->assertSessionHasErrors('kelas');
    }

    public function test_archived_student_can_be_restored(): void
    {
        $user = User::create([
            'name' => 'Admin Arsip',
            'email' => 'arsip@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);
        $siswa = Siswa::create([
            'nama' => 'Siswa Arsip',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'status' => 'alumni',
            'tahun_masuk' => 2018,
            'tahun_lulus' => 2024,
        ]);

        $this->actingAs($user)->delete(route('admin.siswa.destroy', $siswa))->assertSessionHasNoErrors();
        $this->assertSoftDeleted('siswas', ['id' => $siswa->id]);

        $this->actingAs($user)->patch(route('admin.siswa.restore', $siswa->id))->assertSessionHasNoErrors();
        $this->assertNotSoftDeleted('siswas', ['id' => $siswa->id]);
    }

    public function test_outgoing_student_details_can_be_saved_and_edited(): void
    {
        $user = User::create([
            'name' => 'Admin Pindah',
            'email' => 'pindah@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);
        $siswa = Siswa::create([
            'nama' => 'Siswa Pindah',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'status' => 'keluar',
            'tahun_masuk' => 2024,
        ]);

        $this->actingAs($user)->put(route('admin.siswa.update', $siswa), [
            'nama' => 'Siswa Pindah',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'status' => 'keluar',
            'tahun_masuk' => 2024,
            'tanggal_keluar' => '2026-07-05',
            'sekolah_tujuan' => 'SD Tujuan',
            'alasan_keluar' => 'Mengikuti orang tua',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('siswas', [
            'id' => $siswa->id,
            'status' => 'keluar',
            'kelas_id' => null,
            'sekolah_tujuan' => 'SD Tujuan',
            'alasan_keluar' => 'Mengikuti orang tua',
        ]);
    }

    public function test_class_cannot_be_deleted_when_archived_students_still_reference_it(): void
    {
        $user = User::create([
            'name' => 'Admin Integritas Kelas',
            'email' => 'integritas-kelas@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);
        $kelas = Kelas::create(['tingkat' => 'Kelas Arsip']);
        $siswa = Siswa::create([
            'nama' => 'Siswa Kelas Arsip',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'kelas' => $kelas->tingkat,
            'kelas_id' => $kelas->id,
            'status' => 'aktif',
            'tahun_masuk' => 2026,
        ]);
        $siswa->delete();

        $this->actingAs($user)
            ->delete(route('admin.kelas.destroy', $kelas))
            ->assertSessionHasErrors('kelas');

        $this->assertDatabaseHas('kelas', ['id' => $kelas->id]);
    }

    public function test_academic_year_must_be_consecutive(): void
    {
        $user = User::create([
            'name' => 'Admin Tahun Ajaran',
            'email' => 'tahun-ajaran@example.test',
            'password' => 'password',
            'role' => 'Admin',
        ]);

        $this->actingAs($user)->post(route('admin.kelas.store'), [
            'tingkat' => 'Kelas Tahun Salah',
            'tahun_ajaran' => '2026/2029',
        ])->assertSessionHasErrors('tahun_ajaran');
    }
}
