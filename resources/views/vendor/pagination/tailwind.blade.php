@if ($paginator->hasPages())
    <nav role="navigation" aria-label="ناوبری صفحه‌بندی">

        <div class="flex gap-2 items-center justify-between sm:hidden">

            @if ($paginator->onFirstPage())
                <span
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 cursor-not-allowed rounded-md">
                    قبلی
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-800 bg-white border border-gray-300 rounded-md hover:bg-gray-100">
                    قبلی
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-800 bg-white border border-gray-300 rounded-md hover:bg-gray-100">
                    بعدی
                </a>
            @else
                <span
                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-300 cursor-not-allowed rounded-md">
                    بعدی
                </span>
            @endif

        </div>

        <div class="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">

            <div>
                <p class="text-sm text-gray-700 leading-5">
                    نمایش
                    @if ($paginator->firstItem())
                        <span class="font-medium">{{ $paginator->firstItem() }}</span>
                        تا
                        <span class="font-medium">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    از
                    <span class="font-medium">{{ $paginator->total() }}</span>
                    نتیجه
                </p>
            </div>

            <div>
                <span class="inline-flex direction-ltr rtl:flex-row-reverse shadow-sm rounded-md">

                    {{-- لینک قبلی --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="صفحه قبلی">
                            <span
                                class="inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed rounded-l-md">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                            class="inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-100"
                            aria-label="صفحه قبلی">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @endif

                    {{-- صفحات --}}
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span
                                    class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span
                                            class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 cursor-default">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}"
                                        class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-100"
                                        aria-label="رفتن به صفحه {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- لینک بعدی --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                            class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-100"
                            aria-label="صفحه بعدی">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="صفحه بعدی">
                            <span
                                class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 bg-white border border-gray-300 cursor-not-allowed rounded-r-md">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif

                </span>
            </div>
        </div>
    </nav>
@endif
