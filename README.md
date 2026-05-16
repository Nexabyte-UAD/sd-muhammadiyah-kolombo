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

| Peran (Role) | Nama Lengkap | NIM | Kontribusi |
| :--- | :--- | :--- | :--- |
| Project Manager | Dzaky Ridhwan Rosyada | 2300018398 | Project Planning & Coordination |
| UI/UX Designer | Fauziyah Tahta Dirgantari | 2300018252 | Wireframe & Design System |
| Frontend Developer | M Ilham Nurdin | 2300018406 | Frontend Development |
| Backend Developer | Aditya Bintang Rianda Syahputra | 2300018399 | Backend, Database & Docker |
| Quality Assurance (Tester) | Trizana Wafi Reswara | 2300018258 | Testing & Quality Assurance |

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
   git clone https://github.com/Nexabyte-UAD/sd-muhammadiyah-kolombo.git
   cd sd-muhammadiyah-kolombo
   ```
2. **Persiapkan Environment**
   ```bash
   cp .env.example .env
   ```
3. **Install Dependensi Awal (Composer)**
   Karena konfigurasi Docker dari Laravel Sail berada di dalam folder `vendor` yang disembunyikan dari GitHub, Anda harus menginstal dependensinya dulu via container composer sementara:
   ```bash
   docker run --rm -v "%cd%:/var/www/html" -w /var/www/html laravelsail/php82-composer:latest composer install --ignore-platform-reqs
   ```
   *(Catatan: Jika memakai Mac/Linux/WSL, ganti `"%cd%"` menjadi `"$(pwd)"`)*

4. **Nyalakan Container & Build**
   ```bash
   docker-compose up -d --build
   ```
5. **Konfigurasi & Migrasi Database**
   ```bash
   docker-compose exec laravel.test php artisan key:generate
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
   - **Penting:** Buat database kosong terlebih dahulu di phpMyAdmin / HeidiSQL Anda (misalnya dengan nama `muhammadiahkolombo`).
   - Buka file `.env`, lalu sesuaikan koneksinya:
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

## 📄 License

Proyek ini dilisensikan di bawah **MIT License**. Lihat file [LICENSE](LICENSE) untuk informasi lebih lanjut.
