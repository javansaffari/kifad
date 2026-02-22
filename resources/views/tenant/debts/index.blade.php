@extends('tenant.layouts.app')

@section('pageTitle', 'مدیریت بدهی‌ها و طلب‌ها')

@section('styles')
    <link rel="stylesheet" href="/assets/css/persian-datepicker.css">
    <link rel="stylesheet" href="/assets/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection

@section('content')
    @php
        // Sample data
        $accounts = [
            (object) ['id' => 1, 'title' => 'کیف پول', 'balance' => 1500000],
            (object) ['id' => 2, 'title' => 'بانک ملت', 'balance' => 5400000],
            (object) ['id' => 3, 'title' => 'بانک ملی', 'balance' => 2500000],
        ];
        $person = ['علی', 'زهرا', 'مریم', 'رضا', 'سارا', 'کامران', 'نگار', 'پویا', 'مینا', 'امیر'];
        $tags = ['شخصی', 'کاری', 'سایر'];

        // Sample debts/receivables
        $records = [];
        foreach (range(1, 20) as $i) {
            $type = rand(0, 1) ? 'بدهی' : 'طلب';
            $account = $accounts[array_rand($accounts)]->title;
            $person = $person[array_rand($person)];
            $amount = rand(500000, 5000000);
            $dueDate = '1402/07/' . str_pad(rand(10, 30), 2, '0', STR_PAD_LEFT);

            $records[] = (object) [
                'type' => $type,
                'title' => "موضوع $i",
                'person' => $person,
                'account' => $account,
                'amount' => $amount,
                'due_date' => $dueDate,
                'tags' => [$tags[array_rand($tags)]],
                'desc' => "توضیح $i",
                'paid' => rand(0, 1),
            ];
        }

        // Chart data
        $chartData = [];
        foreach ($records as $r) {
            $key = $r->paid ? 'تسویه شده' : 'تسویه نشده';
            $chartData[$key] = ($chartData[$key] ?? 0) + $r->amount;
        }

        $totalAmount = array_sum(array_map(fn($r) => $r->amount, $records));
        $totalPaid = array_sum(array_map(fn($r) => $r->paid ? $r->amount : 0, $records));
        $totalUnpaid = $totalAmount - $totalPaid;
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Form --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 ">
            <h2 class="text-lg font-semibold mb-4">ثبت بدهی / طلب جدید</h2>
            <form class="space-y-4" method="POST" action="#">
                @csrf
                <div>
                    <label class="block text-sm mb-2">نوع</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-1">
                            <input type="radio" name="type" value="بدهی" required checked>
                            <span>بدهی (قرض گرفتن)</span>
                        </label>
                        <label class="flex items-center gap-1">
                            <input type="radio" name="type" value="طلب" required>
                            <span>طلب (قرض دادن)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-2">عنوان</label>
                    <input type="text" name="title" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm mb-2">مبلغ (ریال)</label>
                    <input type="text" name="amount" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm mb-2">تاریخ موعد</label>
                    <input type="text" id="dueDate" name="due_date"
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm mb-2">حساب</label>
                    <select name="account" class="w-full border-gray-300 rounded-lg shadow-sm">
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->title }} (موجودی:
                                {{ number_format($acc->balance) }} ریال)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-2">شخص</label>
                    <select name="person" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">انتخاب کنید</option>
                        @foreach ($person as $p)
                            <option>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-2">برچسب‌ها</label>
                    <select name="tags[]" class="select w-full border-gray-300 rounded-lg" multiple="multiple">
                        @foreach ($tags as $tag)
                            <option value="{{ $tag }}">{{ $tag }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-2">توضیحات</label>
                    <textarea name="desc" class="w-full border-gray-300 rounded-lg shadow-sm h-24"></textarea>
                </div>

                <div>
                    <x-button class="text-[18px] w-full">ثبت</x-button>
                </div>
            </form>
        </div>

        {{-- Chart --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 ">
            <h2 class="text-lg font-semibold mb-4">وضعیت تسویه</h2>
            <canvas id="chart" height="200"></canvas>
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5  mt-6">
        <h2 class="text-lg font-semibold mb-4">لیست بدهی‌ها و طلب‌ها</h2>

        {{-- Filters --}}
        <div class="flex flex-col md:flex-row flex-wrap gap-4 items-start mb-4 bg-gray-50 p-4 rounded-lg">
            <form action="#" class="flex flex-col md:flex-row flex-wrap gap-2 w-full md:flex-1">
                <input type="text" placeholder="جستجو..."
                    class="border border-gray-300 rounded-lg shadow-sm w-full md:w-64 px-3 py-2">
                <select
                    class="border border-gray-300 rounded-lg shadow-sm w-full md:w-48 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option>همه نوع‌ها</option>
                    <option>بدهی (قرض گرفتن)</option>
                    <option>طلب (قرض دادن)
                    </option>
                </select>
                <input type="text" placeholder="از تاریخ" id="fromDate" pattern="\d{4}/\d{2}/\d{2}"
                    title="فرمت صحیح: YYYY/MM/DD"
                    class="border border-gray-300 rounded-lg shadow-sm w-full md:w-32 px-3 py-2">
                <input type="text" placeholder="تا تاریخ" id="toDate" pattern="\d{4}/\d{2}/\d{2}"
                    title="فرمت صحیح: YYYY/MM/DD"
                    class="border border-gray-300 rounded-lg shadow-sm w-full md:w-32 px-3 py-2">
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

        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm text-gray-700 text-center">
                <thead class="bg-gray-100">
                    <tr>
                        @foreach (['نوع', 'عنوان', 'مبلغ (ریال)', 'تسویه شده (ریال)', 'باقیمانده (ریال)', 'تاریخ موعد', 'شخص', 'عملیات'] as $th)
                            <th class="border px-2 py-2 whitespace-nowrap">{{ $th }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($records as $r)
                        @php
                            $paidAmount = $r->paid ? $r->amount : 0;
                            $remaining = $r->amount - $paidAmount;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $r->type }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $r->title }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ number_format($r->amount) }}</td>
                            <td class="border px-2 py-2 text-green-600 whitespace-nowrap">{{ number_format($paidAmount) }}
                            </td>
                            <td class="border px-2 py-2 text-red-600 whitespace-nowrap">{{ number_format($remaining) }}
                            </td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $r->due_date }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $r->person }}</td>
                            <td class="border px-2 py-2 flex justify-center gap-2 whitespace-nowrap">
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
                    @php
                        $totalPaid = array_sum(array_map(fn($r) => $r->paid ? $r->amount : 0, $records));
                        $totalRemaining = array_sum(array_map(fn($r) => $r->paid ? 0 : $r->amount, $records));
                        $totalAmount = array_sum(array_map(fn($r) => $r->amount, $records));
                    @endphp
                    <tr>
                        <td colspan="2" class="border px-2 py-2 text-center">جمع کل</td>
                        <td class="border px-2 py-2 whitespace-nowrap">{{ number_format($totalAmount) }} ریال</td>
                        <td class="border px-2 py-2 text-green-600 whitespace-nowrap">{{ number_format($totalPaid) }} ریال
                        </td>
                        <td class="border px-2 py-2 text-red-600 whitespace-nowrap">{{ number_format($totalRemaining) }}
                            ریال</td>
                        <td colspan="3"></td>
                    </tr>
                </tfoot>
            </table>

        </div>

        {{-- Record count --}}
        <div class="mt-4 flex flex-col md:flex-row justify-between text-sm text-gray-600 gap-2 md:gap-0">
            <div>تعداد کل رکوردها: {{ count($records) }}</div>
            <div>نمایش 1 تا {{ count($records) }} از {{ count($records) }}</div>
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


            const ctx = document.getElementById('chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: @json(array_keys($chartData)),
                        datasets: [{
                            label: 'مجموع (ریال)',
                            data: @json(array_values($chartData)),
                            backgroundColor: ['#34d399', '#f87171']
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
