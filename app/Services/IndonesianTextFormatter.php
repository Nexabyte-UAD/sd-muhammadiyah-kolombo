<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Service IndonesianTextFormatter
 * 
 * Layanan khusus untuk merapikan dan memformat penulisan teks otomatis (auto-formatting)
 * berdasarkan tata bahasa Indonesia dan standar penulisan akademis/alamat di Indonesia.
 */
class IndonesianTextFormatter
{
    // Kata-kata yang harus tetap ditulis dalam huruf kecil jika berada di tengah judul (Preposisi & Konjungsi)
    private const LOWERCASE_TITLE_WORDS = [
        'dan', 'atau', 'dari', 'di', 'ke', 'pada', 'dalam', 'dengan',
        'untuk', 'yang', 'oleh', 'sebagai', 'tentang', 'terhadap',
    ];

    // Partikel nama keluarga/nasab yang ditulis huruf kecil
    private const NAME_PARTICLES = ['bin', 'binti', 'ibn', 'van', 'von', 'de'];

    /**
     * Memformat string menjadi format Judul (Title Case).
     * Contoh: "pembelajaran di kelas tiga" -> "Pembelajaran di Kelas Tiga"
     * 
     * @param  string|null  $value
     * @return string|null
     */
    public function title(?string $value): ?string
    {
        return $this->formatWords($value, function (array $words): array {
            $last = count($words) - 1;

            return array_map(function (string $word, int $index) use ($last): string {
                $normalized = $this->normalizeWord($word);

                // Jika kata di tengah kalimat termasuk kata hubung/preposisi, biarkan huruf kecil
                if ($index > 0 && $index < $last && in_array(Str::lower($normalized), self::LOWERCASE_TITLE_WORDS, true)) {
                    return Str::lower($normalized);
                }

                // Format kapital awal kata
                return $this->normalizeWord($this->capitalizeCompound($normalized));
            }, $words, array_keys($words));
        });
    }

    /**
     * Memformat string Nama Orang beserta partikel dan gelar akademik secara profesional.
     * Contoh: "dr. budi santoso, s.pd." -> "Dr. Budi Santoso, S.Pd."
     * 
     * @param  string|null  $value
     * @return string|null
     */
    public function name(?string $value): ?string
    {
        return $this->formatWords($value, function (array $words): array {
            $inDegreeSuffix = false;

            foreach ($words as $index => $word) {
                // Lewati format jika mendeteksi gelar ambigu "dr." / "Dr." di awal nama
                if ($this->isAmbiguousDoctorTitle($word)) {
                    $words[$index] = $word;

                    continue;
                }

                // Jika terdeteksi setelah tanda koma, format sebagai gelar akademis
                if ($inDegreeSuffix) {
                    $degree = $this->normalizeAcademicDegree($word);
                    if ($degree !== null) {
                        $words[$index] = $degree;

                        continue;
                    }

                    $inDegreeSuffix = false;
                }

                $normalized = $this->normalizeWord($word);

                // Format partikel nama (bin, binti) dengan huruf kecil
                if ($index > 0 && in_array(Str::lower($normalized), self::NAME_PARTICLES, true)) {
                    $words[$index] = Str::lower($normalized);
                } elseif ($this->hasIntentionalInternalCapital($normalized)) {
                    // Biarkan jika sudah ada huruf kapital internal (misal: "McCartney")
                    $words[$index] = $normalized;
                } else {
                    // Format standar kapital awal kata
                    $words[$index] = $this->normalizeWord($this->capitalizeCompound($normalized));
                }

                // Tandai bahwa kata selanjutnya mungkin adalah gelar akademik jika kata saat ini berakhiran koma
                if (str_ends_with($word, ',')) {
                    $inDegreeSuffix = true;
                }
            }

            return $words;
        });
    }

    /**
     * Memformat string menjadi kalimat (Sentence Case).
     * Contoh: "sd muhammadiyah kolombo. sekolah ramah anak." -> "Sd muhammadiyah kolombo. Sekolah ramah anak."
     * 
     * @param  string|null  $value
     * @return string|null
     */
    public function sentence(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        $value = $this->normalizeSpacing($value);
        $capitalizeNext = true;

        return $this->formatSentenceFragment($value, $capitalizeNext, $this->hasUniformCase($value));
    }

    /**
     * Memformat penulisan Alamat.
     * Merapikan spasi, huruf kapital per kata, dan memastikan kata singkatan "RT" / "RW" ditulis kapital penuh.
     * Contoh: "jl. kolombo, rt 01, rw 02" -> "Jl. Kolombo, RT 01, RW 02"
     * 
     * @param  string|null  $value
     * @return string|null
     */
    public function address(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        // Split alamat berdasarkan koma, rapikan per segmen dengan format Judul, lalu gabungkan kembali
        $segments = preg_split('/\s*,\s*/u', $this->normalizeSpacing($value)) ?: [];
        $address = implode(', ', array_map(fn (string $segment): string => $this->title($segment), $segments));

        // Ubah rt/rw menjadi huruf besar (RT/RW)
        return preg_replace_callback(
            '/\b(?:rt|rw)\b/iu',
            fn (array $match): string => Str::upper($match[0]),
            $address
        ) ?? $address;
    }

    /**
     * Memformat Nomor Telepon (mengembalikan nilai string yang dirapikan spasinya).
     * 
     * @param  string|null  $value
     * @return string|null
     */
    public function phone(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        return trim($value);
    }

    /**
     * Memformat teks yang mengandung tag HTML.
     * Mengabaikan tag HTML atau entitas HTML saat merapikan kalimat di dalam paragraf.
     * 
     * @param  string|null  $value
     * @return string|null
     */
    public function html(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        // Pisahkan teks berdasarkan pola tag HTML dan entitas HTML
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
        
        // Proses format hanya pada bagian teks biasa (bukan tag markup/entitas)
        foreach ($parts as $index => $part) {
            $isMarkupOrEntity = str_starts_with($part, '<')
                || preg_match('/^&(?:#\d+|#x[\da-f]+|[a-z][\da-z]+);$/iu', $part);

            if ($part !== '' && ! $isMarkupOrEntity) {
                $parts[$index] = $this->formatSentenceFragment($part, $capitalizeNext, $normalizeCase);
            }
        }

        return implode('', $parts);
    }

    /**
     * Memformat array data (seperti request input) secara massal berdasarkan konfigurasi kolom.
     * 
     * @param  array  $data     Data input mentah.
     * @param  array  $formats  Peta kolom dan jenis formatnya (misal: ['nama' => 'name', 'alamat' => 'address'])
     * @return array            Data input yang sudah bersih dan rapi.
     */
    public function fields(array $data, array $formats): array
    {
        foreach ($formats as $field => $format) {
            if (array_key_exists($field, $data) && is_string($data[$field])) {
                $data[$field] = $this->{$format}($data[$field]);
            }
        }

        return $data;
    }

    /**
     * Helper internal untuk memisahkan string menjadi kata-kata, menjalankan fungsi formatter,
     * lalu menggabungkannya kembali dengan satu spasi.
     */
    private function formatWords(?string $value, callable $formatter): ?string
    {
        if ($value === null || trim($value) === '') {
            return $value;
        }

        $words = preg_split('/\s+/u', $this->normalizeSpacing($value)) ?: [];

        return implode(' ', $formatter($words));
    }

    /**
     * Menormalisasi kata menggunakan konfigurasi istilah kustom (glossary) jika terdaftar.
     */
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

    /**
     * Membuat huruf awal kata menjadi kapital, mendukung tanda penghubung strip/slash (misal: "tanya-jawab" -> "Tanya-Jawab").
     */
    private function capitalizeCompound(string $word): string
    {
        return preg_replace_callback(
            '/(^|[-\/])(\p{L})/u',
            fn (array $match): string => $match[1].Str::upper($match[2]),
            Str::lower($word)
        ) ?? $word;
    }

    /**
     * Merapikan spasi ganda, spasi sebelum tanda baca, dan memastikan spasi setelah tanda baca.
     */
    private function normalizeSpacing(string $value): string
    {
        $value = preg_replace('/[ \t]+/u', ' ', $value) ?? $value;
        $value = preg_replace('/\s+([,;:!?])/u', '$1', $value) ?? $value;
        $value = preg_replace('/([,;:!?])(?=\p{L})/u', '$1 ', $value) ?? $value;

        return trim($value);
    }

    /**
     * Memeriksa apakah teks memiliki kesamaan huruf besar semua (UPPERCASE) atau kecil semua (lowercase).
     */
    private function hasUniformCase(string $value): bool
    {
        $letters = preg_replace('/[^\p{L}]+/u', '', $value) ?? '';

        return $letters !== '' && ($letters === Str::upper($letters) || $letters === Str::lower($letters));
    }

    /**
     * Memeriksa apakah kata tersebut sengaja ditulis dengan huruf besar di tengah (seperti McArthur atau FitRI).
     */
    private function hasIntentionalInternalCapital(string $word): bool
    {
        $letters = preg_replace('/[^\p{L}]+/u', '', $word) ?? '';

        return (bool) preg_match('/\p{Ll}.*\p{Lu}/u', $letters);
    }

    /**
     * Helper internal untuk memformat pecahan kalimat atau fragmen paragraf.
     */
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

    /**
     * Memeriksa apakah kata tersebut merupakan gelar dokter "dr." yang bisa ambigu dengan tanda titik akhir kalimat.
     */
    private function isAmbiguousDoctorTitle(string $word): bool
    {
        return (bool) preg_match('/^(?:dr|Dr|DR)\.[,;:]?$/u', $word);
    }

    /**
     * Menormalisasi singkatan gelar akademik Indonesia agar penulisannya baku.
     * Contoh: "s.pd" -> "S.Pd." atau "s.kom" -> "S.Kom."
     */
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
