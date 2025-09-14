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
