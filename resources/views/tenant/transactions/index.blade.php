@extends('tenant.layouts.app')

@section('pageTitle', 'مدیریت تراکنش ها')

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

        $expenseTags = ['کار', 'خانواده', 'تفریح', 'سفر', 'سایر'];
        $incomeTags = ['حقوق', 'سرمایه گذاری', 'فروش', 'هدیه', 'سایر'];

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

        $categories = [];
        $incomeData = [];
        $expenseData = [];

        foreach ($transactions as $t) {
            if ($t->type === 'income') {
                $incomeData[$t->category] = ($incomeData[$t->category] ?? 0) + $t->amount;
                if (!in_array($t->category, $categories)) {
                    $categories[] = $t->category;
                }
            } elseif ($t->type === 'expense') {
                $expenseData[$t->category] = ($expenseData[$t->category] ?? 0) + $t->amount;
                if (!in_array($t->category, $categories)) {
                    $categories[] = $t->category;
                }
            }
        }
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Income Form --}}

        <div class="bg-white p-6 rounded-xl shadow mb-6">
            <h2 class="text-lg font-semibold mb-4">ثبت تراکنش جدید</h2>
            <div class="mb-4 flex gap-2">
                <button
                    class="transaction-tab px-4 py-2 rounded border border-gray-300 text-white bg-red-600 transition-colors duration-200"
                    data-type="expense">ثبت هزینه</button>
                <button
                    class="transaction-tab px-4 py-2 rounded border border-gray-300 text-gray-700 bg-white transition-colors duration-200"
                    data-type="income">ثبت درآمد</button>
                <button
                    class="transaction-tab px-4 py-2 rounded border border-gray-300 text-gray-700 bg-white transition-colors duration-200"
                    data-type="transfer">انتقال وجه</button>
            </div>


            {{-- Transaction Forms --}}
            <div id="transactionForms">
                {{-- Expense Form --}}

                <form class="transaction-form expense-form space-y-4" id="expense" method="POST" action="#">
                    @csrf
                    <!-- Amount input -->
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                            مبلغ (ریال)
                        </label>
                        <input type="text" name="amount" required
                            class="amount w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span id="amountError" class="text-red-600 text-sm mt-1 block"></span>
                    </div>

                    <!-- Date input with Persian datepicker -->
                    <div class="mb-4">
                        <label for="datapicker" class="block text-sm font-medium text-gray-700 mb-1">
                            تاریخ
                        </label>
                        <input type="text" id="expenseDatapicker" name="date" required
                            class="w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <!-- Category selection -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm mb-2">دسته‌بندی اصلی</label>
                            <select id="expenseMainCategory" name="category" required
                                class="w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="">انتخاب کنید</option>
                                @foreach (array_keys($expenseCategories) as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subcategory selection -->
                        <div>
                            <label class="block text-sm mb-2">زیر دسته</label>
                            <select id="expenseSubCategory" name="expenseSubCategory"
                                class="w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="">انتخاب کنید</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tags selection -->
                    <div>
                        <label class="block text-sm mb-2">برچسب‌ها</label>
                        <select name="expenseTags[]" class="expensSelect w-full border-gray-300 rounded-lg"
                            multiple="multiple">
                            @foreach ($expenseTags as $tag)
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
                        <button
                            class="w-full py-2 bg-red-600 hover:bg-red-700 transition-colors duration-200 text-white rounded">
                            ثبت تراکنش هزینه
                        </button>
                    </div>
                </form>

                {{-- Income Form --}}
                <form class="transaction-form income-form space-y-4 hidden" id="income" method="POST" action="#">
                    @csrf
                    <!-- Amount input -->
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                            مبلغ (ریال)
                        </label>
                        <input type="text" name="amount" required
                            class="amount w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span id="amountError" class="text-red-600 text-sm mt-1 block"></span>
                    </div>

                    <!-- Date input with Persian datepicker -->
                    <div class="mb-4">
                        <label for="datapicker" class="block text-sm font-medium text-gray-700 mb-1">
                            تاریخ
                        </label>
                        <input type="text" id="incomeDatapicker" name="date" required
                            class="w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>

                    <!-- Category selection -->
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm mb-2">دسته‌بندی اصلی</label>
                            <select id="incomeMainCategory" name="category" required
                                class="w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="">انتخاب کنید</option>
                                @foreach (array_keys($incomeCategories) as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subcategory selection -->
                        <div>
                            <label class="block text-sm mb-2">زیر دسته</label>
                            <select id="incomeSubCategory" name="incomeSubCategory"
                                class="w-full border-gray-300 rounded-lg shadow-sm">
                                <option value="">انتخاب کنید</option>
                            </select>
                        </div>
                    </div>

                    <!-- Tags selection -->
                    <div>
                        <label class="block text-sm mb-2">برچسب‌ها</label>
                        <select name="incomeTags[]" class="incomSelect w-full border-gray-300 rounded-lg"
                            multiple="multiple">
                            @foreach ($incomeTags as $tag)
                                <option value="{{ $tag }}">{{ $tag }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Account selection -->
                    <div>
                        <label class="block text-sm mb-2">واریز به حساب</label>
                        <select id="account" name="account" required
                            class="w-full border-gray-300 rounded-lg shadow-sm">
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
                        <button
                            class="w-full py-2 bg-green-600 hover:bg-green-700 transition-colors duration-200 text-white rounded">
                            ثبت تراکنش درآمد
                        </button>
                    </div>
                </form>

                {{-- Transfer Form --}}
                <form class="transaction-form transfer-form space-y-4 hidden" id="transfer" method="POST"
                    action="#">
                    @csrf


                    <!-- Amount input -->
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                            مبلغ (ریال)
                        </label>
                        <input type="text" name="amount" required
                            class="amount w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <span id="amountError" class="text-red-600 text-sm mt-1 block"></span>
                    </div>

                    <!-- Date input with Persian datepicker -->
                    <div class="mb-4">
                        <label for="datapicker" class="block text-sm font-medium text-gray-700 mb-1">
                            تاریخ
                        </label>
                        <input type="text" id="transferDatapicker" name="date" required
                            class="w-full border border-gray-300 rounded-lg shadow-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>


                    <!-- Account selection -->
                    <div>
                        <label class="block text-sm mb-2">برداشت از حساب</label>
                        <select id="fromAccount" name="fromAccount" required
                            class="w-full border-gray-300 rounded-lg shadow-sm">
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->title }} (موجودی:
                                    {{ number_format($acc->balance) }} ریال)</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Account selection -->
                    <div>
                        <label class="block text-sm mb-2">واریز به حساب</label>
                        <select id="toAccount" name="toAccount" required
                            class="w-full border-gray-300 rounded-lg shadow-sm">
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->title }} (موجودی:
                                    {{ number_format($acc->balance) }} ریال)</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Description input -->
                    <div>
                        <label class="block text-sm mb-2">توضیحات</label>
                        <textarea name="desc" class="w-full border-gray-300 rounded-lg shadow-sm h-24"></textarea>
                    </div>

                    <button
                        class="w-full py-2 bg-blue-600 hover:bg-blue-700 transition-colors duration-200 text-white rounded">
                        ثبت تراکنش انتقال
                    </button>
                </form>
            </div>
        </div>


        {{-- Income Chart --}}
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-lg font-semibold mb-4">تقسیم بندی تراکنش‌های ماهیانه</h2>
            <canvas id="chart" height="600"></canvas>
        </div>
    </div>

    {{-- Incomes Table --}}
    <div class="bg-white p-4 md:p-6 rounded-xl shadow mt-6">
        <h2 class="text-lg font-semibold mb-4">لیست تراکنش ها</h2>

        {{-- Filters --}}
        <div class="flex flex-col md:flex-row flex-wrap gap-4 items-start mb-4 bg-gray-50 p-4 rounded-lg">
            <form action="#" class="flex flex-col md:flex-row flex-wrap gap-2 w-full md:flex-1">
                <!-- Search input -->
                <input type="text" placeholder="جستجو..."
                    class="border border-gray-300 rounded-lg shadow-sm w-full md:w-64 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">


                <!-- type filter -->
                <select
                    class="border border-gray-300 rounded-lg shadow-sm w-full md:w-48 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <option>نوع تراکنش</option>
                    <option>هزینه</option>
                    <option>درآمد</option>
                    <option>انتقال وجه</option>
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

        {{-- Transactions table --}}
        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm text-gray-700 text-center">
                <thead class="bg-gray-100">
                    <tr>
                        @foreach (['نوع', 'تاریخ', 'مبلغ (ریال)', 'دسته‌بندی', 'حساب', 'شخص', 'توضیحات', 'زمان ایجاد تراکنش', 'عملیات'] as $th)
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
                                class="border px-2 py-2 {{ $inc->type === 'expense' ? 'text-red-600' : ($inc->type === 'income' ? 'text-green-600' : 'text-blue-600') }} font-semibold whitespace-nowrap">
                                {{ number_format($inc->amount) }}
                            </td>

                            {{-- category --}}
                            <td class="border px-2 py-2 whitespace-nowrap">
                                @if ($inc->type === 'transfer')
                                    {{ $inc->from }} - {{ $inc->to }}
                                @else
                                    {{ $inc->category }} @if ($inc->subcategory)
                                        - {{ $inc->subcategory }}
                                    @endif
                                @endif
                            </td>

                            <td class="border px-2 py-2 whitespace-nowrap">
                                @if ($inc->type !== 'transfer')
                                    {{ $inc->account }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="border px-2 py-2 whitespace-nowrap">
                                @if ($inc->type !== 'transfer')
                                    {{ $inc->person }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="border px-2 py-2 whitespace-nowrap">{{ $inc->desc }}</td>

                            <td class="border px-2 py-2 whitespace-nowrap">{{ $inc->date }}</td>


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

                {{-- جمع کل تراکنش‌ها --}}
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>

                        <td colspan="3" class="border px-2 py-2 text-red-600 whitespace-nowrap">
                            جمع هزینه‌ها:
                            {{ number_format(array_sum(array_map(fn($e) => $e->type === 'expense' ? $e->amount : 0, $transactions))) }}
                            ریال
                        </td>

                        <td colspan="3" class="border px-2 py-2 text-green-600 whitespace-nowrap">
                            جمع درآمد‌ها:
                            {{ number_format(array_sum(array_map(fn($e) => $e->type === 'income' ? $e->amount : 0, $transactions))) }}
                            ریال
                        </td>

                        <td colspan="3" class="border px-2 py-2 text-blue-600 whitespace-nowrap">
                            جمع انتقال:
                            {{ number_format(array_sum(array_map(fn($e) => $e->type === 'transfer' ? $e->amount : 0, $transactions))) }}
                            ریال
                        </td>

                    </tr>
                </tfoot>

            </table>
        </div>


        {{-- Record count --}}
        <div class="mt-4 flex flex-col md:flex-row justify-between text-sm text-gray-600 gap-2 md:gap-0">
            <div>تعداد کل رکوردها: {{ count($transactions) }}</div>
            <div>نمایش 1 تا {{ count($transactions) }} از {{ count($transactions) }}</div>
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



            // Handle tab switching (single unified block)
            const tabs = document.querySelectorAll('.transaction-tab');
            const forms = document.querySelectorAll('.transaction-form');

            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const type = this.dataset.type;

                    // Hide all forms
                    forms.forEach(f => f.classList.add('hidden'));

                    // Show selected form
                    const formToShow = document.getElementById(type);
                    if (formToShow) formToShow.classList.remove('hidden');

                    // Remove active from all tabs
                    tabs.forEach(t => {
                        t.classList.remove('bg-red-600', 'bg-green-600', 'bg-blue-600',
                            'text-white');
                        t.classList.add('bg-white', 'text-gray-700');
                    });

                    // Add active style to clicked tab
                    this.classList.remove('bg-white', 'text-gray-700');
                    if (type === 'expense') this.classList.add('bg-red-600', 'text-white');
                    if (type === 'income') this.classList.add('bg-green-600', 'text-white');
                    if (type === 'transfer') this.classList.add('bg-blue-600', 'text-white');
                });
            });


            // Handle main category change and populate subcategories
            function bindSubCategories(mainSelector, subSelector, categories) {
                $(mainSelector).on('change', function() {
                    const subs = categories[this.value] ?? [];
                    const subCat = $(subSelector).empty().append('<option value="">انتخاب کنید</option>');
                    subs.forEach(s => subCat.append(`<option value="${s}">${s}</option>`));
                });
            }

            const expenseCategories = @json($expenseCategories);
            const incomeCategories = @json($incomeCategories);

            bindSubCategories('#expenseMainCategory', '#expenseSubCategory', expenseCategories);
            bindSubCategories('#incomeMainCategory', '#incomeSubCategory', incomeCategories);




            // Bar chart for income & expense
            const ctx = document.getElementById('chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($categories),
                        datasets: [{
                                label: 'درآمد (ریال)',
                                data: @json(array_map(fn($cat) => $incomeData[$cat] ?? 0, $categories)),
                                backgroundColor: '#34d399'
                            },
                            {
                                label: 'هزینه (ریال)',
                                data: @json(array_map(fn($cat) => $expenseData[$cat] ?? 0, $categories)),
                                backgroundColor: '#f87171'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
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
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    font: {
                                        family: 'YekanBakh',
                                        size: 12
                                    }
                                }
                            },
                            x: {
                                ticks: {
                                    font: {
                                        family: 'YekanBakh',
                                        size: 12
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

@endsection
