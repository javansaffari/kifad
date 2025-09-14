@extends('tenant.layouts.app')

@section('pageTitle', 'مدیریت هزینه‌ها')

@section('content')
    @php
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

        $chartData = [];
        foreach ($expenses as $ex) {
            $chartData[$ex->category] = ($chartData[$ex->category] ?? 0) + $ex->amount;
        }
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- فرم ثبت هزینه --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-4">ثبت هزینه جدید</h2>
            <form class="space-y-4">
                @php $fields = [['label' => 'مبلغ (ریال)', 'id' => 'amount', 'type' => 'text'], ['label' => 'تاریخ', 'id' => 'date', 'type' => 'text', 'class' => 'observer-example']]; @endphp
                @foreach ($fields as $f)
                    <div>
                        <label class="block text-sm mb-2">{{ $f['label'] }}</label>
                        <input type="{{ $f['type'] }}" id="{{ $f['id'] }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm {{ $f['class'] ?? '' }}">
                    </div>
                @endforeach

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-sm mb-2">دسته‌بندی اصلی</label>
                        <select id="mainCategory" class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">انتخاب کنید</option>
                            @foreach (array_keys($categories) as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm mb-2">زیر دسته</label>
                        <select id="subCategory" class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">انتخاب کنید</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-2">برچسب‌ها</label>

                    <select class="select w-full border-gray-300 rounded-lg" multiple="multiple">
                        @foreach ($tags as $tag)
                            <option value="{{ $tag }}">{{ $tag }}</option>
                        @endforeach
                    </select>



                </div>

                <div>
                    <label class="block text-sm mb-2">حساب</label>
                    <select id="account" class="w-full border-gray-300 rounded-lg shadow-sm">
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->title }} (موجودی:
                                {{ number_format($acc->balance) }} ریال)</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-2">شخص</label>
                    <select id="person" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">انتخاب کنید</option>
                        @foreach ($people as $p)
                            <option>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-2">توضیحات</label>
                    <textarea class="w-full border-gray-300 rounded-lg shadow-sm h-24"></textarea>
                </div>

                <div>
                    <x-button class="ms-4 bg-blue-600 hover:bg-blue-700 text-white">ثبت هزینه</x-button>
                </div>
            </form>
        </div>

        {{-- نمودار --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-4">تقسیم‌بندی هزینه‌ها</h2>
            <canvas id="chart" height="200"></canvas>
        </div>
    </div>

    {{-- جدول هزینه‌ها --}}
    <div class="bg-white p-4 md:p-6 rounded-xl shadow mt-6">
        <h2 class="text-lg font-semibold mb-4">لیست هزینه‌ها</h2>

        {{-- فیلترها --}}
        <div class="flex flex-col md:flex-row flex-wrap gap-2 items-start md:items-center mb-4 bg-gray-50 p-3 rounded-lg">
            <input type="text" placeholder="جستجو..."
                class="border-gray-300 rounded-lg shadow-sm flex-1 md:flex-auto px-2 py-1">

            <select class="border-gray-300 rounded-lg shadow-sm px-2 py-1 flex-1 md:w-48">
                <option>همه دسته‌ها</option>
                @foreach (array_keys($categories) as $cat)
                    <option>{{ $cat }}</option>
                @endforeach
            </select>

            <input type="text" placeholder="از تاریخ" value="1404/01/01" id="fromDate"
                class="border-gray-300 rounded-lg shadow-sm px-2 py-1 w-full md:w-32">
            <input type="text" placeholder="تا تاریخ" value="1405/01/01" id="toDate"
                class="border-gray-300 rounded-lg shadow-sm px-2 py-1 w-full md:w-32">

            <button class="px-4 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700">اعمال</button>

            <div class="flex-1 md:flex-none ms-auto flex flex-wrap gap-2 items-center">
                <span>نمایش در صفحه:</span>
                <select id="perPage" class="border-gray-300 rounded-lg px-2 py-1 w-24">
                    <option>25</option>
                    <option>50</option>
                    <option>75</option>
                    <option>100</option>
                </select>

                <button class="flex items-center gap-1 px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4h16v16H4V4zm4 4l8 8m0-8l-8 8" />
                    </svg>
                    خروجی اکسل
                </button>
            </div>
        </div>

        {{-- جدول --}}
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
                                <button class="text-blue-600 hover:underline px-2 py-1 border rounded">ویرایش</button>
                                <button class="text-red-600 hover:underline px-2 py-1 border rounded">حذف</button>
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

        {{-- تعداد رکوردها --}}
        <div class="mt-4 flex flex-col md:flex-row justify-between text-sm text-gray-600 gap-2 md:gap-0">
            <div>تعداد کل رکوردها: {{ count($expenses) }}</div>
            <div>نمایش 1 تا {{ count($expenses) }} از {{ count($expenses) }}</div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalali-datepicker.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0/dist/chart.umd.min.js"></script>
    <script src="https://babakhani.github.io/PersianWebToolkit/beta/lib/jquery/dist/jquery.js"></script>
    <script src="https://babakhani.github.io/PersianWebToolkit/beta/lib/persian-date/dist/persian-date.js"></script>
    <script src="https://babakhani.github.io/PersianWebToolkit/beta/lib/persian-datepicker/dist/js/persian-datepicker.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // سه‌رقمی کردن مبلغ
            $('#amount').on('input', function() {
                let value = $(this).val().replace(/,/g, '');
                if (!isNaN(value) && value !== '') $(this).val(Number(value).toLocaleString());
            });

            // Persian Datepicker
            $('.observer-example').persianDatepicker({
                observer: true,
                format: 'YYYY/MM/DD'
            });

            // دسته‌بندی و زیر دسته
            const categories = @json($categories);
            $('#mainCategory').on('change', function() {
                const subs = categories[this.value] ?? [];
                const subCat = $('#subCategory').empty().append('<option value="">انتخاب کنید</option>');
                subs.forEach(s => subCat.append(`<option value="${s}">${s}</option>`));
            });

            // Chart.js
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
                    }
                });
            }

            // Select2 با قابلیت ایجاد تگ

            $(".select").select2({
                dir: "rtl",
                tags: true,
                tokenSeparators: [',', ' ']
            })



        });
    </script>
@endsection
