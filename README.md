<div align="center">
  <img src="assets/banner.png" alt="KolomboEdu Banner" width="100%">
  <br><br>
  <h1>KolomboEdu</h1>
  <p><strong>Sistem Informasi Terpadu SD Muhammadiyah Komplek Kolombo Yogyakarta</strong></p>
  <p>A modern, responsive, and dynamic web platform designed to streamline school management and digital presence.</p>
</div>

---

Sistem informasi dan website resmi untuk SD Muhammadiyah Komplek Kolombo Yogyakarta.

KolomboEdu menyediakan portal publik untuk informasi sekolah dan dashboard admin untuk mengelola konten secara dinamis. Proyek ini berbasis Laravel, Blade, custom CSS, Bootstrap, dan MySQL.

## 📖 Ringkasan

Aplikasi ini memiliki dua area utama:

1. Portal publik untuk pengunjung, orang tua, siswa, alumni, dan masyarakat umum.
2. Dashboard admin untuk mengelola berita, profil sekolah, guru/staf, prestasi, ekstrakurikuler, kelas, siswa, pesan pengunjung, pengguna, dan pengaturan website.

Konten yang diinput dari admin menjadi sumber data untuk halaman publik. Beberapa input teks juga dinormalisasi melalui `IndonesianTextFormatter` agar penulisan konten sekolah lebih konsisten.

---

## 👨‍💻 Tim Pengembang

| Peran | Nama Lengkap | NIM | Kontribusi |
| :--- | :--- | :--- | :--- |
| Project Manager | Dzaky Ridhwan Rosyada | 2300018398 | Project planning dan koordinasi |
| UI/UX Designer | Fauziyah Tahta Dirgantari | 2300018252 | Wireframe dan design system |
| Frontend Developer | M Ilham Nurdin | 2300018406 | Pengembangan tampilan publik |
| Backend Developer | Aditya Bintang Rianda Syahputra | 2300018399 | Backend, database, dan Docker |
| Quality Assurance | Trizana Wafi Reswara | 2300018258 | Testing dan quality assurance |

---

## ✨ Fitur Utama

### Portal Publik

- Beranda dengan hero, statistik, sambutan, berita terbaru, dan penghargaan.
- Halaman profil sekolah: sambutan, tentang, visi misi, dan akreditasi.
- Halaman guru dan staf dengan biodata.
- Halaman prestasi berbasis kategori.
- Halaman ekstrakurikuler.
- Halaman kelas, siswa, dan alumni.
- Direktori berita dan detail berita.
- Form kontak/pesan pengunjung.

### Dashboard Admin

- Login admin dengan proteksi middleware.
- Manajemen akun admin.
- CRUD berita.
- CRUD guru dan staf.
- CRUD prestasi.
- CRUD ekstrakurikuler.
- CRUD kelas dan siswa.
- Kenaikan kelas siswa.
- Inbox pesan pengunjung.
- Pengelolaan profil sekolah.
- Pengaturan website seperti logo, kontak, hero, dan sambutan.
- Activity log untuk aktivitas sistem.

---

## 🛠️ Tech Stack

| Bagian | Teknologi |
| :--- | :--- |
| Framework | Laravel 13 |
| Bahasa | PHP 8.3+ |
| Template | Blade |
| Portal Publik | Bootstrap 5.3, Blade, custom CSS |
| Dashboard Admin | Bootstrap Bundle 4.6.1, Blade, custom CSS (tanpa jQuery) |
| Database | MySQL atau MariaDB |
| Testing Laravel | PHPUnit |
| Testing Browser | Python, Pytest, Playwright |
| Local process runner | npm script dengan `concurrently` |
| Container | Docker / Laravel Sail |

Catatan: proyek ini tidak bergantung pada Vite/Tailwind untuk tampilan aktif. `npm run dev` dipakai sebagai process runner lokal untuk menjalankan server, queue listener, dan log watcher.

---

## 🚀 Instalasi

### Opsi 1: Docker

Syarat:

- Docker Desktop sudah terpasang dan berjalan.

Langkah:

```bash
git clone https://github.com/Nexabyte-UAD/sd-muhammadiyah-kolombo.git
cd sd-muhammadiyah-kolombo
cp .env.example .env
```

Install dependency Composer melalui container sementara:

```bash
docker run --rm -v "%cd%:/var/www/html" -w /var/www/html laravelsail/php83-composer:latest composer install --ignore-platform-reqs
```

Untuk Mac, Linux, atau WSL, ganti `"%cd%"` menjadi `"$(pwd)"`.

Jalankan container:

```bash
docker compose up -d --build
```

Generate key, migrasi, dan seed database:

```bash
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate:fresh --seed
```

Buka website:

```text
http://localhost
```

### Opsi 2: Non-Docker

Syarat:

- PHP 8.3+
- Composer
- MySQL atau MariaDB
- Node.js dan npm jika ingin memakai `npm run dev`

Langkah:

```bash
git clone https://github.com/Nexabyte-UAD/sd-muhammadiyah-kolombo.git
cd sd-muhammadiyah-kolombo
copy .env.example .env
composer install
php artisan key:generate
```

Buat database kosong, lalu sesuaikan `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=muhammadiahkolombo
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migrasi dan seeder:

```bash
php artisan migrate:fresh --seed
```

Jalankan server lokal:

```bash
php artisan serve
```

Buka:

```text
http://127.0.0.1:8000
```

Alternatif untuk development:

```bash
npm install
npm run dev
```

Di Windows PowerShell, gunakan `npm.cmd run dev` jika eksekusi `npm.ps1` diblokir oleh execution policy.

---

## 🔐 Akun Admin

Akun admin dibuat melalui seeder. Periksa `database/seeders/DatabaseSeeder.php` untuk kredensial bawaan yang sedang aktif.

---

## 📂 Struktur Folder

```text
app/
  Http/Controllers/       Controller public dan admin
  Http/Middleware/        Middleware auth, admin, security, dan timeout
  Models/                 Model Eloquent
  Services/               Service aplikasi, termasuk IndonesianTextFormatter
config/                   Konfigurasi Laravel dan aplikasi
database/
  migrations/             Skema database
  seeders/                Data awal
public/
  css/                    CSS publik/admin
  js/                     JavaScript admin
  images/                 Aset gambar publik
  vendor/                 Bootstrap Bundle 4.6.1 untuk panel admin
resources/views/
  admin/                  Halaman dashboard admin
  auth/                   Halaman login dan reset password
  components/             Komponen Blade
  layouts/                Layout public, admin, dan auth
  pages/                  Halaman publik
routes/
  web.php                 Route publik dan admin
  auth.php                Route autentikasi
test/user/                Test browser Playwright
tests/                    Test PHPUnit Laravel
```

---

## 🗄️ Modul Data

- `User`: akun administrator.
- `Setting`: konfigurasi website.
- `ProfilSekolah`: konten profil sekolah.
- `GuruStaff`: data guru dan tenaga kependidikan.
- `Berita`: artikel, pengumuman, dan berita sekolah.
- `Prestasi`: prestasi siswa/sekolah per kategori.
- `Ekstrakurikuler`: kegiatan ekstrakurikuler.
- `Kelas`: data kelas dinamis dan wali kelas.
- `Siswa`: data siswa aktif, mutasi, alumni, dan riwayat akademik.
- `Pesan`: pesan dari formulir kontak.
- `ActivityLog`: catatan aktivitas sistem.

---

## 🧪 Testing

### Test Laravel

Jalankan seluruh test PHPUnit:

```bash
php artisan test
```

Atau melalui Composer:

```bash
composer test
```

Test Laravel mencakup halaman publik, route admin, dan formatter teks Indonesia.

### Test Browser Playwright

Buat virtual environment Python:

```bash
python -m venv venv
```

Aktivasi di Windows PowerShell:

```powershell
.\venv\Scripts\Activate.ps1
```

Aktivasi di Windows CMD:

```cmd
venv\Scripts\activate
```

Install dependency:

```bash
pip install playwright pytest
playwright install
```

Pastikan Laravel berjalan:

```bash
php artisan serve
```

Jalankan semua test browser:

```bash
pytest test/user
```

Jalankan test tertentu:

```bash
pytest test/user/test_navigation.py
pytest test/user/test_profile.py
pytest test/user/test_structural.py
pytest test/user/test_contact.py
```

---

## 📝 Catatan Pengembangan

- Gunakan `app/Services/IndonesianTextFormatter.php` sebagai pusat normalisasi teks.
- Jangan menduplikasi aturan kapitalisasi di controller atau view.
- Modul prestasi memakai kategori sebagai sumber yang sama untuk admin dan halaman publik.
- Modul guru/staf memakai model `GuruStaff` dengan pembeda `tipe`.
- Modul kelas dan siswa dibuat dinamis, bukan daftar kelas statis.
- Setelah mengubah route, controller, migration, atau formatter, jalankan test yang relevan.

---

## 🤖 Integrasi Chatbot FAQ & Gemini AI

Aplikasi ini dilengkapi dengan fitur Asisten Chatbot interaktif pada halaman publik yang bekerja dengan alur pencarian berikut:
1. **FAQ Lokal**: Chatbot mencocokkan pertanyaan pengguna dengan kata kunci dan pertanyaan pada database FAQ lokal yang dikelola oleh Admin. Jika skor kecocokan berada di atas threshold, jawaban dari database FAQ lokal akan dikirimkan.
2. **Fallback Gemini AI**: Jika tidak ditemukan FAQ lokal yang cocok, chatbot akan meneruskan pertanyaan secara aman ke API Google Gemini (`gemini-3.5-flash`) untuk memberikan respons pintar dinamis (apabila fitur diaktifkan dan API key dikonfigurasi).
3. **Fallback Statis Lokal**: Jika Gemini dinonaktifkan, gagal merespons, atau terjadi error jaringan, chatbot akan memberikan jawaban fallback statis yang ramah pengguna yang mengarahkan mereka ke halaman kontak sekolah.

### Konfigurasi Gemini AI

Untuk mengaktifkan integrasi Gemini AI, tambahkan variabel konfigurasi berikut pada file `.env` Anda:

```env
GEMINI_ENABLED=true
GEMINI_API_KEY=isi_dengan_api_key_gemini_anda
GEMINI_MODEL=gemini-3.5-flash
GEMINI_TIMEOUT=15
GEMINI_MAX_OUTPUT_TOKENS=1000
GEMINI_TEMPERATURE=0.3
CHATBOT_FAQ_THRESHOLD=4
CHATBOT_HISTORY_LIMIT=4
CHATBOT_LOG_RETENTION_DAYS=90
```

> [!NOTE]
> Jika `GEMINI_ENABLED` disetel ke `false` atau `GEMINI_API_KEY` kosong, chatbot akan secara otomatis melewati pemanggilan API Gemini dan langsung menggunakan fallback statis lokal.

Log pertanyaan dan jawaban chatbot dibersihkan otomatis oleh jadwal `model:prune` setelah 90 hari. Nilainya dapat disesuaikan melalui `CHATBOT_LOG_RETENTION_DAYS`.

Konteks data publik di-cache selama 5 menit dan hanya bagian yang relevan dengan topik pertanyaan yang dikirim ke Gemini. Pengguna juga dapat memberi penilaian Membantu atau Tidak membantu pada setiap jawaban; penilaian dilindungi token khusus per jawaban.

### Pengujian Otomatis

Seluruh pengujian chatbot, baik untuk FAQ lokal maupun integrasi Gemini AI, disimulasikan menggunakan pengujian unit/feature Laravel dengan `Http::fake()` dan `Http::preventStrayRequests()` untuk mencegah panggilan API nyata.

Jalankan test suite chatbot dengan perintah berikut:
```bash
php artisan test --filter=Chatbot
```

---

## 🌐 Deployment

Checklist dasar production:

```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Pastikan `.env` production sudah disesuaikan:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-sekolah.example
```

Arahkan document root web server ke folder `public/`.

---

## 📄 Lisensi

Proyek ini menggunakan lisensi MIT. Lihat [LICENSE](LICENSE) untuk detailnya.
