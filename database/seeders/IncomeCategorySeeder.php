<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant\Category;

class IncomeCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'فریلنسینگ' => [
                'داخلی',
                'خارجی'
            ],
            'فروش' => [
                'ژاکت',
                'سایر فروش‌ها'
            ],
            'وام و تسهیلات بانکی' => [],
            'حقوق' => [],
            'سود سرمایه‌گذاری' => [],
            'چک‌های دریافتی' => [],
            'قرض' => [],
            'هدیه' => [],
            'دسته بندی نشده' => [],
        ];

        foreach ($categories as $parent => $children) {

            $parentCategory = Category::create([
                'name' => $parent,
                'type' => 'income',
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($children as $child) {
                Category::create([
                    'name' => $child,
                    'type' => 'income',
                    'parent_id' => $parentCategory->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
