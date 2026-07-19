<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
SD Muhammadiyah Komplek Kolombo
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
Email ini dikirim otomatis oleh sistem Website Resmi SD Muhammadiyah Komplek Kolombo.<br>
Mohon tidak membalas email ini.<br>
&copy; {{ date('Y') }} SD Muhammadiyah Komplek Kolombo.
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
