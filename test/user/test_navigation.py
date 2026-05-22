from playwright.sync_api import sync_playwright

def test_navigation():

    with sync_playwright() as p:

        browser = p.chromium.launch(headless=False)

        page = browser.new_page()

        # Homepage
        page.goto("http://127.0.0.1:8000")
        page.wait_for_timeout(2000)

        # Prestasi
        page.get_by_role(
            "link",
            name="Prestasi",
            exact=True
        ).click()

        page.wait_for_timeout(2000)

        # Berita
        page.get_by_role(
            "link",
            name="Berita",
            exact=True
        ).click()

        page.wait_for_timeout(2000)

        # Ekstrakurikuler
        page.get_by_role(
            "link",
            name="Ekstrakurikuler",
            exact=True
        ).click()

        page.wait_for_timeout(2000)

        browser.close()

        