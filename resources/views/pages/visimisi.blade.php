@extends('layouts.public')

@section('content')
<x-breadcrumb>Profil: Visi & Misi</x-breadcrumb>

@php
    $konten = trim($profil->konten ?? '');
    
    $visiLines = [];
    $misiLines = [];
    $isMisiSection = false;
    
    if (!empty($konten)) {
        $lines = explode("\n", str_replace("\r", "", $konten));
        foreach ($lines as $line) {
            $trimmedLine = trim($line);
            
            // Check if this line signals the start of the Mission section
            $isMisiHeader = false;
            if (preg_match('/^(?i)\s*(?:Misi|Misi\s*Sekolah|Misi\s*Kami|Misi\s*:\s*)\s*:?\s*$/', $trimmedLine)) {
                $isMisiHeader = true;
            }
            
            // If we find the Misi header, switch to the Misi section
            if ($isMisiHeader) {
                $isMisiSection = true;
                continue; // Skip the header line itself
            }
            
            // Clean up Visi header if it exists
            if (!$isMisiSection && preg_match('/^(?i)\s*(?:Visi|Visi\s*Sekolah|Visi\s*Kami|Visi\s*:\s*)\s*:?\s*$/', $trimmedLine)) {
                continue; // Skip the Visi header line
            }
            
            if ($isMisiSection) {
                if ($trimmedLine !== '') {
                    // Clean up leading list/numbering indicators (e.g. "1. ", "- ", "* ")
                    $cleanedLine = preg_replace('/^(?:\d+[\.\)]|-|\*)\s*/', '', $trimmedLine);
                    if ($cleanedLine !== '') {
                        $misiLines[] = $cleanedLine;
                    }
                }
            } else {
                if ($trimmedLine !== '') {
                    $visiLines[] = $trimmedLine;
                }
            }
        }
    }
    
    // Join Visi lines together
    $visiText = implode("\n", $visiLines);
    $visiText = preg_replace('/(?i)^\s*Visi\s*:?\s*/', '', $visiText); // Final fallback cleanup
    if (empty($visiText)) {
        $visiText = 'Belum ada data visi.';
    }
    
    $misiItems = $misiLines;
    if (empty($misiItems)) {
        $misiItems = ['Belum ada data misi.'];
    }
@endphp

<section class="py-5 bg-white min-vh-100">
    <div class="container">
        
        <!-- Page Header -->
        <div class="row mb-5">
            <div class="col-12">
                <h2 class="fw-bold text-dark mb-2">Visi & Misi</h2>
                <p class="text-secondary mb-0">
                    Arah pandang dan komitmen SD Muhammadiyah Komplek Kolombo dalam mewujudkan pendidikan dasar berkualitas.
                </p>
            </div>
        </div>
        
        <!-- Header Image (If Uploaded) -->
        @if(isset($profil) && $profil->gambar)
            <div class="row mb-5">
                <div class="col-12">
                    <div class="rounded-3 overflow-hidden border border-light">
                        <img src="{{ asset('storage/' . $profil->gambar) }}" class="w-100" style="max-height: 400px; object-fit: cover;" alt="Visi Misi SD Muhammadiyah Komplek Kolombo">
                    </div>
                </div>
            </div>
        @endif

        <!-- Unified Visi Misi Card -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="bg-white p-4 p-md-5 rounded-4 shadow-sm border">
                    <!-- Visi Section -->
                    <div class="text-center mb-5">
                        <h4 class="text-primary fw-bold text-uppercase tracking-wider mb-3" style="font-size: 0.9rem; letter-spacing: 1.5px;">Visi Sekolah</h4>
                        <p class="text-dark fs-2 fw-bold lh-sm mb-0" style="letter-spacing: -0.5px; font-family: 'Outfit', sans-serif;">
                            “{{ $visiText }}”
                        </p>
                    </div>

                    <hr class="my-5" style="opacity: 0.15;">

                    <!-- Misi Section -->
                    <div>
                        <h4 class="text-primary fw-bold text-uppercase tracking-wider text-center mb-4" style="font-size: 0.9rem; letter-spacing: 1.5px;">Misi Sekolah</h4>
                        <div class="d-flex flex-column gap-3">
                            @foreach($misiItems as $index => $item)
                                <div class="d-flex align-items-start gap-3">
                                    <div class="d-flex align-items-center justify-content-center text-primary rounded-circle fw-bold" style="width: 28px; height: 28px; font-size: 0.9rem; flex-shrink: 0; background-color: #eff6ff;">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="text-secondary lh-lg mb-0" style="font-size: 1.05rem; text-align: justify;">
                                        {{ $item }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
