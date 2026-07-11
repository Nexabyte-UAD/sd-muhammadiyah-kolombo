{{--
    Komponen Notifikasi Format Otomatis (components/auto-format-notice.blade.php)
    Menampilkan kotak informasi bertema info (alert alert-info) untuk memberi tahu admin
    bahwa kolom input teks (seperti nama, alamat, gelar, dll.) akan dirapikan otomatis saat disimpan
    oleh sistem (IndonesianTextFormatter).
--}}
<div class="alert alert-info py-2 px-3 admin-format-notice">
    <small>
        Teks akan dirapikan otomatis saat disimpan, termasuk kapitalisasi, spasi,
        tanda baca, gelar, dan istilah sekolah. Email, URL, serta nomor identitas tidak diubah.
    </small>
</div>
