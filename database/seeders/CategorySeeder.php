<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        // دسته‌بندی‌های اصلی
        $mainCategories = [
            'expense' => ['خوراک', 'حمل و نقل', 'تفریح و سرگرمی', 'بهداشت و سلامت', 'اجاره'],
            'income' => ['حقوق', 'پاداش', 'سرمایه گذاری', 'هدیه'],
        ];

        foreach ($mainCategories as $type => $names) {
            foreach ($names as $name) {
                $mainId = DB::table('categories')->insertGetId([
                    'name' => $name,
                    'type' => $type,
                    'parent_id' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // مثال اضافه کردن دسته‌بندی فرعی برای برخی دسته‌ها
                if ($type === 'expense' && $name === 'خوراک') {
                    DB::table('categories')->insert([
                        ['name' => 'میوه و سبزیجات', 'type' => 'expense', 'parent_id' => $mainId, 'created_at' => now(), 'updated_at' => now()],
                        ['name' => 'نان و غلات', 'type' => 'expense', 'parent_id' => $mainId, 'created_at' => now(), 'updated_at' => now()],
                        ['name' => 'گوشت و پروتئین', 'type' => 'expense', 'parent_id' => $mainId, 'created_at' => now(), 'updated_at' => now()],
                    ]);
                }

                if ($type === 'income' && $name === 'سرمایه گذاری') {
                    DB::table('categories')->insert([
                        ['name' => 'سهام', 'type' => 'income', 'parent_id' => $mainId, 'created_at' => now(), 'updated_at' => now()],
                        ['name' => 'رمزارز', 'type' => 'income', 'parent_id' => $mainId, 'created_at' => now(), 'updated_at' => now()],
                    ]);
                }
            }
        }
    }
}
