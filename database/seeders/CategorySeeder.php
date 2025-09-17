<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'components' => ['Motherboard' , 'CPU' , 'RAM' , 'GPU' , 'Storage' , 'Power Supply'],
            'phones' => ['iPhone', 'Samsung', 'Xiaomi', 'Nokia']
        ];

        foreach ($data as $parentName => $childNames) {
            //create parent category if dont have it
            $parent = Category::firstOrCreate([
                'name' => $parentName,
                'parent_id' => null
            ]);

        foreach ($childNames as $child) {
            Category::firstOrCreate([
                'name' => $child,
                'parent_id' => $parent->id
            ]);
        }    
        }
    }
}
