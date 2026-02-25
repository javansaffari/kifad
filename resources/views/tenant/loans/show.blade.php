@extends('tenant.layouts.app')

@section('pageTitle', 'جزئیات وام: ' . $loan->title)

@section('styles')
    <link rel="stylesheet" href="/assets/css/persian-datepicker.css">
    <link rel="stylesheet" href="/assets/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection

@section('content')
    <div class="rounded-xl border border-slate-200 bg-white p-6 mt-6 shadow-sm">
        <h2 class="text-2xl font-bold mb-6 text-gray-700">جزئیات وام: {{ $loan->title }}</h2>

        <!-- Loan Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 mb-6 text-sm text-gray-700">
            <div><strong>مبلغ کل:</strong> {{ number_format($loan->amount) }} ریال</div>
            <div><strong>بانک:</strong> {{ $loan->bank }}</div>
            <div><strong>تعداد اقساط پرداخت شده:</strong> {{ $loan->installments_paid }}</div>
            <div><strong>تعداد اقساط باقی مانده:</strong> {{ $loan->installments_remaining }}</div>
            <div><strong>مبلغ هر قسط:</strong> {{ number_format($loan->installment_amount) }} ریال</div>
            <div><strong>سررسید اقساط:</strong> {{ $loan->installment_due_day ?? '-' }} {{ $loan->due_type ?? '' }}</div>
            <div><strong>تاریخ شروع وام:</strong> {{ $loan->start_date ?? '-' }}</div>
            <div><strong>وضعیت یادآوری:</strong> <span
                    class="{{ $loan->reminder ? 'text-green-600' : 'text-red-600' }}">{{ $loan->reminder ? 'فعال' : 'غیرفعال' }}</span>
            </div>
        </div>

        <!-- Loan Installments Table -->
        <h3 class="text-lg font-semibold mb-4 text-gray-700">اقساط وام</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm text-gray-700 text-center">
                <thead class="bg-gray-100">
                    <tr>
                        @foreach (['شماره قسط', 'مبلغ قسط (ریال)', 'تاریخ سررسید', 'پرداخت شده', 'تاریخ پرداخت', 'عملیات'] as $th)
                            <th class="border px-3 py-2 whitespace-nowrap font-medium">{{ $th }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($installments as $inst)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="border px-3 py-2">{{ $inst->installment_number }}</td>
                            <td class="border px-3 py-2">{{ number_format($inst->amount) }}</td>
                            <td class="border px-3 py-2">
                                {{ \Morilog\Jalali\Jalalian::fromDateTime($inst->due_date)->format('Y/m/d') }}

                            </td>
                            <td class="border px-3 py-2">
                                @if ($inst->paid)
                                    <span class="text-green-600 font-semibold">بله</span>
                                @else
                                    <span class="text-red-600 font-semibold">خیر</span>
                                @endif
                            </td>
                            <td class="border px-3 py-2">
                                {{ $inst->paid_at ? \Morilog\Jalali\Jalalian::fromDateTime($inst->paid_at)->format('Y/m/d') : '-' }}

                            </td>
                            <td class="border px-3 py-2 flex justify-center gap-2">
                                @if (!$inst->paid)
                                    <button
                                        class="pay-btn text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded-md shadow-sm"
                                        data-id="{{ $inst->id }}" data-amount="{{ $inst->amount }}">
                                        ثبت پرداخت
                                    </button>
                                @else
                                    <span class="text-gray-500">پرداخت شده</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Back Button -->
        <div class="mt-6">
            <a href="{{ route('tenant.loans.index') }}" class="px-4 py-2 border rounded bg-gray-100 hover:bg-gray-200">
                بازگشت به لیست وام‌ها
            </a>
        </div>
    </div>
    <!-- Pay Modal -->
    <div id="payModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white w-full max-w-md rounded-xl shadow-lg p-6 relative">
            <h2 class="text-lg font-bold mb-4 text-center">ثبت پرداخت</h2>

            <form id="payForm" method="POST">
                @csrf

                <!-- Account Select -->
                <div class="mb-4">
                    <label class="block text-sm mb-1">انتخاب حساب</label>
                    <select name="account_id" required
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-blue-200">
                        @forelse ($accounts as $acc)
                            <option value="{{ $acc->id }}">{{ $acc->title }} ({{ number_format($acc->balance) }}
                                ریال)</option>
                        @empty
                            <option disabled>حسابی موجود نیست</option>
                        @endforelse
                    </select>
                </div>

                <!-- Amount -->
                <div class="mb-4">
                    <label class="block text-sm mb-1">مبلغ پرداخت</label>
                    <input type="number" name="amount" id="payAmount" required min="1"
                        class="w-full border rounded-lg px-3 py-2 focus:ring focus:ring-green-200">
                </div>

                <!-- Payment Date (readonly) -->

                <div class="mb-4">
                    <label class="block text-sm mb-1">تاریخ پرداخت</label>
                    <input type="text" id="payDate" name="payDate" value="{{ old('payDate') }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">

                </div>


                <!-- Remaining -->
                <div class="mb-2 text-sm text-gray-500">
                    مبلغ باقی‌مانده: <span id="remainingAmount" class="font-bold text-red-600"></span> ریال
                </div>

                <!-- Buttons -->
                <div class="flex justify-end gap-2 mt-4">
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


@endsection

@section('scripts')
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/persianDatepicker.min.js"></script>

    <script>
        $(document).ready(function() {
            // باز کردن مدال پرداخت
            $('.pay-btn').click(function() {
                let installmentId = $(this).data('id');
                let amount = $(this).data('amount');

                $('#payAmount').val(amount);
                $('#remainingAmount').text(amount);

                // فرم اکشن داینامیک
                $('#payForm').attr('action', '/tenant/loans/installments/' + installmentId + '/pay');

                $('#payModal').removeClass('hidden').addClass('flex');
            });

            // بستن مدال
            $('#closeModal').click(function() {
                $('#payModal').addClass('hidden').removeClass('flex');
            });

            // بروزرسانی مبلغ باقی‌مانده با تغییر مقدار پرداخت
            $('#payAmount').on('input', function() {
                let val = $(this).val();
                $('#remainingAmount').text(val);
            });
        });
    </script>
@endsection
