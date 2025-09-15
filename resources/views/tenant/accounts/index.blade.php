@extends('tenant.layouts.app')

@section('pageTitle', 'مدیریت حساب‌ها')

@section('styles')
    <link rel="stylesheet" href="/assets/css/persian-datepicker.css">
    <link rel="stylesheet" href="/assets/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection

@section('content')
    @php
        $accountTypes = [
            'سپرده‌های بانکی قرض‌الحسنه',
            'سپرده‌های بانکی سرمایه‌گذاری',
            'حساب وکالتی',
            'حساب مشترک',
            'سایر',
        ];

        $banks = ['بانک ملت', 'بانک ملی', 'بانک صادرات', 'بانک تجارت', 'بانک پارسیان'];

        $accounts = [];
        foreach (range(1, 10) as $i) {
            $type = $accountTypes[array_rand($accountTypes)];
            $bank = $banks[array_rand($banks)];
            $balance = rand(-500000, 10000000); // مثبت و منفی برای نمونه
            $accounts[] = (object) [
                'title' => "حساب $i",
                'balance' => $balance,
                'type' => $type,
                'bank' => $bank,
                'desc' => "توضیح حساب $i",
            ];
        }

        $totalBalance = array_sum(array_map(fn($a) => $a->balance, $accounts));

        // داده چارت بر اساس نوع حساب
        $chartData = [];
        foreach ($accounts as $acc) {
            $chartData[$acc->type] = ($chartData[$acc->type] ?? 0) + $acc->balance;
        }
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Account Form --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-4">ثبت حساب جدید</h2>
            <form class="space-y-4" method="POST" action="#">
                @csrf
                <div>
                    <label class="block text-sm mb-2">عنوان حساب</label>
                    <input type="text" name="title" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm mb-2">موجودی (ریال)</label>
                    <input type="text" name="balance" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm mb-2">نوع حساب</label>
                    <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">انتخاب کنید</option>
                        @foreach ($accountTypes as $type)
                            <option>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-2">بانک</label>
                    <select name="bank" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">انتخاب کنید</option>
                        @foreach ($banks as $bank)
                            <option>{{ $bank }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-2">توضیح</label>
                    <textarea name="desc" class="w-full border-gray-300 rounded-lg shadow-sm h-24"></textarea>
                </div>

                <div>
                    <x-button class="text-[18px] w-full">ثبت حساب</x-button>
                </div>
            </form>
        </div>

        {{-- Account Summary Chart --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-4">تقسیم‌بندی حساب‌ها بر اساس نوع</h2>
            <canvas id="chart" height="200"></canvas>
        </div>
    </div>

    {{-- Accounts Table --}}
    <div class="bg-white p-4 md:p-6 rounded-xl shadow">

        <h2 class="text-lg font-semibold mb-4">لیست حساب‌ها</h2>

        {{-- Filters --}}
        <div class="flex flex-col md:flex-row flex-wrap gap-4 items-start mb-4 bg-gray-50 p-4 rounded-lg">
            <form action="#" class="flex flex-col md:flex-row flex-wrap gap-2 w-full md:flex-1">
                <input type="text" placeholder="جستجو..."
                    class="border border-gray-300 rounded-lg shadow-sm w-full md:w-64 px-3 py-2">

                <button type="submit"
                    class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">اعمال</button>
            </form>

            <!-- Export to Excel -->
            <div class="flex flex-col md:flex-row items-start md:items-center gap-2 w-full md:w-auto mt-2 md:mt-0">
                <button
                    class="flex items-center gap-1 justify-center w-full md:w-auto px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4h16v16H4V4zm4 4l8 8m0-8l-8 8" />
                    </svg>
                    خروجی اکسل
                </button>
            </div>
        </div>

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
                        <td class="border px-2 py-2">{{ $acc->desc }}</td>
                        <td class="border px-2 py-2 flex justify-center gap-2">
                            <button class="text-green-600 hover:underline px-2 py-1 border rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>

                            </button>

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
                    <td colspan="1" class="border px-2 py-2 text-center">جمع کل موجودی‌ها</td>
                    <td class="border px-2 py-2">{{ number_format($totalBalance) }}</td>
                    <td colspan="4"></td>
                </tr>
            </tfoot>
        </table>
    </div>


    {{-- Record count --}}
    <div class="mt-4 flex flex-col md:flex-row justify-between text-sm text-gray-600 gap-2 md:gap-0">
        <div>تعداد کل رکوردها: {{ count($accounts) }}</div>
        <div>نمایش 1 تا {{ count($accounts) }} از {{ count($accounts) }}</div>
    </div>
@endsection

@section('scripts')
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/select2.min.js"></script>
    <script src="/assets/js/chart.umd.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // Handle main type change to populate subtypes
            const accountTypes = @json($accountTypes);
            $('#mainType').on('change', function() {
                const subs = accountTypes[this.value] ?? [];
                const subSelect = $('#subType').empty().append('<option value="">انتخاب کنید</option>');
                subs.forEach(s => subSelect.append(`<option value="${s}">${s}</option>`));
            });

            // Chart.js for accounts by main type
            const chartData = @json(array_map(function ($a) {
                    return $a->type;
                }, $accounts));

            const counts = {};
            chartData.forEach(t => counts[t] = (counts[t] || 0) + 1);

            const ctx = document.getElementById('chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(counts),
                        datasets: [{
                            label: 'تعداد حساب‌ها',
                            data: Object.values(counts),
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
