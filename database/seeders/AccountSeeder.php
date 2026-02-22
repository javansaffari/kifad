<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    public function run()
    {
        DB::table('accounts')->insert([
            'title' => 'کیف پول نقدی',
            'balance' => 0,
            'type' => 'cash',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
