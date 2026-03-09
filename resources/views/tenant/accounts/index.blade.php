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
                            <a href="{{ route('tenant.accounts.show', $acc->id) }}"
                                class="text-green-600 hover:underline px-2 py-1 border rounded">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                                </svg>
                            </a>



                            <form action="{{ route('tenant.accounts.balance', $acc->id) }}" method="POST"
                                class="inline-block balance-confirm">
                                @csrf
                                <button type="submit" class="text-yellow-600 hover:underline px-2 py-1 border rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V13.5Zm0 2.25h.008v.008H8.25v-.008Zm0 2.25h.008v.008H8.25V18Zm2.498-6.75h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V13.5Zm0 2.25h.007v.008h-.007v-.008Zm0 2.25h.007v.008h-.007V18Zm2.504-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5Zm0 2.25h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V18Zm2.498-6.75h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V13.5ZM8.25 6h7.5v2.25h-7.5V6ZM12 2.25c-1.892 0-3.758.11-5.593.322C5.307 2.7 4.5 3.65 4.5 4.757V19.5a2.25 2.25 0 0 0 2.25 2.25h10.5a2.25 2.25 0 0 0 2.25-2.25V4.757c0-1.108-.806-2.057-1.907-2.185A48.507 48.507 0 0 0 12 2.25Z" />
                                    </svg>

                                </button>
                            </form>


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
        {{-- لینک‌های صفحه‌بندی --}}
        <div class="mt-5">

            {{ $accounts->links('pagination::tailwind') }}
        </div>
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

            // Confirm before balance
            $('.balance-confirm').on('submit', function(e) {
                if (!confirm(
                        '⚠️ هشدار!\n\n' +
                        'شما در حال بروزرسانی موجودی این حساب هستید.\n\n' +
                        'این عملیات:\n' +
                        '1. تمام تراکنش‌های مرتبط با این حساب (درآمد، هزینه و انتقال) را بررسی می‌کند.\n' +
                        '2. موجودی حساب را مجدداً بر اساس تراکنش‌ها و موجودی اولیه محاسبه می‌کند.\n\n' +
                        'توجه داشته باشید که این عملیات باعث تغییر موجودی حساب می‌شود و قابل بازگشت نیست.\n\n' +
                        'آیا مطمئن هستید که می‌خواهید این کار انجام شود؟'
                    )) e.preventDefault();
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
