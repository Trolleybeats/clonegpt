<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="title" content="RunAI - AI chatbot pour la course à pied">
        <meta name="description" content="Assistant IA personnel pour des programmes de course à pied personnalisés en quelques secondes.">

        {{-- Open Graph defaults (override via $metaTitle, $metaDescription, $metaImage, $metaType, $metaUrl, $metaSiteName) --}}
        @php
            $og = [
                'title' => $metaTitle ?? 'RunAI - AI chatbot pour la course à pied',
                'description' => $metaDescription ?? 'Assistant IA personnel pour des programmes de course à pied personnalisés en quelques secondes.',
                'type' => $metaType ?? 'website',
                'url' => $metaUrl ?? url()->current(),
                'image' => $metaImage ?? asset('apple-touch-icon.png'),
                'site_name' => $metaSiteName ?? config('app.name', 'Laravel'),
            ];
        @endphp
        <meta property="og:title" content="{{ $og['title'] }}">
        <meta property="og:description" content="{{ $og['description'] }}">
        <meta property="og:type" content="{{ $og['type'] }}">
        <meta property="og:url" content="{{ $og['url'] }}">
        <meta property="og:image" content="{{ $og['image'] }}">
        <meta property="og:site_name" content="{{ $og['site_name'] }}">
        <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
