@extends('tenant.layouts.app')

@section('pageTitle', 'مدیریت تراکنش‌ها')

@section('styles')
    <link rel="stylesheet" href="/assets/css/persian-datepicker.css">
    <link rel="stylesheet" href="/assets/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection

@section('content')
    @php
        // Sample categories, tags, accounts, and people data
        $expenseCategories = [
            'خوراک' => ['رستوران', 'مواد غذایی', 'میوه و سبزیجات'],
            'حمل و نقل' => ['بنزین', 'تاکسی', 'اتوبوس'],
            'قبوض' => ['آب', 'برق', 'گاز', 'اینترنت'],
            'سلامت' => ['دارو', 'دکتر', 'ویزیت'],
            'سرگرمی' => ['کتاب', 'سینما', 'ورزش'],
        ];

        $incomeCategories = [
            'حقوق' => ['ماهانه', 'پاداش', 'اضافه کار'],
            'سرمایه گذاری' => ['بورس', 'رمزارز', 'سپرده بانکی'],
            'فروش' => ['محصولات', 'خدمات'],
            'هدیه' => ['خانواده', 'دوستان'],
        ];

        $tags = ['کار', 'خانواده', 'تفریح', 'سفر', 'سایر'];

        $accounts = [
            (object) ['id' => 1, 'title' => 'کیف پول', 'balance' => 1500000],
            (object) ['id' => 2, 'title' => 'بانک ملت', 'balance' => 5400000],
            (object) ['id' => 3, 'title' => 'بانک ملی', 'balance' => 2500000],
        ];

        $people = ['علی', 'زهرا', 'مریم', 'رضا', 'سارا', 'کامران', 'نگار', 'پویا', 'مینا', 'امیر'];

        // Sample transactions
        $transactions = [];
        foreach (range(1, 20) as $i) {
            $type = ['expense', 'income', 'transfer'][array_rand(['expense', 'income', 'transfer'])];
            $date = '1402/06/' . str_pad(rand(10, 30), 2, '0', STR_PAD_LEFT);
            $account = $accounts[array_rand($accounts)]->title;
            $person = $people[array_rand($people)];
            $amount = rand(10000, 500000);

            if ($type === 'expense') {
                $mainCat = array_rand($expenseCategories);
                $subCat = $expenseCategories[$mainCat][array_rand($expenseCategories[$mainCat])];
                $transactions[] = (object) [
                    'type' => 'expense',
                    'date' => $date,
                    'amount' => $amount,
                    'category' => $mainCat,
                    'subcategory' => $subCat,
                    'account' => $account,
                    'person' => $person,
                    'desc' => "هزینه $i",
                ];
            } elseif ($type === 'income') {
                $mainCat = array_rand($incomeCategories);
                $subCat = $incomeCategories[$mainCat][array_rand($incomeCategories[$mainCat])];
                $transactions[] = (object) [
                    'type' => 'income',
                    'date' => $date,
                    'amount' => $amount,
                    'category' => $mainCat,
                    'subcategory' => $subCat,
                    'account' => $account,
                    'person' => $person,
                    'desc' => "درآمد $i",
                ];
            } else {
                // transfer
                $toAccount = $accounts[array_rand($accounts)]->title;
                $transactions[] = (object) [
                    'type' => 'transfer',
                    'date' => $date,
                    'amount' => $amount,
                    'from' => $account,
                    'to' => $toAccount,
                    'desc' => "انتقال $i",
                ];
            }
        }

        // Prepare chart data (bar chart) for total amount per category
        $chartData = [];
        foreach ($transactions as $t) {
            if ($t->type !== 'transfer') {
                $chartData[$t->category] = ($chartData[$t->category] ?? 0) + $t->amount;
            }
        }
    @endphp

    {{-- Transaction Type Tabs --}}
    <div class="bg-white p-6 rounded-xl shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">ثبت تراکنش جدید</h2>
        <div class="mb-4 flex gap-2">
            <button class="transaction-tab px-4 py-2 bg-blue-600 text-white rounded" data-type="expense">ثبت هزینه</button>
            <button class="transaction-tab px-4 py-2 bg-green-600 text-white rounded" data-type="income">ثبت درآمد</button>
            <button class="transaction-tab px-4 py-2 bg-purple-600 text-white rounded" data-type="transfer">انتقال
                وجه</button>
        </div>

        {{-- Transaction Forms --}}
        <div id="transactionForms">
            {{-- Expense Form --}}
            <form class="transaction-form expense-form space-y-4" method="POST" action="#">
                @csrf
                <input type="text" name="amount" placeholder="مبلغ (ریال)" class="w-full border px-3 py-2 rounded"
                    required>
                <input type="text" id="expenseDate" name="date" placeholder="تاریخ"
                    class="w-full border px-3 py-2 rounded" required>

                <select id="expenseMainCategory" name="category" class="w-full border px-3 py-2 rounded">
                    <option value="">دسته‌بندی اصلی</option>
                    @foreach (array_keys($expenseCategories) as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>

                <select id="expenseSubCategory" name="subcategory" class="w-full border px-3 py-2 rounded">
                    <option value="">زیر دسته</option>
                </select>

                <select name="account" class="w-full border px-3 py-2 rounded">
                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                    @endforeach
                </select>

                <select name="person" class="w-full border px-3 py-2 rounded">
                    <option value="">شخص</option>
                    @foreach ($people as $p)
                        <option>{{ $p }}</option>
                    @endforeach
                </select>

                <textarea name="desc" class="w-full border px-3 py-2 rounded" placeholder="توضیحات"></textarea>
                <button class="w-full py-2 bg-blue-600 text-white rounded">ثبت هزینه</button>
            </form>

            {{-- Income Form --}}
            <form class="transaction-form income-form space-y-4 hidden" method="POST" action="#">
                @csrf
                <input type="text" name="amount" placeholder="مبلغ (ریال)" class="w-full border px-3 py-2 rounded"
                    required>
                <input type="text" id="incomeDate" name="date" placeholder="تاریخ"
                    class="w-full border px-3 py-2 rounded" required>

                <select id="incomeMainCategory" name="category" class="w-full border px-3 py-2 rounded">
                    <option value="">دسته‌بندی اصلی</option>
                    @foreach (array_keys($incomeCategories) as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>

                <select id="incomeSubCategory" name="subcategory" class="w-full border px-3 py-2 rounded">
                    <option value="">زیر دسته</option>
                </select>

                <select name="account" class="w-full border px-3 py-2 rounded">
                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                    @endforeach
                </select>

                <select name="person" class="w-full border px-3 py-2 rounded">
                    <option value="">شخص</option>
                    @foreach ($people as $p)
                        <option>{{ $p }}</option>
                    @endforeach
                </select>

                <textarea name="desc" class="w-full border px-3 py-2 rounded" placeholder="توضیحات"></textarea>
                <button class="w-full py-2 bg-green-600 text-white rounded">ثبت درآمد</button>
            </form>

            {{-- Transfer Form --}}
            <form class="transaction-form transfer-form space-y-4 hidden" method="POST" action="#">
                @csrf
                <input type="text" name="amount" placeholder="مبلغ (ریال)" class="w-full border px-3 py-2 rounded"
                    required>
                <input type="text" id="transferDate" name="date" placeholder="تاریخ"
                    class="w-full border px-3 py-2 rounded" required>

                <select name="from" class="w-full border px-3 py-2 rounded">
                    <option value="">از حساب</option>
                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                    @endforeach
                </select>

                <select name="to" class="w-full border px-3 py-2 rounded">
                    <option value="">به حساب</option>
                    @foreach ($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->title }}</option>
                    @endforeach
                </select>

                <textarea name="desc" class="w-full border px-3 py-2 rounded" placeholder="توضیحات"></textarea>
                <button class="w-full py-2 bg-purple-600 text-white rounded">ثبت انتقال</button>
            </form>
        </div>
    </div>

    {{-- Transactions Chart --}}
    <div class="bg-white p-6 rounded-xl shadow mb-6">
        <h2 class="text-lg font-semibold mb-4">نمودار تراکنش‌ها</h2>
        <canvas id="chart" height="200"></canvas>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white p-4 md:p-6 rounded-xl shadow">
        <h2 class="text-lg font-semibold mb-4">لیست تراکنش‌ها</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm text-gray-700 text-center">
                <thead class="bg-gray-100">
                    <tr>
                        @foreach (['تاریخ', 'نوع', 'مبلغ (ریال)', 'دسته', 'زیر دسته', 'حساب/از', 'به', 'شخص', 'توضیحات'] as $th)
                            <th class="border px-2 py-2 whitespace-nowrap">{{ $th }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $t)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-2 py-2">{{ $t->date }}</td>
                            <td class="border px-2 py-2">{{ ucfirst($t->type) }}</td>
                            <td
                                class="border px-2 py-2 @if ($t->type === 'expense') text-red-600 @elseif($t->type === 'income') text-green-600 @endif font-semibold">
                                {{ number_format($t->amount) }}</td>
                            <td class="border px-2 py-2">{{ $t->type !== 'transfer' ? $t->category : '-' }}</td>
                            <td class="border px-2 py-2">{{ $t->type !== 'transfer' ? $t->subcategory : '-' }}</td>
                            <td class="border px-2 py-2">{{ $t->type === 'transfer' ? $t->from : $t->account }}</td>
                            <td class="border px-2 py-2">{{ $t->type === 'transfer' ? $t->to : '-' }}</td>
                            <td class="border px-2 py-2">{{ $t->type !== 'transfer' ? $t->person : '-' }}</td>
                            <td class="border px-2 py-2">{{ $t->desc }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
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

            // Initialize Persian datepickers
            $("#expenseDate, #incomeDate, #transferDate").persianDatepicker({
                selectedBefore: true
            });

            // Transaction tabs
            $('.transaction-tab').click(function() {
                let type = $(this).data('type');
                $('.transaction-form').addClass('hidden');
                $('.' + type + '-form').removeClass('hidden');
            });

            // Populate subcategories dynamically
            const expenseCategories = @json($expenseCategories);
            const incomeCategories = @json($incomeCategories);

            $('#expenseMainCategory').on('change', function() {
                let subs = expenseCategories[this.value] || [];
                let subSelect = $('#expenseSubCategory').empty().append(
                    '<option value="">زیر دسته</option>');
                subs.forEach(s => subSelect.append('<option>' + s + '</option>'));
            });

            $('#incomeMainCategory').on('change', function() {
                let subs = incomeCategories[this.value] || [];
                let subSelect = $('#incomeSubCategory').empty().append(
                '<option value="">زیر دسته</option>');
                subs.forEach(s => subSelect.append('<option>' + s + '</option>'));
            });

            // Bar Chart for transactions
            const ctx = document.getElementById('chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json(array_keys($chartData)),
                        datasets: [{
                            label: 'مجموع مبلغ (ریال)',
                            data: @json(array_values($chartData)),
                            backgroundColor: '#60a5fa'
                        }]
                    },
                    options: {
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    </script>
@endsection
