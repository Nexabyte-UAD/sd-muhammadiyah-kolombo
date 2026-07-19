from playwright.sync_api import sync_playwright

def test_structural():

    with sync_playwright() as p:

        browser = p.chromium.launch(headless=False)

        page = browser.new_page()

        # Homepage
        page.goto("http://127.0.0.1:8000")

        page.wait_for_timeout(2000)

        navbar = page.locator("#navbarNav")

        # =========================
        # Guru
        # =========================
        navbar.get_by_role("button", name="Struktural").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Guru").click()

        page.wait_for_timeout(2000)

        # =========================
        # Staf
        # =========================
        navbar.get_by_role("button", name="Struktural").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Staf").click()

        page.wait_for_timeout(5000)

        browser.close()