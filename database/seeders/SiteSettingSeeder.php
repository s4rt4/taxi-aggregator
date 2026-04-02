<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Company
            ['key' => 'company_name', 'value' => 'RushXO', 'group' => 'company', 'label' => 'Company Name', 'type' => 'text'],
            ['key' => 'company_legal_name', 'value' => 'RushXO Ltd', 'group' => 'company', 'label' => 'Legal Company Name', 'type' => 'text'],
            ['key' => 'company_registration', 'value' => '', 'group' => 'company', 'label' => 'Registration Number', 'type' => 'text'],
            ['key' => 'company_vat', 'value' => '', 'group' => 'company', 'label' => 'VAT Number', 'type' => 'text'],

            // Contact
            ['key' => 'contact_email', 'value' => 'support@rushxo.com', 'group' => 'contact', 'label' => 'Support Email', 'type' => 'email'],
            ['key' => 'contact_phone', 'value' => '+44 1474 554933', 'group' => 'contact', 'label' => 'Phone Number', 'type' => 'tel'],
            ['key' => 'contact_whatsapp', 'value' => 'https://wa.me/447466237870', 'group' => 'contact', 'label' => 'WhatsApp Link', 'type' => 'url'],
            ['key' => 'contact_address', 'value' => '6, Woodland Villas, 12 Muir Drive, Dartford, DA1 5RN', 'group' => 'contact', 'label' => 'Office Address', 'type' => 'textarea'],

            // Website
            ['key' => 'website_url', 'value' => 'https://dashboard.rushxo.com/', 'group' => 'website', 'label' => 'Website URL', 'type' => 'url'],
            ['key' => 'website_tagline', 'value' => 'Compare taxi prices from hundreds of UK operators', 'group' => 'website', 'label' => 'Tagline', 'type' => 'text'],

            // Social
            ['key' => 'social_facebook', 'value' => '', 'group' => 'social', 'label' => 'Facebook URL', 'type' => 'url'],
            ['key' => 'social_twitter', 'value' => '', 'group' => 'social', 'label' => 'X (Twitter) URL', 'type' => 'url'],
            ['key' => 'social_instagram', 'value' => '', 'group' => 'social', 'label' => 'Instagram URL', 'type' => 'url'],
            ['key' => 'social_linkedin', 'value' => '', 'group' => 'social', 'label' => 'LinkedIn URL', 'type' => 'url'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
