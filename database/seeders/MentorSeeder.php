<?php

namespace Database\Seeders;

use App\Models\Mentor;
use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Satu mentor contoh, dipasang ke semua program.
 *
 * Diminta sebagai contoh agar bagian Mentor di halaman program langsung
 * terlihat bekerja sebelum admin sempat mengisi datanya sendiri. Fotonya
 * memakai potret pendiri yang sudah ada di situs, disalin ke storage
 * supaya ia berperilaku persis seperti unggahan admin — bisa diganti dari
 * panel tanpa perlakuan khusus.
 *
 * Hanya mengisi bila tabel mentor masih kosong. Dijalankan ulang tidak
 * menggandakan apa pun dan tidak menyentuh mentor yang sudah dibuat admin.
 */
class MentorSeeder extends Seeder
{
    public function run(): void
    {
        if (Mentor::exists()) {
            $this->command?->info('Pustaka mentor sudah terisi, dilewati.');

            return;
        }

        // Potret pendiri disalin ke storage/app/public/mentors/. Merujuk
        // langsung ke public/images/ sempat dipertimbangkan dan dilepas:
        // kolom photo dibaca lewat Storage::url() di seluruh tampilan, dan
        // satu baris yang isinya jalur di luar storage akan jadi satu-satunya
        // pengecualian yang harus diingat setiap kali kolom itu dipakai.
        $source = public_path('images/pendiri.webp');
        $target = 'mentors/contoh-pendiri.webp';

        if (is_file($source) && ! Storage::disk('public')->exists($target)) {
            Storage::disk('public')->put($target, file_get_contents($source));
        }

        $mentor = Mentor::create([
            'name' => 'Dani Taupan Ramdani, S.T., M.M., Ph.D (Cand.)',
            'photo' => Storage::disk('public')->exists($target) ? $target : null,
            'role' => [
                'id' => 'Pendiri & CEO',
                'en' => 'Founder & CEO',
            ],
            'bio' => [
                'id' => 'Lebih dari 20 tahun di bidang Human Resources dan Manajemen Strategis, mendampingi BUMN dan perusahaan swasta melakukan restrukturisasi organisasi. Turun langsung pada program yang menyangkut penataan organisasi dan pengembangan kompetensi.',
                'en' => 'Over 20 years in Human Resources and Strategic Management, guiding state-owned and private companies through organisational restructuring. Personally involved in programmes covering organisational design and competency development.',
            ],
            'linkedin_url' => null,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        // Dipasang ke seluruh program yang aktif. syncWithoutDetaching, bukan
        // sync: bila suatu saat seeder ini berjalan di basis data yang
        // programnya sudah punya mentor lain, yang sudah ada tidak terlepas.
        $services = Service::where('is_active', true)->pluck('id');

        foreach ($services as $serviceId) {
            $mentor->services()->syncWithoutDetaching([$serviceId => ['sort_order' => 0]]);
        }

        $this->command?->info("Mentor contoh dibuat dan dipasang ke {$services->count()} program.");
    }
}
