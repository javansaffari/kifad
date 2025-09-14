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

<body class="font-sans antialiased">
    <div class="min-h-screen bg-[#F9FAFB]">
        @include('navigation-menu')

        <!-- Page Heading -->
        {{-- @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset --}}

        <!-- Page Content -->
        <main class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col gap-4 py-4">
            {{ $slot }}
        </main>
        <footer class="w-full py-2 bg-gray-50 text-center text-gray-400 text-xs  select-none">
            نسخه فعلی کیفاد 1.0.0
        </footer>

    </div>

    @livewireScripts

</body>

</html>



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

<body class="h-full bg-gray-100">

    <div id="root" class="flex h-full">
        <!-- Sidebar -->
        <aside class="w-64 bg-white shadow-lg h-full overflow-y-auto">
            <div class="flex flex-col h-full justify-between">
                <div>
                    <header class="p-4 border-b">
                        <div class="flex justify-between items-center">
                            <div class="logo">
                                <x-application-logo class="block h-7 w-auto" />

                            </div>
                            <button id="toggle-sidebar" class="p-2 md:block hidden">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg" class="text-gray-600">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.85719 3H15.1428C16.2266 2.99999 17.1007 2.99998 17.8086 3.05782C18.5375 3.11737 19.1777 3.24318 19.77 3.54497C20.7108 4.02433 21.4757 4.78924 21.955 5.73005C22.2568 6.32234 22.3826 6.96253 22.4422 7.69138C22.5 8.39925 22.5 9.27339 22.5 10.3572V13.6428C22.5 14.7266 22.5 15.6008 22.4422 16.3086C22.3826 17.0375 22.2568 17.6777 21.955 18.27C21.4757 19.2108 20.7108 19.9757 19.77 20.455C19.1777 20.7568 18.5375 20.8826 17.8086 20.9422C17.1008 21 16.2266 21 15.1428 21H8.85717C7.77339 21 6.89925 21 6.19138 20.9422C5.46253 20.8826 4.82234 20.7568 4.23005 20.455C3.28924 19.9757 2.52433 19.2108 2.04497 18.27C1.74318 17.6777 1.61737 17.0375 1.55782 16.3086C1.49998 15.6007 1.49999 14.7266 1.5 13.6428V10.3572C1.49999 9.27341 1.49998 8.39926 1.55782 7.69138C1.61737 6.96253 1.74318 6.32234 2.04497 5.73005C2.52433 4.78924 3.28924 4.02433 4.23005 3.54497C4.82234 3.24318 5.46253 3.11737 6.19138 3.05782C6.89926 2.99998 7.77341 2.99999 8.85719 3ZM6.35424 5.05118C5.74907 5.10062 5.40138 5.19279 5.13803 5.32698C4.57354 5.6146 4.1146 6.07354 3.82698 6.63803C3.69279 6.90138 3.60062 7.24907 3.55118 7.85424C3.50078 8.47108 3.5 9.26339 3.5 10.4V13.6C3.5 14.7366 3.50078 15.5289 3.55118 16.1458C3.60062 16.7509 3.69279 17.0986 3.82698 17.362C4.1146 17.9265 4.57354 18.3854 5.13803 18.673C5.40138 18.8072 5.74907 18.8994 6.35424 18.9488C6.97108 18.9992 7.76339 19 8.9 19H9.5V5H8.9C7.76339 5 6.97108 5.00078 6.35424 5.05118ZM11.5 5V19H15.1C16.2366 19 17.0289 18.9992 17.6458 18.9488C18.2509 18.8994 18.5986 18.8072 18.862 18.673C19.4265 18.3854 19.8854 17.9265 20.173 17.362C20.3072 17.0986 20.3994 16.7509 20.4488 16.1458C20.4992 15.5289 20.5 14.7366 20.5 13.6V10.4C20.5 9.26339 20.4992 8.47108 20.4488 7.85424C20.3994 7.24907 20.3072 6.90138 20.173 6.63803C19.8854 6.07354 19.4265 5.6146 18.862 5.32698C18.5986 5.19279 18.2509 5.10062 17.6458 5.05118C17.0289 5.00078 16.2366 5 15.1 5H11.5ZM5 8.5C5 7.94772 5.44772 7.5 6 7.5H7C7.55229 7.5 8 7.94772 8 8.5C8 9.05229 7.55229 9.5 7 9.5H6C5.44772 9.5 5 9.05229 5 8.5ZM5 12C5 11.4477 5.44772 11 6 11H7C7.55229 11 8 11.4477 8 12C8 12.5523 7.55229 13 7 13H6C5.44772 13 5 12.5523 5 12Z"
                                        fill="currentColor"></path>
                                </svg>
                            </button>
                        </div>

                    </header>
                    <nav class="p-4">
                        <ul class="space-y-2">
                            <li><a href="/user/dashboard/229/conversations"
                                    class="flex items-center p-2 hover:bg-gray-100 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" class="mr-2 text-gray-600" fill="none" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                        <polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline>
                                        <path
                                            d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z">
                                        </path>
                                    </svg>
                                    <span>گفتگوها</span>
                                </a></li>
                            <li><a href="/user/dashboard/229/knowledge"
                                    class="flex items-center p-2 hover:bg-gray-100 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" class="mr-2 text-gray-600" fill="none" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                                        <polyline points="2 17 12 22 22 17"></polyline>
                                        <polyline points="2 12 12 17 22 12"></polyline>
                                    </svg>
                                    <span>پایگاه دانش</span>
                                </a></li>
                            <li><a href="/user/dashboard/229/contacts"
                                    class="flex items-center p-2 hover:bg-gray-100 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" class="mr-2 text-gray-600" fill="none" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                    <span>مخاطبین</span>
                                </a></li>
                            <li><a href="/user/dashboard/229/settings"
                                    class="flex items-center p-2 hover:bg-gray-100 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" class="mr-2 text-gray-600" fill="none" stroke="currentColor"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                        <circle cx="12" cy="12" r="3"></circle>
                                        <path
                                            d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                                        </path>
                                    </svg>
                                    <span>تنظیمات</span>
                                </a></li>
                            <li class="text-gray-500 font-medium py-2">عمومی</li>
                            <li><a href="/user/dashboard/229/billing"
                                    class="flex items-center p-2 hover:bg-gray-100 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" class="mr-2 text-gray-600" fill="none"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2">
                                        <rect height="16" rx="2" ry="2" width="22" x="1"
                                            y="4"></rect>
                                        <line x1="1" x2="23" y1="10" y2="10"></line>
                                    </svg>
                                    <span>صورتحساب و مالی</span>
                                </a></li>
                            <li><a href="/user/dashboard/229/support"
                                    class="flex items-center p-2 bg-gray-200 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" class="mr-2 text-gray-600" fill="none"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2">
                                        <path d="M3 18v-6a9 9 0 0 1 18 0v6"></path>
                                        <path
                                            d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3zM3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z">
                                        </path>
                                    </svg>
                                    <span>پشتیبانی</span>
                                </a></li>
                            <li><a href="/user/dashboard/229/help"
                                    class="flex items-center p-2 hover:bg-gray-100 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                        height="24" class="mr-2 text-gray-600" fill="none"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2">
                                        <circle cx="12" cy="12" r="10"></circle>
                                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                                        <line x1="12" x2="12.01" y1="17" y2="17"></line>
                                    </svg>
                                    <span>راهنما</span>
                                </a></li>
                        </ul>
                    </nav>
                </div>
                <footer class="p-4">
                    <div class="border border-danger-400 rounded-lg p-4 bg-danger-100">
                        <p class="text-center text-sm text-danger-700 mb-2">اشتراک شما به پایان رسیده است، لطفا اشتراک
                            خود را تمدید کنید.</p>
                        <a href="/user/dashboard/229/billing"
                            class="block w-full text-center bg-danger-400 text-white py-2 rounded hover:bg-danger-500">تمدید
                            اشتراک</a>
                    </div>
                </footer>
            </div>
        </aside>
        <!-- Main Content -->
        <main class="flex-1 p-4">
            <div class="bg-white shadow-lg rounded-lg">
                <!-- Topbar -->
                <div class="p-4 border-b flex justify-between items-center">
                    <button id="open-sidebar" class="p-2 md:hidden" aria-label="باز کردن منوی کناری">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                            xmlns="http://www.w3.org/2000/svg" class="text-gray-600">
                            <line x1="3" x2="21" y1="12" y2="12" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></line>
                            <line x1="3" x2="21" y1="6" y2="6" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></line>
                            <line x1="3" x2="21" y1="18" y2="18" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></line>
                        </svg>
                    </button>
                    <div class="logo">
                        <x-application-logo class="block h-9 w-auto" />

                    </div>

                    <div class="relative">
                        <button class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded" aria-label="پروفایل">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24"
                                height="24" class="text-gray-600" fill="none" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span class="hidden md:inline-block text-sm">محمد</span>
                        </button>
                        <div class="absolute left-0 mt-2 w-48 bg-white shadow-lg rounded-lg z-50 hidden">
                            <ul class="py-2">
                                <li><a href="/user/dashboard/229/profile"
                                        class="block px-4 py-2 hover:bg-gray-100">مدیریت حساب کاربری</a></li>
                                <li><button class="block w-full text-right px-4 py-2 hover:bg-gray-100">خروج</button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Content -->
                <div class="p-4">
                    <header class="mb-4">
                        <nav class="flex items-center gap-2 text-sm text-gray-600">
                            <a href="/user/dashboard" class="hover:text-gray-800">مدیریت</a>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16"
                                height="16" class="text-gray-600" fill="none" stroke="currentColor"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
                                <polyline points="15 18 9 12 15 6"></polyline>
                            </svg>
                            <a href="/user/dashboard/229/support" class="text-gray-800">پشتیبانی</a>
                        </nav>
                        <h1 class="text-2xl font-bold mt-2">
                            @yield('pageTitle')
                        </h1>
                    </header>
                    <div>

                        {{ $slot }}

                    </div>
                </div>
            </div>
        </main>
    </div>

</body>

</html>
