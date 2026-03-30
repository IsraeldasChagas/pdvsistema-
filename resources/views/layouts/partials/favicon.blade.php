@php
    $dir = public_path('imagem');
    $faviconRel = null;
    foreach (['pdv.ico', 'pdv.png', 'pdv.jpg', 'pdv.jpeg', 'pdv.webp'] as $f) {
        if (is_file($dir . DIRECTORY_SEPARATOR . $f)) {
            $faviconRel = 'imagem/' . $f;
            break;
        }
    }
@endphp
@if ($faviconRel !== null)
    @php
        $ext = strtolower(pathinfo($faviconRel, PATHINFO_EXTENSION));
        $type = match ($ext) {
            'ico' => 'image/x-icon',
            'png' => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            default => 'image/png',
        };
    @endphp
    <link rel="icon" href="{{ asset($faviconRel) }}" type="{{ $type }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset($faviconRel) }}">
@endif
