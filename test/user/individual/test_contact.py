from playwright.sync_api import sync_playwright

def test_contact():

    with sync_playwright() as p:

        browser = p.chromium.launch(headless=False)

        page = browser.new_page()

        # Homepage
        page.goto("http://127.0.0.1:8000")

        page.wait_for_timeout(2000)

        # Buka halaman kontak (scroll ke footer)
        page.get_by_role(
            "link",
            name="Kontak",
            exact=True
        ).click()

        page.wait_for_timeout(2000)

        # Isi nama
        page.locator(
            'input[name="nama"]'
        ).press_sequentially(
            "Zetha",
            delay=100
        )

        page.wait_for_timeout(1000)

        # Isi email
        page.locator(
            'input[name="email"]'
        ).press_sequentially(
            "zetha@gmail.com",
            delay=100
        )

        page.wait_for_timeout(1000)

        # Isi pesan
        page.locator(
            'textarea[name="pesan"]'
        ).press_sequentially(
            "Ini adalah testing otomatis menggunakan Playwright.",
            delay=50
        )

        page.wait_for_timeout(2000)

        # Klik tombol kirim
        page.get_by_role(
            "button",
            name="Bagikan Pesan"
        ).click()

        # Tunggu halaman selesai load setelah submit
        page.wait_for_load_state("networkidle")

        page.wait_for_timeout(3000)

        browser.close()
