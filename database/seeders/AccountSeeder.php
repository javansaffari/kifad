<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    public function run()
    {
        $accounts = [
            ['title' => 'کیف پول', 'balance' => 0, 'type' => 'پول نقد', 'bank' => 'سایر'],
            ['title' => 'مهر ایران', 'balance' => 0, 'type' => 'حساب پس‌انداز', 'bank' => 'بانک مهر ایران'],
            ['title' => 'پاسارگاد', 'balance' => 0, 'type' => 'حساب پس‌انداز', 'bank' => 'بانک پاسارگاد'],
            ['title' => 'پارسیان', 'balance' => 0, 'type' => 'حساب پس‌انداز', 'bank' => 'بانک پارسیان'],
            ['title' => 'ملی', 'balance' => 0, 'type' => 'حساب پس‌انداز', 'bank' => 'بانک ملی'],
            ['title' => 'ملت', 'balance' => 0, 'type' => 'حساب پس‌انداز', 'bank' => 'بانک ملت'],
            ['title' => 'سپه', 'balance' => 0, 'type' => 'حساب پس‌انداز', 'bank' => 'بانک سپه'],
            ['title' => 'کشاورزی', 'balance' => 0, 'type' => 'حساب پس‌انداز', 'bank' => 'بانک کشاورزی'],
            ['title' => 'صادرات', 'balance' => 0, 'type' => 'حساب پس‌انداز', 'bank' => 'بانک صادرات'],
            ['title' => 'سامان', 'balance' => 0, 'type' => 'حساب پس‌انداز', 'bank' => 'بانک سامان'],
            ['title' => 'توسعه تعاون - مرکزی', 'balance' => 0, 'type' => 'حساب پس‌انداز', 'bank' => 'بانک توسعه تعاون'],
            ['title' => 'توسعه تعاون - جاری', 'balance' => 0, 'type' => 'حساب جاری', 'bank' => 'بانک توسعه تعاون'],
            ['title' => 'توسعه تعاون - امام', 'balance' => 0, 'type' => 'حساب پس‌انداز', 'bank' => 'بانک توسعه تعاون'],
        ];

        foreach ($accounts as &$account) {
            $account['created_at'] = now();
            $account['updated_at'] = now();
        }

        DB::table('accounts')->insert($accounts);
    }
}
