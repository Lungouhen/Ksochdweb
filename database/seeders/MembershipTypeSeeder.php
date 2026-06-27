<?php

namespace Database\Seeders;

use App\Models\MembershipType;
use Illuminate\Database\Seeder;

class MembershipTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $membershipTypes = [
            [
                'name' => 'Free Member',
                'slug' => 'free-member',
                'description' => 'Basic free membership with limited access',
                'price' => 0,
                'currency' => 'USD',
                'duration_months' => 12,
                'benefits' => ['Access to newsletter', 'Event announcements'],
                'is_active' => true,
            ],
            [
                'name' => 'Silver Member',
                'slug' => 'silver-member',
                'description' => 'Silver tier with enhanced benefits',
                'price' => 50,
                'currency' => 'USD',
                'duration_months' => 12,
                'benefits' => ['All Free benefits', 'Discounted event tickets', 'Member directory listing'],
                'is_active' => true,
            ],
            [
                'name' => 'Gold Member',
                'slug' => 'gold-member',
                'description' => 'Gold tier with premium benefits',
                'price' => 100,
                'currency' => 'USD',
                'duration_months' => 12,
                'benefits' => ['All Silver benefits', 'Free event tickets', 'Voting rights', 'Exclusive content'],
                'is_active' => true,
            ],
            [
                'name' => 'Platinum Member',
                'slug' => 'platinum-member',
                'description' => 'Platinum tier with all inclusive benefits',
                'price' => 250,
                'currency' => 'USD',
                'duration_months' => 12,
                'benefits' => ['All Gold benefits', 'Priority support', 'Board meeting access', 'Annual gift'],
                'is_active' => true,
            ],
            [
                'name' => 'Corporate Member',
                'slug' => 'corporate-member',
                'description' => 'Corporate sponsorship membership',
                'price' => 500,
                'currency' => 'USD',
                'duration_months' => 12,
                'benefits' => ['All Platinum benefits', 'Logo on website', 'Social media mentions', 'Event booth'],
                'is_active' => true,
            ],
        ];

        foreach ($membershipTypes as $typeData) {
            $benefits = $typeData['benefits'];
            unset($typeData['benefits']);
            
            MembershipType::firstOrCreate(
                ['slug' => $typeData['slug']],
                $typeData
            );
        }
    }
}
