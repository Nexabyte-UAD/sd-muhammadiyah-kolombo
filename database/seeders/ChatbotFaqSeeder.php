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
                'answer' => 'Alamat SD Muhammadiyah Komplek Kolombo berada di Jl. Rajawali No. 10, Demangan Baru, Caturtunggal, Depok, Sleman, Daerah Istimewa Yogyakarta.',
                'keywords' => 'alamat, lokasi, di mana, letak, jalan',
                'category' => 'Kontak',
            ],
            [
                'question' => 'Bagaimana cara menghubungi sekolah?',
                'answer' => 'Anda dapat menghubungi kami melalui telepon di (0274) 585755, atau email ke sdmuhkkolombo@gmail.com. Anda juga bisa mengisi form pesan di halaman kontak website kami.',
                'keywords' => 'kontak, hubungi, telepon, no telp, nomor, email, whatsapp, wa',
                'category' => 'Kontak',
            ],
            [
                'question' => 'Apa profil SD Muhammadiyah Komplek Kolombo?',
                'answer' => 'SD Muhammadiyah Komplek Kolombo Yogyakarta adalah institusi pendidikan dasar yang berkomitmen untuk mendidik generasi Islami, cerdas, berprestasi, dan berkarakter mulia. Informasi lengkap mengenai sejarah dan profil dapat dilihat di halaman Tentang Kami di website.',
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
                'answer' => 'Untuk mengetahui berita, pengumuman, dan artikel terbaru seputar kegiatan di SD Muhammadiyah Komplek Kolombo, silakan kunjungi halaman Berita di website kami.',
                'keywords' => 'berita, kabar, info terbaru, pengumuman, artikel',
                'category' => 'Informasi',
            ],
            [
                'question' => 'Apa saja prestasi yang diraih oleh siswa?',
                'answer' => 'Siswa-siswi kami telah meraih berbagai prestasi membanggakan baik di tingkat kabupaten, provinsi, maupun nasional dalam berbagai bidang. Daftar lengkap prestasi dapat dilihat di halaman Prestasi pada website kami.',
                'keywords' => 'prestasi, juara, lomba, kejuaraan, penghargaan',
                'category' => 'Informasi',
            ],
            [
                'question' => 'Siapa saja guru dan staf yang mengajar di sini?',
                'answer' => 'Kami memiliki tenaga pendidik (guru) dan kependidikan (staf) yang profesional dan berdedikasi. Anda dapat melihat daftar nama serta profil singkat mereka di halaman Guru dan Staf pada menu Struktural.',
                'keywords' => 'guru, staf, pengajar, karyawan, wali kelas',
                'category' => 'Profil',
            ],
            [
                'question' => 'Ekstrakurikuler apa saja yang tersedia?',
                'answer' => 'SD Muhammadiyah Komplek Kolombo menyediakan berbagai pilihan ekstrakurikuler untuk mengembangkan bakat dan minat siswa, seperti kepanduan HW, tapak suci, tahfidz, dan lainnya. Silakan cek halaman Ekstrakurikuler di website kami untuk daftar lengkapnya.',
                'keywords' => 'ekskul, ekstrakurikuler, kegiatan, bakat, minat',
                'category' => 'Informasi',
            ],
            [
                'question' => 'Bagaimana cara melihat data siswa atau alumni?',
                'answer' => 'Informasi statistik dan data terkait kesiswaan, baik siswa aktif, kelas, maupun alumni dapat Anda temukan pada menu Kesiswaan di website resmi kami.',
                'keywords' => 'siswa, murid, anak, alumni, lulusan, data',
                'category' => 'Informasi',
            ],
            [
                'question' => 'Kapan jam pelayanan tata usaha sekolah?',
                'answer' => 'Informasi jam pelayanan operasional belum tersedia pada sistem. Silakan menghubungi pihak sekolah melalui halaman Kontak.',
                'keywords' => 'jam kerja, jam buka, pelayanan, operasional, tutup',
                'category' => 'Kontak',
            ],
            [
                'question' => 'Berapa biaya masuk atau informasi pendaftaran (PPDB)?',
                'answer' => 'Informasi resmi mengenai pendaftaran (PPDB) saat ini belum tersedia pada sistem. Silakan memantau halaman pengumuman berita atau menghubungi pihak sekolah melalui halaman Kontak.',
                'keywords' => 'biaya, pendaftaran, ppdb, masuk, spp, daftar',
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
