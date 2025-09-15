@extends('tenant.layouts.app')

@section('pageTitle', 'راهنما')

@section('styles')
    <link rel="stylesheet" href="/assets/css/styles.css">
@endsection

@section('content')
    <div class="bg-white p-6 rounded-xl shadow">
        <h1 class="text-2xl font-bold mb-6">راهنمای استفاده از کیفاد</h1>

        <div class="space-y-4">
            {{-- Dashboard --}}
            <div>
                <h2 class="text-lg font-semibold">داشبورد</h2>
                <p class="text-gray-700 mt-1">نمایش کلی وضعیت مالی، خلاصه تراکنش‌ها و نمودارها.</p>
            </div>

            {{-- Personal Accounting --}}
            <div>
                <h2 class="text-lg font-semibold">حسابداری شخصی</h2>
                <p class="text-gray-700 mt-1">مدیریت درآمدها، هزینه‌ها و تراکنش‌های شخصی.</p>
            </div>

            {{-- Expenses --}}
            <div>
                <h2 class="text-lg font-semibold">هزینه‌ها</h2>
                <p class="text-gray-700 mt-1">ثبت و پیگیری هزینه‌های مختلف شخصی و خانوادگی.</p>
            </div>

            {{-- Incomes --}}
            <div>
                <h2 class="text-lg font-semibold">درآمدها</h2>
                <p class="text-gray-700 mt-1">ثبت و مدیریت درآمدهای ماهانه و غیر ماهانه.</p>
            </div>

            {{-- Transactions --}}
            <div>
                <h2 class="text-lg font-semibold">تراکنش‌ها</h2>
                <p class="text-gray-700 mt-1">پیگیری تراکنش‌های انجام شده بین حساب‌ها و افراد.</p>
            </div>

            {{-- Checks --}}
            <div>
                <h2 class="text-lg font-semibold">چک‌ها</h2>
                <p class="text-gray-700 mt-1">مدیریت چک‌های صادره و دریافتی، وضعیت و موعد آنها.</p>
            </div>

            {{-- Facilities --}}
            <div>
                <h2 class="text-lg font-semibold">تسهیلات</h2>
                <p class="text-gray-700 mt-1">مدیریت و پیگیری وام‌ها و تسهیلات دریافتی.</p>
            </div>

            {{-- Debts and Receivables --}}
            <div>
                <h2 class="text-lg font-semibold">بدهکاری‌ها و طلب‌ها</h2>
                <p class="text-gray-700 mt-1">ثبت و پیگیری بدهکاری‌ها و طلب‌های شخصی.</p>
            </div>

            {{-- Accounts --}}
            <div>
                <h2 class="text-lg font-semibold">حساب‌ها</h2>
                <p class="text-gray-700 mt-1">مدیریت حساب‌های بانکی، کیف پول و سایر حساب‌ها.</p>
            </div>

            {{-- Persons --}}
            <div>
                <h2 class="text-lg font-semibold">اشخاص</h2>
                <p class="text-gray-700 mt-1">ثبت و مدیریت اطلاعات افراد مرتبط با تراکنش‌ها و حساب‌ها.</p>
            </div>

            {{-- Investments --}}
            <div>
                <h2 class="text-lg font-semibold">سرمایه‌گذاری‌ها</h2>
                <p class="text-gray-700 mt-1">مدیریت سرمایه‌گذاری‌ها در بورس، سپرده‌ها و سایر منابع.</p>
            </div>

            {{-- Reports --}}
            <div>
                <h2 class="text-lg font-semibold">گزارش‌ها</h2>
                <p class="text-gray-700 mt-1">تولید گزارش‌های مالی، نمودارها و تحلیل‌های آماری.</p>
            </div>

            {{-- Services --}}
            <div>
                <h2 class="text-lg font-semibold">سرویس‌ها</h2>
                <p class="text-gray-700 mt-1">مدیریت سرویس‌های اشتراکی و خدمات مرتبط با حساب‌ها.</p>
            </div>

            {{-- Billing and Finance --}}
            <div>
                <h2 class="text-lg font-semibold">صورتحساب و مالی</h2>
                <p class="text-gray-700 mt-1">پیگیری فاکتورها، پرداخت‌ها و وضعیت مالی.</p>
            </div>

            {{-- Support --}}
            <div>
                <h2 class="text-lg font-semibold">پشتیبانی</h2>
                <p class="text-gray-700 mt-1">دریافت راهنمایی و حل مشکلات کاربری.</p>
            </div>

            {{-- Help --}}
            <div>
                <h2 class="text-lg font-semibold">راهنما</h2>
                <p class="text-gray-700 mt-1">دسترسی به مستندات و نکات آموزشی استفاده از اپلیکیشن.</p>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // You can add interactive scripts here if needed
    </script>
@endsection
