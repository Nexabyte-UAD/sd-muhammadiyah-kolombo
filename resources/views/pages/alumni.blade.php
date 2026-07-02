@extends('layouts.public')

@section('content')
<x-breadcrumb>Kesiswaan: Alumni</x-breadcrumb>

<section class="py-5 bg-white">
    <div class="container">
        <!-- Header -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <h2 class="fw-bold text-dark mb-0">Direktori Alumni</h2>
                <p class="text-secondary small mt-1 mb-0">
                    Daftar alumni lulusan SD Muhammadiyah Komplek Kolombo yang telah berhasil menyelesaikan pendidikan dasar.
                </p>
            </div>
        </div>

        <!-- Filter Tahun Kelulusan -->
        <div class="row justify-content-center mb-4">
            <div class="col-lg-10">
                <form method="GET" action="{{ route('alumni') }}" class="d-inline-block bg-light p-3 rounded-4 shadow-sm">
                    <div class="d-flex align-items-center gap-2">
                        <label for="tahun" class="text-dark fw-bold small mb-0" style="white-space: nowrap;">Tahun Kelulusan:</label>
                        <select name="tahun" id="tahun" class="form-select form-select-sm border-secondary-subtle" style="width: 220px;" onchange="this.form.submit()">
                            <option value="">Semua Tahun Lulus</option>
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun Lulus {{ $y }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Card -->
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Controls Row -->
                <div class="row align-items-center mb-3">
                    <!-- Show Entries -->
                    <div class="col-6 col-sm-6">
                        <div class="d-inline-flex align-items-center gap-2">
                            <span class="text-secondary small">Show</span>
                            <select id="show-entries" class="form-select form-select-sm border-secondary-subtle" style="width: 75px;">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <span class="text-secondary small">entries</span>
                        </div>
                    </div>
                    <!-- Search -->
                    <div class="col-6 col-sm-6 text-end">
                        <div class="d-inline-flex align-items-center justify-content-end gap-2 w-100">
                            <span class="text-secondary small">Search:</span>
                            <input type="text" id="search-input" class="form-control form-control-sm border-secondary-subtle" style="width: 180px;">
                        </div>
                    </div>
                </div>

                <!-- Simple Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle text-center" id="table-alumni" style="width:100%">
                        <thead class="table-light">
                            <tr>
                                <th class="py-2.5" style="width: 80px;">No</th>
                                <th class="py-2.5">Nama</th>
                                <th class="py-2.5" style="width: 150px;">Angkatan</th>
                                <th class="py-2.5" style="width: 150px;">Tahun Lulus</th>
                                <th class="py-2.5" style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($alumni as $index => $item)
                            <tr>
                                <td class="py-2.5 td-no">{{ $index + 1 }}</td>
                                <td class="py-2.5 text-start fw-semibold text-dark ps-4">{{ $item->nama }}</td>
                                <td class="py-2.5 text-secondary">Tahun {{ $item->tahun_masuk }}</td>
                                <td class="py-2.5 text-success fw-bold">Lulus {{ $item->tahun_lulus }}</td>
                                <td class="py-2.5">
                                    <button class="btn btn-sm btn-outline-primary rounded-3 px-3 py-1 btn-detail" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailAlumniModal" 
                                            data-nama="{{ $item->nama }}"
                                            data-nis="{{ $item->nis ?? '-' }}"
                                            data-nisn="{{ $item->nisn ?? '-' }}"
                                            data-angkatan="{{ $item->tahun_masuk }}"
                                            data-lulus="{{ $item->tahun_lulus }}"
                                            data-jk="{{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}"
                                            data-ttl="{{ $item->tempat_lahir ?? '-' }}, {{ $item->tanggal_lahir ? $item->tanggal_lahir->translatedFormat('d F Y') : '-' }}"
                                            data-alamat="{{ $item->alamat ?? '-' }}"
                                            data-foto="{{ $item->foto ? asset('storage/' . $item->foto) : '' }}"
                                            data-huruf="{{ substr($item->nama, 0, 1) }}">
                                        <i class="bi bi-eye-fill me-1"></i> Detail
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr class="no-data-row">
                                <td colspan="5" class="py-4 text-center text-muted">No data available in table</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal Detail Alumni -->
<div class="modal fade" id="detailAlumniModal" tabindex="-1" aria-labelledby="detailAlumniModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 bg-light py-3 px-4">
                <h5 class="modal-title fw-bold text-dark" id="detailAlumniModalLabel">Detail Alumni</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <!-- Foto Alumni / Avatar -->
                <div class="mb-4 d-flex justify-content-center">
                    <div id="modal-foto-container" class="rounded-circle overflow-hidden shadow-sm border" style="width: 130px; height: 130px; display: none;">
                        <img id="modal-foto" src="" alt="Foto Alumni" class="w-100 h-100" style="object-fit: cover;">
                    </div>
                    <div id="modal-avatar" class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 130px; height: 130px; font-size: 3.5rem; font-weight: bold;">
                        A
                    </div>
                </div>

                <h4 id="modal-nama" class="fw-bold text-dark mb-1">Nama Alumni</h4>
                <p id="modal-alumni-badge" class="badge bg-success bg-opacity-10 text-success px-3 py-1.5 rounded-pill fw-bold mb-4">Alumni</p>

                <div class="text-start border-top pt-3">
                    <div class="row g-2 small">
                        <div class="col-4 text-secondary">NIS</div>
                        <div class="col-8 text-dark fw-medium" id="modal-nis">-</div>
                        
                        <div class="col-4 text-secondary">NISN</div>
                        <div class="col-8 text-dark fw-medium" id="modal-nisn">-</div>

                        <div class="col-4 text-secondary">Angkatan</div>
                        <div class="col-8 text-dark fw-medium" id="modal-angkatan">-</div>

                        <div class="col-4 text-secondary">Tahun Lulus</div>
                        <div class="col-8 text-success fw-bold" id="modal-lulus">-</div>

                        <div class="col-4 text-secondary">Jenis Kelamin</div>
                        <div class="col-8 text-dark fw-medium" id="modal-jk">-</div>

                        <div class="col-4 text-secondary">Tempat, Tgl Lahir</div>
                        <div class="col-8 text-dark fw-medium" id="modal-ttl">-</div>

                        <div class="col-4 text-secondary">Alamat</div>
                        <div class="col-8 text-dark fw-medium" id="modal-alamat">-</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-light rounded-bottom-4 justify-content-center">
                <button type="button" class="btn btn-secondary rounded-3 px-4 py-2" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("search-input");
        const showEntries = document.getElementById("show-entries");
        const tableBody = document.querySelector("#table-alumni tbody");
        
        // Find existing rows
        const originalRows = Array.from(tableBody.querySelectorAll("tr:not(.no-data-row)"));
        const noDataRow = tableBody.querySelector(".no-data-row");

        function filterTable() {
            const query = searchInput.value.toLowerCase().trim();
            const limit = parseInt(showEntries.value);
            
            let visibleCount = 0;
            let matchCount = 0;
            
            originalRows.forEach(row => {
                const cells = Array.from(row.querySelectorAll("td"));
                const nama = cells[1] ? cells[1].textContent.toLowerCase() : "";
                const angkatan = cells[2] ? cells[2].textContent.toLowerCase() : "";
                const lulus = cells[3] ? cells[3].textContent.toLowerCase() : "";
                
                const matchesSearch = nama.includes(query) || angkatan.includes(query) || lulus.includes(query);
                
                if (matchesSearch) {
                    matchCount++;
                    if (visibleCount < limit) {
                        row.style.display = "";
                        visibleCount++;
                        // Update dynamic row number
                        const noCell = row.querySelector(".td-no");
                        if (noCell) {
                            noCell.textContent = visibleCount;
                        }
                    } else {
                        row.style.display = "none";
                    }
                } else {
                    row.style.display = "none";
                }
            });

            // Handle Empty State
            if (matchCount === 0) {
                if (!noDataRow) {
                    const newNoDataRow = document.createElement("tr");
                    newNoDataRow.className = "no-data-row";
                    newNoDataRow.innerHTML = `<td colspan="5" class="py-4 text-center text-muted">No data available in table</td>`;
                    tableBody.appendChild(newNoDataRow);
                } else {
                    noDataRow.style.display = "";
                }
            } else {
                if (noDataRow) {
                    noDataRow.style.display = "none";
                }
            }
        }

        if (searchInput) {
            searchInput.addEventListener("input", filterTable);
        }
        if (showEntries) {
            showEntries.addEventListener("change", filterTable);
        }
        
        // Run initially
        filterTable();

        // Modal Detail Binding
        const detailButtons = document.querySelectorAll(".btn-detail");
        const modalFotoContainer = document.getElementById("modal-foto-container");
        const modalFoto = document.getElementById("modal-foto");
        const modalAvatar = document.getElementById("modal-avatar");
        const modalNama = document.getElementById("modal-nama");
        const modalAlumniBadge = document.getElementById("modal-alumni-badge");
        const modalNis = document.getElementById("modal-nis");
        const modalNisn = document.getElementById("modal-nisn");
        const modalAngkatan = document.getElementById("modal-angkatan");
        const modalLulus = document.getElementById("modal-lulus");
        const modalJk = document.getElementById("modal-jk");
        const modalTtl = document.getElementById("modal-ttl");
        const modalAlamat = document.getElementById("modal-alamat");

        detailButtons.forEach(button => {
            button.addEventListener("click", function() {
                const nama = this.getAttribute("data-nama");
                const nis = this.getAttribute("data-nis");
                const nisn = this.getAttribute("data-nisn");
                const angkatan = this.getAttribute("data-angkatan");
                const lulus = this.getAttribute("data-lulus");
                const jk = this.getAttribute("data-jk");
                const ttl = this.getAttribute("data-ttl");
                const alamat = this.getAttribute("data-alamat");
                const foto = this.getAttribute("data-foto");
                const huruf = this.getAttribute("data-huruf");

                modalNama.textContent = nama;
                modalAlumniBadge.textContent = "Alumni Lulus " + lulus;
                modalNis.textContent = nis;
                modalNisn.textContent = nisn;
                modalAngkatan.textContent = "Tahun Masuk " + angkatan;
                modalLulus.textContent = "Tahun Lulus " + lulus;
                modalJk.textContent = jk;
                modalTtl.textContent = ttl;
                modalAlamat.textContent = alamat;

                if (foto) {
                    modalFoto.src = foto;
                    modalFotoContainer.style.display = "block";
                    modalAvatar.style.display = "none";
                } else {
                    modalAvatar.textContent = huruf;
                    modalFotoContainer.style.display = "none";
                    modalAvatar.style.display = "flex";
                }
            });
        });
    });
</script>
@endpush
