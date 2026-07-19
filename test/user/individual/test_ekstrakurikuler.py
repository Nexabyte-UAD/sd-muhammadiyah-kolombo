from playwright.sync_api import sync_playwright

def test_ekstrakurikuler():

    with sync_playwright() as p:

        browser = p.chromium.launch(headless=False)

        page = browser.new_page()

        # Homepage
        page.goto("http://127.0.0.1:8000")

        page.wait_for_timeout(2000)

        # Scope navigasi ke navbar
        navbar = page.locator("#navbarNav")

        # =========================
        # Halaman Ekstrakurikuler
        # =========================
        navbar.get_by_role(
            "link",
            name="Ekstrakurikuler",
            exact=True
        ).click()

        page.wait_for_timeout(2000)

        # Verifikasi halaman terbuka dengan cek breadcrumb
        page.get_by_text("Ekstrakurikuler").first.wait_for()

        page.wait_for_timeout(2000)

        # Cek apakah ada card ekstrakurikuler
        cards = page.locator("article.card")

        if cards.count() > 0:
            # Scroll ke bawah untuk melihat semua card
            page.evaluate("window.scrollTo(0, document.body.scrollHeight)")
            page.wait_for_timeout(1500)

            # Scroll kembali ke atas
            page.evaluate("window.scrollTo(0, 0)")
            page.wait_for_timeout(1000)

        page.wait_for_timeout(5000)

        browser.close()
