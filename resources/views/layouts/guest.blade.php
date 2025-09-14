<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('pageTitle', 'سیستم حسابداری شخصی کیفاد')</title>
    <link rel="icon" href="/assets/img/favicon.png" sizes="32x32" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="/assets/css/style.css">
    @livewireStyles
</head>

<body>
    <div class="">
        {{ $slot }}
    </div>

    @livewireScripts
</body>

</html>
