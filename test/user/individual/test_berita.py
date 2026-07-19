from playwright.sync_api import sync_playwright

def test_berita():

    with sync_playwright() as p:

        browser = p.chromium.launch(headless=False)

        page = browser.new_page()

        # Homepage
        page.goto("http://127.0.0.1:8000")

        page.wait_for_timeout(2000)

        # Scope navigasi ke navbar
        navbar = page.locator("#navbarNav")

        # =========================
        # Halaman Daftar Berita
        # =========================
        navbar.get_by_role(
            "link",
            name="Berita",
            exact=True
        ).click()

        page.wait_for_timeout(2000)

        # Coba fitur pencarian berita
        page.locator(
            'input[name="search"]'
        ).press_sequentially(
            "sekolah",
            delay=100
        )

        page.wait_for_timeout(500)

        page.get_by_role(
            "button",
            name=""
        ).first.click()

        page.wait_for_timeout(2000)

        # Reset pencarian (klik tombol refresh jika ada)
        refresh_btn = page.locator('a[title="Reset Pencarian"]')
        if refresh_btn.count() > 0:
            refresh_btn.click()
            page.wait_for_timeout(2000)

        # =========================
        # Detail Berita (klik berita pertama jika ada)
        # =========================
        berita_cards = page.locator("article.card a.stretched-link")

        if berita_cards.count() > 0:
            berita_cards.first.click()

            page.wait_for_timeout(2000)

            # Klik tombol Kembali ke daftar berita
            page.get_by_role(
                "link",
                name="Kembali"
            ).click()

            page.wait_for_timeout(3000)

        page.wait_for_timeout(5000)

        browser.close()
