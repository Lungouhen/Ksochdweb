<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'News', 'slug' => 'news', 'description' => 'Organization news and updates'],
            ['name' => 'Events', 'slug' => 'events', 'description' => 'Upcoming and past events'],
            ['name' => 'Programs', 'slug' => 'programs', 'description' => 'Our programs and initiatives'],
            ['name' => 'Success Stories', 'slug' => 'success-stories', 'description' => 'Stories of impact and change'],
            ['name' => 'Volunteer', 'slug' => 'volunteer', 'description' => 'Volunteer opportunities and stories'],
            ['name' => 'Donations', 'slug' => 'donations', 'description' => 'Donation campaigns and appeals'],
            ['name' => 'Press', 'slug' => 'press', 'description' => 'Press releases and media coverage'],
            ['name' => 'Resources', 'slug' => 'resources', 'description' => 'Educational resources and guides'],
        ];

        foreach ($categories as $categoryData) {
            Category::firstOrCreate(['slug' => $categoryData['slug']], $categoryData);
        }
    }
}
