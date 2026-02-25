@extends('tenant.layouts.app')

@section('pageTitle', 'مدیریت تسهیلات')

@section('styles')
    <link rel="stylesheet" href="/assets/css/persian-datepicker.css">
    <link rel="stylesheet" href="/assets/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="/assets/css/select2.min.css">
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Loan Form --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-lg font-semibold mb-4">ثبت تسهیلات جدید</h2>
            <form class="space-y-4" method="POST" action="{{ route('tenant.loans.store') }}">
                @csrf

                <div>
                    <label class="block text-sm mb-2">عنوان تسهیلات</label>
                    <input type="text" name="title" value="{{ old('title') }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                    @error('title')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
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
                    <label class="block text-sm mb-2">تاریخ شروع وام</label>
                    <input type="text" id="startDate" name="start_date" value="{{ old('start_date') }}"
                        class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                    @error('start_date')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-2">تعداد اقساط پرداخت شده</label>
                        <input type="number" name="installments_paid" min="0"
                            value="{{ old('installments_paid', 0) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                        @error('installments_paid')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-2">تعداد اقساط باقی مانده</label>
                        <input type="number" name="installments_remaining" min="0"
                            value="{{ old('installments_remaining', 0) }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                        @error('installments_remaining')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-2">سررسید اقساط (روز ماه/سال)</label>
                        <div class="flex gap-2">
                            <input type="number" name="due_day" min="1" max="31" value="{{ old('due_day') }}"
                                class="w-1/2 border-gray-300 rounded-lg shadow-sm px-3 py-2" placeholder="روز">
                            <select name="due_type" class="w-1/2 border-gray-300 rounded-lg shadow-sm">
                                <option value="ماهانه" @selected(old('due_type') == 'ماهانه')>ماهانه</option>
                                <option value="سالانه" @selected(old('due_type') == 'سالانه')>سالانه</option>
                            </select>
                        </div>
                        @error('due_day')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm mb-2">مبلغ هر قسط (ریال)</label>

                        <input type="text" name="installment_amount" id="amount"
                            value="{{ old('installment_amount') }}"
                            class="w-full border-gray-300 rounded-lg shadow-sm px-3 py-2">
                        @error('installment_amount')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>



                <div>
                    <x-button class="text-[18px] w-full">ثبت تسهیلات</x-button>
                </div>
            </form>
        </div>

        {{-- Loan Chart --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-lg font-semibold mb-4">تقسیم‌بندی تسهیلات بر اساس بانک</h2>
            <canvas id="loanChart" height="100"></canvas>
        </div>

    </div>

    {{-- Loans Table --}}
    <div class="rounded-xl border border-slate-200 bg-white p-5 mt-6">
        <h2 class="text-lg font-semibold mb-4">لیست تسهیلات</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full border text-sm text-gray-700 text-center">
                <thead class="bg-gray-100">
                    <tr>
                        @foreach (['عنوان وام', 'مبلغ (ریال)', 'اقساط پرداخت شده', 'اقساط باقی مانده', 'بدهی مانده (ریال)', 'مبلغ هر قسط', 'بانک', 'عملیات'] as $th)
                            <th class="border px-2 py-2 whitespace-nowrap">{{ $th }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($loans as $loan)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $loan->title }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap text-blue-600 font-semibold">
                                {{ number_format($loan->amount) }}
                            </td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $loan->installments_paid }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $loan->installments_remaining }}</td>
                            <td class="border px-2 py-2 whitespace-nowrap text-red-600 font-semibold">
                                {{ number_format($loan->installments_remaining * $loan->installment_amount) }}
                            </td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ number_format($loan->installment_amount) }}
                            </td>
                            <td class="border px-2 py-2 whitespace-nowrap">{{ $loan->bank }}</td>

                            <td class="border px-2 py-2 flex justify-center gap-2 whitespace-nowrap">
                                {{-- View --}}
                                <a href="{{ route('tenant.loans.show', $loan) }}"
                                    class="text-green-600 hover:underline px-2 py-1 border rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"></path>
                                    </svg> </a>
                                {{-- Edit --}}
                                <a href="{{ route('tenant.loans.edit', $loan) }}"
                                    class="text-blue-600 hover:underline px-2 py-1 border rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125">
                                        </path>
                                    </svg>
                                </a>
                                {{-- Delete --}}
                                <form action="{{ route('tenant.loans.destroy', $loan) }}" method="POST"
                                    onsubmit="return confirm('آیا مطمئن هستید؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline px-2 py-1 border rounded">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-4">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                            </path>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Record count --}}
        <div class="mt-4 flex flex-col md:flex-row justify-between text-sm text-gray-600 gap-2 md:gap-0">
            <div>تعداد کل رکوردها: {{ count($loans) }}</div>
            <div>نمایش 1 تا {{ count($loans) }} از {{ count($loans) }}</div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/select2.min.js"></script>
    <script src="/assets/js/chart.umd.min.js"></script>
    <script src="/assets/js/persianDatepicker.min.js"></script>

    <script>
        $(document).ready(function() {
            // Confirm before delete
            $('.delete-confirm').on('submit', function(e) {
                if (!confirm('آیا از حذف این تسهیلات اطمینان دارید؟')) e.preventDefault();
            });
        });
    </script>
@endsection
