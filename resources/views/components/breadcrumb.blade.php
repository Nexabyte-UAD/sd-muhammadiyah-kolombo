{{--
    Komponen Navigasi Jejak Halaman / Breadcrumb (components/breadcrumb.blade.php)
    Menampilkan lokasi navigasi halaman saat ini (misal: Home > Tentang Sekolah) pada frontend
    untuk membantu user memahami letak halaman yang dikunjungi.
--}}
<div class="bg-white pt-5 pb-2">
    <div class="container">
        <div class="rounded d-flex align-items-center w-100 px-4 py-2" style="background-color: #f4f4f5; font-size: 0.875rem;">
            <a href="{{ route('home') }}" class="text-primary text-decoration-none d-flex align-items-center gap-2">
                <x-admin-icon name="home" size="16" class="text-primary"/>
                Home
            </a>
            <x-admin-icon name="circle" size="6" class="text-warning mx-3"/>
            <span class="text-secondary">{{ $slot }}</span>
        </div>
    </div>
</div>
