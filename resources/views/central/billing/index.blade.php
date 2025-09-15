@extends('tenant.layouts.app')

@section('pageTitle', 'صورتحساب و مالی')

@section('content')
    @php
        $walletBalance = 1500000;

        $subscription = (object) [
            'plan' => 'رشد',
            'remaining_days' => 267,
            'start_date' => '1404/03/18',
            'expiry_date' => '1405/03/19',
            'last_payment' => '1404/03/18',
        ];

        $transactions = [
            (object) [
                'title' => 'اشتراک رشد سالیانه',
                'amount' => 6678000,
                'status' => 'پرداخت شده',
                'date' => '1404/03/18',
            ],
            (object) [
                'title' => 'اشتراک رشد سالیانه',
                'amount' => 3820800,
                'status' => 'پرداخت شده',
                'date' => '1403/03/25',
            ],
            (object) [
                'title' => 'اشتراک شخصی ماهانه',
                'amount' => 158400,
                'status' => 'پرداخت شده',
                'date' => '1403/01/13',
            ],
        ];

        $plans = [
            (object) [
                'name' => 'یک ماهه',
                'price' => 104000,
                'button' => 'خرید اشتراک',
            ],
            (object) [
                'name' => 'سه ماهه',
                'price' => 294000,
                'button' => 'خرید اشتراک',
            ],
            (object) [
                'name' => 'شش ماهه',
                'price' => 564000,
                'button' => 'خرید اشتراک',
            ],
            (object) [
                'name' => 'یک ساله',
                'price' => 999000,
                'button' => 'خرید اشتراک',
            ],
        ];

        $subscriptionPercent = intval(($subscription->remaining_days / 365) * 100);
    @endphp

    <div class="space-y-6">

        {{-- کیف پول --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6 flex flex-col lg:flex-row justify-between items-center">
            <div>
                <h2 class="text-xl font-bold mb-2">کیف پول</h2>
                <div class="text-2xl font-semibold">{{ number_format($walletBalance) }} ریال</div>
                <div class="text-sm text-slate-500 mt-1">موجودی کل کیف‌پول</div>
            </div>
            <div class="mt-4 lg:mt-0 flex gap-2">
                <x-button>واریز وجه</x-button>
                <x-button class="border">تاریخچه استفاده</x-button>
            </div>
        </div>

        {{-- اشتراک فعلی --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-bold mb-4">اشتراک شما</h2>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="p-4 bg-slate-100 rounded-lg text-center">
                    <div class="text-sm text-slate-600">پلن فعلی</div>
                    <div class="text-lg font-semibold">{{ $subscription->plan }}</div>
                </div>
                <div class="p-4 bg-slate-100 rounded-lg text-center">
                    <div class="text-sm text-slate-600">زمان باقی‌مانده</div>
                    <div class="text-lg font-semibold">{{ $subscription->remaining_days }} روز</div>
                </div>
                <div class="p-4 bg-slate-100 rounded-lg text-center">
                    <div class="text-sm text-slate-600">تاریخ شروع</div>
                    <div class="text-lg font-semibold">{{ $subscription->start_date }}</div>
                </div>
                <div class="p-4 bg-slate-100 rounded-lg text-center">
                    <div class="text-sm text-slate-600">تاریخ پایان</div>
                    <div class="text-lg font-semibold">{{ $subscription->expiry_date }}</div>
                </div>
                <div class="p-4 bg-slate-100 rounded-lg text-center">
                    <div class="text-sm text-slate-600">آخرین پرداخت</div>
                    <div class="text-lg font-semibold">{{ $subscription->last_payment }}</div>
                </div>
            </div>

            {{-- نوار پیشرفت --}}
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-green-500 h-4 rounded-full" style="width: {{ $subscriptionPercent }}%"></div>
                </div>
                <div class="text-sm text-slate-500 mt-1 text-right">{{ $subscriptionPercent }}٪ باقی مانده</div>
            </div>

            <div class="mt-4">
                <x-button>تمدید یا ارتقا</x-button>
            </div>
        </div>

        {{-- پلن‌های موجود --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-bold mb-4">پلن‌های قابل انتخاب</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach ($plans as $plan)
                    <div
                        class="p-4 border border-slate-200 rounded-lg flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div class="mb-3">
                            <div class="text-lg font-semibold">{{ $plan->name }}</div>
                            @if ($plan->price > 0)
                                <div class="text-sm text-slate-700 mt-2">قیمت: {{ number_format($plan->price) }} تومان</div>
                            @else
                                <div class="text-sm text-green-600 mt-2 font-semibold">رایگان</div>
                            @endif

                        </div>
                        <x-button>{{ $plan->button }}</x-button>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- تراکنش‌ها --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h2 class="text-xl font-bold mb-4">آخرین پرداخت‌ها</h2>

            <div class="overflow-x-auto">
                <table class="min-w-full border text-sm text-center">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="border px-3 py-2">شرح تراکنش</th>
                            <th class="border px-3 py-2">مبلغ (تومان)</th>
                            <th class="border px-3 py-2">وضعیت</th>
                            <th class="border px-3 py-2">تاریخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $t)
                            <tr class="hover:bg-gray-50">
                                <td class="border px-3 py-2">{{ $t->title }}</td>
                                <td class="border px-3 py-2 font-semibold">{{ number_format($t->amount) }}</td>
                                <td class="border px-3 py-2">
                                    @if ($t->status === 'پرداخت شده')
                                        <span
                                            class="px-3 py-1 text-green-600 bg-green-50 rounded-full">{{ $t->status }}</span>
                                    @else
                                        <span
                                            class="px-3 py-1 text-red-600 bg-red-50 rounded-full">{{ $t->status }}</span>
                                    @endif
                                </td>
                                <td class="border px-3 py-2">{{ $t->date }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>



    </div>
@endsection
