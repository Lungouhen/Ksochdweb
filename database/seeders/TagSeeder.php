<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'Community', 'slug' => 'community'],
            ['name' => 'Fundraising', 'slug' => 'fundraising'],
            ['name' => 'Education', 'slug' => 'education'],
            ['name' => 'Health', 'slug' => 'health'],
            ['name' => 'Environment', 'slug' => 'environment'],
            ['name' => 'Youth', 'slug' => 'youth'],
            ['name' => 'Seniors', 'slug' => 'seniors'],
            ['name' => 'Homelessness', 'slug' => 'homelessness'],
            ['name' => 'Food Security', 'slug' => 'food-security'],
            ['name' => 'Mental Health', 'slug' => 'mental-health'],
            ['name' => 'Disaster Relief', 'slug' => 'disaster-relief'],
            ['name' => 'Advocacy', 'slug' => 'advocacy'],
            ['name' => 'Partnership', 'slug' => 'partnership'],
            ['name' => 'Innovation', 'slug' => 'innovation'],
        ];

        foreach ($tags as $tagData) {
            Tag::firstOrCreate(['slug' => $tagData['slug']], $tagData);
        }
    }
}
