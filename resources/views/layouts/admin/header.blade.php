{{--
    Header Panel Admin (layouts/admin/header.blade.php)
    Menampilkan header bagian atas panel admin yang memiliki tombol toggle sidebar,
    fitur switch tema gelap (dark mode), dan tautan cepat ke halaman publik sekolah.
--}}
<header class="admin-header">
    <button type="button" class="icon-button sidebar-toggle" data-sidebar-toggle aria-controls="adminSidebar" aria-expanded="false" aria-label="Toggle Sidebar">
        <x-admin-icon name="menu" size="22" class="sidebar-menu-icon"/>
        <x-admin-icon name="sidebar-close" size="22" class="sidebar-chevron-left"/>
        <x-admin-icon name="sidebar-open" size="22" class="sidebar-chevron-right"/>
    </button>

    <div class="admin-header-spacer"></div>

    <button type="button" class="icon-button theme-toggle-btn" id="theme-toggle" aria-label="Ganti tema" style="margin-right: 14px;">
        <x-admin-icon name="sun" size="18" class="theme-icon-light" style="display: none;"/>
        <x-admin-icon name="moon" size="18" class="theme-icon-dark"/>
    </button>

    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="header-site-link">
        <x-admin-icon name="external" size="18"/>
        <span>Lihat Website</span>
    </a>
</header>
