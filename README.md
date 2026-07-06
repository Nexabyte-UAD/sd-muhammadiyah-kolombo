# KolomboEdu

Sistem informasi dan website resmi untuk SD Muhammadiyah Komplek Kolombo Yogyakarta.

KolomboEdu menyediakan portal publik untuk informasi sekolah dan dashboard admin untuk mengelola konten secara dinamis. Proyek ini berbasis Laravel, Blade, Bootstrap/AdminLTE, dan MySQL.

## Ringkasan

Aplikasi ini memiliki dua area utama:

1. Portal publik untuk pengunjung, orang tua, siswa, alumni, dan masyarakat umum.
2. Dashboard admin untuk mengelola berita, profil sekolah, guru/staf, prestasi, ekstrakurikuler, kelas, siswa, pesan pengunjung, pengguna, dan pengaturan website.

Konten yang diinput dari admin menjadi sumber data untuk halaman publik. Beberapa input teks juga dinormalisasi melalui `IndonesianTextFormatter` agar penulisan konten sekolah lebih konsisten.

## Tim Pengembang

| Peran | Nama Lengkap | NIM | Kontribusi |
| :--- | :--- | :--- | :--- |
| Project Manager | Dzaky Ridhwan Rosyada | 2300018398 | Project planning dan koordinasi |
| UI/UX Designer | Fauziyah Tahta Dirgantari | 2300018252 | Wireframe dan design system |
| Frontend Developer | M Ilham Nurdin | 2300018406 | Pengembangan tampilan publik |
| Backend Developer | Aditya Bintang Rianda Syahputra | 2300018399 | Backend, database, dan Docker |
| Quality Assurance | Trizana Wafi Reswara | 2300018258 | Testing dan quality assurance |

## Fitur Utama

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

## Tech Stack

| Bagian | Teknologi |
| :--- | :--- |
| Framework | Laravel 13 |
| Bahasa | PHP 8.3+ |
| Template | Blade |
| Dashboard Admin | AdminLTE 3, Bootstrap 4, jQuery |
| Database | MySQL atau MariaDB |
| Testing Laravel | PHPUnit |
| Testing Browser | Python, Pytest, Playwright |
| Local process runner | npm script dengan `concurrently` |
| Container | Docker / Laravel Sail |

Catatan: proyek ini tidak bergantung pada Vite/Tailwind untuk tampilan aktif. `npm run dev` dipakai sebagai process runner lokal untuk menjalankan server, queue listener, dan log watcher.

## Instalasi

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

## Akun Admin

Akun admin dibuat melalui seeder. Periksa `database/seeders/DatabaseSeeder.php` untuk kredensial bawaan yang sedang aktif.

## Struktur Folder

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
  vendor/                 Aset vendor AdminLTE/Bootstrap/jQuery
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

## Modul Data

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

## Testing

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

## Catatan Pengembangan

- Gunakan `app/Services/IndonesianTextFormatter.php` sebagai pusat normalisasi teks.
- Jangan menduplikasi aturan kapitalisasi di controller atau view.
- Modul prestasi memakai kategori sebagai sumber yang sama untuk admin dan halaman publik.
- Modul guru/staf memakai model `GuruStaff` dengan pembeda `tipe`.
- Modul kelas dan siswa dibuat dinamis, bukan daftar kelas statis.
- Setelah mengubah route, controller, migration, atau formatter, jalankan test yang relevan.

## Deployment

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

## Lisensi

Proyek ini menggunakan lisensi MIT. Lihat [LICENSE](LICENSE) untuk detailnya.
