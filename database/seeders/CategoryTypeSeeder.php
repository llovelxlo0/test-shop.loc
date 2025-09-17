<?php

namespace Database\Seeders;

use App\Models\CategoryName;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoryType;

class CategoryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['components', 'phones'];
        foreach ($types as $type) {
            CategoryType::firstOrCreate(['name'=> $type]);
        }
    }
}
        // \App\Models\User::factory(10)->create();
        // \App\Models\CategoryType::factory()->create([
        //     'name' => 'Example CategoryType',
        // ]);
