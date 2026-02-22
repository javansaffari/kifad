@extends('tenant.layouts.app')

@section('pageTitle', 'ویرایش شخص')

@section('styles')
    <link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection

@section('content')
    <div class="grid grid-cols-1 ">

        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-lg font-semibold mb-4">ویرایش شخص</h2>

            <form action="{{ route('tenant.person.update', $person->id) }}" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                {{-- Name --}}
                <div>
                    <label class="block text-sm mb-1">نام</label>
                    <input type="text" name="name" value="{{ old('name', $person->name) }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    @error('name')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Type --}}
                <div>
                    <label class="block text-sm mb-1">نوع شخص</label>
                    <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">انتخاب کنید</option>
                        @foreach ($personTypes as $type)
                            <option value="{{ $type }}" {{ old('type', $person->type) == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm mb-1">توضیح</label>
                    <textarea name="desc" class="w-full border border-gray-300 rounded-lg px-3 py-2 h-24">{{ old('desc', $person->description) }}</textarea>
                    @error('desc')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-button class="w-full text-[18px]">به‌روزرسانی شخص</x-button>
                </div>
            </form>
        </div>
    @endsection

    @section('scripts')
        <script src="/assets/js/jquery.min.js"></script>
        <script src="/assets/js/select2.min.js"></script>
    @endsection
