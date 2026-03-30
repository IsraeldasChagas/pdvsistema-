@php
    $dir = public_path('favicon');
    $stamp = is_file($dir . DIRECTORY_SEPARATOR . 'favicon.ico')
        ? (string) filemtime($dir . DIRECTORY_SEPARATOR . 'favicon.ico')
        : (string) time();
@endphp
@if (is_file($dir . DIRECTORY_SEPARATOR . 'favicon.ico'))
    <link rel="icon" href="{{ asset('favicon/favicon.ico') }}?v={{ $stamp }}" sizes="any">
    <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}?v={{ $stamp }}" type="image/x-icon">
@endif
@if (is_file($dir . DIRECTORY_SEPARATOR . 'favicon-32x32.png'))
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}?v={{ $stamp }}">
@endif
@if (is_file($dir . DIRECTORY_SEPARATOR . 'favicon-16x16.png'))
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}?v={{ $stamp }}">
@endif
@if (is_file($dir . DIRECTORY_SEPARATOR . 'apple-touch-icon.png'))
    <link rel="apple-touch-icon" href="{{ asset('favicon/apple-touch-icon.png') }}?v={{ $stamp }}">
@endif
@if (is_file($dir . DIRECTORY_SEPARATOR . 'site.webmanifest'))
    <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}?v={{ $stamp }}">
@endif
