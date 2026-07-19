from playwright.sync_api import sync_playwright

def test_profile():

    with sync_playwright() as p:

        browser = p.chromium.launch(headless=False)

        page = browser.new_page()

        page.goto("http://127.0.0.1:8000")

        page.wait_for_timeout(2000)

        navbar = page.locator("#navbarNav")

        # Buka dropdown "Profil" lalu klik "Kata Sambutan"
        navbar.get_by_role("button", name="Profil").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Kata Sambutan").click()

        page.wait_for_timeout(2000)

        # Buka dropdown "Profil" lalu klik "Tentang"
        navbar.get_by_role("button", name="Profil").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Tentang").click()

        page.wait_for_timeout(2000)

        # Buka dropdown "Profil" lalu klik "Visi & Misi"
        navbar.get_by_role("button", name="Profil").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Visi & Misi").click()

        page.wait_for_timeout(2000)

        # Buka dropdown "Profil" lalu klik "Akreditasi"
        navbar.get_by_role("button", name="Profil").click()
        page.wait_for_timeout(500)
        navbar.get_by_role("link", name="Akreditasi").click()

        page.wait_for_timeout(5000)

        browser.close()