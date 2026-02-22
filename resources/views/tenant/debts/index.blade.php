@extends('tenant.layouts.app')

@section('pageTitle', 'مدیریت بدهی‌ها و طلب‌ها')

@section('styles')
    <link rel="stylesheet" href="/assets/css/persian-datepicker.css">
    <link rel="stylesheet" href="/assets/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Form --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5 ">
            <h2 class="text-lg font-semibold mb-4">ثبت بدهی / طلب جدید</h2>
            <form class="space-y-4" method="POST" action="{{ route('tenant.debts.store') }}">
                @csrf
                <div>
                    <label class="block text-sm mb-2">نوع</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-1">
                            <input type="radio" name="type" value="borrow" required checked>
                            <span>بدهی (قرض گرفتن)</span>
                        </label>
                        <label class="flex items-center gap-1">
                            <input type="radio" name="type" value="lend" required>
                            <span>طلب (قرض دادن)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-2">مبلغ (ریال)</label>
                    <input type="text" name="amount" id="amount" value="{{ old('amount') }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                    @error('amount')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm mb-2">تاریخ موعد</label>
                    <input type="text" id="dueDate" name="due_date" value="{{ old('due_date') }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                    @error('due_date')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm mb-2">حساب</label>
                    <select name="account_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                        @foreach ($accounts as $acc)
                            <option value="{{ $acc->id }}">
                                @if ($acc->balance < 0)
                                    ⚠️
                                @endif
                                {{ $acc->title }} (موجودی: {{ number_format($acc->balance) }} ریال)
                            </option>
                        @endforeach
                    </select>
                    @error('account_id')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm mb-2">شخص</label>
                    <select name="person_id" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">انتخاب کنید</option>
                        @foreach ($persons as $p)
                            <option value="{{ $p->id }}" @selected(old('person_id') == $p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    @error('person_id')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm mb-2">برچسب‌ها</label>
                    <select name="tags[]" class="select w-full border-gray-300 rounded-lg" multiple="multiple">
                        @foreach ($tags as $tag)
                            <option value="{{ $tag }}" @selected(collect(old('tags'))->contains($tag))>{{ $tag }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-2">توضیحات</label>
                    <textarea name="description" class="w-full border-gray-300 rounded-lg shadow-sm h-24">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm mb-2">ثبت همزمان در تراکنش‌ها</label>
                    <label class="flex items-center gap-2 ">
                        <input type="checkbox" name="create_transaction" value="1" @checked(old('create_transaction'))>
                        <span class="text-sm">
                            این بدهی/طلب همزمان به عنوان تراکنش ثبت شود
                            <span class="text-gray-400">(در صورت تیک، موجودی حساب به‌روزرسانی می‌شود)</span>
                        </span> </label>
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

        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm text-gray-700 text-center">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-2 py-2">نوع</th>
                        <th class="border px-2 py-2">حساب</th>
                        <th class="border px-2 py-2">مبلغ</th>
                        <th class="border px-2 py-2">پرداخت شده</th>
                        <th class="border px-2 py-2">باقی مانده</th>
                        <th class="border px-2 py-2">وضعیت</th>
                        <th class="border px-2 py-2">شخص</th>
                        <th class="border px-2 py-2">توضیحات</th>
                        <th class="border px-2 py-2">عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($debts as $d)
                        @php
                            $remaining = $d->amount - $d->paid_amount;
                            $status = $remaining == 0 ? 'تسویه شده' : ($d->paid_amount > 0 ? 'نیمه تسویه' : 'باز');
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="border px-2 py-2">
                                {{ $d->type == 'borrow' ? 'بدهی' : 'طلب' }}
                            </td>
                            <td class="border px-2 py-2">{{ $d->account->title ?? '-' }}</td>
                            <td class="border px-2 py-2">{{ number_format($d->amount) }}</td>
                            <td class="border px-2 py-2 text-green-600">{{ number_format($d->paid_amount) }}</td>
                            <td class="border px-2 py-2 text-red-600">{{ number_format($remaining) }}</td>
                            <td class="border px-2 py-2">
                                <span
                                    class="
@if ($status == 'تسویه شده') text-green-600
@elseif($status == 'نیمه تسویه') text-yellow-600
@else text-red-600 @endif
font-bold">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="border px-2 py-2">{{ $d->person->name ?? '-' }}</td>
                            <td class="border px-2 py-2">{{ $d->description ?? '-' }}</td>
                            <td class="border px-2 py-2 flex justify-center gap-2">

                                @if ($remaining > 0)
                                    <button class="open-pay-modal text-green-600 border px-2 py-1 rounded"
                                        data-id="{{ $d->id }}" data-remaining="{{ $remaining }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                        </svg>



                                    </button>
                                @endif

                                <a href="{{ route('tenant.debts.edit', $d->id) }}"
                                    class="text-blue-600 hover:underline px-2 py-1 border rounded"><svg
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125">
                                        </path>
                                    </svg></a>
                                <form action="{{ route('tenant.debts.destroy', $d->id) }}" method="POST"
                                    class="inline-block delete-confirm">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:underline px-2 py-1 border rounded"><svg
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                            </path>
                                        </svg></button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <!-- Pay Modal -->
        <div id="payModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

            <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6 relative">

                <!-- Title -->
                <h2 id="modalTitle" class="text-lg font-bold mb-4 text-center">
                    پرداخت
                </h2>

                <form id="payForm" method="POST">
                    @csrf

                    <!-- Account Select -->
                    <div class="mb-4">
                        <label class="block text-sm mb-1">انتخاب حساب</label>
                        <select name="account_id" required
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                            @foreach ($accounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->title }} ({{ number_format($acc->balance) }} ریال)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Remaining -->
                    <div class="mb-2 text-sm text-gray-500">
                        مبلغ باقی‌مانده:
                        <span id="remainingAmount" class="font-bold text-red-600"></span>
                        ریال
                    </div>

                    <!-- Amount -->
                    <div class="mb-4">
                        <label class="block text-sm mb-1">مبلغ پرداخت</label>
                        <input type="number" name="amount" id="payAmount" required min="1"
                            class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-green-200">
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-2">
                        <button type="button" id="closeModal" class="px-4 py-2 border rounded-lg">
                            انصراف
                        </button>

                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            ثبت عملیات
                        </button>
                    </div>
                </form>

            </div>
        </div>

        {{-- Record count --}}
        <div class="mt-4 flex flex-col md:flex-row justify-between text-sm text-gray-600 gap-2 md:gap-0">
            <div>تعداد کل رکوردها: {{ $debts->count() }}</div>
            <div>نمایش 1 تا {{ $debts->count() }} از {{ $debts->count() }}</div>
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


            // Confirm before delete
            $('.delete-confirm').on('submit', function(e) {
                if (!confirm('آیا از حذف این شخص اطمینان دارید؟')) e.preventDefault();
            });


            $('.select').select2();

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


        document.addEventListener('DOMContentLoaded', function() {

            const modal = document.getElementById('payModal');
            const form = document.getElementById('payForm');
            const amountInput = document.getElementById('payAmount');
            const remainingText = document.getElementById('remainingAmount');
            const modalTitle = document.getElementById('modalTitle');

            // open modal
            document.querySelectorAll('.open-pay-modal').forEach(btn => {
                btn.addEventListener('click', function() {

                    const debtId = this.dataset.id;
                    const remaining = this.dataset.remaining;
                    const type = this.closest('tr')
                        .querySelector('td')
                        .innerText.trim();

                    modal.classList.remove('hidden');
                    modal.classList.add('flex');

                    remainingText.innerText = Number(remaining).toLocaleString();
                    amountInput.max = remaining;
                    amountInput.value = '';

                    // change title based on type
                    if (type === 'بدهی') {
                        modalTitle.innerText = 'بازپرداخت بدهی';
                    } else {
                        modalTitle.innerText = 'دریافت طلب';
                    }

                    form.action = `/tenant/debts/${debtId}/pay`;
                });
            });

            // close modal
            document.getElementById('closeModal').addEventListener('click', function() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            });

        });
    </script>
@endsection
