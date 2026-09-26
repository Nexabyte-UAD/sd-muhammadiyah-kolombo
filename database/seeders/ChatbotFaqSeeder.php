<?php

namespace Database\Seeders;

use App\Models\ChatbotFaq;
use Illuminate\Database\Seeder;

class ChatbotFaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Di mana alamat sekolah SD Muhammadiyah Komplek Kolombo?',
                'answer' => 'Alamat SD Muhammadiyah Komplek Kolombo berada di Komplek Kolombo, Caturtunggal, Depok, Sleman, Daerah Istimewa Yogyakarta.',
                'keywords' => 'alamat, lokasi, di mana, letak, jalan',
                'category' => 'Kontak',
            ],
            [
                'question' => 'Bagaimana cara menghubungi sekolah?',
                'answer' => 'Anda dapat menghubungi kami melalui telepon di (0274) 585755, atau langsung berkonsultasi via WhatsApp Panitia PPDB. Anda juga bisa mengisi form pesan di halaman kontak website kami.',
                'keywords' => 'kontak, hubungi, telepon, no telp, nomor, email, whatsapp, wa',
                'category' => 'Kontak',
            ],
            [
                'question' => 'Apa profil SD Muhammadiyah Komplek Kolombo?',
                'answer' => 'SD Muhammadiyah Komplek Kolombo Yogyakarta adalah institusi pendidikan dasar berakreditasi A yang berkomitmen untuk mendidik generasi Islami, cerdas, berprestasi, dan berkarakter mulia. Informasi lengkap mengenai profil dapat dilihat di menu Profil website.',
                'keywords' => 'profil, tentang, sejarah, background',
                'category' => 'Profil',
            ],
            [
                'question' => 'Apa visi dan misi sekolah?',
                'answer' => 'Visi dan misi SD Muhammadiyah Komplek Kolombo difokuskan pada pembentukan karakter Islami, keunggulan akademik, dan keterampilan hidup. Detail visi dan misi secara lengkap dapat Anda baca pada halaman Visi & Misi di menu Profil website kami.',
                'keywords' => 'visi, misi, tujuan, visi misi',
                'category' => 'Profil',
            ],
            [
                'question' => 'Ada berita terbaru apa di sekolah?',
                'answer' => 'Untuk mengetahui berita, pengumuman, dan artikel terbaru seputar kegiatan di SD Muhammadiyah Komplek Kolombo, silakan kunjungi halaman Berita di menu Informasi website kami.',
                'keywords' => 'berita, kabar, info terbaru, pengumuman, artikel',
                'category' => 'Informasi',
            ],
            [
                'question' => 'Apa saja prestasi yang diraih oleh siswa?',
                'answer' => 'Siswa-siswi kami telah meraih berbagai prestasi membanggakan baik di tingkat kabupaten, provinsi, maupun nasional. Daftar lengkap prestasi dapat dilihat di halaman Prestasi Siswa pada menu Kesiswaan.',
                'keywords' => 'prestasi, juara, lomba, kejuaraan, penghargaan',
                'category' => 'Informasi',
            ],
            [
                'question' => 'Siapa saja guru dan staf yang mengajar di sini?',
                'answer' => 'Kami memiliki tenaga pendidik (guru) dan kependidikan (staf) yang profesional dan berdedikasi. Anda dapat melihat daftar nama serta profil mereka di halaman Guru dan Staf pada menu Struktural.',
                'keywords' => 'guru, staf, pengajar, karyawan, wali kelas',
                'category' => 'Profil',
            ],
            [
                'question' => 'Ekstrakurikuler apa saja yang tersedia?',
                'answer' => 'SD Muhammadiyah Komplek Kolombo menyediakan berbagai pilihan ekstrakurikuler seperti Kepanduan Hizbul Wathan (HW), Tapak Suci, Tahfidz Al-Qur\'an, dan lainnya. Silakan cek halaman Ekstrakurikuler pada menu Kesiswaan.',
                'keywords' => 'ekskul, ekstrakurikuler, kegiatan, bakat, minat',
                'category' => 'Informasi',
            ],
            [
                'question' => 'Apa itu Guru Menulis?',
                'answer' => 'Guru Menulis adalah wadah publikasi kumpulan artikel, opini, dan karya ilmiah dari bapak/ibu guru SD Muhammadiyah Komplek Kolombo. Anda dapat membacanya di menu Informasi > Guru Menulis.',
                'keywords' => 'guru menulis, artikel guru, opini guru, karya guru, tulisan',
                'category' => 'Informasi',
            ],
            [
                'question' => 'Kapan jam pelayanan tata usaha sekolah?',
                'answer' => 'Sekretariat dan Layanan Tata Usaha SD Muhammadiyah Komplek Kolombo beroperasi setiap hari Senin hingga Sabtu mulai pukul 08.00 – 13.00 WIB.',
                'keywords' => 'jam kerja, jam buka, pelayanan, operasional, tutup, jam',
                'category' => 'Kontak',
            ],
            [
                'question' => 'Bagaimana informasi pendaftaran siswa baru (SPMB / PPDB)?',
                'answer' => 'Informasi lengkap Pendaftaran Siswa Baru (SPMB), kuota per tahun ajaran, persyaratan berkas, dan alur pendaftaran dapat Anda akses di menu Informasi > SPMB, atau menghubungi Panitia PPDB di (0274) 585755.',
                'keywords' => 'biaya, pendaftaran, ppdb, spmb, masuk, spp, daftar, kuota',
                'category' => 'Informasi',
            ]
        ];

        foreach ($faqs as $faq) {
            ChatbotFaq::updateOrCreate(
                ['question' => $faq['question']], // Search by question to make it idempotent
                [
                    'answer' => $faq['answer'],
                    'keywords' => $faq['keywords'],
                    'category' => $faq['category'],
                    'is_active' => true,
                ]
            );
        }
    }
}
