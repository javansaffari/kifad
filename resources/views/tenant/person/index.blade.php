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
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2">
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
                        <td class="border p-2">{{ $person->updated_at }}</td>
                        <td class="border p-2 flex justify-center gap-2">

                            <a href="{{ route('tenant.person.edit', $person->id) }}" class="text-blue-600">ویرایش</a>

                            <form action="{{ route('tenant.person.destroy', $person->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600">حذف</button>
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
    </div>

@endsection


@section('scripts')
    <script src="/assets/js/chart.umd.min.js"></script>
    <script>
        const chartData = @json($chartData);

        new Chart(document.getElementById('chart'), {
            type: 'pie',
            data: {
                labels: Object.keys(chartData),
                datasets: [{
                    data: Object.values(chartData)
                }]
            }
        });
    </script>
@endsection
