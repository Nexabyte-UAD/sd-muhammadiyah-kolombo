<?php

namespace App\Services;

use App\Models\ChatbotFaq;

class ChatbotFaqService
{
    /**
     * Common Indonesian stopwords to ignore in search.
     */
    protected array $stopWords = [
        'yang', 'di', 'ke', 'dari', 'pada', 'dalam', 'untuk', 'dengan', 
        'dan', 'atau', 'ini', 'itu', 'juga', 'sudah', 'saya', 'anda',
        'dia', 'mereka', 'kita', 'kami', 'adalah', 'akan', 'bisa', 'ada',
        'apa', 'bagaimana', 'kapan', 'siapa', 'mengapa', 'kenapa', 'mana',
        'halo', 'hai', 'selamat', 'pagi', 'siang', 'sore', 'malam', 'tolong'
    ];

    /**
     * Find the best matching FAQ for a given question.
     */
    public function findBestMatch(string $userQuestion): ?array
    {
        $normalizedText = $this->normalizeText($userQuestion);
        $userTokens = $this->tokenize($normalizedText);
        
        if (empty($userTokens)) {
            return null;
        }

        $faqs = ChatbotFaq::active()->get();
        
        $bestMatch = null;
        $highestScore = 0;
        $threshold = config('chatbot.faq_threshold', 4);

        foreach ($faqs as $faq) {
            $score = $this->calculateScore($normalizedText, $userTokens, $faq);
            
            if ($score > $highestScore) {
                $highestScore = $score;
                $bestMatch = $faq;
            } elseif ($score === $highestScore && $score > 0 && $bestMatch !== null) {
                // Deterministic tie-breaker by ID
                if ($faq->id < $bestMatch->id) {
                    $bestMatch = $faq;
                }
            }
        }

        if ($bestMatch && $highestScore >= $threshold) {
            return [
                'faq' => $bestMatch,
                'score' => $highestScore
            ];
        }

        return null;
    }

    /**
     * Calculate match score for a given FAQ against user tokens.
     */
    protected function calculateScore(string $normalizedText, array $userTokens, ChatbotFaq $faq): int
    {
        $score = 0;
        
        $faqKeywords = array_unique($faq->getCleanKeywordsArray());
        
        // Exact Keyword Match (Higher weight)
        foreach ($faqKeywords as $keyword) {
            $keyword = $this->normalizeText($keyword);
            if ($keyword === '') {
                continue;
            }

            // Check if keyword consists of multiple words (e.g. "visi misi")
            if (str_contains($keyword, ' ')) {
                if (str_contains($normalizedText, $keyword)) {
                    $score += 4;
                }
            } else {
                if (in_array($keyword, $userTokens, true)) {
                    $score += 2;
                }
            }
        }

        $faqQuestionTokens = $this->tokenize($this->normalizeText($faq->question));
        $faqQuestionTokens = array_unique($faqQuestionTokens);
        
        // Question Token Match (Normal weight)
        foreach ($userTokens as $token) {
            if (in_array($token, $faqQuestionTokens, true)) {
                $score += 1;
            }
        }

        return $score;
    }

    /**
     * Normalize text: lowercase and keep only alphanumeric chars and spaces.
     */
    protected function normalizeText(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $text);

        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    /**
     * Remove stopwords, and tokenize normalized text into an array of words.
     */
    protected function tokenize(string $normalizedText): array
    {
        // Split into words
        $words = explode(' ', $normalizedText);
        
        $tokens = [];
        foreach ($words as $word) {
            $word = trim($word);
            // Ignore short tokens (less than 3 chars) unless it's a specific known acronym like "sd", "tk"
            // And ignore stopwords
            if (mb_strlen($word, 'UTF-8') >= 3 && !in_array($word, $this->stopWords, true)) {
                $tokens[] = $word;
            } elseif (in_array($word, ['sd', 'tk', 'smp', 'sma', 'wa', 'ig'], true)) {
                $tokens[] = $word;
            }
        }
        
        return array_values(array_unique($tokens));
    }
}
