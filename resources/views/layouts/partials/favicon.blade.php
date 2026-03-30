@php
    $icon = null;
    $base = public_path();
    foreach (['icon1.ico', 'icon1.png', 'icon1.svg', 'icon1.jpg', 'icon1.jpeg', 'icon1.webp'] as $f) {
        if (is_file($base . DIRECTORY_SEPARATOR . $f)) {
            $icon = $f;
            break;
        }
    }
@endphp
@if ($icon !== null)
    @php
        $ext = strtolower(pathinfo($icon, PATHINFO_EXTENSION));
        $type = match ($ext) {
            'ico' => 'image/x-icon',
            'png' => 'image/png',
            'svg' => 'image/svg+xml',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/png',
        };
        $m = (string) filemtime($base . DIRECTORY_SEPARATOR . $icon);
    @endphp
    <link rel="icon" href="{{ asset($icon) }}?v={{ $m }}" type="{{ $type }}" sizes="any">
    <link rel="shortcut icon" href="{{ asset($icon) }}?v={{ $m }}" type="{{ $type }}">
    <link rel="apple-touch-icon" href="{{ asset($icon) }}?v={{ $m }}">
@endif
