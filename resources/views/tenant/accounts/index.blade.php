@extends('tenant.layouts.app')

@section('pageTitle', 'مدیریت حساب‌ها')

@section('styles')
    <link rel="stylesheet" href="/assets/css/persian-datepicker.css">
    <link rel="stylesheet" href="/assets/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Account Form --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 ">
            <h2 class="text-lg font-semibold mb-4">ثبت حساب جدید</h2>
            <form class="space-y-4" method="POST" action="{{ route('tenant.accounts.store') }}">
                @csrf
                <div>
                    <label class="block text-sm mb-2">عنوان حساب</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                    @error('title')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm mb-2">موجودی (ریال)</label>
                    <input type="text" name="balance" value="{{ old('balance', 0) }}"
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
                            <option value="{{ $type }}" @selected(old('type') == $type)>{{ $type }}</option>
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
                            <option value="{{ $bank }}" @selected(old('bank') == $bank)>{{ $bank }}</option>
                        @endforeach
                    </select>
                    @error('bank')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm mb-2">توضیح</label>
                    <textarea name="desc" class="w-full border-gray-300 rounded-lg shadow-sm h-24">{{ old('desc') }}</textarea>
                    @error('desc')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-button class="text-[18px] w-full">ثبت حساب</x-button>
                </div>
            </form>
        </div>

        {{-- Account Summary Chart --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 ">
            <h2 class="text-lg font-semibold mb-4">تقسیم‌بندی حساب‌ها بر اساس نوع</h2>
            <canvas id="chart" height="200"></canvas>
        </div>
    </div>

    {{-- Accounts Table --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 ">
        <h2 class="text-lg font-semibold mb-4">لیست حساب‌ها</h2>

        <table class="min-w-full border text-sm text-gray-700 text-center" id="accountsTable">
            <thead class="bg-gray-100">
                <tr>
                    @foreach (['عنوان', 'موجودی (ریال)', 'نوع حساب', 'بانک', 'توضیح', 'عملیات'] as $th)
                        <th class="border px-2 py-2 whitespace-nowrap">{{ $th }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($accounts as $acc)
                    @php
                        $balanceColor =
                            $acc->balance >= 0 ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold';
                    @endphp
                    <tr>
                        <td class="border px-2 py-2">{{ $acc->title }}</td>
                        <td class="border px-2 py-2 {{ $balanceColor }}">{{ number_format($acc->balance) }}</td>
                        <td class="border px-2 py-2">{{ $acc->type }}</td>
                        <td class="border px-2 py-2">{{ $acc->bank }}</td>
                        <td class="border px-2 py-2">{{ $acc->description }}</td>
                        <td class="border px-2 py-2 flex justify-center gap-2">
                            <a href="{{ route('tenant.accounts.edit', $acc->id) }}"
                                class="text-blue-600 hover:underline px-2 py-1 border rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                            </a>
                            <form action="{{ route('tenant.accounts.destroy', $acc->id) }}" method="POST"
                                class="inline-block delete-confirm">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline px-2 py-1 border rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50 font-semibold">
                <tr>
                    <td colspan="1" class="border px-2 py-2 text-center">جمع کل موجودی‌ها</td>
                    <td class="border px-2 py-2">{{ number_format($totalBalance) }}</td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Record count --}}
    <div class="mt-4 flex flex-col md:flex-row justify-between text-sm text-gray-600 gap-2 md:gap-0">
        <div>تعداد کل رکوردها: {{ $accounts->count() }}</div>
        <div>نمایش 1 تا {{ $accounts->count() }} از {{ $accounts->count() }}</div>
    </div>
@endsection

@section('scripts')
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/select2.min.js"></script>
    <script src="/assets/js/chart.umd.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Confirm before delete
            $('.delete-confirm').on('submit', function(e) {
                if (!confirm('آیا از حذف این حساب اطمینان دارید؟')) e.preventDefault();
            });

            // Chart.js for accounts by type
            const chartData = @json($chartData);
            const ctx = document.getElementById('chart');

            if (ctx) {
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(chartData),
                        datasets: [{
                            label: 'موجودی حساب‌ها',
                            data: Object.values(chartData),
                            backgroundColor: ['#34d399', '#60a5fa', '#fbbf24', '#a78bfa', '#f87171',
                                '#f472b6', '#facc15', '#22d3ee'
                            ]
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
            }
        });
    </script>
@endsection
