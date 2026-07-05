<?php

namespace Tests\Feature;

use App\Models\Ekstrakurikuler;
use App\Models\GuruStaff;
use App\Models\Kelas;
use App\Models\Prestasi;
use App\Models\Siswa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_are_available(): void
    {
        foreach ([
            '/',
            '/sambutan',
            '/tentang',
            '/visi-misi',
            '/akreditasi',
            '/guru',
            '/prestasi',
            '/ekstrakurikuler',
            '/siswa',
            '/kelas',
            '/alumni',
            '/berita',
        ] as $uri) {
            $this->get($uri)->assertOk();
        }
    }

    public function test_contact_message_can_be_submitted_anonymously(): void
    {
        $this->post('/pesan', ['pesan' => 'Pesan pengujian'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success_pesan');

        $this->assertDatabaseHas('pesans', [
            'nama' => '*Anonim*',
            'email' => 'anonim@rahasia.com',
            'isi' => 'Pesan pengujian',
        ]);
    }

    public function test_homepage_shows_guru_and_staf_from_struktural_data(): void
    {
        GuruStaff::create([
            'tipe' => 'guru',
            'nama' => 'Guru Beranda',
            'jenis_kelamin' => 'perempuan',
            'jabatan' => 'Wali Kelas',
            'status_kepegawaian' => 'PPPK',
            'pendidikan_terakhir' => 'S2',
            'agama' => 'Islam',
        ]);
        GuruStaff::create([
            'tipe' => 'staf',
            'nama' => 'Staf Beranda',
            'jabatan' => 'Tata Usaha',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Guru Beranda')
            ->assertSee('Staf Beranda')
            ->assertSee('Wali Kelas')
            ->assertSee('Tata Usaha')
            ->assertSee('Biodata Guru')
            ->assertSee('Perempuan')
            ->assertSee('PPPK')
            ->assertSee('S2')
            ->assertSee('Islam')
            ->assertSee('data-bs-target="#biodataTenaga-', false);

    }

    public function test_homepage_places_staf_between_balanced_groups_of_guru(): void
    {
        foreach (['Guru A', 'Guru B', 'Guru C', 'Guru D'] as $nama) {
            GuruStaff::create([
                'tipe' => 'guru',
                'nama' => $nama,
                'jabatan' => 'Guru',
            ]);
        }
        GuruStaff::create([
            'tipe' => 'staf',
            'nama' => 'Staf Tengah',
            'jabatan' => 'Tata Usaha',
        ]);

        $content = $this->get('/')->assertOk()->getContent();

        $this->assertTrue(
            strpos($content, 'Guru A') <
            strpos($content, 'Guru B') &&
            strpos($content, 'Guru B') <
            strpos($content, 'Staf Tengah') &&
            strpos($content, 'Staf Tengah') <
            strpos($content, 'Guru C') &&
            strpos($content, 'Guru C') <
            strpos($content, 'Guru D')
        );
    }

    public function test_homepage_participant_count_uses_active_student_data(): void
    {
        Siswa::create([
            'nama' => 'Siswa Aktif',
            'jenis_kelamin' => 'L',
            'kelas' => '3',
            'status' => 'aktif',
            'tahun_masuk' => 2024,
        ]);
        Siswa::create([
            'nama' => 'Siswa Alumni',
            'jenis_kelamin' => 'P',
            'status' => 'alumni',
            'tahun_masuk' => 2018,
            'tahun_lulus' => 2024,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('1<span class="text-success"', false)
            ->assertSee(route('siswa'), false);
    }

    public function test_homepage_activities_use_extracurricular_data(): void
    {
        Ekstrakurikuler::create([
            'nama' => 'Robotika Beranda',
            'deskripsi' => 'Program robotika siswa.',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Robotika Beranda')
            ->assertSee('Program robotika siswa.')
            ->assertSee(route('ekstrakurikuler'), false);
    }

    public function test_achievement_page_renders_competition_details_in_the_correct_category(): void
    {
        Prestasi::create([
            'judul' => 'Lomba Tahfiz',
            'kategori' => 'keagamaan',
            'nama_siswa' => 'Ahmad Test',
            'prestasi_medali' => 'Juara 1',
            'penyelenggara' => 'PCM Test',
            'tanggal' => '2026-07-05',
            'deskripsi' => 'Tingkat Kabupaten',
            'gambar' => 'prestasi/file-yang-hilang.jpg',
        ]);

        $this->get('/prestasi')
            ->assertOk()
            ->assertDontSee('accordion-collapse collapse show', false)
            ->assertSee('Lomba Tahfiz')
            ->assertSee('Ahmad Test')
            ->assertSee('Juara 1')
            ->assertSee('PCM Test')
            ->assertSee('Tingkat Kabupaten')
            ->assertSee('No Image')
            ->assertSee('kategori-keagamaan');
    }

    public function test_class_page_uses_the_teacher_selected_from_guru_data(): void
    {
        $guru = GuruStaff::create([
            'tipe' => 'guru',
            'nama' => 'Wali Kelas Tiga',
            'jabatan' => 'Guru Kelas',
        ]);
        Kelas::where('tingkat', '3')->update(['wali_kelas_id' => $guru->id]);
        Siswa::create([
            'nama' => 'Siswa Kelas Tiga',
            'jenis_kelamin' => 'L',
            'kelas' => '3',
            'status' => 'aktif',
            'tahun_masuk' => 2024,
        ]);

        $this->get('/kelas')
            ->assertOk()
            ->assertSee('Kelas 3')
            ->assertSee('Wali Kelas Tiga');
    }
}
