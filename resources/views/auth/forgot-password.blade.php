<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="mb-4 text-sm text-gray-600">
            رمز عبور خود را فراموش کرده‌اید؟ مشکلی نیست. فقط آدرس ایمیل خود را به ما اطلاع دهید تا یک لینک بازنشانی رمز
            عبور برای شما ایمیل کنیم که به شما امکان می‌دهد رمز عبور جدیدی انتخاب کنید.

        </div>

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="block">
                <x-label for="email" value="آدرس ایمیل" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus autocomplete="username" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-button>
                    ارسال ایمیل بازنشانی رمز عبور
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
