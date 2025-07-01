<?php

namespace Database\Seeders;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * */
    public function run(): void
    {
        $categories = ['Technology', 'Business', 'Sports', 'Health', 'Science', 'Politics', 'Entertainment', 'General',];
        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}