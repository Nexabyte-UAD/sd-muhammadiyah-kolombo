<?php

namespace App\Http\Controllers;

use App\Models\ChatbotFaq;
use App\Models\ChatbotLog;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Controller ChatbotFaqController
 *
 * Mengelola CRUD data FAQ chatbot pada panel admin,
 * termasuk toggle status aktif/nonaktif, statistik, dan log chatbot.
 */
class ChatbotFaqController extends Controller
{
    /**
     * Menampilkan daftar FAQ chatbot dengan filter dan statistik.
     */
    public function index(Request $request)
    {
        $search   = trim((string) $request->query('search', ''));
        $category = trim((string) $request->query('category', ''));
        $status   = $request->query('status', '');

        $query = ChatbotFaq::query()->orderBy('created_at', 'desc');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%")
                  ->orWhere('keywords', 'like', "%{$search}%");
            });
        }

        if ($category !== '') {
            $query->where('category', $category);
        }

        if ($status !== '') {
            $query->where('is_active', $status === '1');
        }

        $faqs = $query->paginate(10)->withQueryString();

        // Stats — menggunakan query tunggal yang efisien
        $totalFaq       = ChatbotFaq::count();
        $activeFaq      = ChatbotFaq::where('is_active', true)->count();
        $inactiveFaq    = ChatbotFaq::where('is_active', false)->count();
        $totalLogs      = ChatbotLog::count();
        $faqLogs        = ChatbotLog::where('answer_source', 'faq')->count();
        $fallbackLogs   = ChatbotLog::where('answer_source', 'fallback')->count();

        $topFaqs = ChatbotFaq::where('usage_count', '>', 0)
            ->orderBy('usage_count', 'desc')
            ->limit(5)
            ->get(['id', 'question', 'category', 'usage_count']);

        $recentLogs = ChatbotLog::orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'question', 'answer_source', 'status', 'created_at']);

        // Daftar kategori unik untuk filter
        $categories = ChatbotFaq::distinct()->pluck('category')->filter()->sort()->values();

        return view('admin.chatbot_faqs.index', compact(
            'faqs', 'search', 'category', 'status', 'categories',
            'totalFaq', 'activeFaq', 'inactiveFaq',
            'totalLogs', 'faqLogs', 'fallbackLogs',
            'topFaqs', 'recentLogs'
        ));
    }

    /**
     * Menampilkan form tambah FAQ baru.
     */
    public function create()
    {
        return view('admin.chatbot_faqs.create');
    }

    /**
     * Menyimpan FAQ baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'question'  => 'required|string|max:255',
            'answer'    => 'required|string|max:3000',
            'keywords'  => 'nullable|string|max:1000',
            'category'  => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ], [
            'question.required' => 'Pertanyaan wajib diisi.',
            'question.max'      => 'Pertanyaan maksimal 255 karakter.',
            'answer.required'   => 'Jawaban wajib diisi.',
            'answer.max'        => 'Jawaban maksimal 3000 karakter.',
            'keywords.max'      => 'Kata kunci maksimal 1000 karakter.',
            'category.max'      => 'Kategori maksimal 100 karakter.',
        ]);

        $data = [
            'question'  => $request->input('question'),
            'answer'    => $request->input('answer'),
            'keywords'  => $this->normalizeKeywords($request->input('keywords')),
            'category'  => $request->input('category'),
            'is_active' => $request->boolean('is_active', true),
        ];

        $faq = ChatbotFaq::create($data);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action_type' => 'Tambah',
            'module'      => 'FAQ Chatbot',
            'description' => 'Menambahkan FAQ baru: ' . Str::limit($faq->question, 60),
        ]);

        return redirect()->route('admin.chatbot-faqs.index')
            ->with('success', 'FAQ chatbot berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit FAQ.
     */
    public function edit(ChatbotFaq $chatbotFaq)
    {
        return view('admin.chatbot_faqs.edit', compact('chatbotFaq'));
    }

    /**
     * Memperbarui data FAQ tanpa mereset usage_count.
     */
    public function update(Request $request, ChatbotFaq $chatbotFaq)
    {
        $request->validate([
            'question'  => 'required|string|max:255',
            'answer'    => 'required|string|max:3000',
            'keywords'  => 'nullable|string|max:1000',
            'category'  => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ], [
            'question.required' => 'Pertanyaan wajib diisi.',
            'question.max'      => 'Pertanyaan maksimal 255 karakter.',
            'answer.required'   => 'Jawaban wajib diisi.',
            'answer.max'        => 'Jawaban maksimal 3000 karakter.',
            'keywords.max'      => 'Kata kunci maksimal 1000 karakter.',
            'category.max'      => 'Kategori maksimal 100 karakter.',
        ]);

        // Hanya update field yang relevan; usage_count tidak disentuh
        $chatbotFaq->update([
            'question'  => $request->input('question'),
            'answer'    => $request->input('answer'),
            'keywords'  => $this->normalizeKeywords($request->input('keywords')),
            'category'  => $request->input('category'),
            'is_active' => $request->boolean('is_active', true),
        ]);

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action_type' => 'Update',
            'module'      => 'FAQ Chatbot',
            'description' => 'Memperbarui FAQ: ' . Str::limit($chatbotFaq->question, 60),
        ]);

        return redirect()->route('admin.chatbot-faqs.index')
            ->with('success', 'FAQ chatbot berhasil diperbarui.');
    }

    /**
     * Soft delete FAQ. ChatbotLog tidak ikut dihapus.
     * matched_faq_id akan di-null-kan secara otomatis oleh nullOnDelete
     * apabila hard delete dijalankan di kemudian hari.
     */
    public function destroy(ChatbotFaq $chatbotFaq)
    {
        $question = $chatbotFaq->question;

        // Soft delete — ChatbotLog tidak ikut terhapus
        $chatbotFaq->delete();

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action_type' => 'Hapus',
            'module'      => 'FAQ Chatbot',
            'description' => 'Menghapus FAQ: ' . Str::limit($question, 60),
        ]);

        return redirect()->route('admin.chatbot-faqs.index')
            ->with('success', 'FAQ chatbot berhasil dihapus.');
    }

    /**
     * Toggle status aktif/nonaktif FAQ.
     */
    public function toggleStatus(ChatbotFaq $chatbotFaq)
    {
        $chatbotFaq->update(['is_active' => !$chatbotFaq->is_active]);

        $statusLabel = $chatbotFaq->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::create([
            'user_id'     => auth()->id(),
            'action_type' => 'Update',
            'module'      => 'FAQ Chatbot',
            'description' => 'FAQ "' . Str::limit($chatbotFaq->question, 50) . '" telah ' . $statusLabel,
        ]);

        return redirect()->route('admin.chatbot-faqs.index')
            ->with('success', 'FAQ chatbot berhasil ' . $statusLabel . '.');
    }

    /**
     * Normalisasi keywords: trim, lowercase, hapus duplikasi dan kosong, gabung koma.
     */
    protected function normalizeKeywords(?string $keywords): ?string
    {
        if (empty($keywords)) {
            return null;
        }

        $items = array_filter(
            array_unique(
                array_map('trim', explode(',', strtolower($keywords)))
            ),
            fn($k) => $k !== ''
        );

        return implode(', ', $items);
    }
}
