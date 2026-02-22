@extends('tenant.layouts.app')

@section('pageTitle', 'ویرایش درآمد')

@section('styles')
    <link rel="stylesheet" href="/assets/css/persian-datepicker.css">
    <link rel="stylesheet" href="/assets/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection

@section('content')
    <div class="grid grid-cols-1 ">
        {{-- Income Edit Form --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-lg font-semibold mb-4">ویرایش درآمد</h2>
            <form class="space-y-4" method="POST" action="{{ route('tenant.income.update', $income) }}">
                @csrf
                @method('PUT')

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium mb-1">مبلغ (ریال)</label>
                    <input type="text" name="amount" value="{{ old('amount', $income->amount) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    @error('amount')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium mb-1">تاریخ</label>
                    <input type="text" id="datapicker" name="date" value="{{ old('date', $income->date) }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    @error('date')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Categories -->
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm mb-1">دسته‌بندی اصلی</label>
                        <select id="mainCategory" name="category" required
                            class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">انتخاب کنید</option>
                            @foreach ($categories[null] ?? [] as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('category', $income->main_category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm mb-1">زیر دسته</label>
                        <select id="subCategory" name="subcategory" class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">انتخاب کنید</option>
                            @php
                                $subs = $categories[$income->main_category_id] ?? [];
                            @endphp
                            @foreach ($subs as $sub)
                                <option value="{{ $sub->id }}"
                                    {{ old('subcategory', $income->sub_category_id) == $sub->id ? 'selected' : '' }}>
                                    {{ $sub->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subcategory')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-sm mb-1">برچسب‌ها</label>
                    <select name="tags[]" class="select w-full border rounded-lg" multiple>
                        @foreach ($tags as $tag)
                            <option value="{{ $tag }}"
                                {{ in_array($tag, old('tags', $income->tags ?? [])) ? 'selected' : '' }}>
                                {{ $tag }}
                            </option>
                        @endforeach
                    </select>
                    @error('tags')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Account -->
                <div>
                    <label class="block text-sm mb-1">واریز به حساب</label>
                    <select name="account" required class="w-full border-gray-300 rounded-lg shadow-sm">
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}"
                                {{ old('account', $income->to_account_id) == $acc->id ? 'selected' : '' }}>
                                {{ $acc->title }} (موجودی: {{ number_format($acc->balance) }} ریال)
                            </option>
                        @endforeach
                    </select>
                    @error('account')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Person -->
                <div>
                    <label class="block text-sm mb-1">شخص</label>
                    <select name="person" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">انتخاب کنید</option>
                        @foreach ($persons as $p)
                            <option value="{{ $p->id }}"
                                {{ old('person', $income->person_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('person')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm mb-1">توضیحات</label>
                    <textarea name="desc" class="w-full border-gray-300 rounded-lg shadow-sm h-24">{{ old('desc', $income->description) }}</textarea>
                    @error('desc')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-button class="w-full text-[18px]">به‌روزرسانی درآمد</x-button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/select2.min.js"></script>
    <script src="/assets/js/persianDatepicker.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Persian datepicker
            $('#datapicker').persianDatepicker({
                format: 'YYYY/MM/DD'
            });

            // Select2 for tags
            $('.select').select2({
                tags: true,
                width: '100%'
            });

            // Handle subcategory loading dynamically
            const categories = @json($categories);
            $('#mainCategory').on('change', function() {
                const subs = categories[this.value] ?? [];
                const subCat = $('#subCategory').empty().append('<option value="">انتخاب کنید</option>');
                subs.forEach(s => subCat.append(`<option value="${s.id}">${s.name}</option>`));
            });
        });
    </script>
@endsection
