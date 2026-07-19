{{--
    Komponen Modal Biodata Guru/Staf (components/guru-staff-modal.blade.php)
    Menampilkan rincian biodata Guru atau Staf dalam bentuk pop-up modal Bootstrap (modal fade)
    yang interaktif, lengkap dengan aksesibilitas keyboard (menekan Enter atau Spasi untuk men-trigger).
--}}
@props(['tenaga'])

<div class="modal fade" id="biodataTenaga-{{ $tenaga->id }}" tabindex="-1"
     aria-labelledby="biodataTenagaLabel-{{ $tenaga->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 rounded-4 overflow-hidden shadow">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <div>
                    <h4 class="modal-title fw-bold text-dark" id="biodataTenagaLabel-{{ $tenaga->id }}">
                        Biodata {{ $tenaga->tipe === 'guru' ? 'Guru' : 'Staf' }}
                    </h4>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>

            <div class="modal-body p-4">
                <div class="row g-4 align-items-start">
                    <div class="col-md-4">
                        <div class="rounded-3 overflow-hidden" style="aspect-ratio: 2 / 3;">
                            @if($tenaga->foto && \Illuminate\Support\Facades\Storage::disk('public')->exists($tenaga->foto))
                                <img src="{{ asset('storage/' . $tenaga->foto) }}" class="w-100 h-100"
                                     style="object-fit: cover; object-position: center top;" alt="{{ $tenaga->nama }}">
                            @else
                                <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-secondary">
                                    <x-admin-icon name="person-circle" size="112" class="default-profile-icon opacity-25 mb-2"/>
                                    <span class="small opacity-50">No Image</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-8">
                        <h5 class="fw-bold text-dark mb-1">{{ $tenaga->nama }}</h5>
                        <p class="text-primary fw-semibold mb-4">{{ $tenaga->jabatan }}</p>

                        <div class="row g-3">
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 bg-light h-100">
                                    <span class="d-block text-secondary small mb-1">Jenis Kelamin</span>
                                    <strong class="text-dark">
                                        {{ \App\Models\GuruStaff::JENIS_KELAMIN[$tenaga->jenis_kelamin] ?? '-' }}
                                    </strong>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 bg-light h-100">
                                    <span class="d-block text-secondary small mb-1">Status Kepegawaian</span>
                                    <strong class="text-dark">{{ $tenaga->status_kepegawaian ?: '-' }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 bg-light h-100">
                                    <span class="d-block text-secondary small mb-1">Pendidikan Terakhir</span>
                                    <strong class="text-dark">{{ $tenaga->pendidikan_terakhir ?: '-' }}</strong>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 bg-light h-100">
                                    <span class="d-block text-secondary small mb-1">Agama</span>
                                    <strong class="text-dark">{{ $tenaga->agama ?: '-' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

@once
    @push('styles')
    <style>
        [data-biodata-trigger] {
            cursor: pointer;
        }

        [data-biodata-trigger]:focus-visible {
            outline: 3px solid rgba(23, 37, 84, .35);
            outline-offset: 3px;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('keydown', function (event) {
            const trigger = event.target.closest('[data-biodata-trigger]');
            if (!trigger || !['Enter', ' '].includes(event.key)) {
                return;
            }

            event.preventDefault();
            const target = document.querySelector(trigger.getAttribute('data-bs-target'));
            if (target) {
                bootstrap.Modal.getOrCreateInstance(target).show();
            }
        });
    </script>
    @endpush
@endonce
