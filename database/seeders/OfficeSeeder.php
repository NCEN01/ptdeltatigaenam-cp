<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Seeder;

/**
 * Memindahkan daftar kantor dari config/company.php ke basis data, apa adanya.
 *
 * Alamat, nomor, dan kata kunci petanya tidak diubah sedikit pun: keduanya
 * sudah tampil di situs yang berjalan, dan penyemai ini memindahkan tempat
 * penyimpanannya, bukan isinya.
 *
 * Idempoten lewat pencocokan alamat. Dijalankan dua kali tidak menghasilkan
 * empat baris — yang penting karena Office::MAX_RECORDS hanya tiga, dan
 * penyemai yang menggandakan isinya akan membuat tabelnya penuh oleh
 * duplikat lalu menolak kantor sungguhan yang ketiga.
 */
class OfficeSeeder extends Seeder
{
    public function run(): void
    {
        $kantor = config('company.offices', []);

        foreach ($kantor as $i => $k) {
            $alamat = $k['address'] ?? '';

            if ($alamat === '' || Office::where('address', $alamat)->exists()) {
                continue;
            }

            if (! Office::hasRoomForMore()) {
                break;
            }

            Office::create([
                'name' => is_array($k['name'] ?? null) ? $k['name'] : ['id' => (string) ($k['name'] ?? '')],
                'address' => $alamat,
                'phone' => $k['phone'] ?? null,
                'tel' => $k['tel'] ?? null,
                'maps' => $k['maps'] ?? null,
                'sort_order' => $i + 1,
                'is_active' => true,
            ]);
        }
    }
}
