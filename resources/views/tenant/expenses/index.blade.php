@extends('tenant.layouts.app')

@section('pageTitle', 'مدیریت هزینه‌ها')

@section('styles')
    <!-- Persian datepicker styles -->
    <link rel="stylesheet" href="/assets/css/persian-datepicker.css">
    <link rel="stylesheet" href="/assets/css/persianDatepicker-default.css">
    <!-- Select2 dropdown styles -->
    <link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection

@section('content')
    @php
        // Sample categories, tags, accounts, and people data
        $categories = [
            'خوراک' => ['رستوران', 'مواد غذایی', 'میوه و سبزیجات'],
            'حمل و نقل' => ['بنزین', 'تاکسی', 'اتوبوس'],
            'قبوض' => ['آب', 'برق', 'گاز', 'اینترنت'],
            'سلامت' => ['دارو', 'دکتر', 'ویزیت'],
            'سرگرمی' => ['کتاب', 'سینما', 'ورزش'],
        ];

        $tags = ['کافه', 'خانواده', 'دوستان', 'کار', 'تفریح', 'سفر'];

        $accounts = [
            (object) ['id' => 1, 'title' => 'کیف پول', 'balance' => 1500000],
            (object) ['id' => 2, 'title' => 'بانک ملت', 'balance' => 5400000],
            (object) ['id' => 3, 'title' => 'بانک ملی', 'balance' => 2500000],
        ];

        $people = ['علی', 'زهرا', 'مریم', 'رضا', 'سارا', 'کامران', 'نگار', 'پویا', 'مینا', 'امیر'];

        // Generate sample expenses
        $expenses = [];
        foreach (range(1, 20) as $i) {
            $mainCat = array_rand($categories);
            $subCat = $categories[$mainCat][array_rand($categories[$mainCat])];
            $account = $accounts[array_rand($accounts)]->title;
            $person = $people[array_rand($people)];
            $amount = rand(10000, 500000);
            $date = '1402/06/' . str_pad(rand(10, 30), 2, '0', STR_PAD_LEFT);

            $expenses[] = (object) [
                'date' => $date,
                'amount' => $amount,
                'category' => $mainCat,
                'subcategory' => $subCat,
                'account' => $account,
                'person' => $person,
                'desc' => "توضیح هزینه $i",
            ];
        }

        // Prepare chart data by category
        $chartData = [];
        foreach ($expenses as $expense) {
            $chartData[$expense->category] = ($chartData[$expense->category] ?? 0) + $expense->amount;
        }
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Expense Form --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-4">ثبت هزینه جدید</h2>
            <form class="space-y-4" method="POST" action="#">
                @csrf
                <!-- Amount input -->
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                        مبلغ (ریال)
                    </label>
                    <input type="text" id="amount" name="amount" required
                        class="w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <span id="amountError" class="text-red-600 text-sm mt-1 block"></span>
                </div>

                <!-- Date input with Persian datepicker -->
                <div class="mb-4">
                    <label for="datapicker" class="block text-sm font-medium text-gray-700 mb-1">
                        تاریخ
                    </label>
                    <input type="text" id="datapicker" name="date" required
                        class="w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>

                <!-- Category selection -->
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm mb-2">دسته‌بندی اصلی</label>
                        <select id="mainCategory" name="category" required
                            class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">انتخاب کنید</option>
                            @foreach (array_keys($categories) as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subcategory selection -->
                    <div>
                        <label class="block text-sm mb-2">زیر دسته</label>
                        <select id="subCategory" name="subcategory" class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">انتخاب کنید</option>
                        </select>
                    </div>
                </div>

                <!-- Tags selection -->
                <div>
                    <label class="block text-sm mb-2">برچسب‌ها</label>
                    <select name="tags[]" class="select w-full border-gray-300 rounded-lg" multiple="multiple">
                        @foreach ($tags as $tag)
                            <option value="{{ $tag }}">{{ $tag }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Account selection -->
                <div>
                    <label class="block text-sm mb-2">برداشت از حساب</label>
                    <select id="account" name="account" required class="w-full border-gray-300 rounded-lg shadow-sm">
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->title }} (موجودی:
                                {{ number_format($acc->balance) }} ریال)</option>
                        @endforeach
                    </select>
                </div>

                <!-- Person selection -->
                <div>
                    <label class="block text-sm mb-2">شخص</label>
                    <select id="person" name="person" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">انتخاب کنید</option>
                        @foreach ($people as $p)
                            <option>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Description input -->
                <div>
                    <label class="block text-sm mb-2">توضیحات</label>
                    <textarea name="desc" class="w-full border-gray-300 rounded-lg shadow-sm h-24"></textarea>
                </div>

                <!-- Submit button -->
                <div>
                    <x-button class="text-[18px] w-full">ثبت هزینه</x-button>
                </div>
            </form>
        </div>

        {{-- Expense Chart --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-4">تقسیم‌بندی هزینه‌ها</h2>
            <canvas id="chart" height="200"></canvas>
        </div>
    </div>

    {{-- Expenses Table --}}
    <div class="bg-white p-4 md:p-6 rounded-xl shadow mt-6">
        <h2 class="text-lg font-semibold mb-4">لیست هزینه‌ها</h2>

        {{-- Filters --}}
        <div class="flex flex-col md:flex-row flex-wrap gap-4 items-start mb-4 bg-gray-50 p-4 rounded-lg">
            <form action="#" class="flex flex-col md:flex-row flex-wrap gap-2 w-full md:flex-1">
                <!-- Search input -->
                <input type="text" placeholder="جستجو..."
                    class="border border-gray-300 rounded-lg shadow-sm w-full md:w-64 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">

                <!-- Category filter -->
                <select
                    class="border border-gray-300 rounded-lg shadow-sm w-full md:w-48 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option>همه دسته‌ها</option>
                    @foreach (array_keys($categories) as $cat)
                        <option>{{ $cat }}</option>
                    @endforeach
                </select>

                <!-- Date filters -->
                <input type="text" placeholder="از تاریخ" id="fromDate" pattern="\d{4}/\d{2}/\d{2}"
                    title="فرمت صحیح: YYYY/MM/DD"
                    class="border border-gray-300 rounded-lg shadow-sm w-full md:w-32 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <input type="text" placeholder="تا تاریخ" id="toDate" pattern="\d{4}/\d{2}/\d{2}"
                    title="فرمت صحیح: YYYY/MM/DD"
                    class="border border-gray-300 rounded-lg shadow-sm w-full md:w-32 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">

                <!-- Apply filters button -->
                <button type="submit"
                    class="w-full md:w-auto px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    اعمال
                </button>
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

        {{-- Expenses table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm text-gray-700 text-center">
                <thead class="bg-gray-100">
                    <tr>
                        @foreach (['تاریخ', 'مبلغ (ریال)', 'دسته', 'زیر دسته', 'حساب', 'شخص', 'توضیحات', 'عملیات'] as $th)
                            <th class="border px-2 py-2 whitespace-nowrap">{{ $th }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenses as $ex)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $ex->date }}</td>
                            <td class="border px-2 py-2 text-red-600 font-semibold whitespace-nowrap">
                                {{ number_format($ex->amount) }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $ex->category }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $ex->subcategory }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $ex->account }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $ex->person }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $ex->desc }}</td>
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
                        <td colspan="7" class="border px-2 py-2 text-center">جمع کل</td>
                        <td class="border px-2 py-2 text-red-600 whitespace-nowrap">
                            {{ number_format(array_sum(array_map(fn($e) => $e->amount, $expenses))) }} ریال
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Record count --}}
        <div class="mt-4 flex flex-col md:flex-row justify-between text-sm text-gray-600 gap-2 md:gap-0">
            <div>تعداد کل رکوردها: {{ count($expenses) }}</div>
            <div>نمایش 1 تا {{ count($expenses) }} از {{ count($expenses) }}</div>
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


            // Handle main category change and populate subcategories
            const categories = @json($categories);
            $('#mainCategory').on('change', function() {
                const subs = categories[this.value] ?? [];
                const subCat = $('#subCategory').empty().append('<option value="">انتخاب کنید</option>');
                subs.forEach(s => subCat.append(`<option value="${s}">${s}</option>`));
            });

            // Render Chart.js pie chart
            const ctx = document.getElementById('chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: @json(array_keys($chartData)),
                        datasets: [{
                            label: 'مجموع هزینه‌ها (ریال)',
                            data: @json(array_values($chartData)),
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
            }
        });
    </script>
@endsection
