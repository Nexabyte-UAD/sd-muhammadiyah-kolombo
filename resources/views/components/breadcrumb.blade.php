{{--
    Komponen Navigasi Jejak Halaman / Breadcrumb (components/breadcrumb.blade.php)
    Menampilkan lokasi navigasi halaman saat ini (misal: Home > Tentang Sekolah) pada frontend
    untuk membantu user memahami letak halaman yang dikunjungi.
--}}
<div class="bg-white pt-5 pb-2">
    <div class="container">
        <div class="rounded d-flex align-items-center w-100 px-4 py-2" style="background-color: #f4f4f5; font-size: 0.875rem;">
            <a href="{{ route('home') }}" class="text-primary text-decoration-none d-flex align-items-center gap-2">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor" class="text-primary flex-shrink-0" aria-hidden="true">
                    <path d="M8.707 1.5a1 1 0 0 0-1.414 0L.646 8.146a.5.5 0 0 0 .708.708L8 2.207l6.646 6.647a.5.5 0 0 0 .708-.708L13 5.793V2.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1.293z"/>
                    <path d="m8 3.293 6 6V13.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5V9.293z"/>
                </svg>
                Home
            </a>
            <x-admin-icon name="circle" size="6" class="text-warning mx-3"/>
            <span class="text-secondary">{{ $slot }}</span>
        </div>
    </div>
</div>
