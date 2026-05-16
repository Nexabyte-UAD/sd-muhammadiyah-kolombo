<div align="center">
  <h1>SD Muhammadiyah Kolombo Website</h1>
  <p>A modern, responsive, and dynamic web platform for SD Muhammadiyah Kolombo.</p>
</div>

---

## 📖 Deskripsi Project

Aplikasi ini adalah sistem informasi sekolah terpadu berbasis web yang dirancang khusus untuk **SD Muhammadiyah Kolombo**. Website ini memiliki dua antarmuka utama:
1. **Public Portal**: Menampilkan informasi seputar sekolah, profil, tenaga pendidik, berita, hingga pencapaian dan kegiatan ekstrakurikuler kepada masyarakat umum.
2. **Admin Dashboard**: Sistem Manajemen Konten (CMS) komprehensif bagi pihak internal sekolah untuk mengelola informasi, postingan, foto, dan konfigurasi website secara dinamis.

Proyek ini dibangun dengan memprioritaskan antarmuka yang modern, kecepatan akses, serta kemudahan dalam pengelolaan (User-Friendly).

---

## 👨‍💻 Tim Pengembang

Proyek ini dikembangkan oleh:

| Peran (Role) | Nama Lengkap | NIM |
| :--- | :--- | :--- |
| Project Manager | Dzaky Ridhwan Rosyada | 2300018398 |
| UI/UX Designer | Fauziyah Tahta Dirgantari | 2300018252 |
| Frontend Developer | M Ilham Nurdin | 2300018406 |
| Backend Developer | Aditya Bintang Rianda Syahputra | 2300018399 |
| Quality Assurance (Tester) | Trizana Wafi Reswara | 2300018258 |

---

## 📸 Screenshots

*(Tambahkan URL gambar screenshot halaman utama, halaman berita, dan tampilan dashboard AdminLTE di sini).*

- **Homepage**: `[Screenshot Placeholder]`
- **Admin Dashboard**: `[Screenshot Placeholder]`
- **Mobile View**: `[Screenshot Placeholder]`

---

## ✨ Features

- **Dynamic Public Portal**: 
  - Beranda (Hero section, statistik, sambutan)
  - Profil Singkat & Visi Misi
  - Daftar Guru & Tenaga Kependidikan
  - Direktori Berita & Artikel
  - Galeri Prestasi & Ekstrakurikuler
  - Formulir Kontak / Pesan
- **Admin Dashboard (AdminLTE 3)**:
  - Manajemen Pengguna (Role: Admin)
  - Pengelolaan Berita (CRUD)
  - Pengelolaan Guru & Staff (CRUD)
  - Pengelolaan Prestasi (CRUD)
  - Pengelolaan Ekstrakurikuler (CRUD)
  - Inbox Pesan dari Pengunjung
  - Pengaturan Website (Logo, Kontak, Teks Hero)
- **Modern UI/UX**: Tema profesional (Navy Blue), responsif di semua perangkat (Mobile, Tablet, Desktop).
- **Environment**: Terkonfigurasi untuk lokal dan *containerized development* dengan Docker.

---

## 🛠 Tech Stack

- **Backend / Framework**: [Laravel 11.x](https://laravel.com)
- **Frontend (Public)**: Vanilla CSS / TailwindCSS, Blade Templating
- **Frontend (Admin)**: [AdminLTE 3](https://adminlte.io), Bootstrap 4, jQuery
- **Database**: MySQL 8.x
- **Development Environment**: Docker & Laravel Sail (atau Laragon/XAMPP)

---

## 🚀 Installation

Website ini dapat dijalankan menggunakan **Docker** (direkomendasikan) atau **Non-Docker** (Laragon/XAMPP). 

### Opsi 1: Menggunakan Docker (Rekomendasi)
*Syarat: Docker Desktop harus terinstal dan berjalan.*

1. **Clone repository**
   ```bash
   git clone <repo-url>
   cd MuhammadiahKolombo
   ```
2. **Persiapkan Environment**
   ```bash
   cp .env.example .env
   ```
3. **Nyalakan Container & Build**
   ```bash
   docker-compose up -d --build
   ```
4. **Install Dependensi & Konfigurasi**
   ```bash
   docker-compose exec laravel.test composer install
   docker-compose exec laravel.test php artisan key:generate
   ```
5. **Migrasi Database & Seeding**
   ```bash
   docker-compose exec laravel.test php artisan migrate:fresh --seed
   ```
6. **Akses Website**
   Buka `http://localhost` di browser Anda.

### Opsi 2: Non-Docker (Laragon / XAMPP)
*Syarat: PHP 8.2+, Composer, dan MySQL/MariaDB.*

1. **Clone dan persiapkan repo** (sama seperti langkah 1 & 2 di atas).
2. **Install Dependensi**
   ```bash
   composer install
   ```
3. **Konfigurasi Database**
   Buka file `.env`, sesuaikan nama database, username, dan password:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=muhammadiahkolombo
   DB_USERNAME=root
   DB_PASSWORD=
   ```
4. **Generate Key & Migrasi**
   ```bash
   php artisan key:generate
   php artisan migrate:fresh --seed
   ```
5. **Jalankan Server Lokal**
   ```bash
   php artisan serve
   ```
   Akses di `http://127.0.0.1:8000`.

---

## 🔑 Admin Credentials (Demo)

Setelah menjalankan `php artisan migrate:fresh --seed`, Anda dapat mengakses halaman Admin Dashboard melalui rute `/login`.

- **Email**: `admin@sekolah.com`
- **Password**: `password`

*(Pastikan untuk mengubah password bawaan ini setelah aplikasi berada di tahap produksi).*

---

## 🌐 Deployment

Untuk melakukan deployment ke server production (VPS / Shared Hosting):
1. Pastikan server memiliki dukungan minimal PHP 8.2 dan ekstensi yang dibutuhkan.
2. Atur konfigurasi `.env`, khususnya:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://domainsekolah.com`
3. Optimalkan framework:
   ```bash
   composer install --optimize-autoloader --no-dev
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
4. Arahkan *Document Root* dari web server (Nginx/Apache) ke folder `public/`.

---

## 🏛 System Architecture

Arsitektur sistem mengikuti pola standar MVC (Model-View-Controller) bawaan Laravel:
- **Models**: Merepresentasikan tabel database dan relasi antar data (Eloquent ORM).
- **Views**: Blade template untuk merender antarmuka pengguna, dipisahkan antara `layouts/public` dan `layouts/admin`.
- **Controllers**: Mengendalikan *business logic* dan penghubung antara Model dan View.
- **Routing**: Dikelola secara terpisah untuk rute *public* (`web.php`) dan rute otentikasi/admin (`auth.php`).

---

## 🗄 Database / Modules

Aplikasi ini menggunakan modul-modul berikut untuk mengelola data:

1. **Users**: Manajemen akun administrator.
2. **ProfilSekolah**: Pengaturan data Visi & Misi dan profil panjang.
3. **GuruStaff**: Manajemen data tenaga pendidik dan staff administrasi.
4. **Berita**: Modul pengelolaan artikel, pengumuman, dan berita sekolah.
5. **Prestasi**: Katalog pencapaian siswa dan sekolah.
6. **Ekstrakurikuler**: Katalog kegiatan luar jam pelajaran.
7. **Pesan**: Menyimpan pesan/masukan dari pengunjung via formulir kontak.
8. **Setting**: Konfigurasi dinamis web (seperti Hero Image, Sambutan Kepala Sekolah, Kontak).
9. **ActivityLog**: Catatan log sistem/aktivitas.

---

## 📂 Folder Structure

Berikut adalah struktur folder utama dari aplikasi ini:

```text
/
├── app/
│   ├── Http/Controllers/     # Logika aplikasi (Admin & Public)
│   ├── Models/               # Representasi data (Berita, Guru, dll)
├── database/
│   ├── migrations/           # Skema tabel database
│   ├── seeders/              # Data awal untuk database (Admin)
├── public/                   # Aset publik (CSS, JS, Images, Uploads)
├── resources/
│   ├── views/
│   │   ├── admin/            # Tampilan dashboard AdminLTE
│   │   ├── layouts/          # Template induk (Public & Admin)
│   │   ├── pages/            # Tampilan public (Beranda, Berita, dll)
│   │   └── auth/             # Tampilan login
├── routes/
│   ├── web.php               # Routing untuk public portal
│   └── auth.php              # Routing untuk area otentikasi
├── compose.yaml              # Konfigurasi Docker (Laravel Sail)
└── .env                      # Variabel lingkungan dan koneksi DB
```

---

## 🗺 Roadmap

- [x] Konversi tema dari Hijau (Default) ke Navy Blue.
- [x] Setup environment pengembangan menggunakan Docker.
- [ ] Integrasi fitur export Laporan Pendaftaran/Pesan (PDF/Excel).
- [ ] Penambahan fitur Galeri Foto/Video.
- [ ] Peningkatan SEO untuk halaman profil dan artikel berita.

---

## 📄 License

Proyek ini dilisensikan di bawah **MIT License**. Lihat file [LICENSE](LICENSE) untuk informasi lebih lanjut.
