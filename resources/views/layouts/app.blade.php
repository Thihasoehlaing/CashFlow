@props(['title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ app()->getLocale() === 'my' ? 'font-myanmar' : '' }}">
<head>
    @include('partials.head', ['title' => $title])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="min-h-screen bg-[var(--color-bg)] text-[var(--color-text)] antialiased">
    <div class="min-h-screen lg:pl-[260px]">
        <x-sidebar />
        <x-navbar :title="$title" />
        <main class="page-enter px-4 pb-28 pt-24 sm:px-6 lg:px-8 lg:pb-10">
            {{ $slot }}
        </main>
        <x-toast />
    </div>
</body>
</html>
