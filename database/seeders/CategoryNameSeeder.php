<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoryName;
use App\Models\CategoryType;

class CategoryNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'components' => ['Motherboard' , 'CPU' , 'RAM' , 'GPU' , 'Storage' , 'Power Supply'],
            'phones' => ['iPhone', 'Samsung', 'Xiaomi', 'Nokia'],
        ];
        foreach ($data as $typeName => $names) {
            $typeId = CategoryType::where('name', $typeName)->first()->id;
            foreach ($names as $name) {
                CategoryName::firstOrCreate([
                    'name' => $name,
                    'category_type_id' => $typeId,
                ]);
            }
        }
    }
}
