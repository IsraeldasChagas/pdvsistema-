<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @include('layouts.partials.favicon')

        <title>Login - {{ config('app.name', 'PDV Sistema') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen font-sans text-gray-900 antialiased bg-[#0c1929] flex items-center justify-center px-4 py-6">
        <div class="mx-auto w-full max-w-sm">
            <div class="rounded-xl bg-white px-6 py-8 shadow-xl lg:px-8 lg:py-10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
