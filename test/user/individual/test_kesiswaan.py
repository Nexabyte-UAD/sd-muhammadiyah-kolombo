from playwright.sync_api import sync_playwright

def test_kesiswaan():

    with sync_playwright() as p:

        browser = p.chromium.launch(headless=False)

        page = browser.new_page()

        # Homepage
        page.goto("http://127.0.0.1:8000")

        page.wait_for_timeout(2000)

        # Scope navigasi ke navbar
        navbar = page.locator("#navbarNav")

        # =========================
        # Data Siswa
        # =========================
        navbar.get_by_role("button", name="Kesiswaan").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Data Siswa").click()

        page.wait_for_timeout(2000)

        # Coba fitur pencarian siswa
        page.locator("#search-input").press_sequentially(
            "Ali",
            delay=100
        )

        page.wait_for_timeout(1500)

        # Reset pencarian
        page.locator("#search-input").fill("")

        page.wait_for_timeout(1000)

        # =========================
        # Data Kelas
        # =========================
        navbar.get_by_role("button", name="Kesiswaan").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Data Kelas").click()

        page.wait_for_timeout(2000)

        # Coba fitur pencarian kelas
        page.locator("#search-input").press_sequentially(
            "1",
            delay=100
        )

        page.wait_for_timeout(1500)

        # Reset pencarian
        page.locator("#search-input").fill("")

        page.wait_for_timeout(1000)

        # =========================
        # Data Alumni
        # =========================
        navbar.get_by_role("button", name="Kesiswaan").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Data Alumni").click()

        page.wait_for_timeout(2000)

        # Coba filter tahun kelulusan (dropdown)
        tahun_select = page.locator("select#tahun")
        options = tahun_select.locator("option").all()

        # Jika ada opsi tahun selain "Semua Tahun Lulus", pilih yang pertama
        if len(options) > 1:
            options[1].click()
            page.wait_for_timeout(2000)

        # Kembali ke semua tahun
        tahun_select.select_option("")
        page.wait_for_timeout(1500)

        # Coba pencarian alumni
        page.locator("#search-input").press_sequentially(
            "Ahmad",
            delay=100
        )

        page.wait_for_timeout(5000)

        browser.close()
