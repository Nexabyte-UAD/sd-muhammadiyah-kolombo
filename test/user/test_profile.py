from playwright.sync_api import sync_playwright

def test_profile():

    with sync_playwright() as p:

        browser = p.chromium.launch(headless=False)

        page = browser.new_page()

        page.goto("http://127.0.0.1:8000")

        page.wait_for_timeout(2000)

        # Kata Sambutan
        page.locator("a", has_text="Kata Sambutan").first.click()

        page.wait_for_timeout(2000)

        # Tentang
        page.locator("a", has_text="Tentang").first.click()

        page.wait_for_timeout(2000)

        # Visi & Misi
        page.locator("a", has_text="Visi & Misi").first.click()

        page.wait_for_timeout(2000)

        # Akreditasi
        page.locator("a", has_text="Akreditasi").first.click()

        page.wait_for_timeout(5000)

        browser.close()