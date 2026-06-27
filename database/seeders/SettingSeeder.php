<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'localization', 'key' => 'default_locale', 'value' => 'id', 'type' => 'text'],
            ['group' => 'localization', 'key' => 'available_locales', 'value' => json_encode(['id', 'en']), 'type' => 'json'],

            ['group' => 'partnership', 'key' => 'partnership_intro', 'type' => 'json', 'value' => json_encode([
                'id' => 'Keterangan program kemitraan PT Delta Tiga Enam (in-house corporate training).',
                'en' => 'Description of PT Delta Tiga Enam corporate partnership program (in-house corporate training).',
            ])],
            ['group' => 'partnership', 'key' => 'partnership_billing_mode', 'value' => 'invoice', 'type' => 'text'],

            // Company profile defaults
            ['group' => 'general', 'key' => 'site_name', 'value' => 'PT Delta Tiga Enam', 'type' => 'text'],
            ['group' => 'general', 'key' => 'site_email', 'value' => 'info@deltatigaenam.com', 'type' => 'text'],
            ['group' => 'general', 'key' => 'site_phone', 'value' => '021-5890 5002', 'type' => 'text'],
            ['group' => 'general', 'key' => 'linkedin_url', 'value' => 'https://linkedin.com/company/deltatigaenam', 'type' => 'text'],
            ['group' => 'general', 'key' => 'company_tagline', 'type' => 'json', 'value' => json_encode([
                'id' => 'Mitra strategis dalam transformasi human capital berkelanjutan.',
                'en' => 'A strategic partner in sustainable human capital transformation.',
            ])],
            ['group' => 'general', 'key' => 'company_about', 'type' => 'json', 'value' => json_encode([
                'id' => 'PT Delta Tiga Enam adalah lembaga pelatihan dan penyelenggara sertifikasi profesi yang membantu perusahaan dan individu meningkatkan kompetensi melalui pelatihan, konsultasi manajemen, pengelolaan human capital, dan layanan headhunter yang terintegrasi.',
                'en' => 'PT Delta Tiga Enam is a training institution and professional certification provider that helps companies and individuals improve competencies through integrated training, management consulting, human capital management, and headhunter services.',
            ])],
            ['group' => 'general', 'key' => 'company_vision', 'type' => 'json', 'value' => json_encode([
                'id' => 'Menjadi mitra strategis dalam transformasi human capital berkelanjutan.',
                'en' => 'To become a strategic partner in sustainable human capital transformation.',
            ])],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
