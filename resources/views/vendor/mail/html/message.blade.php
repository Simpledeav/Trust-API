<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.frontend.url')">
<!-- {{ config('app.name') }} -->
<img src="https://api.itrustinvestment.com/logo.png" style="width: 100px; height: 30px;" class="logo" alt="Logo">
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{{ $subcopy }}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.') <br>
<!-- Powered by <a href="{{ config('softweb.url') }}">{{ config('softweb.name') }}</a> -->
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
