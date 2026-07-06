<?php

namespace App\Services;

use Illuminate\Support\Str;

class IndonesianTextFormatter
{
    private const LOWERCASE_TITLE_WORDS = [
        'dan', 'atau', 'dari', 'di', 'ke', 'pada', 'dalam', 'dengan',
        'untuk', 'yang', 'oleh', 'sebagai', 'tentang', 'terhadap',
    ];

    private const NAME_PARTICLES = ['bin', 'binti', 'ibn', 'van', 'von', 'de'];

    public function title(?string $value): ?string
    {
        return $this->formatWords($value, function (array $words): array {
            $last = count($words) - 1;

            return array_map(function (string $word, int $index) use ($last): string {
                $normalized = $this->normalizeWord($word);

                if ($index > 0 && $index < $last && in_array(Str::lower($normalized), self::LOWERCASE_TITLE_WORDS, true)) {
                    return Str::lower($normalized);
                }

                return $this->normalizeWord($this->capitalizeCompound($normalized));
            }, $words, array_keys($words));
        });
    }

    public function name(?string $value): ?string
    {
        return $this->formatWords($value, function (array $words): array {
            $inDegreeSuffix = false;

            foreach ($words as $index => $word) {
                if ($this->isAmbiguousDoctorTitle($word)) {
                    $words[$index] = $word;

                    continue;
                }

                if ($inDegreeSuffix) {
                    $degree = $this->normalizeAcademicDegree($word);
                    if ($degree !== null) {
                        $words[$index] = $degree;

                        continue;
                    }

                    $inDegreeSuffix = false;
                }

                $normalized = $this->normalizeWord($word);

                if ($index > 0 && in_array(Str::lower($normalized), self::NAME_PARTICLES, true)) {
                    $words[$index] = Str::lower($normalized);
                } elseif ($this->hasIntentionalInternalCapital($normalized)) {
                    $words[$index] = $normalized;
                } else {
                    $words[$index] = $this->normalizeWord($this->capitalizeCompound($normalized));
                }

                if (str_ends_with($word, ',')) {
                    $inDegreeSuffix = true;
                }
            }

            return $words;
        });
    }

    public function sentence(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        $value = $this->normalizeSpacing($value);
        $capitalizeNext = true;

        return $this->formatSentenceFragment($value, $capitalizeNext, $this->hasUniformCase($value));
    }

    public function address(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        $segments = preg_split('/\s*,\s*/u', $this->normalizeSpacing($value)) ?: [];
        $address = implode(', ', array_map(fn (string $segment): string => $this->title($segment), $segments));

        return preg_replace_callback(
            '/\b(?:rt|rw)\b/iu',
            fn (array $match): string => Str::upper($match[0]),
            $address
        ) ?? $address;
    }

    public function phone(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        return trim($value);
    }

    public function html(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        $parts = preg_split(
            '/(<[^>]+>|&(?:#\d+|#x[\da-f]+|[a-z][\da-z]+);)/iu',
            $value,
            -1,
            PREG_SPLIT_DELIM_CAPTURE
        );
        if ($parts === false) {
            return $this->sentence($value);
        }

        $capitalizeNext = true;
        $plainText = html_entity_decode(strip_tags($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $normalizeCase = $this->hasUniformCase($plainText);
        foreach ($parts as $index => $part) {
            $isMarkupOrEntity = str_starts_with($part, '<')
                || preg_match('/^&(?:#\d+|#x[\da-f]+|[a-z][\da-z]+);$/iu', $part);

            if ($part !== '' && ! $isMarkupOrEntity) {
                $parts[$index] = $this->formatSentenceFragment($part, $capitalizeNext, $normalizeCase);
            }
        }

        return implode('', $parts);
    }

    public function fields(array $data, array $formats): array
    {
        foreach ($formats as $field => $format) {
            if (array_key_exists($field, $data) && is_string($data[$field])) {
                $data[$field] = $this->{$format}($data[$field]);
            }
        }

        return $data;
    }

    private function formatWords(?string $value, callable $formatter): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        $words = preg_split('/\s+/u', $this->normalizeSpacing($value)) ?: [];

        return implode(' ', $formatter($words));
    }

    private function normalizeWord(string $word): string
    {
        $customTerms = config('text-formatting.terms', []);
        $key = Str::lower(trim($word, " \t\n\r\0\x0B.,;:!?()[]{}\"'"));

        if (! isset($customTerms[$key])) {
            return $word;
        }

        return preg_replace(
            '/'.preg_quote(trim($word, " \t\n\r\0\x0B.,;:!?()[]{}\"'"), '/').'/u',
            $customTerms[$key],
            $word,
            1
        ) ?? $word;
    }

    private function capitalizeCompound(string $word): string
    {
        return preg_replace_callback(
            '/(^|[-\/])(\p{L})/u',
            fn (array $match): string => $match[1].Str::upper($match[2]),
            Str::lower($word)
        ) ?? $word;
    }

    private function normalizeSpacing(string $value): string
    {
        $value = preg_replace('/[ \t]+/u', ' ', $value) ?? $value;
        $value = preg_replace('/\s+([,;:!?])/u', '$1', $value) ?? $value;
        $value = preg_replace('/([,;:!?])(?=\p{L})/u', '$1 ', $value) ?? $value;

        return trim($value);
    }

    private function hasUniformCase(string $value): bool
    {
        $letters = preg_replace('/[^\p{L}]+/u', '', $value) ?? '';

        return $letters !== '' && ($letters === Str::upper($letters) || $letters === Str::lower($letters));
    }

    private function hasIntentionalInternalCapital(string $word): bool
    {
        $letters = preg_replace('/[^\p{L}]+/u', '', $word) ?? '';

        return (bool) preg_match('/\p{Ll}.*\p{Lu}/u', $letters);
    }

    private function formatSentenceFragment(string $value, bool &$capitalizeNext, bool $normalizeCase): string
    {
        $ambiguousTitles = [];
        $value = preg_replace_callback(
            '/\b(?:dr|Dr|DR)\.(?=\s|$)/u',
            function (array $match) use (&$ambiguousTitles): string {
                $index = count($ambiguousTitles);
                $ambiguousTitles[$index] = $match[0];

                return "\x1A{$index}\x1A";
            },
            $value
        ) ?? $value;

        if ($normalizeCase) {
            $value = Str::lower($value);
        }

        $value = preg_replace_callback(
            '/\x1A(\d+)\x1A/',
            fn (array $match): string => $ambiguousTitles[(int) $match[1]],
            $value
        ) ?? $value;

        return preg_replace_callback(
            '/\b(?:dr|Dr|DR)\.(?=\s|$)|[\p{L}\p{N}][\p{L}\p{N}\p{M}\'’\/-]*|[.!?]+/u',
            function (array $match) use (&$capitalizeNext): string {
                if ($this->isAmbiguousDoctorTitle($match[0])) {
                    $capitalizeNext = true;

                    return $match[0];
                }

                if (preg_match('/^[.!?]+$/u', $match[0])) {
                    $capitalizeNext = true;

                    return $match[0];
                }

                $word = $this->normalizeWord($match[0]);
                if ($capitalizeNext) {
                    $word = $this->normalizeWord($this->capitalizeCompound($word));
                    $capitalizeNext = false;
                }

                return $word;
            },
            $value
        ) ?? $value;
    }

    private function isAmbiguousDoctorTitle(string $word): bool
    {
        return (bool) preg_match('/^(?:dr|Dr|DR)\.[,;:]?$/u', $word);
    }

    private function normalizeAcademicDegree(string $word): ?string
    {
        $degrees = config('text-formatting.academic_degrees', []);
        $core = trim($word, " \t\n\r\0\x0B,;:");
        $key = Str::lower($core);

        if (! isset($degrees[$key])) {
            return null;
        }

        return preg_replace(
            '/'.preg_quote($core, '/').'/u',
            $degrees[$key],
            $word,
            1
        ) ?? $word;
    }
}
