<aside class="admin-sidebar" id="adminSidebar" aria-label="Navigasi admin">
    <a href="{{ route('dashboard') }}" class="admin-brand">
        <img src="{{ asset('images/logo-sd-muhammadiyah-kolombo.png') }}"
             class="admin-brand-logo"
             alt="Logo SD Muhammadiyah Komplek Kolombo">
        <span>
            <strong>Admin Sekolah</strong>
            <small>Komplek Kolombo</small>
        </span>
    </a>

    <nav class="admin-nav">
        <a href="{{ route('dashboard') }}" class="admin-nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <x-admin-icon name="dashboard"/>
            <span>Dashboard</span>
        </a>

        <div class="admin-nav-label">Akademik</div>
        <a href="{{ route('admin.siswa.index') }}" class="admin-nav-link {{ request()->routeIs('admin.siswa.*') && !request()->routeIs('admin.siswa.promote.*') ? 'active' : '' }}">
            <x-admin-icon name="students"/>
            <span>Data Siswa</span>
        </a>
        <a href="{{ route('admin.kelas.index') }}" class="admin-nav-link {{ request()->routeIs('admin.kelas.*') ? 'active' : '' }}">
            <x-admin-icon name="classes"/>
            <span>Data Kelas</span>
        </a>
        <a href="{{ route('admin.siswa.promote.page') }}" class="admin-nav-link {{ request()->routeIs('admin.siswa.promote.*') ? 'active' : '' }}">
            <x-admin-icon name="activity"/>
            <span>Kenaikan Kelas</span>
        </a>

        <div class="admin-nav-label">Konten Website</div>
        <a href="{{ route('admin.berita.index') }}" class="admin-nav-link {{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
            <x-admin-icon name="news"/>
            <span>Berita</span>
        </a>
        <a href="{{ route('admin.prestasi.index') }}" class="admin-nav-link {{ request()->routeIs('admin.prestasi.*') ? 'active' : '' }}">
            <x-admin-icon name="award"/>
            <span>Prestasi</span>
        </a>
        <a href="{{ route('admin.ekstrakurikuler.index') }}" class="admin-nav-link {{ request()->routeIs('admin.ekstrakurikuler.*') ? 'active' : '' }}">
            <x-admin-icon name="activity"/>
            <span>Ekstrakurikuler</span>
        </a>
        <details class="admin-nav-group" @if(request()->routeIs('admin.guru-staff.*')) open @endif>
            <summary class="admin-nav-link {{ request()->routeIs('admin.guru-staff.*') ? 'active' : '' }}">
                <x-admin-icon name="users"/>
                <span>Guru &amp; Staf</span>
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
            <summary class="admin-nav-link {{ request()->routeIs('admin.profil-sekolah.*') ? 'active' : '' }}">
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
                   class="{{ request()->routeIs('admin.profil-sekolah.*') && request()->route('type') === 'visi_misi' ? 'active' : '' }}">Visi &amp; Misi</a>
                <a href="{{ route('admin.profil-sekolah.editType', 'akreditasi') }}"
                   class="{{ request()->routeIs('admin.profil-sekolah.*') && request()->route('type') === 'akreditasi' ? 'active' : '' }}">Akreditasi</a>
            </div>
        </details>

        <div class="admin-nav-label">Pengelolaan</div>
        <a href="{{ route('admin.pesan.index') }}" class="admin-nav-link {{ request()->routeIs('admin.pesan.*') ? 'active' : '' }}">
            <x-admin-icon name="message"/>
            <span>Pesan Masuk</span>
        </a>
        <a href="{{ route('admin.settings.edit') }}" class="admin-nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
            <x-admin-icon name="settings"/>
            <span>Pengaturan</span>
        </a>
        <a href="{{ route('admin.account.edit') }}" class="admin-nav-link {{ request()->routeIs('admin.account.*') ? 'active' : '' }}">
            <x-admin-icon name="users"/>
            <span>Akun Admin</span>
        </a>
    </nav>

    <div class="admin-sidebar-footer">
        <a href="{{ route('admin.account.edit') }}" class="admin-sidebar-account" title="Buka akun admin">
            <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <div class="admin-account-copy">
                <strong>{{ auth()->user()->name ?? 'Administrator' }}</strong>
                <small>Administrator</small>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="admin-logout-button" title="Keluar" aria-label="Keluar dari panel admin">
                <x-admin-icon name="logout" size="18"/>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
