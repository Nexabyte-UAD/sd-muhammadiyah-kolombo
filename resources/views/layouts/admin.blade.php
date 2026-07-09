<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin') - {{ config('app.name', 'Sekolah') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-sd-muhammadiyah-kolombo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-panel.css') }}?v={{ time() }}">
    <script>
        (function() {
            const theme = localStorage.getItem('admin-theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>
    @stack('styles')
</head>
<body class="admin-body">
    <div class="admin-shell">
        @include('layouts.admin.sidebar')

        <div class="admin-main">
            @include('layouts.admin.header')

            <main class="admin-content">
                <div class="admin-container">
                    @if(trim($__env->yieldContent('content_header')) !== '' && trim($__env->yieldContent('page_title')) === '')
                        <div class="legacy-page-heading">
                            @yield('content_header')
                        </div>
                    @else
                        <div class="page-heading">
                            <div>
                                <div class="page-kicker">@yield('page_kicker', 'Panel Admin')</div>
                                <h1 class="page-title">{!! trim($__env->yieldContent('page_title', $__env->yieldContent('title', 'Dashboard'))) !!}</h1>
                                @hasSection('page_description')
                                    <p class="page-description">@yield('page_description')</p>
                                @endif
                            </div>
                            <div class="page-actions">
                                @yield('page_actions')
                            </div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="admin-alert admin-alert-success" role="alert">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="admin-alert admin-alert-danger" role="alert">{{ session('error') }}</div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div class="sidebar-backdrop" data-sidebar-close></div>
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/admin-panel.js') }}"></script>
    @stack('js')
    @stack('scripts')

    <!-- Reusable Admin Confirmation Modal -->
    <div id="confirm-delete-modal" class="admin-modal-overlay" style="display: none;">
        <div class="admin-modal-card">
            <div class="admin-modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="admin-modal-title">Konfirmasi Hapus</h3>
            <p class="admin-modal-message" id="confirm-modal-message">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="admin-modal-actions">
                <button type="button" class="btn-modal btn-modal-secondary" id="confirm-modal-cancel">Batal</button>
                <button type="button" class="btn-modal btn-modal-danger" id="confirm-modal-submit">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <!-- Reusable Session Timeout Confirmation Modal -->
    <div id="session-timeout-modal" class="admin-modal-overlay" style="display: none;">
        <div class="admin-modal-card">
            <div class="admin-modal-icon" style="background: #eff6ff; color: #3b82f6;">
                <i class="fas fa-clock"></i>
            </div>
            <h3 class="admin-modal-title">Sesi Akan Berakhir</h3>
            <p class="admin-modal-message">Sesi Anda akan segera berakhir dalam <strong id="session-countdown-timer">02:00</strong> karena tidak ada aktivitas. Apakah Anda ingin memperpanjang sesi?</p>
            <div class="admin-modal-actions">
                <button type="button" class="btn-modal btn-modal-secondary" id="session-logout-btn">Keluar</button>
                <button type="button" class="btn-modal btn-modal-primary" id="session-extend-btn">Lanjutkan Sesi</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const confirmModal = document.getElementById('confirm-delete-modal');
            const modalMessage = document.getElementById('confirm-modal-message');
            const btnCancel = document.getElementById('confirm-modal-cancel');
            const btnSubmit = document.getElementById('confirm-modal-submit');
            let formToSubmit = null;

            // Track form dirty states
            window.isFormDirty = false;

            document.addEventListener('input', function(event) {
                if (event.target.closest('form')) {
                    window.isFormDirty = true;
                }
            });

            document.addEventListener('change', function(event) {
                if (event.target.closest('form')) {
                    window.isFormDirty = true;
                }
            });

            // Reset dirty state on form submissions
            document.addEventListener('submit', function(event) {
                window.isFormDirty = false;
            });

            // 1. Intercept click on submit buttons for forms using native onsubmit confirm()
            document.addEventListener('click', function (event) {
                const submitBtn = event.target.closest('button[type="submit"], input[type="submit"]');
                if (submitBtn) {
                    const form = submitBtn.closest('form');
                    if (form && form.dataset.confirmed !== 'true') {
                        const onsubmitAttr = form.getAttribute('onsubmit');
                        if (onsubmitAttr && onsubmitAttr.includes('confirm(')) {
                            event.preventDefault();
                            
                            // Extract message
                            let msg = 'Apakah Anda yakin ingin melakukan tindakan ini?';
                            const match = onsubmitAttr.match(/confirm\(['"](.+?)['"]\)/);
                            if (match && match[1]) {
                                msg = match[1];
                            }
                            
                            // Customize modal structure back to Delete/Logout mode
                            const modalTitle = confirmModal.querySelector('.admin-modal-title');
                            const modalIcon = confirmModal.querySelector('.admin-modal-icon');
                            const modalSubmit = document.getElementById('confirm-modal-submit');
                            
                            if (form.id === 'logout-form') {
                                modalTitle.textContent = 'Konfirmasi Keluar';
                                modalMessage.textContent = msg;
                                
                                modalIcon.innerHTML = '<i class="fas fa-sign-out-alt"></i>';
                                modalIcon.style.background = '#fcebea';
                                modalIcon.style.color = '#d63939';
                                
                                modalSubmit.textContent = 'Ya, Keluar';
                                modalSubmit.className = 'btn-modal btn-modal-danger';
                            } else if (form.id === 'account-form') {
                                modalTitle.textContent = 'Perbarui Keamanan Akun';
                                modalMessage.textContent = msg;
                                
                                modalIcon.innerHTML = '<i class="fas fa-user-shield"></i>';
                                modalIcon.style.background = '#eaf2fb';
                                modalIcon.style.color = '#1d4ed8';
                                
                                modalSubmit.textContent = 'Ya, Simpan';
                                modalSubmit.className = 'btn-modal btn-modal-primary';
                            } else if (form.id === 'settings-form') {
                                modalTitle.textContent = 'Perbarui Pengaturan';
                                modalMessage.textContent = msg;
                                
                                modalIcon.innerHTML = '<i class="fas fa-cog"></i>';
                                modalIcon.style.background = '#fff4d6';
                                modalIcon.style.color = '#b57600';
                                
                                modalSubmit.textContent = 'Ya, Simpan';
                                modalSubmit.className = 'btn-modal btn-modal-primary';
                            } else if (form.classList.contains('restore-form')) {
                                modalTitle.textContent = 'Pulihkan Siswa';
                                modalMessage.textContent = msg;
                                
                                modalIcon.innerHTML = '<i class="fas fa-history"></i>';
                                modalIcon.style.background = '#eaf7ec';
                                modalIcon.style.color = '#2fb344';
                                
                                modalSubmit.textContent = 'Ya, Pulihkan';
                                modalSubmit.className = 'btn-modal btn-modal-primary';
                            } else {
                                modalTitle.textContent = 'Konfirmasi Tindakan';
                                modalMessage.textContent = msg;
                                
                                modalIcon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                                modalIcon.style.background = '#fef2f2';
                                modalIcon.style.color = '#ef4444';
                                
                                modalSubmit.textContent = 'Ya, Lakukan';
                                modalSubmit.className = 'btn-modal btn-modal-danger';
                            }
                            
                            confirmModal.dataset.mode = 'delete';
                            formToSubmit = form;
                            
                            confirmModal.style.display = 'flex';
                        }
                    }
                }
            });

            // 2. Intercept submit on forms for delete or berita publish confirmation
            document.addEventListener('submit', function (event) {
                const form = event.target;
                
                // Check if this is a news form (action contains admin/berita) and status is published
                const statusSelect = form.querySelector('select[name="status"]');
                if (statusSelect && statusSelect.value === 'published' && form.action.includes('admin/berita')) {
                    if (form.dataset.confirmed === 'true') {
                        return;
                    }
                    event.preventDefault();
                    formToSubmit = form;
                    
                    // Customize modal structure to Publish mode
                    const modalTitle = confirmModal.querySelector('.admin-modal-title');
                    const modalIcon = confirmModal.querySelector('.admin-modal-icon');
                    const modalSubmit = document.getElementById('confirm-modal-submit');
                    
                    modalTitle.textContent = 'Terbitkan Berita?';
                    modalMessage.textContent = 'Berita ini akan langsung diterbitkan secara publik di website utama sekolah. Lanjutkan?';
                    
                    modalIcon.innerHTML = '<i class="fas fa-bullhorn" style="color: #206bc4;"></i>';
                    modalIcon.style.background = '#eaf2fb';
                    
                    modalSubmit.textContent = 'Ya, Terbitkan';
                    modalSubmit.className = 'btn-modal btn-modal-primary';
                    
                    confirmModal.dataset.mode = 'publish';
                    confirmModal.style.display = 'flex';
                    return;
                }

                if (form.classList.contains('delete-form')) {
                    if (form.dataset.confirmed === 'true') {
                        return;
                    }
                    event.preventDefault();
                    formToSubmit = form;
                    const customMessage = form.dataset.message || 'Apakah Anda yakin ingin menghapus data ini?';
                    
                    // Reset modal structure to Delete mode
                    const modalTitle = confirmModal.querySelector('.admin-modal-title');
                    const modalIcon = confirmModal.querySelector('.admin-modal-icon');
                    const modalSubmit = document.getElementById('confirm-modal-submit');
                    
                    modalTitle.textContent = 'Konfirmasi Hapus';
                    modalIcon.innerHTML = '<i class="fas fa-exclamation-triangle"></i>';
                    modalIcon.style.background = '#fef2f2';
                    modalSubmit.textContent = 'Ya, Hapus';
                    modalSubmit.className = 'btn-modal btn-modal-danger';
                    
                    confirmModal.dataset.mode = 'delete';
                    modalMessage.textContent = customMessage;
                    
                    confirmModal.style.display = 'flex';
                }
            });

            // 3. Intercept click on btn-cancel links when form is dirty
            document.addEventListener('click', function(event) {
                const cancelLink = event.target.closest('.btn-cancel');
                if (cancelLink && window.isFormDirty) {
                    event.preventDefault();
                    
                    // Customize modal structure to Discard mode
                    const modalTitle = confirmModal.querySelector('.admin-modal-title');
                    const modalIcon = confirmModal.querySelector('.admin-modal-icon');
                    const modalSubmit = document.getElementById('confirm-modal-submit');
                    
                    modalTitle.textContent = 'Buang Perubahan?';
                    modalMessage.textContent = 'Ada perubahan yang belum disimpan. Apakah Anda yakin ingin membatalkan dan membuang perubahan ini?';
                    
                    modalIcon.innerHTML = '<i class="fas fa-exclamation-circle" style="color: #d97706;"></i>';
                    modalIcon.style.background = '#fef3c7';
                    
                    modalSubmit.textContent = 'Ya, Buang';
                    modalSubmit.className = 'btn-modal btn-modal-danger';
                    
                    confirmModal.dataset.mode = 'discard';
                    confirmModal.dataset.redirectUrl = cancelLink.href;
                    
                    confirmModal.style.display = 'flex';
                }
            });

            // Cancel click
            btnCancel.addEventListener('click', function () {
                confirmModal.style.display = 'none';
                formToSubmit = null;
            });

            // Overlay click to close
            confirmModal.addEventListener('click', function (event) {
                if (event.target === confirmModal) {
                    confirmModal.style.display = 'none';
                    formToSubmit = null;
                }
            });

            // Confirm submit click
            btnSubmit.addEventListener('click', function () {
                if (confirmModal.dataset.mode === 'discard') {
                    window.isFormDirty = false;
                    window.location.href = confirmModal.dataset.redirectUrl;
                } else if (formToSubmit) {
                    // Temporarily remove onsubmit to prevent triggering it during programmatic submit
                    const originalOnsubmit = formToSubmit.getAttribute('onsubmit');
                    formToSubmit.removeAttribute('onsubmit');
                    
                    formToSubmit.dataset.confirmed = 'true';
                    formToSubmit.submit();
                    
                    // Restore just in case
                    if (originalOnsubmit) {
                        formToSubmit.setAttribute('onsubmit', originalOnsubmit);
                    }
                }
            });

            // --- IDLE TIMEOUT SESSION SYSTEM ---
            const idleTimeoutMinutes = {{ config('auth.admin_idle_timeout', 30) }};
            const idleTimeoutMs = idleTimeoutMinutes * 60 * 1000;
            const warningBeforeMs = 2 * 60 * 1000; // 2 minutes warning
            const warningTimeMs = idleTimeoutMs > warningBeforeMs ? idleTimeoutMs - warningBeforeMs : idleTimeoutMs / 2;

            let idleTimer = null;
            let countdownInterval = null;
            let lastServerPingTime = Date.now();
            let isPinging = false;
            const serverPingIntervalMs = 5 * 60 * 1000; // 5 minutes

            const sessionModal = document.getElementById('session-timeout-modal');
            const countdownTimer = document.getElementById('session-countdown-timer');
            const extendBtn = document.getElementById('session-extend-btn');
            const logoutBtn = document.getElementById('session-logout-btn');

            function showSessionWarning() {
                if (sessionModal) {
                    sessionModal.style.display = 'flex';
                }

                let remainingSeconds = Math.round((idleTimeoutMs - warningTimeMs) / 1000);
                updateCountdownDisplay(remainingSeconds);

                if (countdownInterval) clearInterval(countdownInterval);
                countdownInterval = setInterval(function () {
                    remainingSeconds--;
                    if (remainingSeconds <= 0) {
                        clearInterval(countdownInterval);
                        logoutUser();
                    } else {
                        updateCountdownDisplay(remainingSeconds);
                    }
                }, 1000);
            }

            function updateCountdownDisplay(seconds) {
                if (countdownTimer) {
                    const mins = Math.floor(seconds / 60);
                    const secs = seconds % 60;
                    countdownTimer.textContent = 
                        (mins < 10 ? '0' + mins : mins) + ':' + (secs < 10 ? '0' + secs : secs);
                }
            }

            function extendSession() {
                // Hide modal
                if (sessionModal) {
                    sessionModal.style.display = 'none';
                }
                if (countdownInterval) clearInterval(countdownInterval);

                isPinging = true;
                lastServerPingTime = Date.now();

                // Ping server to update session activity
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                fetch("{{ route('admin.ping') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                })
                .then(response => response.json())
                .then(data => {
                    isPinging = false;
                    resetIdleTimer();
                })
                .catch(error => {
                    console.error('Error extending session:', error);
                    isPinging = false;
                    resetIdleTimer();
                });
            }

            function logoutUser() {
                const logoutForm = document.getElementById('logout-form');
                if (logoutForm) {
                    logoutForm.submit();
                } else {
                    window.location.href = "{{ route('login') }}";
                }
            }

            function resetIdleTimer() {
                // If warning modal is visible, do not auto-extend from user actions
                if (sessionModal && sessionModal.style.display === 'flex') {
                    return;
                }

                if (idleTimer) clearTimeout(idleTimer);
                idleTimer = setTimeout(showSessionWarning, warningTimeMs);

                // Periodic background ping to keep session alive during continuous local activity
                const now = Date.now();
                if (!isPinging && (now - lastServerPingTime > serverPingIntervalMs)) {
                    isPinging = true;
                    lastServerPingTime = now;
                    
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    fetch("{{ route('admin.ping') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        isPinging = false;
                    })
                    .catch(err => {
                        console.error('Error pinging server:', err);
                        isPinging = false;
                    });
                }
            }

            if (extendBtn) extendBtn.addEventListener('click', extendSession);
            if (logoutBtn) logoutBtn.addEventListener('click', logoutUser);

            // Listen to local user activities
            const activityEvents = ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'];
            activityEvents.forEach(function (eventName) {
                document.addEventListener(eventName, resetIdleTimer, { passive: true });
            });

            // Start the timer initially
            resetIdleTimer();
        });
    </script>
</body>
</html>
