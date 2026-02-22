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

    <div class="rounded-xl border border-slate-200 bg-white p-5 mt-6">
        <h2 class="text-lg font-semibold mb-4">لیست تراکنش ها</h2>

        <!-- Transactions table -->
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
                    @php
                        // Initialize totals
                        $totalExpense = 0;
                        $totalIncome = 0;
                        $totalTransfer = 0;
                    @endphp

                    @foreach ($transactions as $tr)
                        @php
                            // Calculate totals dynamically
                            if ($tr->type === 'expense') {
                                $totalExpense += $tr->amount;
                            }
                            if ($tr->type === 'income') {
                                $totalIncome += $tr->amount;
                            }
                            if ($tr->type === 'transfer') {
                                $totalTransfer += $tr->amount;
                            }
                        @endphp

                        <tr class="hover:bg-gray-50">

                            <!-- Transaction Type -->
                            <td
                                class="border px-2 py-2 font-semibold whitespace-nowrap
                            {{ $tr->type === 'expense' ? 'text-red-600' : ($tr->type === 'income' ? 'text-green-600' : 'text-blue-600') }}">
                                {{ $tr->type === 'expense' ? 'هزینه' : ($tr->type === 'income' ? 'درآمد' : 'انتقال وجه') }}
                            </td>

                            <!-- Transaction Date -->
                            <td class="border px-2 py-2 whitespace-nowrap">
                                {{ $tr->date }}
                            </td>

                            <!-- Amount -->
                            <td
                                class="border px-2 py-2 font-semibold whitespace-nowrap
                            {{ $tr->type === 'expense' ? 'text-red-600' : ($tr->type === 'income' ? 'text-green-600' : 'text-blue-600') }}">
                                {{ number_format($tr->amount) }}
                            </td>

                            <!-- Category or Transfer Accounts -->
                            <td class="border px-2 py-2 whitespace-nowrap">
                                @if ($tr->type === 'transfer')
                                    {{ $tr->fromAccount->title ?? '-' }} →
                                    {{ $tr->toAccount->title ?? '-' }}
                                @else
                                    {{ $tr->mainCategory->name ?? '-' }}
                                    @if ($tr->subCategory)
                                        - {{ $tr->subCategory->name }}
                                    @endif
                                @endif
                            </td>

                            <!-- Account Column -->
                            <td class="border px-2 py-2 whitespace-nowrap">
                                @if ($tr->type === 'expense')
                                    {{ $tr->fromAccount->title ?? '-' }}
                                @elseif ($tr->type === 'income')
                                    {{ $tr->toAccount->title ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>

                            <!-- Related Person -->
                            <td class="border px-2 py-2 whitespace-nowrap">
                                {{ $tr->person->name ?? '-' }}
                            </td>

                            <!-- Description -->
                            <td class="border px-2 py-2 whitespace-nowrap">
                                {{ $tr->description ?? '-' }}
                            </td>

                            <!-- Created At -->
                            <td class="border px-2 py-2 whitespace-nowrap">
                                {{ $tr->created_at->format('Y-m-d H:i') }}
                            </td>

                            <!-- Actions -->
                            <td class="border px-2 py-2 flex justify-center gap-2 whitespace-nowrap">

                                @php
                                    // Dynamic edit route based on transaction type
                                    if ($tr->type === 'expense') {
                                        $editRoute = route('tenant.expenses.edit', $tr->id);
                                    } elseif ($tr->type === 'income') {
                                        $editRoute = route('tenant.income.edit', $tr->id);
                                    } else {
                                        $editRoute = route('tenant.transactions.edit', $tr->id);
                                    }
                                @endphp

                                <!-- Edit Icon -->
                                <a href="{{ $editRoute }}"
                                    class="text-blue-600 hover:underline px-2 py-1 border rounded flex items-center justify-center">

                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875
                             1.875 0 1 1 2.652 2.652L6.832
                             19.82a4.5 4.5 0 0 1-1.897
                             1.13l-2.685.8.8-2.685a4.5
                             4.5 0 0 1 1.13-1.897L16.863
                             4.487Zm0 0L19.5 7.125" />
                                    </svg>
                                </a>

                                <!-- Delete Icon -->
                                <form action="{{ route('tenant.transactions.destroy', $tr->id) }}" method="POST"
                                    class="inline-block delete-confirm">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                        class="text-red-600 hover:underline px-2 py-1 border rounded flex items-center justify-center">

                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788
                                 0L9.26 9m9.968-3.21c.342.052.682.107
                                 1.022.166m-1.022-.165L18.16
                                 19.673a2.25 2.25 0 0 1-2.244
                                 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772
                                 5.79m14.456 0a48.108 48.108 0 0
                                 0-3.478-.397m-12
                                 .562c.34-.059.68-.114
                                 1.022-.165m0 0a48.11
                                 48.11 0 0 1 3.478-.397m7.5
                                 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964
                                 51.964 0 0 0-3.32
                                 0c-1.18.037-2.09
                                 1.022-2.09
                                 2.201v.916m7.5 0a48.667
                                 48.667 0 0 0-7.5 0" />
                                        </svg>

                                    </button>
                                </form>

                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <!-- Totals Footer -->
                <tfoot class="bg-gray-50 font-semibold">
                    <tr>
                        <td colspan="3" class="border px-2 py-2 text-red-600 whitespace-nowrap">
                            جمع هزینه‌ها: {{ number_format($totalExpense) }} ریال
                        </td>

                        <td colspan="3" class="border px-2 py-2 text-green-600 whitespace-nowrap">
                            جمع درآمد‌ها: {{ number_format($totalIncome) }} ریال
                        </td>

                        <td colspan="3" class="border px-2 py-2 text-blue-600 whitespace-nowrap">
                            جمع انتقال: {{ number_format($totalTransfer) }} ریال
                        </td>
                    </tr>
                </tfoot>

            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $transactions->links() }}
        </div>

        <!-- Record Count -->
        <div class="mt-4 flex flex-col md:flex-row justify-between text-sm text-gray-600 gap-2 md:gap-0">
            <div>
                Total Records: {{ $transactions->total() }}
            </div>
            <div>
                Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }}
                of {{ $transactions->total() }}
            </div>
        </div>

    </div>

@endsection

@section('scripts')
@endsection
