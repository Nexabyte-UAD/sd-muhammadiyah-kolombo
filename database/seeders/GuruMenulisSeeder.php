<?php

namespace Database\Seeders;

use App\Models\GuruMenulis;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class GuruMenulisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $artikels = [
            [
                'judul' => "Menumbuhkan Minat Baca dan Literasi Al-Qur'an pada Anak Usia Sekolah Dasar",
                'slug' => Str::slug("Menumbuhkan Minat Baca dan Literasi Al-Qur'an pada Anak Usia Sekolah Dasar"),
                'penulis' => 'Dra. Hj. Siti Aminah, M.Pd.',
                'kategori' => 'Opini',
                'isi' => "Pendidikan literasi bukan sekadar membaca teks bacaan, melainkan menanamkan pemahaman mendalam dan cinta terhadap ilmu pengetahuan. Di SD Muhammadiyah Komplek Kolombo, pembiasaan literasi diawali setiap pagi sebelum jam pelajaran dimulai.\n\nMelalui kegiatan Tahfidz pagi dan pembacaan ayat suci Al-Qur'an, para siswa diajak untuk membiasakan diri berinteraksi dengan kitab suci. Hal ini terbukti tidak hanya meningkatkan ketenangan hati anak, tetapi juga mengasah daya ingat dan fokus belajar mereka dalam mata pelajaran akademik lainnya.\n\nPeran orang tua di rumah sangat penting untuk mendampingi dan memberikan teladan membaca agar pembiasaan baik ini terus berlanjut secara konsisten.",
                'gambar' => null,
                'tanggal' => '2026-08-10',
                'status' => 'published',
            ],
            [
                'judul' => 'Pentingnya Pembentukan Karakter Mandiri melalui Kegiatan Kepanduan Hizbul Wathan',
                'slug' => Str::slug('Pentingnya Pembentukan Karakter Mandiri melalui Kegiatan Kepanduan Hizbul Wathan'),
                'penulis' => 'Budi Santoso, S.Pd.',
                'kategori' => 'Panduan Belajar',
                'isi' => "Kepanduan Hizbul Wathan (HW) merupakan salah satu wadah pembentukan karakter kepemimpinan dan kemandirian siswa Muhammadiyah. Melalui latihan rutin setiap pekan, siswa diajarkan berbagai keterampilan hidup (life skills).\n\nKegiatan penjelajahan alam, latihan baris-berbaris, pertolongan pertama, dan kerja sama kelompok melatih siswa untuk tanggap, bertanggung jawab, dan peduli terhadap sesama. Nilai-nilai kedisiplinan yang ditanamkan dalam HW menjadi bekal berharga bagi siswa saat menghadapi tantangan di masa depan.",
                'gambar' => null,
                'tanggal' => '2026-08-08',
                'status' => 'published',
            ],
            [
                'judul' => 'Inovasi Pembelajaran Matematika yang Menyenangkan dengan Metode Kontekstual',
                'slug' => Str::slug('Inovasi Pembelajaran Matematika yang Menyenangkan dengan Metode Kontekstual'),
                'penulis' => 'Rina Wulandari, S.Pd.Si.',
                'kategori' => 'Artikel Ilmiah',
                'isi' => "Matematika sering kali dianggap sebagai pelajaran yang rumit oleh sebagian siswa sekolah dasar. Oleh karena itu, pendekatan pembelajaran matematika berbasis kontekstual dan permainan edukatif menjadi solusi efektif di kelas.\n\nDengan memanfaatkan benda-benda di sekitar kelas dan alat peraga interaktif, siswa dapat memahami konsep penjumlahan, perkalian, hingga bangun ruang secara konkrit. Ketika matematika dikemas secara menyenangkan, rasa percaya diri dan antusiasme belajar siswa meningkat secara signifikan.",
                'gambar' => null,
                'tanggal' => '2026-08-05',
                'status' => 'published',
            ],
        ];

        foreach ($artikels as $item) {
            GuruMenulis::updateOrCreate(
                ['slug' => $item['slug']],
                $item
            );
        }
    }
}
