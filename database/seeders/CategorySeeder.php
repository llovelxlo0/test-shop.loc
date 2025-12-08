<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Attribute;
use Dom\Attr;

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
        $motherboard = Category::where('name', 'Motherboard')->first();
        $socket = Attribute::firstOrCreate(['name' => 'Socket']);
        $chipset = Attribute::firstOrCreate(['name' => 'Chipset']);
        $formFactor = Attribute::firstOrCreate(['name' => 'Form Factor']);
        $motherboard->attributes()->sync([$socket->id, $chipset->id, $formFactor->id]);

        $cpu = Category::where('name', 'CPU')->first();
        $cores = Attribute::firstOrCreate(['name' => 'Cores']);
        $threads = Attribute::firstOrCreate(['name' => 'Threads']);
        $tdp = Attribute::firstOrCreate(['name' => 'TDP']);
        $cpu->attributes()->sync([$socket->id, $cores->id, $threads->id, $tdp->id]);

        $ram = Category::where('name', 'RAM')->first();
        $gb = Attribute::firstOrCreate(['name' => 'GB']);
        $type = Attribute::firstOrCreate(['name' => 'Type']);
        $speed = Attribute::firstOrCreate(['name' => 'Speed']);
        $ram->attributes()->sync([$gb->id, $type->id, $speed->id]);

        $gpu = Category::where('name', 'GPU')->first();
        $vram = Attribute::firstOrCreate(['name' => 'VRAM']);
        $gpu->attributes()->sync([$vram->id, $tdp->id]);

        $storage = Category::where('name', 'Storage')->first();
        $capacity = Attribute::firstOrCreate(['name' => 'Capacity']);
        $storageType = Attribute::firstOrCreate(['name' => 'Type']);
        $storage->attributes()->sync([$capacity->id, $storageType->id]);

        $powerSupply = Category::where('name', 'Power Supply')->first();
        $wattage = Attribute::firstOrCreate(['name' => 'Wattage']);
        $powerSupply->attributes()->sync([$wattage->id]);

        $iphone = Category::where('name', 'iPhone')->first();
        $screenSize = Attribute::firstOrCreate(['name' => 'Screen Size']);
        $battery = Attribute::firstOrCreate(['name' => 'Battery']);
        $color = Attribute::firstOrCreate(['name' => 'Color']);
        $iphone->attributes()->sync([$screenSize->id, $battery->id, $color->id]);

        
        $samsung = Category::where('name', 'Samsung')->first();
        $screenSize = Attribute::firstOrCreate(['name' => 'Screen Size']);
        $battery = Attribute::firstOrCreate(['name' => 'Battery']);
        $color = Attribute::firstOrCreate(['name' => 'Color']);
        $samsung->attributes()->sync([$screenSize->id, $battery->id, $color->id]);

        
        $xiaomi = Category::where('name', 'Xiaomi')->first();
        $screenSize = Attribute::firstOrCreate(['name' => 'Screen Size']);
        $battery = Attribute::firstOrCreate(['name' => 'Battery']);
        $color = Attribute::firstOrCreate(['name' => 'Color']);
        $xiaomi->attributes()->sync([$screenSize->id, $battery->id, $color->id]);

        
        $nokia = Category::where('name', 'Nokia')->first();
        $screenSize = Attribute::firstOrCreate(['name' => 'Screen Size']);
        $battery = Attribute::firstOrCreate(['name' => 'Battery']);
        $color = Attribute::firstOrCreate(['name' => 'Color']);
        $nokia->attributes()->sync([$screenSize->id, $battery->id, $color->id]);
    }
}
