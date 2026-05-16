<div class="bg-white pt-5 pb-2">
    <div class="container">
        <div class="rounded d-flex align-items-center w-100 px-4 py-2" style="background-color: #f4f4f5; font-size: 0.875rem;">
            <a href="{{ route('home') }}" class="text-primary text-decoration-none d-flex align-items-center gap-2">
                <i class="bi bi-house-door-fill text-primary"></i> Home
            </a>
            <i class="bi bi-circle text-warning mx-3" style="font-size: 0.4rem; border-width: 2px;"></i>
            <span class="text-secondary">{{ $slot }}</span>
        </div>
    </div>
</div>
