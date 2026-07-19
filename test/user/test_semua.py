from playwright.sync_api import sync_playwright

def test_semua_halaman():

    with sync_playwright() as p:

        browser = p.chromium.launch(headless=False)
        page = browser.new_page()

        # ==================================================
        # 1. BERANDA (Homepage + Scroll + Kontak)
        # ==================================================
        page.goto("http://127.0.0.1:8000")
        page.wait_for_timeout(2000)

        # Scroll ke bawah perlahan (melihat konten beranda)
        page.evaluate("window.scrollTo({ top: document.body.scrollHeight / 3, behavior: 'smooth' })")
        page.wait_for_timeout(1000)
        page.evaluate("window.scrollTo({ top: document.body.scrollHeight / 3 * 2, behavior: 'smooth' })")
        page.wait_for_timeout(1000)
        page.evaluate("window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })")
        page.wait_for_timeout(2000)

        # Isi form kontak di footer/beranda
        page.locator('input[name="nama"]').press_sequentially(
            "Zetha",
            delay=100
        )
        page.wait_for_timeout(500)

        page.locator('input[name="email"]').press_sequentially(
            "zetha@gmail.com",
            delay=100
        )
        page.wait_for_timeout(500)

        page.locator('textarea[name="pesan"]').press_sequentially(
            "Ini adalah testing otomatis menggunakan Playwright.",
            delay=50
        )
        page.wait_for_timeout(1000)

        page.get_by_role("button", name="Bagikan Pesan").click()
        page.wait_for_load_state("networkidle")
        page.wait_for_timeout(3000)

        # Kembali ke atas
        page.evaluate("window.scrollTo({ top: 0, behavior: 'smooth' })")
        page.wait_for_timeout(1000)

        # Scope navbar
        navbar = page.locator("#navbarNav")

        # ==================================================
        # 2. PROFIL
        # ==================================================

        # Kata Sambutan
        navbar.get_by_role("button", name="Profil").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Kata Sambutan").click()
        page.wait_for_timeout(2000)

        # Tentang
        navbar.get_by_role("button", name="Profil").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Tentang").click()
        page.wait_for_timeout(2000)

        # Visi & Misi
        navbar.get_by_role("button", name="Profil").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Visi & Misi").click()
        page.wait_for_timeout(2000)

        # Akreditasi
        navbar.get_by_role("button", name="Profil").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Akreditasi").click()
        page.wait_for_timeout(2000)

        # ==================================================
        # 3. PRESTASI
        # ==================================================
        navbar.get_by_role("link", name="Prestasi", exact=True).click()
        page.wait_for_timeout(2000)

        # ==================================================
        # 4. STRUKTURAL
        # ==================================================

        # Guru
        navbar.get_by_role("button", name="Struktural").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Guru").click()
        page.wait_for_timeout(2000)

        # Staf
        navbar.get_by_role("button", name="Struktural").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Staf").click()
        page.wait_for_timeout(2000)

        # ==================================================
        # 5. KESISWAAN
        # ==================================================

        # Data Siswa
        navbar.get_by_role("button", name="Kesiswaan").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Data Siswa").click()
        page.wait_for_timeout(2000)

        page.locator("#search-input").press_sequentially("Ali", delay=100)
        page.wait_for_timeout(1500)
        page.locator("#search-input").fill("")
        page.wait_for_timeout(500)

        # Data Kelas
        navbar.get_by_role("button", name="Kesiswaan").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Data Kelas").click()
        page.wait_for_timeout(2000)

        page.locator("#search-input").press_sequentially("1", delay=100)
        page.wait_for_timeout(1500)
        page.locator("#search-input").fill("")
        page.wait_for_timeout(500)

        # Data Alumni
        navbar.get_by_role("button", name="Kesiswaan").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Data Alumni").click()
        page.wait_for_timeout(2000)

        # Filter tahun alumni jika ada
        tahun_select = page.locator("select#tahun")
        options = tahun_select.locator("option").all()
        if len(options) > 1:
            options[1].click()
            page.wait_for_timeout(1500)
            tahun_select.select_option("")
            page.wait_for_timeout(500)

        page.locator("#search-input").press_sequentially("Ahmad", delay=100)
        page.wait_for_timeout(1500)
        page.locator("#search-input").fill("")
        page.wait_for_timeout(500)

        # ==================================================
        # 6. BERITA
        # ==================================================
        navbar.get_by_role("link", name="Berita", exact=True).click()
        page.wait_for_timeout(2000)

        # Cari berita
        page.locator('input[name="search"]').press_sequentially("sekolah", delay=100)
        page.wait_for_timeout(500)
        page.locator('input[name="search"]').press("Enter")
        page.wait_for_timeout(2000)

        # Reset pencarian
        refresh_btn = page.locator('a[title="Reset Pencarian"]')
        if refresh_btn.count() > 0:
            refresh_btn.click()
            page.wait_for_timeout(1500)

        # Buka detail berita pertama jika ada
        berita_cards = page.locator("article.card a.stretched-link")
        if berita_cards.count() > 0:
            berita_cards.first.click()
            page.wait_for_timeout(2000)

            # Kembali ke daftar berita
            page.get_by_role("link", name="Kembali").click()
            page.wait_for_timeout(1500)

        # ==================================================
        # 7. EKSTRAKURIKULER
        # ==================================================
        navbar.get_by_role("link", name="Ekstrakurikuler", exact=True).click()
        page.wait_for_timeout(2000)

        # Scroll melihat semua card
        page.evaluate("window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })")
        page.wait_for_timeout(1500)
        page.evaluate("window.scrollTo({ top: 0, behavior: 'smooth' })")
        page.wait_for_timeout(3000)

        browser.close()
