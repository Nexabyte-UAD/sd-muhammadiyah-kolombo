<?php

namespace Tests\Feature;

use App\Models\ChatbotFaq;
use App\Models\ChatbotLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatbotFaqAdminTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Membuat user Admin untuk keperluan pengujian.
     */
    private function makeAdmin(): User
    {
        return User::create([
            'name'     => 'Admin Test',
            'email'    => 'admin@test.local',
            'username' => 'admintest',
            'password' => 'Password1!',
            'role'     => 'Admin',
        ]);
    }

    /**
     * Membuat user biasa (non-admin) untuk keperluan pengujian.
     */
    private function makeUser(): User
    {
        return User::create([
            'name'     => 'User Biasa',
            'email'    => 'user@test.local',
            'username' => 'userbiasa',
            'password' => 'Password1!',
            'role'     => 'user',
        ]);
    }

    // ------------------------------------------------------------------
    // 1. Pengunjung umum tidak dapat mengakses halaman daftar FAQ admin
    // ------------------------------------------------------------------
    public function test_guest_cannot_access_chatbot_faqs_index(): void
    {
        $response = $this->get(route('admin.chatbot-faqs.index'));
        $response->assertRedirect(route('login'));
    }

    // ------------------------------------------------------------------
    // 2. Pengguna non-Admin tidak dapat mengakses CRUD
    // ------------------------------------------------------------------
    public function test_non_admin_cannot_access_chatbot_faqs(): void
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $this->get(route('admin.chatbot-faqs.index'))->assertForbidden();
        $this->get(route('admin.chatbot-faqs.create'))->assertForbidden();
    }

    // ------------------------------------------------------------------
    // 3. Admin dapat membuka daftar FAQ
    // ------------------------------------------------------------------
    public function test_admin_can_view_chatbot_faqs_index(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->get(route('admin.chatbot-faqs.index'));
        $response->assertOk();
        $response->assertSee('FAQ Chatbot');
    }

    // ------------------------------------------------------------------
    // 4. Admin dapat membuat FAQ baru
    // ------------------------------------------------------------------
    public function test_admin_can_create_chatbot_faq(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->post(route('admin.chatbot-faqs.store'), [
            'question'  => 'Di mana lokasi sekolah?',
            'answer'    => 'Sekolah berada di Jl. Kolombo No. 4, Yogyakarta.',
            'keywords'  => 'alamat, lokasi',
            'category'  => 'Kontak',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.chatbot-faqs.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('chatbot_faqs', [
            'question' => 'Di mana lokasi sekolah?',
            'category' => 'Kontak',
        ]);
    }

    // ------------------------------------------------------------------
    // 5. Validasi question wajib diisi
    // ------------------------------------------------------------------
    public function test_store_fails_when_question_is_empty(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->post(route('admin.chatbot-faqs.store'), [
            'question'  => '',
            'answer'    => 'Jawaban valid.',
            'is_active' => 1,
        ]);

        $response->assertSessionHasErrors(['question']);
    }

    // ------------------------------------------------------------------
    // 6. Validasi answer wajib diisi
    // ------------------------------------------------------------------
    public function test_store_fails_when_answer_is_empty(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $response = $this->post(route('admin.chatbot-faqs.store'), [
            'question'  => 'Pertanyaan valid?',
            'answer'    => '',
            'is_active' => 1,
        ]);

        $response->assertSessionHasErrors(['answer']);
    }

    // ------------------------------------------------------------------
    // 7. Admin dapat memperbarui FAQ
    // ------------------------------------------------------------------
    public function test_admin_can_update_chatbot_faq(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $faq = ChatbotFaq::create([
            'question'    => 'Pertanyaan Lama?',
            'answer'      => 'Jawaban Lama.',
            'keywords'    => 'lama',
            'is_active'   => true,
            'usage_count' => 5,
        ]);

        $response = $this->put(route('admin.chatbot-faqs.update', $faq), [
            'question'  => 'Pertanyaan Baru?',
            'answer'    => 'Jawaban Baru.',
            'keywords'  => 'baru',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.chatbot-faqs.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('chatbot_faqs', [
            'id'       => $faq->id,
            'question' => 'Pertanyaan Baru?',
        ]);
    }

    // ------------------------------------------------------------------
    // 8. Update tidak mereset usage_count
    // ------------------------------------------------------------------
    public function test_update_does_not_reset_usage_count(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $faq = ChatbotFaq::create([
            'question'    => 'Pertanyaan?',
            'answer'      => 'Jawaban.',
            'is_active'   => true,
            'usage_count' => 42,
        ]);

        $this->put(route('admin.chatbot-faqs.update', $faq), [
            'question'  => 'Pertanyaan Diperbarui?',
            'answer'    => 'Jawaban Diperbarui.',
            'is_active' => 1,
        ]);

        $this->assertEquals(42, $faq->fresh()->usage_count);
    }

    // ------------------------------------------------------------------
    // 9. Admin dapat toggle status FAQ (aktifkan & nonaktifkan)
    // ------------------------------------------------------------------
    public function test_admin_can_toggle_faq_status(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $faq = ChatbotFaq::create([
            'question'  => 'Toggle Test?',
            'answer'    => 'Jawaban.',
            'is_active' => true,
        ]);

        // Nonaktifkan
        $this->patch(route('admin.chatbot-faqs.toggle-status', $faq))
            ->assertRedirect(route('admin.chatbot-faqs.index'));

        $this->assertFalse($faq->fresh()->is_active);

        // Aktifkan kembali
        $this->patch(route('admin.chatbot-faqs.toggle-status', $faq))
            ->assertRedirect(route('admin.chatbot-faqs.index'));

        $this->assertTrue($faq->fresh()->is_active);
    }

    // ------------------------------------------------------------------
    // 10. Admin dapat soft delete FAQ
    // ------------------------------------------------------------------
    public function test_admin_can_soft_delete_chatbot_faq(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        $faq = ChatbotFaq::create([
            'question'  => 'FAQ Akan Dihapus?',
            'answer'    => 'Jawaban.',
            'is_active' => true,
        ]);

        $this->delete(route('admin.chatbot-faqs.destroy', $faq))
            ->assertRedirect(route('admin.chatbot-faqs.index'))
            ->assertSessionHas('success');

        // Tidak ada di query normal
        $this->assertNull(ChatbotFaq::find($faq->id));

        // Masih ada di soft delete
        $this->assertNotNull(ChatbotFaq::withTrashed()->find($faq->id));
        $this->assertNotNull(ChatbotFaq::withTrashed()->find($faq->id)->deleted_at);
    }

    // ------------------------------------------------------------------
    // 11. FAQ yang dihapus tidak muncul pada query normal
    // ------------------------------------------------------------------
    public function test_deleted_faq_does_not_appear_in_normal_query(): void
    {
        $faq = ChatbotFaq::create([
            'question'  => 'FAQ Dihapus?',
            'answer'    => 'Jawaban.',
            'is_active' => true,
        ]);

        $faq->delete();

        $this->assertEquals(0, ChatbotFaq::count());
        $this->assertEquals(1, ChatbotFaq::withTrashed()->count());
    }

    // ------------------------------------------------------------------
    // 12. Filter pencarian berjalan
    // ------------------------------------------------------------------
    public function test_search_filter_works(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        ChatbotFaq::create(['question' => 'Pertanyaan Satu?', 'answer' => 'Jawaban satu.', 'is_active' => true]);
        ChatbotFaq::create(['question' => 'Pertanyaan Dua?', 'answer' => 'Jawaban dua.', 'is_active' => true]);

        $response = $this->get(route('admin.chatbot-faqs.index', ['search' => 'Satu']));
        $response->assertOk();
        $response->assertSee('Pertanyaan Satu?');
        $response->assertDontSee('Pertanyaan Dua?');
    }

    // ------------------------------------------------------------------
    // 13. Filter kategori berjalan
    // ------------------------------------------------------------------
    public function test_category_filter_works(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        ChatbotFaq::create(['question' => 'FAQ Kontak?', 'answer' => 'Jawaban.', 'category' => 'Kontak', 'is_active' => true]);
        ChatbotFaq::create(['question' => 'FAQ Akademik?', 'answer' => 'Jawaban.', 'category' => 'Akademik', 'is_active' => true]);

        $response = $this->get(route('admin.chatbot-faqs.index', ['category' => 'Kontak']));
        $response->assertOk();
        $response->assertSee('FAQ Kontak?');
        $response->assertDontSee('FAQ Akademik?');
    }

    // ------------------------------------------------------------------
    // 14. Statistik ditampilkan dengan nilai yang benar
    // ------------------------------------------------------------------
    public function test_statistics_are_shown_correctly(): void
    {
        $admin = $this->makeAdmin();
        $this->actingAs($admin);

        ChatbotFaq::create(['question' => 'FAQ Aktif?', 'answer' => 'Jawaban.', 'is_active' => true]);
        ChatbotFaq::create(['question' => 'FAQ Nonaktif?', 'answer' => 'Jawaban.', 'is_active' => false]);

        ChatbotLog::create([
            'question'      => 'Q1',
            'answer_source' => 'faq',
            'status'        => 'success',
            'response_text' => 'Jawaban.',
        ]);
        ChatbotLog::create([
            'question'      => 'Q2',
            'answer_source' => 'fallback',
            'status'        => 'success',
            'response_text' => 'Jawaban fallback.',
        ]);

        $response = $this->get(route('admin.chatbot-faqs.index'));
        $response->assertOk();
        $response->assertSee('2'); // Total FAQ
        $response->assertSee('1'); // Aktif dan Nonaktif (both show 1)
    }
}
