<header class="admin-header">
    <button type="button" class="icon-button sidebar-toggle" data-sidebar-toggle aria-controls="adminSidebar" aria-expanded="false" aria-label="Buka navigasi">
        <x-admin-icon name="menu" size="22"/>
    </button>

    <div class="admin-header-spacer"></div>

    <button type="button" class="icon-button theme-toggle-btn" id="theme-toggle" aria-label="Ganti tema" style="margin-right: 14px;">
        <i class="fas fa-sun theme-icon-light" style="display: none;"></i>
        <i class="fas fa-moon theme-icon-dark"></i>
    </button>

    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="header-site-link">
        <x-admin-icon name="external" size="18"/>
        <span>Lihat Website</span>
    </a>
</header>
