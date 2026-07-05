@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0 text-dark">Dashboard</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <!-- Total Siswa Aktif -->
        <div class="col-lg-2 col-6">
            <div class="small-box bg-indigo">
                <div class="inner">
                    <h3>{{ $countSiswa }}</h3>
                    <p>Siswa Aktif</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <a href="{{ route('admin.siswa.index', ['status' => 'aktif']) }}" class="small-box-footer">
                    Selengkapnya <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Total Alumni -->
        <div class="col-lg-2 col-6">
            <div class="small-box bg-teal">
                <div class="inner">
                    <h3>{{ $countAlumni }}</h3>
                    <p>Total Alumni</p>
                </div>
                <div class="icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <a href="{{ route('admin.siswa.index', ['status' => 'alumni']) }}" class="small-box-footer">
                    Selengkapnya <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Total Guru -->
        <div class="col-lg-2 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $countGuru }}</h3>
                    <p>Total Guru & Staf</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('admin.guru-staff.index') }}" class="small-box-footer">
                    Selengkapnya <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Total Berita -->
        <div class="col-lg-2 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $countBerita }}</h3>
                    <p>Total Berita</p>
                </div>
                <div class="icon">
                    <i class="fas fa-newspaper"></i>
                </div>
                <a href="{{ route('admin.berita.index') }}" class="small-box-footer">
                    Selengkapnya <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Total Prestasi -->
        <div class="col-lg-2 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $countPrestasi }}</h3>
                    <p>Total Prestasi</p>
                </div>
                <div class="icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <a href="{{ route('admin.prestasi.index') }}" class="small-box-footer">
                    Selengkapnya <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <!-- Pesan Masuk -->
        <div class="col-lg-2 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $countPesan }}</h3>
                    <p>Pesan Masuk</p>
                </div>
                <div class="icon">
                    <i class="fas fa-envelope"></i>
                </div>
                <a href="{{ route('admin.pesan.index') }}" class="small-box-footer">
                    Selengkapnya <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Col -->
        <div class="col-md-8">
            <!-- Berita Terbaru -->
            <div class="card">
                <div class="card-header border-transparent">
                    <h3 class="card-title">Berita Terbaru</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table m-0">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($latestBerita as $berita)
                                <tr>
                                    <td>
                                        @if($berita->gambar)
                                            <img src="{{ asset('storage/'.$berita->gambar) }}" class="img-circle" style="width: 40px; height: 40px; object-fit: cover;" alt="">
                                        @else
                                            <div class="bg-light d-flex align-items-center justify-content-center text-muted border img-circle" style="width: 40px; height: 40px;">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($berita->judul, 50) }}</td>
                                    <td>{{ $berita->tanggal->translatedFormat('d M Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer clearfix">
                    <a href="{{ route('admin.berita.index') }}" class="btn btn-sm btn-secondary float-right">Lihat Semua Berita</a>
                </div>
            </div>
        </div>

        <!-- Right Col -->
        <div class="col-md-4">
            <!-- Pesan Terbaru -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pesan Terbaru</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        @foreach($latestPesan as $pesan)
                        <li class="item">
                            <div class="product-info ml-0">
                                <a href="javascript:void(0)" class="product-title">{{ $pesan->nama }}
                                    <span class="badge badge-info float-right"><i class="fas fa-envelope"></i></span>
                                </a>
                                <span class="product-description">
                                    {{ Str::limit($pesan->isi, 40) }}
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('admin.pesan.index') }}" class="uppercase">Lihat Semua Pesan</a>
                </div>
            </div>

            <!-- Aktivitas Terakhir -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Aktivitas Terakhir</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        @forelse($recentActivities as $activity)
                        <li class="item">
                            <div class="product-info ml-0">
                                @php
                                    $badgeClass = 'badge-primary';
                                    if ($activity->action_type == 'Tambah') $badgeClass = 'badge-success';
                                    if ($activity->action_type == 'Update') $badgeClass = 'badge-warning';
                                    if ($activity->action_type == 'Hapus') $badgeClass = 'badge-danger';
                                @endphp
                                <span class="product-title">
                                    {{ $activity->module }}
                                    <span class="badge {{ $badgeClass }} float-right">{{ $activity->action_type }}</span>
                                </span>
                                <span class="product-description">
                                    {{ Str::limit($activity->description, 35) }}
                                </span>
                                <span class="text-muted text-sm">{{ $activity->created_at->diffForHumans() }}</span>
                            </div>
                        </li>
                        @empty
                        <li class="item text-center text-muted py-3">Belum ada aktivitas tercatat.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
