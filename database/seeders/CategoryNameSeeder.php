<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoryName;


class CategoryNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryNames = [
            ['name' => 'Components'],
            ['name' => 'Phone']
        ];
        foreach ($categoryNames as $name) {
            CategoryName::create($name);
        }
    }
}
