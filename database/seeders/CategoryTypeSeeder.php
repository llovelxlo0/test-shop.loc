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
        ['name' => 'Motherboard'],
        ['name' => 'CPU'],
        ['name' => 'RAM'],
        ['name' => 'GPU'],
        ['name' => 'Storage'],
        ['name' => 'Power Supply']
        ];
        foreach ($categoryTypes as $type) {
            CategoryType::create($type);
    }
}
        // \App\Models\User::factory(10)->create();
        // \App\Models\CategoryType::factory()->create([
        //     'name' => 'Example CategoryType',
        // ]);
    }
