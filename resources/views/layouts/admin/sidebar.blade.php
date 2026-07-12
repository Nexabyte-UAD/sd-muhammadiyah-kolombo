{{--
    Sidebar Panel Admin (layouts/admin/sidebar.blade.php)
    Menampilkan sidebar menu navigasi panel admin untuk akses CRUD Siswa, Alumni, Kelas,
    Kenaikan Kelas, Berita, Prestasi, Ekstrakurikuler, Guru & Staf, Profil Sekolah,
    Pesan Masuk, Profil Akun Admin, Konfigurasi Sistem, dan tombol Keluar (Logout).
--}}
<aside class="admin-sidebar" id="adminSidebar" aria-label="Navigasi admin">
    <!-- Floating Arrow Toggle Button for Desktop -->
    <button type="button" class="sidebar-toggle-btn" data-sidebar-toggle aria-label="Toggle Sidebar">
        <x-admin-icon name="chevron-left" size="11" class="sidebar-chevron-left"/>
        <x-admin-icon name="chevron-right" size="11" class="sidebar-chevron-right"/>
    </button>

    <div class="admin-brand-wrapper">
        <a href="{{ route('dashboard') }}" class="admin-brand">
            <img src="{{ asset('images/logo-sd-muhammadiyah-kolombo.png') }}"
                 class="admin-brand-logo"
                 alt="Logo SD Muhammadiyah Komplek Kolombo">
            <span>
                <strong>Admin Sekolah</strong>
                <small>Komplek Kolombo</small>
            </span>
        </a>
    </div>

    <nav class="admin-nav">
        <a href="{{ route('dashboard') }}" class="admin-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" data-tooltip="Dashboard">
            <x-admin-icon name="dashboard"/>
            <span>Dashboard</span>
        </a>

        <div class="admin-nav-label">Akademik</div>
        <a href="{{ route('admin.siswa.index') }}" class="admin-nav-link {{ request()->routeIs('admin.siswa.*') && !request()->routeIs('admin.siswa.promote.*') ? 'active' : '' }}" data-tooltip="Data Siswa">
            <x-admin-icon name="students"/>
            <span>Data Siswa</span>
        </a>
        <a href="{{ route('admin.alumni.index') }}" class="admin-nav-link {{ request()->routeIs('admin.alumni.*') ? 'active' : '' }}" data-tooltip="Data Alumni">
            <x-admin-icon name="graduation"/>
            <span>Data Alumni</span>
        </a>
        <a href="{{ route('admin.kelas.index') }}" class="admin-nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}" data-tooltip="Data Kelas">
            <x-admin-icon name="classes"/>
            <span>Data Kelas</span>
        </a>
        <a href="{{ route('admin.siswa.promote.page') }}" class="admin-nav-link {{ request()->routeIs('admin.siswa.promote.*') ? 'active' : '' }}" data-tooltip="Kenaikan Kelas">
            <x-admin-icon name="activity"/>
            <span>Kenaikan Kelas</span>
        </a>

        <div class="admin-nav-label">Konten Website</div>
        <a href="{{ route('admin.berita.index') }}" class="admin-nav-link {{ request()->routeIs('admin.berita.*') ? 'active' : '' }}" data-tooltip="Berita">
            <x-admin-icon name="news"/>
            <span>Berita</span>
        </a>
        <a href="{{ route('admin.prestasi.index') }}" class="admin-nav-link {{ request()->routeIs('admin.prestasi.*') ? 'active' : '' }}" data-tooltip="Prestasi">
            <x-admin-icon name="award"/>
            <span>Prestasi</span>
        </a>
        <a href="{{ route('admin.ekstrakurikuler.index') }}" class="admin-nav-link {{ request()->routeIs('admin.ekstrakurikuler.*') ? 'active' : '' }}" data-tooltip="Ekstrakurikuler">
            <x-admin-icon name="ekstrakurikuler"/>
            <span>Ekstrakurikuler</span>
        </a>
        <details class="admin-nav-group" @if(request()->routeIs('admin.guru-staff.*')) open @endif>
            <summary class="admin-nav-link {{ request()->routeIs('admin.guru-staff.*') ? 'active' : '' }}" data-tooltip="Guru & Staf">
                <x-admin-icon name="guru_staff"/>
                <span>Guru & Staf</span>
                <x-admin-icon name="arrow-right" size="15" class="nav-group-chevron"/>
            </summary>
            <div class="admin-nav-children">
                <a href="{{ route('admin.guru-staff.index', ['tipe' => 'guru']) }}"
                   class="{{ request('tipe', 'guru') === 'guru' && request()->routeIs('admin.guru-staff.*') ? 'active' : '' }}">Data Guru</a>
                <a href="{{ route('admin.guru-staff.index', ['tipe' => 'staf']) }}"
                   class="{{ request('tipe') === 'staf' && request()->routeIs('admin.guru-staff.*') ? 'active' : '' }}">Data Staf</a>
            </div>
        </details>
        <details class="admin-nav-group" @if(request()->routeIs('admin.profil-sekolah.*')) open @endif>
            <summary class="admin-nav-link {{ request()->routeIs('admin.profil-sekolah.*') ? 'active' : '' }}" data-tooltip="Profil Sekolah">
                <x-admin-icon name="school"/>
                <span>Profil Sekolah</span>
                <x-admin-icon name="arrow-right" size="15" class="nav-group-chevron"/>
            </summary>
            <div class="admin-nav-children">
                <a href="{{ route('admin.profil-sekolah.editType', 'tentang') }}"
                   class="{{ request()->routeIs('admin.profil-sekolah.*') && request()->route('type') === 'tentang' ? 'active' : '' }}">Tentang Sekolah</a>
                <a href="{{ route('admin.profil-sekolah.editType', 'sambutan') }}"
                   class="{{ request()->routeIs('admin.profil-sekolah.*') && request()->route('type') === 'sambutan' ? 'active' : '' }}">Kata Sambutan</a>
                <a href="{{ route('admin.profil-sekolah.editType', 'visi_misi') }}"
                   class="{{ request()->routeIs('admin.profil-sekolah.*') && request()->route('type') === 'visi_misi' ? 'active' : '' }}">Visi & Misi</a>
                <a href="{{ route('admin.profil-sekolah.editType', 'akreditasi') }}"
                   class="{{ request()->routeIs('admin.profil-sekolah.*') && request()->route('type') === 'akreditasi' ? 'active' : '' }}">Akreditasi</a>
            </div>
        </details>

        <div class="admin-nav-label">Sistem & Pesan</div>
        <a href="{{ route('admin.pesan.index') }}" class="admin-nav-link {{ request()->routeIs('admin.pesan.*') ? 'active' : '' }}" data-tooltip="Pesan Masuk">
            <x-admin-icon name="message"/>
            <span>Pesan Masuk</span>
        </a>
        <a href="{{ route('admin.account.edit') }}" class="admin-nav-link {{ request()->routeIs('admin.account.*') ? 'active' : '' }}" data-tooltip="Akun Admin">
            <x-admin-icon name="user"/>
            <span>Akun Admin</span>
        </a>
        <a href="{{ route('admin.settings.edit') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" data-tooltip="Pengaturan">
            <x-admin-icon name="settings"/>
            <span>Pengaturan</span>
        </a>
    </nav>

    <div class="admin-sidebar-footer">
        <a href="{{ route('admin.account.edit') }}" class="admin-sidebar-account" data-tooltip="Akun Admin">
            <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <div class="admin-account-copy">
                <strong>{{ auth()->user()->name ?? 'Administrator' }}</strong>
                <small>Administrator</small>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}" id="logout-form" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari panel admin?')">
            @csrf
            <button type="submit" class="admin-logout-button" data-tooltip="Keluar" aria-label="Keluar dari panel admin">
                <x-admin-icon name="logout" size="18"/>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
