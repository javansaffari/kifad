@extends('tenant.layouts.app')

@section('pageTitle', 'داشبورد')

@section('content')
    @php
        use Carbon\Carbon;
        use Morilog\Jalali\Jalalian;

        $selectedMonth = request('month', Carbon::now()->format('Y-m'));
        $today = Carbon::now();
        $currentMonth = Carbon::parse($selectedMonth . '-01');
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        $daysInMonth = $currentMonth->daysInMonth;
        $weekDays = ['ش', 'ی', 'د', 'س', 'چ', 'پ', 'ج'];
        $firstWeekday = ($startOfMonth->dayOfWeekIso + 1) % 7;

        $currentMonthIncome = 3500000;
        $currentMonthExpense = 2150000;
        $walletBalance = 5400000;
        $incomeExpenseDiff = $currentMonthIncome - $currentMonthExpense;

        $checksReceived = 3;
        $checksIssued = 2;
        $debts = 1200000;
        $receivables = 800000;

        $calendarEvents = [
            '1404/06/18' => [
                ['title' => 'پرداخت قبض برق', 'amount' => 350000, 'type' => 'قبض', 'category' => 'expense'],
                ['title' => 'دریافت حقوق', 'amount' => 10000000, 'type' => 'حقوق', 'category' => 'income'],
            ],
            '1404/06/20' => [
                ['title' => 'پرداخت وام بانک ملت', 'amount' => 2000000, 'type' => 'وام', 'category' => 'expense'],
            ],
            '1404/06/25' => [
                ['title' => 'چک دریافتی از علی', 'amount' => 500000, 'type' => 'چک', 'category' => 'income'],
            ],
            '1404/06/28' => [
                ['title' => 'چک صادر شده سارا', 'amount' => 300000, 'type' => 'چک', 'category' => 'expense'],
            ],
        ];

        $summary = [
            'چک' => ['income' => 0, 'expense' => 0],
            'وام' => ['income' => 0, 'expense' => 0],
            'قبض' => ['income' => 0, 'expense' => 0],
            'حقوق' => ['income' => 0, 'expense' => 0],
        ];
        $totalIncome = 0;
        $totalExpense = 0;

        foreach ($calendarEvents as $dayEvents) {
            foreach ($dayEvents as $event) {
                $summary[$event['type']][$event['category']] += $event['amount'];
                if ($event['category'] === 'income') {
                    $totalIncome += $event['amount'];
                } else {
                    $totalExpense += $event['amount'];
                }
            }
        }
    @endphp


    <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-4">
        <div class="p-4 bg-white border rounded-xl flex flex-col items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                class="size-6 text-green-500 mb-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>

            <div class="text-sm text-gray-500">درآمد ماه جاری</div>
            <div class="text-lg font-bold">{{ number_format($currentMonthIncome) }} ریال</div>
        </div>

        <div class="p-4 bg-white border rounded-xl flex flex-col items-center">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6 text-red-500 mb-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m9 12.75 3 3m0 0 3-3m-3 3v-7.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>


            <div class="text-sm text-gray-500">هزینه ماه جاری</div>
            <div class="text-lg font-bold">{{ number_format($currentMonthExpense) }} ریال</div>
        </div>

        <div class="p-4 bg-white border rounded-xl flex flex-col items-center">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6 text-blue-500 mb-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
            </svg>

            <div class="text-sm text-gray-500">موجودی کل</div>
            <div class="text-lg font-bold">{{ number_format($walletBalance) }} ریال</div>
        </div>

        <div class="p-4 bg-white border rounded-xl flex flex-col items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6 text-yellow-500 mb-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
            </svg>

            <div class="text-sm text-gray-500">اختلاف درآمد و هزینه</div>
            <div class="text-lg font-bold">{{ number_format($incomeExpenseDiff) }} ریال</div>
        </div>

        <div class="p-4 bg-white border rounded-xl flex flex-col items-center">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6 text-purple-500 mb-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" />
            </svg>


            <div class="text-sm text-gray-500">چک‌های دریافتی</div>
            <div class="text-lg font-bold">{{ $checksReceived }}</div>
        </div>

        <div class="p-4 bg-white border rounded-xl flex flex-col items-center">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6 text-pink-500 mb-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859m-19.5.338V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H6.911a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661Z" />
            </svg>

            <div class="text-sm text-gray-500">چک‌های صادر شده</div>
            <div class="text-lg font-bold">{{ $checksIssued }}</div>
        </div>
    </div>


    <!-- Calendar -->
    <div class="bg-white p-6 rounded-xl shadow">
        <!-- Month Navigation -->
        <div class="flex justify-between items-center mb-4">
            <a href="?month={{ $currentMonth->copy()->subMonth()->format('Y-m') }}"
                class="text-blue-600 hover:text-blue-800">
                &rarr; ماه قبل </a>
            <h2 class="font-bold text-xl">{{ Jalalian::fromCarbon($startOfMonth)->format('%B %Y') }}</h2>
            <a href="?month={{ $currentMonth->copy()->addMonth()->format('Y-m') }}"
                class="text-blue-600 hover:text-blue-800">ماه بعد &larr;</a>
        </div>

        <!-- Weekdays -->
        <div class="grid grid-cols-7 text-center font-semibold mb-2 text-gray-700 border-b pb-2">
            @foreach ($weekDays as $wd)
                <div>{{ $wd }}</div>
            @endforeach
        </div>

        <!-- Days -->
        <div class="grid grid-cols-7 gap-2 text-center">
            @for ($i = 0; $i < $firstWeekday; $i++)
                <div class="w-full h-28"></div>
            @endfor

            @for ($day = 1; $day <= $daysInMonth; $day++)
                @php
                    $dateCarbon = $startOfMonth->copy()->addDays($day - 1);
                    $jalaliDate = Jalalian::fromCarbon($dateCarbon);
                    $dateKey = $jalaliDate->format('Y/m/d');
                    $isToday = $today->isSameDay($dateCarbon);
                    $isFriday = $jalaliDate->getDayOfWeek() == 6;
                    $dayEvents = $calendarEvents[$dateKey] ?? [];
                    $dayIncome = array_sum(
                        array_map(fn($e) => $e['category'] == 'income' ? $e['amount'] : 0, $dayEvents),
                    );
                    $dayExpense = array_sum(
                        array_map(fn($e) => $e['category'] == 'expense' ? $e['amount'] : 0, $dayEvents),
                    );
                @endphp
                <div
                    class="w-full h-28 border rounded-xl p-2 flex flex-col justify-between cursor-pointer hover:shadow-lg transition bg-gray-50">
                    <div class="flex justify-between items-center mb-1">
                        <div
                            class="{{ $isToday ? 'bg-blue-200 rounded-full w-6 h-6 flex items-center justify-center font-bold' : '' }} {{ $isFriday ? 'text-red-600' : '' }}">
                            {{ $day }}
                        </div>
                        @if (count($dayEvents))
                            <div class="text-xs flex flex-col items-end">
                                <div class="text-green-600">{{ number_format($dayIncome) }}+</div>
                                <div class="text-red-600">{{ number_format($dayExpense) }}-</div>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 overflow-y-auto text-xs space-y-1">
                        @foreach ($dayEvents as $event)
                            <div class="truncate px-1 py-0.5 rounded {{ $event['category'] == 'income' ? 'bg-green-100' : 'bg-red-100' }}"
                                title="{{ $event['title'] }} - {{ number_format($event['amount']) }} ریال">
                                {{ Str::limit($event['title'], 15) }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endfor
        </div>
    </div>

@endsection
