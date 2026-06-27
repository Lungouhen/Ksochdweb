<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Organization Settings
            ['key' => 'organization.name', 'value' => 'Hope Foundation', 'type' => 'string', 'group' => 'organization'],
            ['key' => 'organization.tagline', 'value' => 'Making a Difference Together', 'type' => 'string', 'group' => 'organization'],
            ['key' => 'organization.description', 'value' => 'A non-profit organization dedicated to creating positive change in communities worldwide.', 'type' => 'text', 'group' => 'organization'],
            ['key' => 'organization.email', 'value' => 'info@hopefoundation.org', 'type' => 'email', 'group' => 'organization'],
            ['key' => 'organization.phone', 'value' => '+1 (555) 123-4567', 'type' => 'string', 'group' => 'organization'],
            ['key' => 'organization.address', 'value' => '123 Charity Lane, Hope City, HC 12345', 'type' => 'text', 'group' => 'organization'],
            
            // Social Media
            ['key' => 'social.facebook', 'value' => 'https://facebook.com/hopefoundation', 'type' => 'url', 'group' => 'social'],
            ['key' => 'social.twitter', 'value' => 'https://twitter.com/hopefoundation', 'type' => 'url', 'group' => 'social'],
            ['key' => 'social.instagram', 'value' => 'https://instagram.com/hopefoundation', 'type' => 'url', 'group' => 'social'],
            ['key' => 'social.linkedin', 'value' => 'https://linkedin.com/company/hopefoundation', 'type' => 'url', 'group' => 'social'],
            ['key' => 'social.youtube', 'value' => 'https://youtube.com/hopefoundation', 'type' => 'url', 'group' => 'social'],
            
            // Donation Settings
            ['key' => 'donation.currency', 'value' => 'USD', 'type' => 'string', 'group' => 'donation'],
            ['key' => 'donation.minimum_amount', 'value' => '5', 'type' => 'number', 'group' => 'donation'],
            ['key' => 'donation.suggested_amounts', 'value' => '10,25,50,100,250', 'type' => 'string', 'group' => 'donation'],
            ['key' => 'donation.enable_recurring', 'value' => '1', 'type' => 'boolean', 'group' => 'donation'],
            
            // Membership Settings
            ['key' => 'membership.enable_applications', 'value' => '1', 'type' => 'boolean', 'group' => 'membership'],
            ['key' => 'membership.require_approval', 'value' => '1', 'type' => 'boolean', 'group' => 'membership'],
            
            // General Settings
            ['key' => 'general.site_name', 'value' => 'Hope Foundation', 'type' => 'string', 'group' => 'general'],
            ['key' => 'general.admin_email', 'value' => 'admin@hopefoundation.org', 'type' => 'email', 'group' => 'general'],
            ['key' => 'general.timezone', 'value' => 'America/New_York', 'type' => 'string', 'group' => 'general'],
            ['key' => 'general.date_format', 'value' => 'F j, Y', 'type' => 'string', 'group' => 'general'],
        ];

        foreach ($settings as $settingData) {
            Setting::firstOrCreate(['key' => $settingData['key']], $settingData);
        }
    }
}
