<?php

namespace Tests\Feature;

use App\Models\ChatbotFaq;
use App\Models\ChatbotLog;
use App\Models\Setting;
use App\Models\Ekstrakurikuler;
use App\Services\GeminiChatbotService;
use Database\Seeders\ChatbotFaqSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('chatbot.public-data.v1');
    }

    public function test_public_page_provides_csrf_token_for_chatbot_request(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('meta name="csrf-token"', false)
            ->assertSee("'X-CSRF-TOKEN': csrfToken", false)
            ->assertSee('bootstrap-icons.svg#chat-dots-fill', false)
            ->assertDontSee('.chatbot-launcher-btn.active svg', false)
            ->assertDontSee('border-top: 4px solid var(--chatbot-primary)', false);

        $this->get(route('berita'))
            ->assertOk()
            ->assertDontSee('>Reset</a>', false);
    }

    public function test_generic_single_keyword_does_not_force_wrong_faq(): void
    {
        ChatbotFaq::create([
            'question' => 'Bagaimana melihat data siswa dan alumni?',
            'answer' => 'Buka menu kesiswaan.',
            'keywords' => 'data, siswa, alumni',
            'is_active' => true,
        ]);

        $this->postJson(route('chatbot.send'), ['message' => 'Tampilkan data guru'])
            ->assertOk()
            ->assertJsonPath('source', 'fallback');
    }

    public function test_unicode_phrase_can_match_faq(): void
    {
        ChatbotFaq::create([
            'question' => 'Apakah ada program Al-Qur’an?',
            'answer' => 'Program Al-Qur’an tersedia.',
            'keywords' => 'al-qur’an',
            'is_active' => true,
        ]);

        $this->postJson(route('chatbot.send'), ['message' => 'Informasi program Al-Qur’an'])
            ->assertOk()
            ->assertJsonPath('source', 'faq');
    }

    public function test_gemini_receives_conversation_history_and_current_public_data(): void
    {
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake([
            'https://generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => 'Jadwalnya hari Jumat.']]],
                    'finishReason' => 'STOP',
                ]],
            ]),
        ]);
        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => 'test-key',
        ]);
        Ekstrakurikuler::create([
            'nama' => 'Panahan',
            'jadwal' => 'Jumat pukul 14.00',
            'pembina' => 'Bapak Ahmad',
        ]);

        $result = app(GeminiChatbotService::class)->generateResponse('Kalau jadwalnya kapan?', [[
            'question' => 'Apakah ada ekstrakurikuler panahan?',
            'response' => 'Ya, tersedia ekstrakurikuler panahan.',
        ]]);

        $this->assertTrue($result['success']);
        $this->assertTrue(Cache::has('chatbot.public-data.v1'));
        \Illuminate\Support\Facades\Http::assertSent(function ($request) {
            $payload = $request->data();
            $this->assertSame(['user', 'model', 'user'], array_column($payload['contents'], 'role'));
            $this->assertSame('Kalau jadwalnya kapan?', $payload['contents'][2]['parts'][0]['text']);
            $this->assertStringContainsString('Panahan (Jumat pukul 14.00, Pembina Bapak Ahmad)', $payload['system_instruction']['parts'][0]['text']);

            return true;
        });
    }

    public function test_user_can_rate_own_chatbot_answer(): void
    {
        $chatResponse = $this->postJson(route('chatbot.send'), [
            'message' => 'Pertanyaan yang belum ada',
        ])->assertOk();

        $logId = $chatResponse->json('log_id');
        $feedbackToken = $chatResponse->json('feedback_token');
        $this->assertNotNull($logId);
        $this->assertNotNull($feedbackToken);

        $this->postJson(route('chatbot.feedback'), [
            'log_id' => $logId,
            'feedback_token' => $feedbackToken,
            'feedback' => 'helpful',
        ])->assertOk()->assertJsonPath('success', true);

        $this->assertDatabaseHas('chatbot_logs', [
            'id' => $logId,
            'feedback' => 'helpful',
        ]);
    }

    public function test_user_cannot_rate_answer_from_another_session(): void
    {
        $log = ChatbotLog::create([
            'session_id' => 'different-session',
            'question' => 'Pertanyaan sesi lain',
            'answer_source' => 'faq',
            'response_text' => 'Jawaban sesi lain',
            'status' => 'success',
        ]);

        $this->postJson(route('chatbot.feedback'), [
            'log_id' => $log->id,
            'feedback_token' => str_repeat('0', 64),
            'feedback' => 'not_helpful',
        ])->assertNotFound();

        $this->assertNull($log->fresh()->feedback);
    }

    public function test_feedback_value_must_be_supported(): void
    {
        $this->postJson(route('chatbot.feedback'), [
            'log_id' => 1,
            'feedback_token' => str_repeat('0', 64),
            'feedback' => 'invalid',
        ])->assertUnprocessable()->assertJsonValidationErrors('feedback');
    }

    public function test_chatbot_faq_seeder_is_safe_to_run_repeatedly(): void
    {
        $this->seed(ChatbotFaqSeeder::class);
        $firstCount = ChatbotFaq::count();
        $this->seed(ChatbotFaqSeeder::class);

        $this->assertGreaterThan(0, $firstCount);
        $this->assertSame($firstCount, ChatbotFaq::count());
    }

    public function test_old_chatbot_logs_are_prunable(): void
    {
        config(['chatbot.log_retention_days' => 90]);

        $oldLog = ChatbotLog::create([
            'question' => 'Log lama',
            'answer_source' => 'fallback',
            'status' => 'success',
        ]);
        ChatbotLog::whereKey($oldLog->getKey())->update([
            'created_at' => now()->subDays(91),
            'updated_at' => now()->subDays(91),
        ]);
        ChatbotLog::create([
            'question' => 'Log baru',
            'answer_source' => 'fallback',
            'status' => 'success',
        ]);

        $this->assertSame(['Log lama'], (new ChatbotLog)->prunable()->pluck('question')->all());
    }

    /**
     * Scenario 1: Test FAQ is found and matched.
     */
    public function test_faq_found_and_matched(): void
    {
        $faq = ChatbotFaq::create([
            'question' => 'Dimana alamat sekolah?',
            'answer' => 'Jl. Rajawali No. 10, Sleman, Yogyakarta',
            'keywords' => 'alamat, lokasi, sleman',
            'category' => 'Kontak',
            'is_active' => true,
        ]);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'Tolong beritahu alamat sekolah',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('message', 'Jl. Rajawali No. 10, Sleman, Yogyakarta');
        $response->assertJsonPath('source', 'faq');

        $this->assertEquals(1, $faq->fresh()->usage_count);
    }

    /**
     * Scenario 2: Test fallback is used when no FAQ matches.
     */
    public function test_fallback_is_used_when_no_faq_matches(): void
    {
        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'Siapakah presiden pertama Indonesia?',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('message', 'Maaf, informasi tersebut belum tersedia. Silakan menghubungi pihak sekolah melalui halaman kontak untuk mendapatkan informasi lebih lanjut.');
        $response->assertJsonPath('source', 'fallback');
        $response->assertJsonPath('score', null);
    }

    /**
     * Scenario 3: Test validation error on empty message.
     */
    public function test_validation_error_on_empty_message(): void
    {
        $response = $this->postJson(route('chatbot.send'), [
            'message' => '',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors(['message']);
    }

    /**
     * Scenario 4: Test validation error on long message (more than 300 characters).
     */
    public function test_validation_error_on_long_message(): void
    {
        $longMessage = str_repeat('a', 301);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => $longMessage,
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ])
            ->assertJsonValidationErrors(['message']);
    }

    /**
     * Scenario 5: Test inactive FAQ is not matched.
     */
    public function test_inactive_faq_is_not_matched(): void
    {
        $faq = ChatbotFaq::create([
            'question' => 'Dimana alamat sekolah?',
            'answer' => 'Jl. Rajawali No. 10, Sleman, Yogyakarta',
            'keywords' => 'alamat, lokasi, sleman',
            'category' => 'Kontak',
            'is_active' => false,
        ]);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'Tolong beritahu alamat sekolah',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('source', 'fallback');
        $this->assertEquals(0, $faq->fresh()->usage_count);
    }

    /**
     * Scenario 6: Test ChatbotLog is successfully created.
     */
    public function test_chatbot_log_is_created(): void
    {
        $this->assertEquals(0, ChatbotLog::count());

        $this->postJson(route('chatbot.send'), [
            'message' => 'Halo apa kabar?',
        ]);

        $this->assertEquals(1, ChatbotLog::count());
        $log = ChatbotLog::first();
        $this->assertEquals('Halo apa kabar?', $log->question);
        $this->assertEquals('fallback', $log->answer_source);
        $this->assertNotEmpty($log->ip_hash);
    }

    /**
     * Scenario 7: Test endpoint returns valid JSON response format.
     */
    public function test_endpoint_returns_json(): void
    {
        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'Test format',
        ]);

        $response->assertHeader('Content-Type', 'application/json');
        $response->assertJsonStructure([
            'success',
            'message',
            'source',
            'score'
        ]);
    }

    public function test_gemini_not_called_when_faq_found(): void
    {
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake();

        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => 'fake-api-key',
        ]);

        ChatbotFaq::create([
            'question' => 'Dimana alamat sekolah?',
            'answer' => 'Jl. Rajawali No. 10, Sleman, Yogyakarta',
            'keywords' => 'alamat, lokasi, sleman',
            'category' => 'Kontak',
            'is_active' => true,
        ]);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'alamat sekolah',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('source', 'faq');
        \Illuminate\Support\Facades\Http::assertNothingSent();
    }

    public function test_gemini_not_called_when_disabled(): void
    {
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake();

        config([
            'services.gemini.enabled' => false,
            'services.gemini.key' => 'fake-api-key',
        ]);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'pertanyaan random',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('source', 'fallback');
        \Illuminate\Support\Facades\Http::assertNothingSent();
    }

    public function test_gemini_not_called_when_key_empty(): void
    {
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake();

        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => '',
        ]);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'pertanyaan random',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('source', 'fallback');
        \Illuminate\Support\Facades\Http::assertNothingSent();
    }

    public function test_gemini_successfully_answers(): void
    {
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        
        $fakeText = 'Ini jawaban dari Gemini Assistant.';
        \Illuminate\Support\Facades\Http::fake([
            'https://generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => $fakeText],
                            ],
                        ],
                        'finishReason' => 'STOP',
                    ],
                ],
            ], 200),
        ]);

        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => 'valid-api-key',
            'services.gemini.model' => 'gemini-3.5-flash',
        ]);

        Setting::updateOrCreate(['key' => 'nama_sekolah'], ['value' => 'Sekolah Uji', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'telepon'], ['value' => '(0274) 585755', 'type' => 'text']);
        Setting::updateOrCreate(['key' => 'email'], ['value' => 'sdmuhkkolombo@gmail.com', 'type' => 'text']);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'pertanyaan random',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('message', $fakeText);
        $response->assertJsonPath('source', 'gemini');

        \Illuminate\Support\Facades\Http::assertSent(function ($request) {
            $this->assertEquals('valid-api-key', $request->header('x-goog-api-key')[0]);
            $this->assertFalse(str_contains($request->url(), 'key='));
            $this->assertEquals('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent', $request->url());
            
            $payload = $request->data();
            $this->assertArrayHasKey('system_instruction', $payload);
            $this->assertArrayHasKey('contents', $payload);
            $this->assertArrayHasKey('generationConfig', $payload);
            $this->assertFalse($payload['store']);
            $this->assertSame(1000, $payload['generationConfig']['maxOutputTokens']);
            $instruction = $payload['system_instruction']['parts'][0]['text'];
            $this->assertStringContainsString('Sekolah Uji', $instruction);
            $this->assertStringContainsString('(0274) 585755', $instruction);
            $this->assertStringContainsString('sdmuhkkolombo@gmail.com', $instruction);
            $this->assertStringNotContainsString('589123', $instruction);
            $this->assertStringNotContainsString('sdmuhkolombo@gmail.com', $instruction);
            $this->assertStringContainsString('satu paragraf ringkas', $instruction);
            $this->assertStringContainsString('maksimal dua paragraf pendek', $instruction);
            $this->assertStringContainsString('Hindari bahasa robotik', $instruction);
            
            return true;
        });

        $this->assertDatabaseHas('chatbot_logs', [
            'question' => 'pertanyaan random',
            'answer_source' => 'gemini',
            'status' => 'success',
            'response_text' => $fakeText,
        ]);
    }

    public function test_gemini_multiple_parts_combined(): void
    {
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        
        \Illuminate\Support\Facades\Http::fake([
            'https://generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Bagian pertama.'],
                                ['text' => 'Bagian kedua.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => 'valid-api-key',
        ]);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'pertanyaan random',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('message', 'Bagian pertama. Bagian kedua.');
        $response->assertJsonPath('source', 'gemini');
    }

    public function test_gemini_http_401_handled(): void
    {
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake([
            'https://generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([], 401),
        ]);

        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => 'invalid-key',
        ]);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'pertanyaan random',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('source', 'fallback');
        
        $this->assertDatabaseHas('chatbot_logs', [
            'question' => 'pertanyaan random',
            'answer_source' => 'fallback',
            'error_message' => 'gemini_unauthorized',
        ]);
    }

    public function test_gemini_http_403_handled(): void
    {
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake([
            'https://generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([], 403),
        ]);

        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => 'forbidden-key',
        ]);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'pertanyaan random',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('source', 'fallback');
        
        $this->assertDatabaseHas('chatbot_logs', [
            'question' => 'pertanyaan random',
            'answer_source' => 'fallback',
            'error_message' => 'gemini_forbidden',
        ]);
    }

    public function test_gemini_http_429_handled(): void
    {
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake([
            'https://generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([], 429),
        ]);

        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => 'quota-key',
        ]);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'pertanyaan random',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('source', 'fallback');
        
        $this->assertDatabaseHas('chatbot_logs', [
            'question' => 'pertanyaan random',
            'answer_source' => 'fallback',
            'error_message' => 'gemini_quota_exceeded',
        ]);
    }

    public function test_gemini_http_500_handled(): void
    {
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake([
            'https://generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([], 500),
        ]);

        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => 'valid-key',
        ]);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'pertanyaan random',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('source', 'fallback');
        
        $this->assertDatabaseHas('chatbot_logs', [
            'question' => 'pertanyaan random',
            'answer_source' => 'fallback',
            'error_message' => 'gemini_server_error',
        ]);
    }

    public function test_gemini_connection_error_handled(): void
    {
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake([
            'https://generativelanguage.googleapis.com/*' => function ($request) {
                throw new \Illuminate\Http\Client\ConnectionException("Connection timed out");
            },
        ]);

        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => 'valid-key',
        ]);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'pertanyaan random',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('source', 'fallback');
        
        $this->assertDatabaseHas('chatbot_logs', [
            'question' => 'pertanyaan random',
            'answer_source' => 'fallback',
            'error_message' => 'gemini_timeout',
        ]);
    }

    public function test_gemini_malformed_json_handled(): void
    {
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake([
            'https://generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response("not-json", 200),
        ]);

        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => 'valid-key',
        ]);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'pertanyaan random',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('source', 'fallback');
        
        $this->assertDatabaseHas('chatbot_logs', [
            'error_message' => 'gemini_invalid_response',
        ]);
    }

    public function test_gemini_blocked_content_handled(): void
    {
        \Illuminate\Support\Facades\Http::preventStrayRequests();
        \Illuminate\Support\Facades\Http::fake([
            'https://generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'finishReason' => 'SAFETY',
                    ],
                ],
            ], 200),
        ]);

        config([
            'services.gemini.enabled' => true,
            'services.gemini.key' => 'valid-key',
        ]);

        $response = $this->postJson(route('chatbot.send'), [
            'message' => 'pertanyaan random',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('source', 'fallback');
        
        $this->assertDatabaseHas('chatbot_logs', [
            'error_message' => 'gemini_blocked',
        ]);
    }
}
