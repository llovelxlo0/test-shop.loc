<?php

namespace Database\Seeders;

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
        $categoryTypes = [
        ['id' => 1, 'name' => 'Motherboard'],
        ['id' => 2, 'name' => 'CPU'],
        ['id' => 3, 'name' => 'RAM'],
        ['id' => 4, 'name' => 'GPU'],
        ['id' => 5, 'name' => 'Power Supply'],
        ['id' => 6, 'name' => 'Storage']
        ];
        foreach ($categoryTypes as $type) {
            CategoryType::create(['name' => $type['name']]);
    }
}
        // \App\Models\User::factory(10)->create();
        // \App\Models\CategoryType::factory()->create([
        //     'name' => 'Example CategoryType',
        // ]);
    }
