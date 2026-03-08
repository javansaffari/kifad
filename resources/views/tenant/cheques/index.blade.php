@extends('tenant.layouts.app')

@section('pageTitle', 'مدیریت چک‌ها')

@section('styles')
    <!-- Persian datepicker styles -->
    <link rel="stylesheet" href="/assets/css/persian-datepicker.css">
    <link rel="stylesheet" href="/assets/css/persianDatepicker-default.css">
    <!-- Select2 dropdown styles -->
    <link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Check Form --}}
        {{-- Check Form --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 ">
            <h2 class="text-lg font-semibold mb-4">ثبت چک جدید</h2>
            <form class="space-y-4" method="POST" action="#">
                @csrf

                <!-- Check type as radio buttons -->
                <div>
                    <label class="block text-sm mb-2">نوع چک</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-1">
                            <input type="radio" name="type" value="issued" required checked>
                            <span>صادر شده</span>
                        </label>
                        <label class="flex items-center gap-1">
                            <input type="radio" name="type" value="received" required>
                            <span>دریافتی</span>
                        </label>
                    </div>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm mb-2">مبلغ (ریال)</label>
                    <input type="text" name="amount" id="amount"
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                </div>

                <!-- Serial -->
                <div>
                    <label class="block text-sm mb-2">شماره سریال چک</label>
                    <input type="text" name="serial" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                </div>

                <!-- Siyad ID -->
                <div>
                    <label class="block text-sm mb-2">شناسه 16 رقمی صیاد</label>
                    <input type="text" name="siyad_id" class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2"
                        maxlength="16">
                </div>


                <!-- Person -->
                <div>
                    <label class="block text-sm mb-2">شخص</label>
                    <select name="person" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">انتخاب کنید</option>
                        @foreach ($persons as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Account -->
                <div>
                    <label class="block text-sm mb-2">حساب</label>
                    <select name="account" class="w-full border-gray-300 rounded-lg shadow-sm">
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->title }} (موجودی:
                                {{ number_format($acc->balance) }} ریال)</option>
                        @endforeach
                    </select>
                </div>

                <!-- Due Date & Issue Date in one row -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-2">تاریخ صدور</label>
                        <input type="text" id="issueDate" name="issue_date"
                            class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                    </div>

                    <div>
                        <label class="block text-sm mb-2">تاریخ سررسید</label>
                        <input type="text" id="dueDate" name="due_date"
                            class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                    </div>
                </div>

                <!-- Bank -->
                <div>
                    <label class="block text-sm mb-2">بانک صادر کننده</label>
                    <select name="bank" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">انتخاب کنید</option>
                        @foreach ($banks as $bank)
                            <option>{{ $bank }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-sm mb-2">برچسب‌ها</label>
                    <select name="tags[]" class="select w-full border-gray-300 rounded-lg" multiple="multiple">
                        @foreach ($tags as $tag)
                            <option value="{{ $tag }}">{{ $tag }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm mb-2">توضیحات</label>
                    <textarea name="desc" class="w-full border-gray-300 rounded-lg shadow-sm h-24"></textarea>
                </div>

                <!-- Submit button -->
                <div>
                    <x-button class="text-[18px] w-full">ثبت چک</x-button>
                </div>
            </form>
        </div>


        {{-- Check Chart --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 ">
            <h2 class="text-lg font-semibold mb-4">تقسیم‌بندی چک‌ها بر اساس نوع</h2>
            <canvas id="chart" height="200"></canvas>
        </div>
    </div>

    {{-- Checks Table --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5  mt-6">
        <h2 class="text-lg font-semibold mb-4">لیست چک‌ها</h2>

        {{-- Filters --}}
        <div class="flex flex-col md:flex-row flex-wrap gap-4 items-start mb-4 bg-gray-50 p-4 rounded-lg">
            <form action="#" class="flex flex-col md:flex-row flex-wrap gap-2 w-full md:flex-1">
                <input type="text" placeholder="جستجو..."
                    class="border border-gray-300 rounded-lg shadow-sm w-full md:w-64 px-3 py-2">
                <select
                    class="border border-gray-300 rounded-lg shadow-sm w-full md:w-48 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option>همه نوع‌ها</option>
                    @foreach ($checkTypes as $type)
                        <option>{{ $type }}</option>
                    @endforeach
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
                        @foreach (['نوع', 'شخص', 'حساب', 'مبلغ (ریال)', 'شماره سریال', 'شناسه صیاد', 'موعد', 'تاریخ صدور', 'بانک عامل', 'توضیحات', 'عملیات'] as $th)
                            <th class="border px-2 py-2 whitespace-nowrap">{{ $th }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($checks as $check)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $check->type }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $check->person->title ?? '-' }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $check->account->title ?? '-' }}</td>
                            <td
                                class="border px-2 py-2 font-semibold whitespace-nowrap
                                {{ $check->type === 'دریافتی' ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($check->amount) }}
                            </td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $check->serial }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $check->siyad_id }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $check->due_date }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $check->issue_date }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $check->bank }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $check->desc }}</td>
                            <td class="border px-2 py-2 flex justify-center gap-2 whitespace-nowrap"> <!-- Edit button -->
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


            </table>
        </div>


        {{-- Record count --}}
        <div class="mt-4 flex flex-col md:flex-row justify-between text-sm text-gray-600 gap-2 md:gap-0">
            <div>تعداد کل رکوردها: {{ count($checks) }}</div>
            <div>نمایش 1 تا {{ count($checks) }} از {{ count($checks) }}</div>
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

            // Chart.js pie chart
            const ctx = document.getElementById('chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: @json(array_keys($chartData)),
                        datasets: [{
                            label: 'مجموع چک‌ها (ریال)',
                            data: @json(array_values($chartData)),
                            backgroundColor: ['#34d399', '#60a5fa', '#fbbf24', '#a78bfa', '#f87171']
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
