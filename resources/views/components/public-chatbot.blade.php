<style>
    /* Chatbot Widget Styles */
    .chatbot-widget-container {
        position: fixed;
        bottom: max(24px, env(safe-area-inset-bottom));
        right: max(24px, env(safe-area-inset-right));
        z-index: 9999;
        font-family: 'Inter', sans-serif;
        --chatbot-primary: #1d4ed8;
        --chatbot-primary-dark: #1e3a8a;
        --chatbot-soft: #eff6ff;
    }

    .chatbot-widget-container,
    .chatbot-widget-container * {
        box-sizing: border-box;
    }

    .chatbot-launcher-btn {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        background: var(--chatbot-primary);
        border: 1px solid rgba(255, 255, 255, 0.35);
        color: white;
        box-shadow: 0 10px 24px rgba(29, 78, 216, 0.28), 0 2px 6px rgba(15, 23, 42, 0.12);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
    }

    .chatbot-launcher-btn:hover {
        background: var(--chatbot-primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 13px 28px rgba(29, 78, 216, 0.34), 0 3px 8px rgba(15, 23, 42, 0.14);
    }

    .chatbot-launcher-btn:active {
        transform: translateY(0);
        box-shadow: 0 7px 18px rgba(29, 78, 216, 0.28);
    }

    .chatbot-launcher-btn:focus-visible {
        outline: 3px solid rgba(37, 99, 235, 0.28);
        outline-offset: 4px;
    }

    .chatbot-launcher-btn svg {
        width: 26px;
        height: 26px;
        fill: currentColor;
        transition: none;
    }

    .chatbot-panel {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: min(390px, calc(100vw - 32px));
        height: min(590px, calc(100vh - 128px));
        height: min(590px, calc(100dvh - 128px));
        background-color: #ffffff;
        border: 1px solid #dbe1ea;
        border-radius: 14px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transform: scale(0.9) translateY(20px);
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .chatbot-panel.active {
        transform: scale(1) translateY(0);
        opacity: 1;
        pointer-events: auto;
    }

    .chatbot-header {
        background: #ffffff;
        color: #0f172a;
        padding: 17px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #e2e8f0;
    }

    .chatbot-header-info {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 0;
    }

    .chatbot-avatar {
        width: 42px;
        height: 42px;
        flex: 0 0 42px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid #dbe1ea;
    }

    .chatbot-avatar img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 5px;
    }

    .chatbot-heading {
        min-width: 0;
    }

    .chatbot-title {
        font-weight: 700;
        font-size: 1rem;
        margin: 0;
        letter-spacing: -0.01em;
        overflow-wrap: anywhere;
    }

    .chatbot-subtitle {
        font-size: 0.78rem;
        color: #64748b;
        margin: 2px 0 0 0;
        overflow-wrap: anywhere;
    }

    .chatbot-close-btn {
        background: none;
        border: none;
        color: #64748b;
        cursor: pointer;
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        padding: 4px;
        opacity: 0.8;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: opacity 0.2s;
    }

    .chatbot-close-btn:hover {
        opacity: 1;
    }

    .chatbot-close-btn svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }

    .chatbot-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .chat-bubble {
        max-width: 80%;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 0.9rem;
        line-height: 1.45;
        word-wrap: break-word;
        white-space: pre-wrap;
    }

    .chat-bubble-bot {
        align-self: flex-start;
        background-color: #ffffff;
        color: #1e293b;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03), 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .chat-bubble-user {
        align-self: flex-end;
        background: var(--chatbot-primary);
        color: #ffffff;
        border-bottom-right-radius: 4px;
        box-shadow: 0 4px 12px rgba(29, 78, 216, 0.22);
    }

    .chatbot-feedback {
        align-self: flex-start;
        display: flex;
        align-items: center;
        gap: 5px;
        margin-top: -10px;
        color: #64748b;
        font-size: 0.72rem;
    }

    .chatbot-feedback-btn {
        width: 28px;
        height: 28px;
        border: 1px solid transparent;
        border-radius: 7px;
        display: grid;
        place-items: center;
        background: transparent;
        color: #64748b;
        transition: color 0.2s ease, background-color 0.2s ease, border-color 0.2s ease;
    }

    .chatbot-feedback-btn:hover:not(:disabled),
    .chatbot-feedback-btn.selected {
        color: var(--chatbot-primary);
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .chatbot-feedback-btn:disabled {
        cursor: default;
        opacity: 0.65;
    }

    .chatbot-feedback-btn svg {
        width: 14px;
        height: 14px;
        fill: currentColor;
    }

    .chatbot-quick-questions {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid #dbeafe;
    }

    .chatbot-quick-label {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 2px;
        width: 100%;
    }

    .quick-question-btn {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 9px 11px;
        font-size: 0.82rem;
        color: #334155;
        cursor: pointer;
        text-align: left;
        flex: 1 1 100%;
        transition: all 0.2s;
    }

    .quick-question-btn:hover {
        background-color: #f8fafc;
        border-color: #93c5fd;
        color: var(--chatbot-primary);
    }

    .chatbot-typing-indicator {
        align-self: flex-start;
        background-color: #ffffff;
        padding: 12px 16px;
        border-radius: 14px;
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.03);
        display: flex;
        gap: 4px;
        align-items: center;
    }

    .typing-dot {
        width: 6px;
        height: 6px;
        background-color: #64748b;
        border-radius: 50%;
        animation: typingBounce 1.4s infinite ease-in-out both;
    }

    .typing-dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-dot:nth-child(2) { animation-delay: -0.16s; }

    @keyframes typingBounce {
        0%, 80%, 100% { transform: scale(0); }
        40% { transform: scale(1); }
    }

    .chatbot-footer {
        padding: 12px 16px;
        background-color: #ffffff;
        border-top: 1px solid #e2e8f0;
    }

    .chatbot-input-container {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 5px 10px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .chatbot-input-container:focus-within {
        border-color: var(--chatbot-primary);
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.12);
    }

    .chatbot-textarea {
        flex: 1;
        border: none;
        background: transparent;
        resize: none;
        padding: 5px 0;
        font-size: 0.88rem;
        line-height: 1.35;
        color: #1e293b;
        max-height: 80px;
        outline: none;
    }

    .chatbot-textarea::placeholder {
        color: #94a3b8;
    }

    .chatbot-send-btn {
        background: none;
        border: none;
        color: var(--chatbot-primary);
        cursor: pointer;
        width: 32px;
        height: 32px;
        flex: 0 0 32px;
        padding: 2px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: color 0.2s, background-color 0.2s;
    }

    .chatbot-send-btn:hover:not(:disabled) {
        color: var(--chatbot-primary-dark);
        background-color: #f1f5f9;
    }

    .chatbot-send-btn:disabled {
        color: #cbd5e1;
        cursor: not-allowed;
    }

    .chatbot-send-btn svg {
        width: 20px;
        height: 20px;
        fill: currentColor;
    }

    .chatbot-input-meta {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        margin-top: 4px;
        font-size: 0.72rem;
        color: #94a3b8;
    }

    /* Mobile adjustments */
    @media (max-width: 600px), (max-height: 520px) {
        body.chatbot-open {
            overflow: hidden;
            overscroll-behavior: none;
        }

        .chatbot-widget-container {
            bottom: max(16px, env(safe-area-inset-bottom));
            right: max(16px, env(safe-area-inset-right));
        }

        .chatbot-launcher-btn.active {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .chatbot-panel {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100vw;
            width: 100dvw;
            max-width: 100%;
            height: 100vh;
            height: 100dvh;
            border-radius: 0;
        }

        .chatbot-header {
            padding-top: max(14px, env(safe-area-inset-top));
            padding-right: max(14px, env(safe-area-inset-right));
            padding-left: max(14px, env(safe-area-inset-left));
        }

        .chatbot-body {
            padding: 16px;
            padding-right: max(16px, env(safe-area-inset-right));
            padding-left: max(16px, env(safe-area-inset-left));
        }

        .chatbot-footer {
            padding-right: max(16px, env(safe-area-inset-right));
            padding-bottom: max(12px, env(safe-area-inset-bottom));
            padding-left: max(16px, env(safe-area-inset-left));
        }

        .quick-question-btn {
            flex-basis: 100%;
        }
    }

    @media (max-width: 360px) {
        .chatbot-avatar {
            width: 36px;
            height: 36px;
            flex-basis: 36px;
        }

        .chatbot-title {
            font-size: 0.9rem;
        }

        .chatbot-subtitle {
            font-size: 0.7rem;
        }

        .chat-bubble {
            max-width: 90%;
        }
    }

    @media (max-height: 520px) and (orientation: landscape) {
        .chatbot-header {
            padding-top: max(9px, env(safe-area-inset-top));
            padding-bottom: 9px;
        }

        .chatbot-avatar {
            width: 34px;
            height: 34px;
            flex-basis: 34px;
        }

        .chatbot-body {
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .chatbot-footer {
            padding-top: 8px;
        }

        .chatbot-input-meta {
            display: none;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .chatbot-panel,
        .chatbot-launcher-btn,
        .typing-dot {
            transition: none;
            animation: none;
        }
    }
</style>

<div class="chatbot-widget-container" id="chatbotWidget">
    <!-- Panel -->
    <div class="chatbot-panel" id="chatbotPanel">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <div class="chatbot-avatar" aria-hidden="true">
                    <img src="{{ asset('images/logo-sd-muhammadiyah-kolombo.png') }}" alt="">
                </div>
                <div class="chatbot-heading">
                    <h4 class="chatbot-title">Layanan Informasi Sekolah</h4>
                    <p class="chatbot-subtitle">SD Muhammadiyah Komplek Kolombo</p>
                </div>
            </div>
            <button class="chatbot-close-btn" id="chatbotCloseBtn" aria-label="Tutup chatbot">
                <svg viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg>
            </button>
        </div>

        <!-- Body -->
        <div class="chatbot-body" id="chatbotBody">
            <div class="chat-bubble chat-bubble-bot">Assalamu'alaikum. Selamat datang di layanan informasi SD Muhammadiyah Komplek Kolombo. Silakan pilih pertanyaan di bawah atau tuliskan hal yang ingin Anda ketahui.</div>
            
            <!-- Quick Questions -->
            <div class="chatbot-quick-questions" id="chatbotQuickQuestions">
                <span class="chatbot-quick-label">Pertanyaan Populer:</span>
                <button class="quick-question-btn" data-question="Di mana alamat sekolah?">Di mana alamat sekolah?</button>
                <button class="quick-question-btn" data-question="Apa saja ekstrakurikuler yang tersedia?">Apa saja ekstrakurikuler yang tersedia?</button>
                <button class="quick-question-btn" data-question="Bagaimana cara menghubungi sekolah?">Bagaimana cara menghubungi sekolah?</button>
                <button class="quick-question-btn" data-question="Apa visi dan misi sekolah?">Apa visi dan misi sekolah?</button>
            </div>
        </div>

        <!-- Footer -->
        <div class="chatbot-footer">
            <form id="chatbotForm" autocomplete="off">
                <div class="chatbot-input-container">
                    <textarea class="chatbot-textarea" id="chatbotTextarea" rows="1" placeholder="Ketik pertanyaan Anda..." maxlength="300" aria-label="Pertanyaan Chatbot"></textarea>
                    <button type="submit" class="chatbot-send-btn" id="chatbotSendBtn" aria-label="Kirim Pesan" disabled>
                        <svg viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    </button>
                </div>
                <div class="chatbot-input-meta">
                    <span>Jangan kirim data pribadi.</span>
                    <span><span id="chatbotCharCounter">0</span>/300</span>
                </div>
            </form>
        </div>
    </div>

    <!-- Floating Launcher Button -->
    <button class="chatbot-launcher-btn" id="chatbotLauncherBtn" aria-label="Buka Asisten Informasi Sekolah" aria-expanded="false" aria-controls="chatbotPanel">
        <svg viewBox="0 0 16 16" aria-hidden="true">
            <use href="{{ asset('assets/bootstrap-icons.svg') }}#chat-dots-fill"/>
        </svg>
    </button>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const chatbotWidget = document.getElementById('chatbotWidget');
        const launcherBtn = document.getElementById('chatbotLauncherBtn');
        const panel = document.getElementById('chatbotPanel');
        const closeBtn = document.getElementById('chatbotCloseBtn');
        const body = document.getElementById('chatbotBody');
        const form = document.getElementById('chatbotForm');
        const textarea = document.getElementById('chatbotTextarea');
        const sendBtn = document.getElementById('chatbotSendBtn');
        const charCounter = document.getElementById('chatbotCharCounter');
        const quickQuestions = document.getElementById('chatbotQuickQuestions');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const fullscreenChat = window.matchMedia('(max-width: 600px), (max-height: 520px)');

        function updatePageScrollLock() {
            document.body.classList.toggle(
                'chatbot-open',
                panel.classList.contains('active') && fullscreenChat.matches
            );
        }

        // Toggle Panel
        function toggleChat() {
            const isActive = panel.classList.toggle('active');
            launcherBtn.classList.toggle('active', isActive);
            launcherBtn.setAttribute('aria-expanded', isActive ? 'true' : 'false');
            updatePageScrollLock();
            
            if (isActive) {
                setTimeout(() => textarea.focus(), 100);
                scrollLatest();
            } else {
                launcherBtn.focus({ preventScroll: true });
            }
        }

        launcherBtn.addEventListener('click', toggleChat);
        closeBtn.addEventListener('click', toggleChat);
        fullscreenChat.addEventListener('change', updatePageScrollLock);

        // Auto-resize textarea and count characters
        textarea.addEventListener('input', function () {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight - 4) + 'px';
            
            const len = this.value.length;
            charCounter.textContent = len;
            
            sendBtn.disabled = this.value.trim().length === 0;
        });

        // Submit message on Enter (but allow newlines on Shift+Enter if they really want, though textarea is small)
        textarea.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                if (this.value.trim().length > 0 && !sendBtn.disabled) {
                    form.requestSubmit();
                }
            }
        });

        // Scroll to latest message
        function scrollLatest() {
            body.scrollTop = body.scrollHeight;
        }

        // Append Message
        function appendMessage(text, sender) {
            const bubble = document.createElement('div');
            bubble.className = `chat-bubble chat-bubble-${sender}`;
            bubble.textContent = text; // Safe from XSS
            
            // Insert before typing indicator or quick questions
            const indicator = body.querySelector('.chatbot-typing-indicator');
            if (indicator) {
                body.insertBefore(bubble, indicator);
            } else if (quickQuestions) {
                body.insertBefore(bubble, quickQuestions);
            } else {
                body.appendChild(bubble);
            }
            
            scrollLatest();

            return bubble;
        }

        function appendFeedback(bubble, logId, feedbackToken) {
            if (!bubble || !logId || !feedbackToken) return;

            const feedback = document.createElement('div');
            feedback.className = 'chatbot-feedback';
            feedback.dataset.logId = logId;
            feedback.dataset.feedbackToken = feedbackToken;
            feedback.innerHTML = `
                <span>Membantu?</span>
                <button type="button" class="chatbot-feedback-btn" data-feedback="helpful" aria-label="Jawaban membantu">
                    <svg viewBox="0 0 16 16" aria-hidden="true"><use href="{{ asset('assets/bootstrap-icons.svg') }}#hand-thumbs-up"></use></svg>
                </button>
                <button type="button" class="chatbot-feedback-btn" data-feedback="not_helpful" aria-label="Jawaban tidak membantu">
                    <svg viewBox="0 0 16 16" aria-hidden="true"><use href="{{ asset('assets/bootstrap-icons.svg') }}#hand-thumbs-down"></use></svg>
                </button>`;
            bubble.after(feedback);
        }

        // Show Typing Indicator
        function showTyping() {
            // Remove previous if any
            hideTyping();
            
            const indicator = document.createElement('div');
            indicator.className = 'chatbot-typing-indicator';
            
            for (let i = 0; i < 3; i++) {
                const dot = document.createElement('div');
                dot.className = 'typing-dot';
                indicator.appendChild(dot);
            }
            
            if (quickQuestions) {
                body.insertBefore(indicator, quickQuestions);
            } else {
                body.appendChild(indicator);
            }
            scrollLatest();
        }

        // Hide Typing Indicator
        function hideTyping() {
            const indicator = body.querySelector('.chatbot-typing-indicator');
            if (indicator) {
                indicator.remove();
            }
        }

        // Handle Quick Questions click
        quickQuestions.addEventListener('click', function(e) {
            const btn = e.target.closest('.quick-question-btn');
            if (btn) {
                const text = btn.dataset.question;
                sendQuestion(text);
            }
        });

        body.addEventListener('click', async function (event) {
            const button = event.target.closest('.chatbot-feedback-btn');
            if (!button || button.disabled) return;

            const feedbackBox = button.closest('.chatbot-feedback');
            const buttons = feedbackBox.querySelectorAll('.chatbot-feedback-btn');
            buttons.forEach(item => item.disabled = true);

            try {
                const response = await fetch("{{ route('chatbot.feedback') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        log_id: Number(feedbackBox.dataset.logId),
                        feedback_token: feedbackBox.dataset.feedbackToken,
                        feedback: button.dataset.feedback
                    })
                });

                if (!response.ok) throw new Error();

                button.classList.add('selected');
                feedbackBox.querySelector('span').textContent = 'Terima kasih';
            } catch (error) {
                buttons.forEach(item => item.disabled = false);
            }
        });

        // Disable/Enable Input elements
        function setFormDisabled(disabled) {
            textarea.disabled = disabled;
            sendBtn.disabled = disabled || textarea.value.trim().length === 0;
            
            // Disable quick questions buttons too
            const buttons = quickQuestions.querySelectorAll('.quick-question-btn');
            buttons.forEach(b => b.disabled = disabled);
        }

        // Send question to backend
        function sendQuestion(text) {
            if (!text || text.trim() === '') return;

            appendMessage(text, 'user');
            
            // Clear textarea
            textarea.value = '';
            textarea.style.height = 'auto';
            charCounter.textContent = '0';
            
            setFormDisabled(true);
            showTyping();

            fetch("{{ route('chatbot.send') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message: text })
            })
            .then(response => {
                if (response.status === 429) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Terlalu banyak permintaan.');
                    });
                }
                if (!response.ok) {
                    throw new Error('Koneksi bermasalah. Silakan hubungi sekolah jika masalah berlanjut.');
                }
                return response.json();
            })
            .then(data => {
                hideTyping();
                if (data.success) {
                    const bubble = appendMessage(data.message, 'bot');
                    appendFeedback(bubble, data.log_id, data.feedback_token);
                } else {
                    appendMessage(data.message || 'Maaf, terjadi kesalahan.', 'bot');
                }
            })
            .catch(error => {
                hideTyping();
                appendMessage(error.message || 'Koneksi bermasalah. Silakan coba lagi nanti.', 'bot');
            })
            .finally(() => {
                setFormDisabled(false);
                if (panel.classList.contains('active')) {
                    textarea.focus({ preventScroll: true });
                }
                scrollLatest();
            });
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const text = textarea.value.trim();
            sendQuestion(text);
        });
    });
</script>
@endpush
