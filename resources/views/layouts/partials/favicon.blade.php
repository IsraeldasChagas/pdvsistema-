@php
    $dir = public_path('imagem');
    $override = null;
    foreach (['pdv.ico', 'pdv.png', 'pdv.jpg', 'pdv.jpeg', 'pdv.webp'] as $f) {
        if (is_file($dir . DIRECTORY_SEPARATOR . $f)) {
            $override = 'imagem/' . $f;
            break;
        }
    }

    $svgPath = public_path('favicon.svg');
    $svgV = is_file($svgPath) ? (string) filemtime($svgPath) : '1';
@endphp
@if ($override !== null)
    @php
        $ext = strtolower(pathinfo($override, PATHINFO_EXTENSION));
        $type = match ($ext) {
            'ico' => 'image/x-icon',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/png',
        };
    @endphp
    @php $ovM = (string) filemtime(public_path($override)); @endphp
    <link rel="icon" href="{{ asset($override) }}?v={{ $ovM }}" type="{{ $type }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset($override) }}?v={{ $ovM }}">
@else
    <link rel="icon" href="{{ asset('favicon.svg') }}?v={{ $svgV }}" type="image/svg+xml" sizes="any">
    <link rel="shortcut icon" href="{{ asset('favicon.svg') }}?v={{ $svgV }}" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}?v={{ $svgV }}">
@endif
