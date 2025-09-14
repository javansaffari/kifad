    @section('pageTitle', 'ایجاد سوال جدید')


    {{-- پیام موفقیت --}}
    @if (session('success'))
        <div class="rounded-xl mb-4 p-3 bg-green-100 text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- پیام خطاها --}}
    @if ($errors->any())
        <div class="rounded-xl mb-4 p-3 bg-red-100 text-red-800">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @csrf

        {{-- انتخاب دانشجو --}}
        <div class="flex flex-col">
            <label class="mb-2 text-sm font-medium text-gray-700">انتخاب دانشجو</label>
            <select name="student_id" class="p-2 border rounded-md border-gray-300 focus:ring focus:ring-blue-200"
                required>

            </select>
        </div>

        {{-- نوع سوال --}}
        <div class="flex flex-col">
            <label class="mb-2 text-sm font-medium text-gray-700">نوع سوال</label>
            <select name="question_type" class="p-2 border rounded-md border-gray-300 focus:ring focus:ring-blue-200"
                required>
                <option value="multiple_choice">چند گزینه‌ای</option>
                <option value="essay">تشریحی</option>
                <option value="true_false">صحیح/غلط</option>
            </select>
        </div>

        {{-- دسته‌بندی --}}
        <div class="flex flex-col">
            <label class="mb-2 text-sm font-medium text-gray-700">دسته‌بندی</label>
            <select name="category_id" class="p-2 border rounded-md border-gray-300 focus:ring focus:ring-blue-200"
                required>
                <option value="1">پیشفرض</option>

            </select>
        </div>

        {{-- روش پاسخ --}}
        <div class="flex flex-col">
            <label class="mb-2 text-sm font-medium text-gray-700">روش پاسخ</label>
            <select name="response_method" class="p-2 border rounded-md border-gray-300 focus:ring focus:ring-blue-200"
                required>
                <option value="text">متنی</option>
                <option value="audio">صوتی</option>
                <option value="video">ویدئویی</option>
            </select>
        </div>

        {{-- متن سوال --}}
        <div class="col-span-full flex flex-col">
            <label class="mb-2 text-sm font-medium text-gray-700">متن سوال</label>
            <textarea name="question_text" rows="5"
                class="p-3 border rounded-md border-gray-300 focus:ring focus:ring-blue-200" required></textarea>
        </div>

        {{-- دکمه ارسال --}}
        <div class="col-span-full flex justify-end">
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md font-medium transition">
                ثبت سوال
            </button>
        </div>
    </form>
    </div>
