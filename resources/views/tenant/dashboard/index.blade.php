@extends('tenant.layouts.app')

@section('pageTitle', 'داشبورد')

@section('content')

    @php
        use Morilog\Jalali\Jalalian;
        use Illuminate\Support\Str;
    @endphp

    <div class="space-y-6">

        {{-- ================= STATS CARDS ================= --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-4">
            <div class="bg-white p-4 rounded-xl shadow flex flex-col items-center">
                <div class="text-sm text-gray-500">درآمد ماه جاری</div>
                <div class="text-green-600 font-bold text-base mt-3">{{ number_format($currentMonthIncome) }} ریال</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow flex flex-col items-center">
                <div class="text-sm text-gray-500">هزینه ماه جاری</div>
                <div class="text-red-600 font-bold text-base mt-3">{{ number_format($currentMonthExpense) }} ریال</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow flex flex-col items-center">
                <div class="text-sm text-gray-500">موجودی کل</div>
                <div class="text-blue-600 font-bold text-base mt-3">{{ number_format($walletBalance) }} ریال</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow flex flex-col items-center">
                <div class="text-sm text-gray-500">اختلاف درآمد و هزینه</div>
                <div class="font-bold text-base mt-3 {{ $incomeExpenseDiff >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ number_format($incomeExpenseDiff) }} ریال
                </div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow flex flex-col items-center">
                <div class="text-sm text-gray-500">چک‌های دریافتی</div>
                <div class="text-purple-600 font-bold text-base mt-3">{{ $checksReceived }}</div>
            </div>
            <div class="bg-white p-4 rounded-xl shadow flex flex-col items-center">
                <div class="text-sm text-gray-500">چک‌های صادر شده</div>
                <div class="text-pink-600 font-bold text-base mt-3">{{ $checksIssued }}</div>
            </div>
        </div>

        {{-- ================= CALENDAR ================= --}}
        <div class="bg-white p-6 rounded-xl shadow">

            {{-- Month navigation --}}
            <div class="flex justify-between items-center mb-4">
                <a href="?year={{ $prevMonth->year }}&month={{ $prevMonth->month }}"
                    class="text-blue-600 hover:text-blue-800">ماه قبل →</a>
                <h2 class="font-bold text-xl">{{ Jalalian::fromCarbon($currentMonth)->format('%B %Y') }}</h2>
                <a href="?year={{ $nextMonth->year }}&month={{ $nextMonth->month }}"
                    class="text-blue-600 hover:text-blue-800">← ماه بعد</a>
            </div>

            {{-- Weekdays --}}
            <div class="grid grid-cols-7 text-center text-gray-700 font-semibold border-b pb-2 mb-2">
                @foreach ($weekDays as $day)
                    <div>{{ $day }}</div>
                @endforeach
            </div>

            {{-- Calendar grid --}}
            <div class="grid grid-cols-7 gap-2 text-center">
                {{-- Empty cells before first day --}}
                @for ($i = 0; $i < $firstWeekday; $i++)
                    <div class="h-28"></div>
                @endfor

                {{-- Month days --}}
                @for ($day = 1; $day <= $daysInMonth; $day++)
                    @php
                        $dateCarbon = $startOfMonth->copy()->addDays($day - 1);
                        $dateKey = $dateCarbon->format('Y-m-d'); // key in events array
                        $jalaliDate = Jalalian::fromCarbon($dateCarbon);
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
                        class="h-28 border rounded-xl p-2 flex flex-col justify-between bg-gray-50 hover:shadow-md transition">
                        <div class="flex justify-between items-center">
                            <div
                                class="text-sm font-bold {{ $isFriday ? 'text-red-600' : '' }} {{ $isToday ? 'bg-blue-200 w-6 h-6 rounded-full flex items-center justify-center' : '' }}">
                                {{ $day }}
                            </div>

                            @if ($dayIncome > 0 || $dayExpense > 0)
                                <div class="text-xs flex flex-col items-end">
                                    @if ($dayIncome > 0)
                                        <div class="text-green-600">+{{ number_format($dayIncome) }}</div>
                                    @endif
                                    @if ($dayExpense > 0)
                                        <div class="text-red-600">-{{ number_format($dayExpense) }}</div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Events list --}}
                        <div class="flex-1 overflow-y-auto text-xs space-y-1">
                            @foreach ($dayEvents as $event)
                                <div class="rounded px-1 py-0.5 truncate {{ $event['category'] == 'income' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}"
                                    title="{{ $event['title'] }} - {{ number_format($event['amount']) }} ریال">
                                    {{ Str::limit($event['title'], 22) }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endfor
            </div>

            {{-- Month Selector --}}
            <form method="get" class="flex gap-2 mb-4 items-center mt-5">
                <select name="year" class="border-gray-300 rounded-lg shadow-sm">
                    @for ($y = now()->year - 5; $y <= now()->year + 5; $y++)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}
                        </option>
                    @endfor
                </select>
                <select name="month" class="border-gray-300 rounded-lg shadow-sm">
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ $m }}
                        </option>
                    @endfor
                </select>
                <x-button class="text-[18px]">نمایش</x-button>
            </form>

        </div>
    </div>

@endsection
