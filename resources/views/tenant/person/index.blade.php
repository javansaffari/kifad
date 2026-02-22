@extends('tenant.layouts.app')

@section('pageTitle', 'مدیریت اشخاص')

@section('content')

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Create Form --}}
        <div class="rounded-xl border border-slate-200 bg-white p-5">
            <h2 class="text-lg font-semibold mb-4">ثبت شخص جدید</h2>
            <form action="{{ route('tenant.person.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1">نام</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    @error('name')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm mb-1">نوع شخص</label>
                    <select name="type" class="w-full border-gray-300 rounded-lg shadow-sm">
                        <option value="">انتخاب کنید</option>
                        @foreach ($personTypes as $type)
                            <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm mb-1">توضیح</label>
                    <textarea name="desc" class="w-full border border-gray-300 rounded-lg px-3 py-2 h-24">{{ old('desc') }}</textarea>
                    @error('desc')
                        <span class="text-red-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <x-button class="w-full text-[18px]">ثبت شخص</x-button>
                </div>
            </form>
        </div>

        {{-- Chart --}}
        <div class="rounded-xl border bg-white p-5">
            <h2 class="text-lg font-semibold mb-4">تقسیم‌بندی اشخاص</h2>
            <canvas id="chart"></canvas>
        </div>
    </div>

    {{-- Table --}}
    <div class="rounded-xl border bg-white p-5 mt-6">
        <h2 class="text-lg font-semibold mb-4">لیست اشخاص</h2>

        <table class="w-full text-center border">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border p-2">نام</th>
                    <th class="border p-2">نوع</th>
                    <th class="border p-2">توضیح</th>
                    <th class="border p-2">آخرین بروزرسانی</th>
                    <th class="border p-2">عملیات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($persons as $person)
                    <tr>
                        <td class="border p-2">{{ $person->name }}</td>
                        <td class="border p-2">{{ $person->type }}</td>
                        <td class="border p-2">{{ $person->description }}</td>
                        <td class="border p-2 ltr">
                            {{ \Morilog\Jalali\Jalalian::fromDateTime($person->updated_at)->format('Y/m/d - H:i') }}
                        </td>
                        <td class="border px-2 py-2 flex justify-center gap-2">
                            <a href="{{ route('tenant.person.edit', $person->id) }}"
                                class="text-blue-600 hover:underline px-2 py-1 border rounded"><svg
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                    stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125">
                                    </path>
                                </svg></a>
                            <form action="{{ route('tenant.person.destroy', $person->id) }}" method="POST"
                                class="inline-block delete-confirm">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline px-2 py-1 border rounded"><svg
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0">
                                        </path>
                                    </svg></button>
                            </form>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="border p-2">
                            هیچ شخصی ثبت نشده است
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{-- Record count --}}
        <div class="mt-4 flex flex-col md:flex-row justify-between text-sm text-gray-600 gap-2 md:gap-0">
            <div>تعداد کل رکوردها: {{ $persons->count() }}</div>
            <div>نمایش 1 تا {{ $persons->count() }} از {{ $persons->count() }}</div>
        </div>
    </div>

@endsection


@section('scripts')
    <script src="/assets/js/jquery.min.js"></script>
    <script src="/assets/js/chart.umd.min.js"></script>

    <script>
        $(document).ready(function() {
            // Confirm before delete
            $('.delete-confirm').on('submit', function(e) {
                if (!confirm('آیا از حذف این شخص اطمینان دارید؟')) e.preventDefault();
            });

            // Chart
            const chartData = @json($chartData);
            const ctx = document.getElementById('chart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(chartData),
                        datasets: [{
                            data: Object.values(chartData),
                            backgroundColor: ['#34d399', '#60a5fa', '#fbbf24', '#f87171', '#a78bfa']
                        }]
                    }
                });
            }
        });
    </script>
@endsection
