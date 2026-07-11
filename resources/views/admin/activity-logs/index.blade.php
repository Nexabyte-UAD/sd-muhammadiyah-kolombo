{{--
    Halaman Riwayat Aktivitas Audit Log (admin/activity-logs/index.blade.php)
    Menampilkan daftar audit log dari tindakan administratif (Tambah, Update, Hapus data)
    beserta detail perubahan dan waktu kejadian log tersebut dibuat.
--}}
@extends('layouts.admin')

@section('title', 'Log Aktivitas')
@section('page_title', 'Log Aktivitas')
@section('page_description', 'Riwayat aktivitas admin, perubahan data, dan autentikasi yang tercatat di panel.')

@section('content')
<section class="data-table-panel">
    <div class="data-table-scroll">
        <table class="clean-data-table activity-log-table">
            <thead>
                <tr>
                    <th>Aktivitas</th>
                    <th>Detail</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($activities as $activity)
                    @php
                        $dotClass = match($activity->action_type) {
                            'Tambah' => 'green',
                            'Update' => 'yellow',
                            'Hapus' => 'red',
                            default => '',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="activity-log-title">
                                <span class="activity-dot {{ $dotClass }}"></span>
                                <strong>{{ $activity->module }} - {{ $activity->action_type }}</strong>
                            </div>
                        </td>
                        <td class="activity-log-description">{{ $activity->description ?: '-' }}</td>
                        <td>
                            <span class="badge badge-secondary">{{ $activity->created_at->diffForHumans() }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="data-empty">
                            <i class="fas fa-history"></i>
                            <span>Belum ada aktivitas tercatat.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($activities->hasPages())
        <div class="data-table-footer">
            {{ $activities->links('pagination::bootstrap-4') }}
        </div>
    @endif
</section>
@endsection
