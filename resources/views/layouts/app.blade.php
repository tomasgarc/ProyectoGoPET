<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- SEO Meta Tags -->
        <title>{{ config('app.name', 'GoPET') }} - Cuidado de Perros con Confianza</title>
        <meta name="description" content="GoPET conecta a dueños de perros con cuidadores locales de total confianza. Con pagos protegidos en depósito de garantía y valoraciones verificadas.">
        <meta name="keywords" content="cuidado de perros, paseador de perros, gopet, alojamiento de mascotas, cuidadores de confianza, residencia canina">
        <meta name="author" content="GoPET Intermodular">
        <meta name="robots" content="index, follow">

        <!-- OpenGraph Protocol -->
        <meta property="og:title" content="GoPET - Cuidado de Perros con Confianza">
        <meta property="og:description" content="Encuentra cuidadores locales verificados para tus mascotas con la tranquilidad del pago en custodia segura.">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ asset('favicon.png') }}">

        <!-- Sindicación RSS -->
        <link rel="alternate" type="application/rss+xml" title="Feed de Peticiones Activas - GoPET" href="{{ route('feeds.care-requests') }}">

        <!-- Favicons -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link rel="alternate icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-accent-950 bg-accent-50">
        <div class="min-h-screen bg-accent-50">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
