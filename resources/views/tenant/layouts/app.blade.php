<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('pageTitle', 'سیستم حسابداری شخصی کیفاد')</title>
    <link rel="icon" href="/assets/img/favicon.png" sizes="32x32" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="/assets/css/style.css">
    @yield('styles')
    @livewireStyles
</head>



<body class="bg-gray-50">
    <div>
        @include('tenant.layouts.navbar')
        <div class="flex overflow-hidden bg-white pt-14">
            @include('tenant.layouts.sidebar')
            <div class="bg-gray-900 opacity-50 hidden fixed inset-0 z-10" id="sidebarBackdrop"></div>
            <div id="main-content" class="h-full w-full bg-gray-50 relative overflow-y-auto lg:mr-64">
                <main>
                    <div class="py-8 px-12 =">
                        @include('tenant.layouts.messages')
                        <h1 class="text-2xl font-bold mb-5">@yield('pageTitle')</h1>
                        <div class="w-full grid grid-cols-1 xl:grid-cols-1 2xl:grid-cols-1 gap-4 overflow-hidden">
                            @yield('content')

                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <script src="/assets/js/general.js"></script>
    @yield('scripts')
    @livewireScripts

</body>

</html>
