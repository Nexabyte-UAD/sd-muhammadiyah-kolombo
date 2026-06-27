@extends('layouts.public')

@section('content')
<x-breadcrumb>Profil: Visi & Misi</x-breadcrumb>

@php
    $konten = $profil->konten ?? '';
    
    // Split content into Vision and Mission sections based on the word "Misi" at start or newlines
    $parts = preg_split('/(?i)(?:^|\n)\s*Misi\s*:?\s*/', $konten);
    
    // Extract Visi statement
    $visiText = trim($parts[0] ?? '');
    $visiText = preg_replace('/(?i)^\s*Visi\s*:?\s*/', '', $visiText);
    if (empty($visiText)) {
        $visiText = 'Belum ada data visi.';
    }
    
    // Extract Misi statements
    $misiText = trim($parts[1] ?? '');
    $misiItems = [];
    if (!empty($misiText)) {
        $lines = explode("\n", $misiText);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Clean up leading lists or numbering structures (e.g. "1. ", "- ", "* ")
            $cleanedLine = preg_replace('/^(?:\d+[\.\)]|-|\*)\s*/', '', $line);
            if (!empty($cleanedLine)) {
                $misiItems[] = $cleanedLine;
            }
        }
    }
    
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
                    Arah pandang dan komitmen SD Muhammadiyah Kolombo dalam mewujudkan pendidikan dasar berkualitas.
                </p>
            </div>
        </div>
        
        <!-- Header Image (If Uploaded) -->
        @if(isset($profil) && $profil->gambar)
            <div class="row mb-5">
                <div class="col-12">
                    <div class="rounded-3 overflow-hidden border border-light">
                        <img src="{{ asset('storage/' . $profil->gambar) }}" class="w-100" style="max-height: 400px; object-fit: cover;" alt="Visi Misi SD Muhammadiyah Kolombo">
                    </div>
                </div>
            </div>
        @endif

        <!-- 2-Column Grid -->
        <div class="row g-4">
            
            <!-- Vision Section (Left) -->
            <div class="col-md-6">
                <div class="p-4 bg-light rounded-3 h-100 border">
                    <h3 class="fw-bold text-dark mb-3">Visi</h3>
                    <p class="text-secondary lh-base fs-5" style="font-style: italic;">
                        "{{ $visiText }}"
                    </p>
                </div>
            </div>

            <!-- Mission Section (Right) -->
            <div class="col-md-6">
                <div class="p-4 bg-light rounded-3 h-100 border">
                    <h3 class="fw-bold text-dark mb-3">Misi</h3>
                    <ol class="text-secondary ps-3 mb-0 lh-lg fs-6">
                        @foreach($misiItems as $item)
                            <li class="mb-2">{{ $item }}</li>
                        @endforeach
                    </ol>
                </div>
            </div>
            
        </div>

    </div>
</section>
@endsection
