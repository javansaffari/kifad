@extends('tenant.layouts.app')

@section('pageTitle', 'گزارشات مالی')

@section('styles')
    <link rel="stylesheet" href="/assets/css/persian-datepicker.css">
    <link rel="stylesheet" href="/assets/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection

@section('content')

    {{-- ۱. فیلترهای گزارش --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 mb-6">
        <h2 class="text-lg font-semibold mb-4 flex items-center gap-2">
            فیلتر گزارشات
        </h2>
        <form method="GET" action="{{ route('tenant.reports') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            {{-- سال --}}
            <div>
                <label class="block text-sm mb-1">سال</label>
                <select name="year" class="w-full border-gray-300 rounded-lg shadow-sm">
                    @foreach (range(now()->year - 5, now()->year + 1) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            {{-- نوع تراکنش --}}
            <div>
                <label class="block text-sm mb-1">نوع تراکنش</label>
                <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm">
                    <option value="">همه</option>
                    <option value="income" {{ $type == 'income' ? 'selected' : '' }}>درآمد</option>
                    <option value="expense" {{ $type == 'expense' ? 'selected' : '' }}>هزینه</option>
                </select>
            </div>
            {{-- حساب --}}
            <div>
                <label class="block text-sm mb-1">حساب</label>
                <select name="account_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                    <option value="">همه حساب‌ها</option>
                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>
                            {{ $acc->title }}</option>
                    @endforeach
                </select>
            </div>
            {{-- دسته‌بندی --}}
            <div>
                <label class="block text-sm mb-1">دسته‌بندی</label>
                <select name="category_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                    <option value="">همه دسته‌ها</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            {{-- جستجو --}}
            <div>
                <label class="block text-sm mb-1">جستجو</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    class="w-full border-gray-300 rounded-lg shadow-sm" placeholder="توضیحات...">
            </div>
            <div class="flex items-end">
                <button type="submit"
                    class="w-full bg-slate-800 text-white rounded-lg py-2 hover:bg-slate-700 transition">بروزرسانی</button>
            </div>
        </form>
    </div>

    {{-- ۲. کارت‌های کلیدی --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6 text-center">
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-sm text-gray-500">نقدینگی کل</p>
            <p class="text-xl font-bold text-blue-600">{{ number_format($totalBalance) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-sm text-gray-500">درآمد سال</p>
            <p class="text-xl font-bold text-green-600">{{ number_format(collect($monthlyMatrix)->sum('income')) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-sm text-gray-500">هزینه سال</p>
            <p class="text-xl font-bold text-red-600">{{ number_format(collect($monthlyMatrix)->sum('expense')) }}</p>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-sm text-gray-500">تراز نهایی</p>
            <p
                class="text-xl font-bold {{ collect($monthlyMatrix)->sum('balance') >= 0 ? 'text-emerald-600' : 'text-orange-600' }}">
                {{ number_format(collect($monthlyMatrix)->sum('balance')) }}
            </p>
        </div>
    </div>

    {{-- ۳. نمودارها --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-lg font-semibold mb-4">روند ماهانه درآمد و هزینه</h2>
            <div class="chart-container">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-lg font-semibold mb-4">توزیع هزینه‌ها (برترین‌ها)</h2>
            <div class="chart-container">
                <canvas id="pieChart"></canvas>
            </div>
        </div>
    </div>

    {{-- ۴. ماتریس ماهانه --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 mb-6">
        <h2 class="text-lg font-semibold mb-4 text-center md:text-right"> سال مالی {{ $year }}</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-center matrix-table">
                <thead class="bg-gray-100">
                    <tr>
                        <th>ماه</th>
                        <th>درآمد</th>
                        <th>هزینه</th>
                        <th>چک صادره</th>
                        <th>بدهی</th>
                        <th>برایند</th>
                        <th>رشد</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($monthlyMatrix as $data)
                        <tr class="hover:bg-gray-50">
                            <td class="font-bold">{{ $data['month_name'] }}</td>
                            <td class="text-green-600 font-semibold">{{ number_format($data['income']) }}</td>
                            <td class="text-red-600">{{ number_format($data['expense']) }}</td>
                            <td class="text-amber-600">{{ number_format($data['cheque_out']) }}</td>
                            <td class="text-gray-500">{{ number_format($data['debt']) }}</td>
                            <td class="font-bold">{{ number_format($data['balance']) }}</td>
                            <td>
                                @if ($data['growth'] >= 0)
                                    <span class="text-green-600 font-bold">↑ %{{ $data['growth'] }}</span>
                                @else
                                    <span class="text-red-500 font-bold">↓ %{{ abs($data['growth']) }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ۵. ماتریس دسته‌بندی‌های هزینه --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 mb-6">
        <h2 class="text-lg font-semibold mb-4">تحلیل هزینه‌ها</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-[12px] text-center matrix-table">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th>دسته‌بندی</th>
                        @foreach ($monthlyMatrix as $m => $data)
                            <th>{{ $data['month_name'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($catMatrixExpense as $catName => $months)
                        <tr class="hover:bg-gray-50">
                            <td class="text-right font-bold bg-gray-50">{{ $catName }}</td>
                            @foreach ($months as $val)
                                <td>{{ number_format($val) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ۶. ماتریس دسته‌بندی‌های درآمد --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 mb-6">
        <h2 class="text-lg font-semibold mb-4">تحلیل درآمدها</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full text-[12px] text-center matrix-table">
                <thead class="bg-green-800 text-white">
                    <tr>
                        <th>دسته‌بندی</th>
                        @foreach ($monthlyMatrix as $m => $data)
                            <th>{{ $data['month_name'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($catMatrixIncome as $catName => $months)
                        <tr class="hover:bg-gray-50">
                            <td class="text-right font-bold bg-gray-50">{{ $catName }}</td>
                            @foreach ($months as $val)
                                <td>{{ number_format($val) }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ۷. لیست تراکنش‌ها --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 mt-6">
        <h2 class="text-lg font-semibold mb-4">لیست تراکنش‌ها</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm text-gray-700 text-center">
                <thead class="bg-gray-100">
                    <tr>
                        @foreach (['نوع', 'تاریخ', 'مبلغ (ریال)', 'دسته‌بندی', 'حساب', 'شخص', 'توضیحات', 'عملیات'] as $th)
                            <th class="border px-2 py-2 whitespace-nowrap">{{ $th }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $inc)
                        <tr class="hover:bg-gray-50">
                            <td
                                class="border px-2 py-2 font-semibold whitespace-nowrap
                            {{ $inc->type === 'expense' ? 'text-red-600' : ($inc->type === 'income' ? 'text-green-600' : 'text-blue-600') }}">
                                {{ $inc->type === 'expense' ? 'هزینه' : ($inc->type === 'income' ? 'درآمد' : 'انتقال وجه') }}
                            </td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $inc->date }}</td>
                            <td
                                class="border px-2 py-2 font-semibold whitespace-nowrap
                            {{ $inc->type === 'expense' ? 'text-red-600' : ($inc->type === 'income' ? 'text-green-600' : 'text-blue-600') }}">
                                {{ number_format($inc->amount) }}
                            </td>
                            <td class="border px-2 py-2 whitespace-nowrap">
                                @if ($inc->type === 'transfer')
                                    {{ $inc->from }} - {{ $inc->to }}
                                @else
                                    {{ $inc->mainCategory?->name ?? 'نامشخص' }}
                                    @if ($inc->subCategory)
                                        - {{ $inc->subCategory->name ?? 'نامشخص' }}
                                    @endif
                                @endif
                            </td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $inc->account?->title ?? '-' }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $inc->person?->name ?? '-' }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $inc->desc }}</td>
                            <td class="border px-2 py-2 flex justify-center gap-2 whitespace-nowrap">
                                <!-- Edit button -->
                                <button class="text-blue-600 hover:underline px-2 py-1 border rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                    </svg>
                                </button>
                                <!-- Delete button -->
                                <button class="text-red-600 hover:underline px-2 py-1 border rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td colspan="3" class="border px-2 py-2 text-red-600">جمع هزینه‌ها:
                            {{ number_format($transactions->where('type', 'expense')->sum('amount')) }}</td>
                        <td colspan="3" class="border px-2 py-2 text-green-600">جمع درآمد‌ها:
                            {{ number_format($transactions->where('type', 'income')->sum('amount')) }}</td>
                        <td colspan="3" class="border px-2 py-2 text-blue-600">جمع انتقال:
                            {{ number_format($transactions->where('type', 'transfer')->sum('amount')) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="mt-5">{{ $transactions->links('pagination::tailwind') }}</div>
    </div>

@endsection

@section('scripts')
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/chart.umd.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const rawData = {!! json_encode(array_values($monthlyMatrix)) !!};

            // نمودار خطی
            new Chart(document.getElementById('lineChart'), {
                type: 'line',
                data: {
                    labels: rawData.map(d => d.month_name),
                    datasets: [{
                            label: 'درآمد',
                            data: rawData.map(d => d.income),
                            borderColor: '#10b981',
                            tension: 0.4,
                            fill: false
                        },
                        {
                            label: 'هزینه',
                            data: rawData.map(d => d.expense),
                            borderColor: '#ef4444',
                            tension: 0.4,
                            fill: false
                        },
                    ]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                font: {
                                    family: 'YekanBakh'
                                }
                            }
                        }
                    }
                }
            });

            // نمودار دایره‌ای هزینه‌ها
            const topExpData = {!! json_encode($topExpenses->pluck('total')) !!};
            const topExpLabels = {!! json_encode($topExpenses->map(fn($e) => $e->mainCategory->name ?? 'نامشخص')) !!};

            new Chart(document.getElementById('pieChart'), {
                type: 'pie',
                data: {
                    labels: topExpLabels,
                    datasets: [{
                        data: topExpData,
                        backgroundColor: ['#f87171', '#fbbf24', '#60a5fa', '#34d399', '#a78bfa']
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    family: 'YekanBakh'
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
