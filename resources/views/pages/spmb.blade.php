@extends('layouts.public')

@section('content')
<x-breadcrumb>SPMB</x-breadcrumb>

<!-- 1. INFORMASI UMUM -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-8">
                <h1 class="fw-bold text-dark mb-3" style="font-size: 2.2rem; letter-spacing: -0.5px;">
                    {{ $spmb->judul ?? 'Informasi Umum Pendaftaran Siswa Baru' }}
                </h1>
                <p class="text-secondary mb-4" style="line-height: 1.8; font-size: 1.05rem;">
                    {!! nl2br(e($spmbData['pengantar'])) !!}
                </p>
                <div class="d-flex flex-wrap gap-2 text-dark small fw-medium">
                    <span class="bg-light border px-3 py-2 rounded-3"><x-admin-icon name="award" size="15" class="me-1 text-primary"/>Akreditasi A Resmi</span>
                    <span class="bg-light border px-3 py-2 rounded-3"><x-admin-icon name="check-circle" size="15" class="me-1 text-success"/>Kurikulum Nasional & Al-Islam</span>
                    <span class="bg-light border px-3 py-2 rounded-3"><x-admin-icon name="clock" size="15" class="me-1 text-warning"/>Gelombang Utama Dibuka</span>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="border rounded-4 p-4 text-center bg-light shadow-sm">
                    <div class="badge bg-primary px-3 py-2 rounded-pill text-uppercase mb-2" style="font-size: 0.75rem;">Status SPMB</div>
                    <h4 class="fw-bold text-dark mb-1">Tahun Ajaran {{ $spmbData['tahun_ajaran_clean'] ?? $spmbData['tahun_ajaran'] }}</h4>
                    <p class="small mb-3 {{ str_contains(strtolower($spmbData['status_pendaftaran']), 'tutup') ? 'text-danger fw-bold' : 'text-success fw-bold' }}">
                        {{ $spmbData['status_pendaftaran'] }}
                    </p>
                    <a href="#kontak-pendaftaran" class="btn btn-primary w-100 rounded-3 font-semibold py-2">
                        Hubungi Panitia PPDB
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. KUOTA PENDAFTARAN -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="mb-4">
            <h3 class="fw-bold text-dark mb-1">Kuota Pendaftaran</h3>
            <p class="text-secondary small mb-0">Informasi ketersediaan kuota pendaftaran siswa baru per tahun ajaran.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white p-4 rounded-4 border shadow-sm">
                    @php
                        $kuotaItems = $spmbData['kuota_items'] ?? [];
                        if (empty($kuotaItems) && !empty($spmbData['kuota_pendaftaran'])) {
                            $lines = array_filter(array_map('trim', explode("\n", $spmbData['kuota_pendaftaran'])));
                            foreach ($lines as $line) {
                                $parts = explode(':', $line, 2);
                                $kuotaItems[] = [
                                    'tahun_ajaran' => trim($parts[0] ?? $line),
                                    'status' => trim($parts[1] ?? 'Masih Ada Kuota'),
                                ];
                            }
                        }

                        // Urutkan: Status yang masih buka (Masih Ada Kuota) ditaruh di atas, status Ditutup di bawah
                        usort($kuotaItems, function ($a, $b) {
                            $aClosed = preg_match('/(tutup|ditutup|full|habis)/i', $a['status'] ?? '') ? 1 : 0;
                            $bClosed = preg_match('/(tutup|ditutup|full|habis)/i', $b['status'] ?? '') ? 1 : 0;
                            return $aClosed <=> $bClosed;
                        });
                    @endphp
                    <div class="d-flex flex-column gap-2">
                        @forelse($kuotaItems as $index => $item)
                            @php
                                $taName = $item['tahun_ajaran'] ?? '';
                                $statusQuota = $item['status'] ?? 'Masih Ada Kuota';
                                $isClosed = preg_match('/(tutup|ditutup|full|habis)/i', $statusQuota);
                            @endphp
                            <div class="d-flex align-items-center justify-content-between py-2 {{ $loop->last ? '' : 'border-bottom' }}">
                                <span class="fw-medium text-dark fs-5">{{ $taName }}</span>
                                <span class="badge rounded-pill text-white px-3 py-2 fw-bold" style="background-color: {{ $isClosed ? '#dc2626' : '#15803d' }}; font-size: 0.85rem;">{{ $statusQuota }}</span>
                            </div>
                        @empty
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <span class="fw-medium text-dark fs-5">Tahun Ajaran {{ $spmbData['tahun_ajaran'] }}</span>
                                <span class="badge rounded-pill text-white px-3 py-2 fw-bold" style="background-color: #15803d; font-size: 0.85rem;">{{ $spmbData['status_pendaftaran'] }}</span>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. PERSYARATAN PENDAFTARAN -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="p-4 rounded-4 border bg-white h-100 shadow-sm">
                    <h4 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <x-admin-icon name="check-circle" size="22" class="text-primary me-2"/>
                        Persyaratan Umum & Usia
                    </h4>
                    @php
                        $syaratUmumLines = array_filter(array_map('trim', explode("\n", $spmbData['persyaratan_umum'] ?? '')));
                    @endphp
                    <ul class="list-group list-group-flush border-0">
                        @forelse($syaratUmumLines as $line)
                            <li class="list-group-item border-0 px-0 py-2 text-secondary" style="line-height: 1.7;">
                                {{ preg_replace('/^[•\-\*]\s*/u', '', $line) }}
                            </li>
                        @empty
                            <li class="list-group-item border-0 px-0 py-2 text-secondary">
                                Calon siswa berusia minimal 6 tahun pada tanggal 1 Juli tahun berjalan.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="p-4 rounded-4 border bg-white h-100 shadow-sm">
                    <h4 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <x-admin-icon name="news" size="22" class="text-primary me-2"/>
                        Dokumen Persyaratan (Berkas)
                    </h4>
                    @php
                        $berkasLines = array_filter(array_map('trim', explode("\n", $spmbData['persyaratan_berkas'] ?? '')));
                    @endphp
                    <ul class="list-group list-group-flush border-0">
                        @forelse($berkasLines as $line)
                            <li class="list-group-item border-0 px-0 py-2 text-secondary" style="line-height: 1.7;">
                                {{ $line }}
                            </li>
                        @empty
                            <li class="list-group-item border-0 px-0 py-2 text-secondary">
                                Fotokopi Akta Kelahiran & Kartu Keluarga.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. CARA PENDAFTARAN -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="mb-4">
            <h3 class="fw-bold text-dark mb-1">Cara Pendaftaran</h3>
            <p class="text-secondary small mb-0">Tahapan dan alur proses pendaftaran siswa baru secara berurutan.</p>
        </div>

        @php
            $alurLines = array_filter(array_map('trim', explode("\n", $spmbData['alur_pendaftaran'] ?? '')));
        @endphp
        <div class="row g-3">
            @forelse($alurLines as $index => $stepLine)
                @php
                    $stepTitle = 'Langkah ' . ($index + 1);
                    $stepDesc = $stepLine;
                    if (preg_match('/^\d+[.)]\s*(.+?):\s*(.+)$/u', $stepLine, $m)) {
                        $stepTitle = $m[1];
                        $stepDesc = $m[2];
                    }
                @endphp
                <div class="col-md-3">
                    <div class="p-4 rounded-4 border bg-white h-100 shadow-sm">
                        <div class="badge {{ $index === 3 ? 'bg-success' : 'bg-primary' }} px-3 py-1.5 rounded-pill mb-3">Langkah {{ $index + 1 }}</div>
                        <h5 class="fw-bold text-dark mb-2">{{ $stepTitle }}</h5>
                        <p class="text-secondary small mb-0" style="line-height: 1.6;">
                            {{ $stepDesc }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-md-3">
                    <div class="p-4 rounded-4 border bg-white h-100 shadow-sm">
                        <div class="badge bg-primary px-3 py-1.5 rounded-pill mb-3">Langkah 1</div>
                        <h5 class="fw-bold text-dark mb-2">Pengisian Formulir</h5>
                        <p class="text-secondary small mb-0" style="line-height: 1.6;">
                            Mengisi formulir pendaftaran fisik atau mendaftar via WhatsApp panitia.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- 5. KONTAK PENDAFTARAN -->
<section class="py-5 bg-white" id="kontak-pendaftaran">
    <div class="container">
        <div class="p-4 p-md-5 rounded-4 border bg-white shadow-sm">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <h3 class="fw-bold text-dark mb-2">Sekretariat Pendaftaran (PPDB)</h3>
                    <p class="text-secondary mb-4" style="line-height: 1.7;">
                        Jika Bapak/Ibu membutuhkan informasi lebih lanjut mengenai pendaftaran, rincian biaya, atau jadwal kunjungan sekolah, silakan hubungi panitia PPDB kami.
                    </p>

                    <div class="d-flex flex-column gap-2 text-dark small mb-4">
                        <div class="d-flex align-items-start gap-2">
                            <x-admin-icon name="school" size="18" class="text-primary mt-1 flex-shrink-0"/>
                            <span><strong>Alamat Sekolah:</strong> Komplek Kolombo, Caturtunggal, Depok, Sleman, DIY.</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <x-admin-icon name="phone-out" size="18" class="text-primary flex-shrink-0"/>
                            <span><strong>Telepon:</strong> (0274) 585755</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <x-admin-icon name="clock" size="18" class="text-primary flex-shrink-0"/>
                            <span><strong>Jam Layanan:</strong> Senin – Sabtu (08.00 – 13.00 WIB)</span>
                        </div>
                    </div>

                    @php
                        $cleanWa = preg_replace('/[^0-9]/', '', $spmbData['nomor_wa'] ?? '6281234567890');
                        if (str_starts_with($cleanWa, '0')) {
                            $cleanWa = '62' . substr($cleanWa, 1);
                        }
                    @endphp
                    <a href="https://wa.me/{{ $cleanWa }}?text=Halo%20Panitia%20PPDB%20SD%20Muhammadiyah%20Kolombo,%20saya%20ingin%20menanyakan%20informasi%20pendaftaran%20siswa%20baru." 
                       target="_blank" 
                       class="btn btn-success btn-lg px-4 py-2.5 rounded-3 fw-semibold text-white d-inline-flex align-items-center gap-2">
                        <x-admin-icon name="message" size="20"/>
                        <span>Chat Panitia PPDB via WhatsApp</span>
                    </a>
                </div>

                <div class="col-lg-5">
                    <div class="p-4 bg-light rounded-4 border text-center">
                        <x-admin-icon name="message" size="48" class="text-primary mb-3"/>
                        <h5 class="fw-bold text-dark mb-1">Konsultasi Pendaftaran</h5>
                        <p class="text-secondary small mb-3">Panitia siap membantu pertanyaan Ayah & Bunda seputar sekolah.</p>
                        <div class="p-3 bg-white rounded-3 border text-start small text-secondary">
                            • Pelayanan ramah & transparan<br>
                            • Survey lokasi & fasilitas sekolah<br>
                            • Pemetaan minat & bakat calon siswa
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

