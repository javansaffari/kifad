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

<body class="bg-gray-50">

    <div>
        <!-- Navbar -->
        <nav class="bg-white border-b border-gray-200 fixed z-30 w-full">
            <div class="px-3 py-3 lg:px-5 lg:pl-3">
                <div class="flex items-center justify-between">


                    <!-- Left: Logo & Mobile toggle -->
                    <div class="flex items-center justify-start">

                        <button id="toggleSidebarMobile" type="button" aria-expanded="true" aria-controls="sidebar"
                            class="lg:hidden ml-2 text-gray-600 hover:text-gray-900 cursor-pointer p-2 hover:bg-gray-100 focus:bg-gray-100 focus:ring-2 focus:ring-gray-100 rounded">
                            <svg id="toggleSidebarMobileHamburger" class="w-6 h-6" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <svg id="toggleSidebarMobileClose" class="w-6 h-6 hidden" fill="currentColor"
                                viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>



                        <a href="#" class="text-xl font-bold flex items-center lg:ml-2.5">
                            <x-application-logo class="block h-8 w-auto" />
                        </a>
                    </div>

                    <!-- Right: Info -->
                    <div class=" lg:flex items-center">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                    <button
                                        class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                        <img class="size-8 rounded-full object-cover"
                                            src="{{ Auth::user()->profile_photo_url }}"
                                            alt="{{ Auth::user()->name }}" />
                                    </button>
                                @else
                                    <span class="inline-flex rounded-md">
                                        <button type="button"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150">
                                            {{ Auth::user()->name }}

                                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                    </span>
                                @endif
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link href="{{ route('profile.show') }}">
                                    حساب کاربری
                                </x-dropdown-link>

                                <div class="border-t border-gray-200"></div>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf

                                    <x-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                        خروج
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Sidebar + Content -->
        <div class="flex overflow-hidden bg-white pt-14">

            <!-- Sidebar -->
            <aside id="sidebar"
                class="fixed hidden z-20 h-full top-0 right-0 pt-16 flex lg:flex flex-shrink-0 flex-col w-64 transition-width duration-75"
                aria-label="Sidebar">
                <div class="relative flex-1 flex flex-col min-h-0 border-r border-gray-200 bg-white pt-0">
                    <div class="flex-1 flex flex-col pt-5 pb-4 overflow-y-auto">
                        <div class="flex-1 px-3 bg-white divide-y space-y-1">

                            <!-- Main Menu -->
                            <ul class="space-y-2 pb-2">
                                <li>
                                    <a href="#"
                                        class="text-base text-gray-900 font-normal rounded-lg flex items-center p-2 hover:bg-gray-100 group">
                                        <svg class="w-6 h-6 text-gray-500 group-hover:text-gray-900 transition duration-75"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path>
                                            <path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path>
                                        </svg>
                                        <span class="ml-3">داشبورد</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#"
                                        class="text-base text-gray-900 font-normal rounded-lg flex items-center p-2 hover:bg-gray-100 group">
                                        <svg class="w-6 h-6 text-gray-500 group-hover:text-gray-900 transition duration-75"
                                            fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                            </path>
                                        </svg>
                                        <span class="ml-3 flex-1 whitespace-nowrap">کانبان</span>
                                        <span
                                            class="bg-gray-200 text-gray-800 ml-3 text-sm font-medium inline-flex items-center justify-center px-2 rounded-full">Pro</span>
                                    </a>
                                </li>
                            </ul>

                            <!-- Personal Accounting Menu -->
                            <div class="space-y-2 pt-2">
                                <h3 class="text-gray-400 text-xs uppercase px-2">حسابداری شخصی</h3>
                                <ul class="space-y-2">
                                    <li>
                                        <a href="#expenses"
                                            class="text-base text-gray-900 font-normal rounded-lg hover:bg-gray-100 flex items-center p-2 group">
                                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM11 17h2v-6h-2v6zm0-8h2V7h-2v2z" />
                                            </svg>
                                            <span class="ml-4">هزینه‌ها</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#income"
                                            class="text-base text-gray-900 font-normal rounded-lg hover:bg-gray-100 flex items-center p-2 group">
                                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15h2v-6h-2v6zm0-8h2V7h-2v2z" />
                                            </svg>
                                            <span class="ml-4">درآمدها</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#checks"
                                            class="text-base text-gray-900 font-normal rounded-lg hover:bg-gray-100 flex items-center p-2 group">
                                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M21 7H3v14h18V7zm-2 12H5V9h14v10zM7 11h5v2H7v-2z" />
                                            </svg>
                                            <span class="ml-4">چک‌ها</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#transactions"
                                            class="text-base text-gray-900 font-normal rounded-lg hover:bg-gray-100 flex items-center p-2 group">
                                            <svg class="w-5 h-5 text-gray-500 group-hover:text-gray-900"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M3 3h18v2H3V3zm0 6h18v2H3V9zm0 6h18v2H3v-2zm0 6h18v2H3v-2z" />
                                            </svg>
                                            <span class="ml-4">تراکنش‌ها</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>

                            <!-- Upgrade -->
                            <div class="space-y-2 pt-2">
                                <a href="#"
                                    class="text-base text-gray-900 font-normal rounded-lg hover:bg-gray-100 group transition duration-75 flex items-center p-2">
                                    <svg class="w-5 h-5 text-gray-500 flex-shrink-0 group-hover:text-gray-900"
                                        fill="currentColor" viewBox="0 0 512 512">
                                        <path fill="currentColor"
                                            d="M378.7 32H133.3L256 182.7L378.7 32zM512 192l-107.4-141.3L289.6 192H512zM107.4 50.67L0 192h222.4L107.4 50.67zM244.3 474.9C247.3 478.2 251.6 480 256 480s8.653-1.828 11.67-5.062L510.6 224H1.365L244.3 474.9z">
                                        </path>
                                    </svg>
                                    <span class="ml-4">Upgrade to Pro</span>
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </aside>

            <div class="bg-gray-900 opacity-50 hidden fixed inset-0 z-10" id="sidebarBackdrop"></div>

            <!-- Main Content -->
            <div id="main-content" class="h-full w-full bg-gray-50 relative overflow-y-auto lg:mr-64">
                <main>
                    <div class="pt-6 px-4">

                        {{-- پیام موفقیت --}}
                        @if (session('success'))
                            <div class="rounded-xl mb-4 p-3 bg-green-100 text-green-800">
                                {{ session('success') }}
                            </div>
                        @endif

                        {{-- پیام خطاها --}}
                        @if ($errors->any())
                            <div class="rounded-xl mb-4 p-3 bg-red-100 text-red-800">
                                <ul class="list-disc pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                        <h1 class="text-2xl font-bold mb-5">@yield('pageTitle')</h1>
                        <div class="w-full grid grid-cols-1 xl:grid-cols-1 2xl:grid-cols-1 gap-4">
                            <div class="bg-white shadow rounded-lg p-4 sm:p-6 xl:p-8  2xl:col-span-2">
                                {{ $slot }}
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>


    </div>

    @livewireScripts

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButton = document.getElementById('toggleSidebarMobile');
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const hamburgerIcon = document.getElementById('toggleSidebarMobileHamburger');
            const closeIcon = document.getElementById('toggleSidebarMobileClose');

            toggleButton.addEventListener('click', function() {
                sidebar.classList.toggle('hidden');
                backdrop.classList.toggle('hidden');
                hamburgerIcon.classList.toggle('hidden');
                closeIcon.classList.toggle('hidden');
            });

            backdrop.addEventListener('click', function() {
                sidebar.classList.add('hidden');
                backdrop.classList.add('hidden');
                hamburgerIcon.classList.remove('hidden');
                closeIcon.classList.add('hidden');
            });
        });
    </script>


</body>

</html>
