<header class="admin-header">
    <button type="button" class="icon-button sidebar-toggle" data-sidebar-toggle aria-controls="adminSidebar" aria-expanded="false" aria-label="Buka navigasi">
        <x-admin-icon name="menu" size="22"/>
    </button>

    <div class="admin-header-spacer"></div>

    <a href="{{ route('home') }}" target="_blank" rel="noopener" class="header-site-link">
        <x-admin-icon name="external" size="18"/>
        <span>Lihat Website</span>
    </a>

    <div class="admin-account">
        <a href="{{ route('admin.account.edit') }}" class="admin-account-link" title="Buka akun admin">
            <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
            <div class="admin-account-copy">
                <strong>{{ auth()->user()->name ?? 'Administrator' }}</strong>
                <small>Administrator</small>
            </div>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="icon-button" title="Keluar" aria-label="Keluar">
                <x-admin-icon name="logout" size="19"/>
            </button>
        </form>
    </div>
</header>
