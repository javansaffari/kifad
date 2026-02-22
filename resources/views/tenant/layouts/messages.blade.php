{{-- Flash Messages Component --}}

{{-- Success Message --}}
@if (session('success'))
    <div class="relative rounded-xl mb-4 p-4 bg-green-50 border border-green-200 text-green-800 shadow-sm flex items-start justify-between"
        role="alert">
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 011.414-1.414L8.414 12.172l7.879-7.879a1 1 0 011.414 0z"
                    clip-rule="evenodd" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="absolute top-2 right-2 text-green-800 hover:text-green-900 focus:outline-none"
            onclick="this.parentElement.style.display='none';">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

{{-- Error Message --}}
@if (session('error'))
    <div class="relative rounded-xl mb-4 p-4 bg-red-50 border border-red-200 text-red-800 shadow-sm flex items-start justify-between"
        role="alert">
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9V6a1 1 0 112 0v3a1 1 0 11-2 0zm0 4a1 1 0 112 0 1 1 0 01-2 0z"
                    clip-rule="evenodd" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
        <button type="button" class="absolute top-2 right-2 text-red-800 hover:text-red-900 focus:outline-none"
            onclick="this.parentElement.style.display='none';">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="relative rounded-xl mb-4 p-4 bg-red-50 border border-red-200 text-red-800 shadow-sm flex flex-col gap-2"
        role="alert">
        <div class="flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm-1-9V6a1 1 0 112 0v3a1 1 0 11-2 0zm0 4a1 1 0 112 0 1 1 0 01-2 0z"
                    clip-rule="evenodd" />
            </svg>
            <span>لطفاً خطاهای زیر را اصلاح کنید:</span>
        </div>
        <ul class="list-disc pl-6 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="absolute top-2 right-2 text-red-800 hover:text-red-900 focus:outline-none"
            onclick="this.parentElement.style.display='none';">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
@endif
