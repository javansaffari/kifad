<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonSeeder extends Seeder
{
    public function run()
    {
        $persons = [
            ['name' => 'امین ا'],
            ['name' => 'رامین ق'],
            ['name' => 'رامین ن'],
            ['name' => 'مامان'],
            ['name' => 'متفرقه'],
            ['name' => 'محمد پ'],
        ];

        foreach ($persons as &$person) {
            $person['type'] = null;
            $person['description'] = null;
            $person['created_at'] = now();
            $person['updated_at'] = now();
        }

        DB::table('persons')->insert($persons);
    }
}
