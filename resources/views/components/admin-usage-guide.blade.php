{{--
    Komponen Panduan Penggunaan Admin (components/admin-usage-guide.blade.php)
    Menampilkan petunjuk/cara pengelolaan data admin (berbentuk accordion/details drop-down)
    yang dinamis sesuai array petunjuk yang dipassing (items).
--}}
@props([
    'title' => 'Panduan Penggunaan',
    'description' => 'Buka untuk melihat cara mengelola data pada halaman ini.',
    'items' => [],
])

<details class="admin-usage-guide">
    <summary>
        <span class="admin-usage-icon">
            <x-admin-icon name="help" size="19"/>
        </span>
        <span class="admin-usage-heading">
            <strong>{{ $title }}</strong>
            <small>{{ $description }}</small>
        </span>
        <x-admin-icon name="arrow-right" size="16" class="admin-usage-chevron"/>
    </summary>
    <div class="admin-usage-body">
        <ol>
            @foreach($items as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ol>
    </div>
</details>
