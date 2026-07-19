<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\ChatbotFaqService;
use App\Services\GeminiChatbotService;
use App\Models\ChatbotLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ChatbotController extends Controller
{
    protected ChatbotFaqService $faqService;
    protected GeminiChatbotService $geminiService;

    public function __construct(ChatbotFaqService $faqService, GeminiChatbotService $geminiService)
    {
        $this->faqService = $faqService;
        $this->geminiService = $geminiService;
    }

    /**
     * Handle incoming chatbot message.
     */
    public function send(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'message' => 'required|string|max:300',
            ], [
                'message.required' => 'Pesan tidak boleh kosong.',
                'message.string' => 'Pesan harus berupa teks.',
                'message.max' => 'Pesan maksimal 300 karakter.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        }

        $message = $request->input('message');
        $ipHash = hash_hmac('sha256', $request->ip(), config('app.key'));
        $sessionId = session()->getId();

        try {
            $match = $this->faqService->findBestMatch($message);

            if ($match) {
                $faq = $match['faq'];
                $score = $match['score'];

                // Increment usage_count safely
                try {
                    $faq->increment('usage_count');
                } catch (\Throwable $e) {
                    Log::warning('Failed to increment chatbot FAQ usage.', ['faq_id' => $faq->id]);
                }

                // Save log
                $log = $this->safeLog([
                    'session_id' => $sessionId,
                    'ip_hash' => $ipHash,
                    'question' => $message,
                    'answer_source' => 'faq',
                    'matched_faq_id' => $faq->id,
                    'response_text' => $faq->answer,
                    'status' => 'success',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $faq->answer,
                    'source' => 'faq',
                    'score' => $score,
                    'log_id' => $log?->id,
                    'feedback_token' => $this->feedbackToken($log),
                ]);
            }

            // Fallback: Try Gemini first
            $history = $this->conversationHistory($sessionId);
            $geminiResult = $this->geminiService->generateResponse($message, $history);

            if ($geminiResult['success']) {
                $geminiResponseText = $geminiResult['text'];

                $log = $this->safeLog([
                    'session_id' => $sessionId,
                    'ip_hash' => $ipHash,
                    'question' => $message,
                    'answer_source' => 'gemini',
                    'response_text' => $geminiResponseText,
                    'status' => 'success',
                ]);

                return response()->json([
                    'success' => true,
                    'message' => $geminiResponseText,
                    'source' => 'gemini',
                    'score' => null,
                    'log_id' => $log?->id,
                    'feedback_token' => $this->feedbackToken($log),
                ]);
            }

            // If Gemini fails, fallback to static message
            $fallbackMessage = 'Maaf, informasi tersebut belum tersedia. Silakan menghubungi pihak sekolah melalui halaman kontak untuk mendapatkan informasi lebih lanjut.';

            $log = $this->safeLog([
                'session_id' => $sessionId,
                'ip_hash' => $ipHash,
                'question' => $message,
                'answer_source' => 'fallback',
                'response_text' => $fallbackMessage,
                'status' => 'success',
                'error_message' => $geminiResult['error'] ?? 'gemini_error',
            ]);

            return response()->json([
                'success' => true,
                'message' => $fallbackMessage,
                'source' => 'fallback',
                'score' => null,
                'log_id' => $log?->id,
                'feedback_token' => $this->feedbackToken($log),
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());

            // Safe fallback response on internal error
            $fallbackMessage = 'Maaf, terjadi kesalahan saat memproses permintaan Anda. Silakan mencoba lagi nanti atau hubungi pihak sekolah.';
            
            try {
                $this->safeLog([
                    'session_id' => $sessionId,
                    'ip_hash' => $ipHash,
                    'question' => $message,
                    'answer_source' => 'fallback',
                    'response_text' => $fallbackMessage,
                    'status' => 'error',
                    'error_message' => 'internal_error',
                ]);
            } catch (\Exception $logException) {
                Log::error('Failed to log chatbot error: ' . $logException->getMessage());
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kendala saat memproses pertanyaan. Silakan coba kembali.',
            ], 500);
        }
    }

    public function feedback(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'log_id' => ['required', 'integer'],
            'feedback_token' => ['required', 'string', 'size:64'],
            'feedback' => ['required', 'in:helpful,not_helpful'],
        ]);

        $log = ChatbotLog::query()
            ->whereKey($validated['log_id'])
            ->where('status', 'success')
            ->first();

        $expectedToken = $this->feedbackToken($log);
        if (!$log || !$expectedToken || !hash_equals($expectedToken, $validated['feedback_token'])) {
            return response()->json([
                'success' => false,
                'message' => 'Jawaban tidak ditemukan.',
            ], 404);
        }

        $log->update([
            'feedback' => $validated['feedback'],
            'feedback_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih atas penilaian Anda.',
        ]);
    }

    /**
     * Ambil beberapa pasangan percakapan terakhir agar pertanyaan lanjutan
     * tetap memiliki konteks tanpa mengirim seluruh riwayat pengguna.
     */
    protected function conversationHistory(string $sessionId): array
    {
        $limit = max(0, min(10, (int) config('chatbot.history_limit', 4)));
        if ($limit === 0 || $sessionId === '') {
            return [];
        }

        try {
            return ChatbotLog::query()
                ->where('session_id', $sessionId)
                ->where('status', 'success')
                ->whereNotNull('response_text')
                ->latest('id')
                ->limit($limit)
                ->get(['question', 'response_text'])
                ->reverse()
                ->values()
                ->map(fn (ChatbotLog $log) => [
                    'question' => $log->question,
                    'response' => $log->response_text,
                ])
                ->all();
        } catch (\Throwable $e) {
            Log::warning('Failed to load chatbot conversation history.');

            return [];
        }
    }

    /** Logging tidak boleh menggagalkan jawaban yang sudah berhasil dibuat. */
    protected function safeLog(array $data): ?ChatbotLog
    {
        try {
            return ChatbotLog::create($data);
        } catch (\Throwable $e) {
            Log::warning('Failed to save chatbot log.', [
                'source' => $data['answer_source'] ?? null,
                'status' => $data['status'] ?? null,
            ]);

            return null;
        }
    }

    protected function feedbackToken(?ChatbotLog $log): ?string
    {
        if (!$log) {
            return null;
        }

        return hash_hmac('sha256', $log->id.'|'.$log->session_id, (string) config('app.key'));
    }
}
