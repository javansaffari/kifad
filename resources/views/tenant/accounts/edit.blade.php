@extends('tenant.layouts.app')

@section('pageTitle', 'ویرایش حساب')

@section('styles')
    <link rel="stylesheet" href="/assets/css/persian-datepicker.css">
    <link rel="stylesheet" href="/assets/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
        {{-- Account Form --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-lg font-semibold mb-4">ویرایش حساب</h2>
            <form class="space-y-4" method="POST" action="{{ route('tenant.accounts.update', $account->id) }}">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm mb-2">عنوان حساب</label>
                    <input type="text" name="title" value="{{ old('title', $account->title) }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                    @error('title')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm mb-2">موجودی (ریال)</label>
                    <input type="text" name="balance" value="{{ old('balance', $account->balance) }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                    @error('balance')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm mb-2">نوع حساب</label>
                    <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">انتخاب کنید</option>
                        @foreach ($accountTypes as $type)
                            <option value="{{ $type }}" @selected(old('type', $account->type) == $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm mb-2">بانک</label>
                    <select name="bank" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">انتخاب کنید</option>
                        @foreach ($banks as $bank)
                            <option value="{{ $bank }}" @selected(old('bank', $account->bank) == $bank)>{{ $bank }}</option>
                        @endforeach
                    </select>
                    @error('bank')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm mb-2">توضیح</label>
                    <textarea name="desc" class="w-full border-gray-300 rounded-lg shadow-sm h-24">{{ old('desc', $account->description) }}</textarea>
                    @error('desc')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-button class="text-[18px] w-full">بروزرسانی حساب</x-button>
                </div>
            </form>
        </div>


    </div>

@endsection

@section('scripts')

@endsection
