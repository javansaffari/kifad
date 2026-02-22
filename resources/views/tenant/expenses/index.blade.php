@extends('tenant.layouts.app')

@section('pageTitle', 'مدیریت هزینه‌ها')

@section('styles')
    <link rel="stylesheet" href="/assets/css/persian-datepicker.css">
    <link rel="stylesheet" href="/assets/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Expense Form --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-lg font-semibold mb-4">ثبت هزینه جدید</h2>
            <form class="space-y-4" method="POST" action="{{ route('tenant.expenses.store') }}">
                @csrf

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium mb-1">مبلغ (ریال)</label>
                    <input type="text" name="amount" value="{{ old('amount') }}" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    @error('amount')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label class="block text-sm font-medium mb-1">تاریخ</label>
                    <input type="text" id="datapicker" name="date" value="{{ old('date') }}" required
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
                                <option value="{{ $cat->id }}" {{ old('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}</option>
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
                            @if (old('category') && isset($categories[old('category')]))
                                @foreach ($categories[old('category')] as $sub)
                                    <option value="{{ $sub->id }}"
                                        {{ old('subcategory') == $sub->id ? 'selected' : '' }}>
                                        {{ $sub->name }}
                                    </option>
                                @endforeach
                            @endif
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
                                {{ collect(old('tags'))->contains($tag) ? 'selected' : '' }}>
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
                    <label class="block text-sm mb-1">برداشت از حساب</label>
                    <select name="account" required class="w-full border-gray-300 rounded-lg shadow-sm">
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}" {{ old('account') == $acc->id ? 'selected' : '' }}>
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
                            <option value="{{ $p->id }}" {{ old('person') == $p->id ? 'selected' : '' }}>
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
                    <textarea name="desc" class="w-full border-gray-300 rounded-lg shadow-sm h-24">{{ old('desc') }}</textarea>
                    @error('desc')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-button class="w-full text-[18px]">ثبت هزینه</x-button>
                </div>
            </form>
        </div>

        {{-- Expense Chart --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-lg font-semibold mb-4">تقسیم‌بندی هزینه‌ها</h2>
            <canvas id="chart" height="200"></canvas>
        </div>
    </div>

    {{-- Expenses Table --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 mt-6">
        <h2 class="text-lg font-semibold mb-4">لیست هزینه‌ها</h2>

        <div class="overflow-x-auto">
            @if ($expenses->isEmpty())
                <div class="text-center py-4 text-gray-500">

                    هنوز هیچ هزینه‌ای ثبت نشده است. همین حالا می‌توانید اولین هزینه خود را ثبت کنید!
                </div>
            @else
                <table class="min-w-full border text-sm text-gray-700 text-center">
                    <thead class="bg-gray-100">
                        <tr>
                            @foreach (['تاریخ', 'مبلغ (ریال)', 'دسته', 'زیر دسته', 'حساب', 'شخص', 'توضیحات', 'عملیات'] as $th)
                                <th class="border px-2 py-2">{{ $th }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($expenses as $ex)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-2 py-2">{{ $ex->date ?? '-' }}</td>
                                <td class="border px-2 py-2 text-red-600 font-semibold">{{ number_format($ex->amount) }}
                                </td>
                                <td class="border px-2 py-2">{{ $ex->mainCategory?->name ?? '-' }}</td>
                                <td class="border px-2 py-2">{{ $ex->subCategory?->name ?? '-' }}</td>
                                <td class="border px-2 py-2">{{ $ex->fromAccount?->title ?? '-' }}</td>
                                <td class="border px-2 py-2">{{ $ex->person?->name ?? '-' }}</td>
                                <td class="border px-2 py-2">{{ $ex->description ?? '-' }}</td>
                                <td class="border px-2 py-2 flex justify-center gap-2">
                                    <a href="{{ route('tenant.expenses.edit', $ex) }}"
                                        class="text-blue-600 hover:underline px-2 py-1 border rounded">ویرایش</a>
                                    <form action="{{ route('tenant.expenses.destroy', $ex) }}" method="POST"
                                        class="inline-block delete-confirm">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:underline px-2 py-1 border rounded">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/select2.min.js"></script>
    <script src="/assets/js/chart.umd.min.js"></script>
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

            // Handle subcategory loading
            const categories = @json($categories);
            $('#mainCategory').on('change', function() {
                const subs = categories[this.value] ?? [];
                const subCat = $('#subCategory').empty().append('<option value="">انتخاب کنید</option>');
                subs.forEach(s => subCat.append(`<option value="${s.id}">${s.name}</option>`));
            });

            // Confirm before delete
            $('.delete-confirm').on('submit', function(e) {
                if (!confirm('آیا از حذف این هزینه اطمینان دارید؟')) e.preventDefault();
            });

            // Render chart
            const ctx = document.getElementById('chart');
            const labels = @json(array_keys($chartData));
            const data = @json(array_values($chartData));
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels.length ? labels : ['بدون داده'],
                    datasets: [{
                        label: 'مجموع هزینه‌ها (ریال)',
                        data: data.length ? data : [0],
                        backgroundColor: ['#f87171', '#60a5fa', '#34d399', '#fbbf24', '#a78bfa']
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    family: 'YekanBakh',
                                    size: 14
                                }
                            }
                        },
                        tooltip: {
                            titleFont: {
                                family: 'YekanBakh',
                                size: 14
                            },
                            bodyFont: {
                                family: 'YekanBakh',
                                size: 12
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
