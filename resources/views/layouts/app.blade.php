@php
    $site = null;
    try {
        $site = \App\Models\SiteSetting::current()->site ?? config('content.site');
    } catch (\Throwable $e) {
        $site = config('content.site');
    }
@endphp
@include('partials.cards')
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $description ?? $site['description'] }}">
    @isset($noindex)
        <meta name="robots" content="noindex,nofollow">
    @endisset
    <title>DORA | {{ $title ?? 'Home' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    @unless(($admin ?? false) === true)
        @include('partials.header')
    @endunless

    <main class="{{ ($admin ?? false) === true ? '' : 'min-h-screen' }}">
        @yield('content')
    </main>

    @unless(($admin ?? false) === true)
        @include('partials.footer')
    @endunless
</body>
</html>
