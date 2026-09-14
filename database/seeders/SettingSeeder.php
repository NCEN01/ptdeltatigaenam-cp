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

            // Bar promo di atas navbar (halaman CMS "Bar Promo Atas").
            // Dibaca components/partials/promo-bar.blade.php; tanpa baris ini
            // bar promonya tidak muncul sama sekali karena topbar_active kosong.
            ['group' => 'topbar', 'key' => 'topbar_active', 'value' => '1', 'type' => 'text'],
            ['group' => 'topbar', 'key' => 'topbar_promo_active', 'value' => '1', 'type' => 'text'],
            ['group' => 'topbar', 'key' => 'topbar_promo_text', 'type' => 'json', 'value' => json_encode([
                'id' => 'Promo Sertifikasi BNSP',
                'en' => 'BNSP Certification Promo',
            ])],
            ['group' => 'topbar', 'key' => 'topbar_promo_price_old', 'value' => 'Rp 15.000.000', 'type' => 'text'],
            ['group' => 'topbar', 'key' => 'topbar_promo_price_new', 'value' => 'Rp 9.000.000', 'type' => 'text'],
            ['group' => 'topbar', 'key' => 'topbar_promo_note', 'type' => 'json', 'value' => json_encode([
                'id' => 'kuota terbatas bulan ini',
                'en' => 'limited quota this month',
            ])],
            ['group' => 'topbar', 'key' => 'topbar_promo_cta', 'type' => 'json', 'value' => json_encode([
                'id' => 'Lihat Program',
                'en' => 'View Program',
            ])],
            ['group' => 'topbar', 'key' => 'topbar_promo_link', 'value' => '/layanan', 'type' => 'text'],
            ['group' => 'topbar', 'key' => 'topbar_agenda_active', 'value' => '1', 'type' => 'text'],
            ['group' => 'topbar', 'key' => 'topbar_agenda_text', 'type' => 'json', 'value' => json_encode([
                'id' => 'Pelatihan & sertifikasi terbaru — lihat jadwal terdekat',
                'en' => 'Latest training & certification — see the upcoming schedule',
            ])],
            ['group' => 'topbar', 'key' => 'topbar_agenda_cta', 'type' => 'json', 'value' => json_encode([
                'id' => 'Lihat Agenda',
                'en' => 'View Agenda',
            ])],
            ['group' => 'topbar', 'key' => 'topbar_agenda_link', 'value' => '', 'type' => 'text'],

            // Angka statistik hero beranda & halaman Tentang (halaman CMS "Label Angka").
            // PageController menyembunyikan satu blok statistik bila nilainya kosong,
            // jadi tanpa baris ini deretan angkanya hilang dari kedua halaman.
            ['group' => 'stats', 'key' => 'hero_stat_1_value', 'value' => '500+', 'type' => 'text'],
            ['group' => 'stats', 'key' => 'hero_stat_1_label', 'type' => 'json', 'value' => json_encode([
                'id' => 'Profesional Terlatih', 'en' => 'Professionals Trained',
            ])],
            ['group' => 'stats', 'key' => 'hero_stat_2_value', 'value' => '98%', 'type' => 'text'],
            ['group' => 'stats', 'key' => 'hero_stat_2_label', 'type' => 'json', 'value' => json_encode([
                'id' => 'Kepuasan Klien', 'en' => 'Client Satisfaction',
            ])],
            ['group' => 'stats', 'key' => 'hero_stat_3_value', 'value' => '10+', 'type' => 'text'],
            ['group' => 'stats', 'key' => 'hero_stat_3_label', 'type' => 'json', 'value' => json_encode([
                'id' => 'Tahun Pengalaman', 'en' => 'Years Experience',
            ])],

            ['group' => 'stats', 'key' => 'about_stat_1_value', 'value' => '10+', 'type' => 'text'],
            ['group' => 'stats', 'key' => 'about_stat_1_label', 'type' => 'json', 'value' => json_encode([
                'id' => 'Tahun pengalaman', 'en' => 'Years of experience',
            ])],
            ['group' => 'stats', 'key' => 'about_stat_2_value', 'value' => '500+', 'type' => 'text'],
            ['group' => 'stats', 'key' => 'about_stat_2_label', 'type' => 'json', 'value' => json_encode([
                'id' => 'Profesional terlatih', 'en' => 'Professionals trained',
            ])],
            ['group' => 'stats', 'key' => 'about_stat_3_value', 'value' => '50+', 'type' => 'text'],
            ['group' => 'stats', 'key' => 'about_stat_3_label', 'type' => 'json', 'value' => json_encode([
                'id' => 'Klien korporat', 'en' => 'Corporate clients',
            ])],
            ['group' => 'stats', 'key' => 'about_stat_4_value', 'value' => '20+', 'type' => 'text'],
            ['group' => 'stats', 'key' => 'about_stat_4_label', 'type' => 'json', 'value' => json_encode([
                'id' => 'Program sertifikasi', 'en' => 'Certification programs',
            ])],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
