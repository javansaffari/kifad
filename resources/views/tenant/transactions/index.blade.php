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


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Income Form --}}

        <div class="rounded-xl border border-slate-200 bg-white p-5  mb-6">
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

                <form class="transaction-form expense-form space-y-4" method="POST" id="expense"
                    action="{{ route('tenant.expenses.store') }}">
                    @csrf

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium mb-1">مبلغ (ریال)</label>
                        <input type="text" name="amount" id="expenseAmount" value="{{ old('amount') }}" required
                            class="amount w-full border border-gray-300 rounded-lg px-3 py-2">
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
                                @foreach ($expenseCategories[null] ?? [] as $expcat)
                                    <option value="{{ $expcat->id }}"
                                        {{ old('category') == $expcat->id ? 'selected' : '' }}>
                                        {{ $expcat->name }}</option>
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

                {{-- Income Form --}}
                <form class="transaction-form income-form space-y-4 hidden" id="income" method="POST"
                    action="{{ route('tenant.income.store') }}">
                    @csrf

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium mb-1">مبلغ (ریال)</label>
                        <input type="text" name="amount" id="incomeAmount" value="{{ old('amount') }}" required
                            class="amount w-full border border-gray-300 rounded-lg px-3 py-2">
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
                                @foreach ($incomeCategories[null] ?? [] as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('category') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <span class="text-red-600 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm mb-1">زیر دسته</label>
                            <select id="subCategory" name="subcategory"
                                class="w-full border-gray-300 rounded-lg shadow-sm">
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
                        <label class="block text-sm mb-1">واریز به حساب</label>
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
                        <x-button class="w-full text-[18px]">ثبت درآمد</x-button>
                    </div>
                </form>

                {{-- Transfer Form --}}
                {{-- Transfer Form --}}
                <form class="transaction-form transfer-form space-y-4 hidden" id="transfer" method="POST"
                    action="{{ route('tenant.transactions.storeTransfer') }}">
                    @csrf
                    <input type="hidden" name="type" value="transfer">

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium mb-1">مبلغ (ریال)</label>
                        <input type="text" name="amount" id="transferAmount" value="{{ old('amount') }}" required
                            class="amount w-full border border-gray-300 rounded-lg px-3 py-2">
                        @error('amount')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div>
                        <label class="block text-sm font-medium mb-1">تاریخ</label>
                        <input type="text" id="transferDatapicker" name="date" value="{{ old('date') }}"
                            required class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        @error('date')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- From Account -->
                    <div>
                        <label class="block text-sm mb-1">برداشت از حساب</label>
                        <select name="from_account_id" required class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">انتخاب کنید</option>
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}"
                                    {{ old('from_account_id') == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->title }} (موجودی: {{ number_format($acc->balance) }} ریال)
                                </option>
                            @endforeach
                        </select>
                        @error('from_account_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- To Account -->
                    <div>
                        <label class="block text-sm mb-1">واریز به حساب</label>
                        <select name="to_account_id" required class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">انتخاب کنید</option>
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}"
                                    {{ old('to_account_id') == $acc->id ? 'selected' : '' }}>
                                    {{ $acc->title }} (موجودی: {{ number_format($acc->balance) }} ریال)
                                </option>
                            @endforeach
                        </select>
                        @error('to_account_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Person -->
                    <div>
                        <label class="block text-sm mb-1">شخص </label>
                        <select name="person_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                            <option value="">انتخاب کنید</option>
                            @foreach ($persons as $p)
                                <option value="{{ $p->id }}" {{ old('person_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('person_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm mb-1">توضیحات</label>
                        <textarea name="description" class="w-full border-gray-300 rounded-lg shadow-sm h-24">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <x-button class="w-full text-[18px]">ثبت تراکنش انتقال وجه</x-button>
                    </div>
                </form>


            </div>
        </div>


        {{-- Income Chart --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 ">
            <h2 class="text-lg font-semibold mb-4">تقسیم بندی تراکنش‌های ماهیانه</h2>
            <canvas id="chart" height="600"></canvas>
        </div>
    </div>

    {{-- Incomes Table --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5  mt-6">
        <h2 class="text-lg font-semibold mb-4">لیست تراکنش ها</h2>

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
                                    {{-- Use mainCategory name --}}
                                    {{ $inc->mainCategory?->name ?? 'نامشخص' }}
                                    @if ($inc->subCategory)
                                        - {{ $inc->subCategory->name ?? 'نامشخص' }}
                                    @endif
                                @endif
                            </td>

                            <td class="border px-2 py-2 whitespace-nowrap">
                                @if ($inc->type !== 'transfer')
                                    {{-- Display account title if exists, otherwise fallback --}}
                                    {{ $inc->account?->title ?? 'بدون حساب' }}
                                @else
                                    -
                                @endif
                            </td>

                            <td class="border px-2 py-2 whitespace-nowrap">
                                @if ($inc->type !== 'transfer')
                                    {{-- Display person name if exists, otherwise fallback --}}
                                    {{ $inc->person?->name ?? 'نامشخص' }}
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

                {{-- جمع کل تراکنش‌ها صفحه فعلی --}}
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td colspan="3" class="border px-2 py-2 text-red-600 whitespace-nowrap">
                            جمع هزینه‌ها: {{ number_format($transactions->where('type', 'expense')->sum('amount')) }} ریال
                        </td>

                        <td colspan="3" class="border px-2 py-2 text-green-600 whitespace-nowrap">
                            جمع درآمد‌ها: {{ number_format($transactions->where('type', 'income')->sum('amount')) }} ریال
                        </td>

                        <td colspan="3" class="border px-2 py-2 text-blue-600 whitespace-nowrap">
                            جمع انتقال: {{ number_format($transactions->where('type', 'transfer')->sum('amount')) }} ریال
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>

        {{-- لینک‌های صفحه‌بندی --}}
        <div class="mt-5">

            {{ $transactions->links('pagination::tailwind') }}
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

                // Bind sub categories
                bindSubCategories('#expenseMainCategory', '#expenseSubCategory', expenseCategories);
                bindSubCategories('#incomeMainCategory', '#incomeSubCategory', incomeCategories);


                // Chart data from backend
                const incomeData = @json($incomeChartData);
                const expenseData = @json($expenseChartData);


                // Merge category labels
                const categories = [
                    ...Object.keys(incomeData),
                    ...Object.keys(expenseData)
                ];

                const labels = [...new Set(categories)];


                // Prepare datasets
                const incomeDataset = labels.map(cat => incomeData[cat] ?? 0);
                const expenseDataset = labels.map(cat => expenseData[cat] ?? 0);


                // Create chart
                const canvas = document.getElementById('chart');

                if (canvas) {

                    if (window.financeChart) {
                        window.financeChart.destroy();
                    }

                    window.financeChart = new Chart(canvas, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                    label: 'درآمد',
                                    data: incomeDataset,
                                    backgroundColor: '#22c55e',
                                    borderRadius: 6
                                },
                                {
                                    label: 'هزینه',
                                    data: expenseDataset,
                                    backgroundColor: '#ef4444',
                                    borderRadius: 6
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,

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
                                    callbacks: {
                                        label: function(context) {
                                            return context.raw.toLocaleString() + ' ریال';
                                        }
                                    }
                                }
                            },

                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: value => value.toLocaleString()
                                    }
                                }
                            }
                        }
                    });
                }


            });
        </script>

    @endsection
