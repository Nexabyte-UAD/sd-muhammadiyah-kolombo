<?php

namespace Tests\Unit;

use App\Services\IndonesianTextFormatter;
use Tests\TestCase;

class IndonesianTextFormatterTest extends TestCase
{
    private IndonesianTextFormatter $formatter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->formatter = app(IndonesianTextFormatter::class);
    }

    public function test_formats_indonesian_title_with_lowercase_connectors(): void
    {
        $this->assertSame(
            'Kunjungan ke Museum dan Taman Pintar',
            $this->formatter->title('KUNJUNGAN KE MUSEUM DAN TAMAN PINTAR')
        );
    }

    public function test_preserves_configured_school_terms(): void
    {
        $this->assertSame(
            'Milad SD Muhammadiyah Kolombo',
            $this->formatter->title('milad sd muhammadiyah kolombo')
        );
    }

    public function test_formats_person_name_and_particles(): void
    {
        $this->assertSame('Ahmad bin Abdullah', $this->formatter->name('AHMAD BIN ABDULLAH'));
    }

    public function test_preserves_academic_degrees_and_internal_name_capitals(): void
    {
        $this->assertSame(
            'Siti Nurhayati, S.Pd., M.Pd.',
            $this->formatter->name('SITI NURHAYATI, S.PD., M.PD.')
        );
        $this->assertSame('Andi McDonald', $this->formatter->name('Andi McDonald'));
    }

    public function test_formats_multiple_unambiguous_indonesian_degree_levels(): void
    {
        $this->assertSame(
            'Siti Aminah, A.Md.Keb., S.Tr.Keb., S.Kes., M.Kes.',
            $this->formatter->name('SITI AMINAH, A.MD.KEB., S.TR.KEB., S.KES., M.KES.')
        );
        $this->assertSame(
            'Budi Santoso, S.I.Kom., M.I.Kom.',
            $this->formatter->name('BUDI SANTOSO, S.I.KOM., M.I.KOM.')
        );
    }

    public function test_academic_degrees_are_not_part_of_the_global_term_dictionary(): void
    {
        $this->assertArrayNotHasKey('s.pd', config('text-formatting.terms'));
        $this->assertArrayHasKey('s.pd.', config('text-formatting.academic_degrees'));
    }

    public function test_preserves_doctor_title_exactly_because_its_case_changes_meaning(): void
    {
        $this->assertSame('dr. Ahmad', $this->formatter->name('dr. AHMAD'));
        $this->assertSame('Dr. Ahmad', $this->formatter->name('Dr. AHMAD'));
        $this->assertSame(
            'dr. Ahmad datang ke sekolah. Siswa menerima vaksin.',
            $this->formatter->sentence('dr. ahmad datang ke sekolah. siswa menerima vaksin.')
        );
        $this->assertSame(
            'Dr. Ahmad memberikan seminar. Siswa mengikuti kegiatan.',
            $this->formatter->sentence('Dr. Ahmad memberikan seminar. siswa mengikuti kegiatan.')
        );
    }

    public function test_preserves_common_acronyms_in_titles(): void
    {
        $this->assertSame(
            'Lomba TIK dan AI untuk SD',
            $this->formatter->title('LOMBA TIK DAN AI UNTUK SD')
        );
    }

    public function test_formats_sentences_without_destroying_intentional_mixed_case(): void
    {
        $this->assertSame(
            'SD Muhammadiyah Kolombo mengikuti lomba. Siswa meraih juara.',
            $this->formatter->sentence('SD Muhammadiyah Kolombo mengikuti lomba. siswa meraih juara.')
        );
    }

    public function test_formats_text_inside_html_without_removing_tags(): void
    {
        $this->assertSame(
            '<p>Kegiatan dimulai pagi hari.</p><p>Siswa hadir tepat waktu.</p>',
            $this->formatter->html('<p>KEGIATAN DIMULAI PAGI HARI.</p><p>SISWA HADIR TEPAT WAKTU.</p>')
        );
    }

    public function test_inline_html_does_not_create_false_sentence_boundaries(): void
    {
        $this->assertSame(
            '<p>Kegiatan <strong>bersama</strong> siswa.</p>',
            $this->formatter->html('<p>KEGIATAN <strong>BERSAMA</strong> SISWA.</p>')
        );
    }

    public function test_html_entities_are_preserved_and_do_not_consume_sentence_capitalization(): void
    {
        $this->assertSame(
            '<p>&nbsp;Kegiatan dimulai.</p>',
            $this->formatter->html('<p>&nbsp;KEGIATAN DIMULAI.</p>')
        );
    }

    public function test_normalizes_safe_spacing_and_punctuation(): void
    {
        $this->assertSame(
            'Kegiatan di sekolah, kemudian siswa pulang!',
            $this->formatter->sentence('kegiatan   di sekolah ,kemudian siswa pulang !')
        );
    }

    public function test_formats_indonesian_address_and_administrative_abbreviations(): void
    {
        $this->assertSame(
            'Jl. Kaliurang Km 7, RT 01/RW 02, Sleman, DIY',
            $this->formatter->address('jl. kaliurang km 7 , rt 01/rw 02, sleman, diy')
        );
    }

    public function test_normalizes_indonesian_phone_to_e164_without_touching_invalid_values(): void
    {
        $this->assertSame('+6281234567890', $this->formatter->phone('0812-3456-7890'));
        $this->assertSame('+62274567890', $this->formatter->phone('(0274) 567890'));
        $this->assertSame('telepon sekolah', $this->formatter->phone('telepon sekolah'));
    }
}
