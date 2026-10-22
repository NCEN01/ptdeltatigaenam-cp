<?php

namespace App\Services;

use App\Models\CertificateHolder;
use App\Rules\PersonName;
use Carbon\CarbonImmutable;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use Throwable;

/**
 * Templat, ekspor, dan impor Excel untuk daftar pemegang sertifikat.
 *
 * Satu bentuk berkas untuk ketiganya: templat kosong yang diunduh admin, data
 * yang sudah ada saat diunduh, dan berkas yang diunggah kembali. Dengan begitu
 * admin bisa mengunduh data, menyunting atau menambah baris di Excel, lalu
 * mengunggahnya lagi — tanpa ada kolom yang perlu dipindah tempatnya.
 *
 * Kolomnya dicocokkan lewat JUDUL di baris pertama, bukan lewat urutannya.
 * Admin yang menukar dua kolom atau menyisipkan kolom catatan sendiri tidak
 * membuat nama peserta masuk ke kolom nomor sertifikat.
 *
 * Impornya semua-atau-tidak-sama-sekali. Seluruh baris diperiksa lebih dulu;
 * satu baris saja yang salah membuat tidak ada yang tersimpan, dan admin
 * mendapat daftar galat per nomor baris Excel. Impor setengah jadi — 300 dari
 * 500 baris masuk lalu berhenti — meninggalkan admin dengan pertanyaan baris
 * mana yang sudah masuk, dan unggahan berikutnya justru menggandakan sisanya.
 */
class CertificateSpreadsheet
{
    /** Judul kolom => kolom basis data. Urutan di sini urutan di templat. */
    public const KOLOM = [
        'No. UJK' => 'ujk_number',
        'Nama Peserta' => 'participant_name',
        'Nama Perusahaan' => 'company_name',
        'No. Sertifikat' => 'certificate_number',
        'Kualifikasi' => 'qualification',
        'Tanggal Terbit' => 'issued_at',
        'Tanggal Berakhir' => 'expires_at',
    ];

    /** Batas satu unggahan, supaya satu berkas tidak menahan peladen terlalu lama. */
    public const MAKS_BARIS = 2000;

    /** Galat yang ditampilkan ke admin; sisanya diringkas jadi "dan N lainnya". */
    private const MAKS_GALAT_TAMPIL = 12;

    /* ── Menulis ───────────────────────────────────────────────────────── */

    /** Templat kosong dengan satu baris contoh dan lembar petunjuk. */
    public function template(): string
    {
        return $this->tulis([[
            'UJK-2026-001', 'Budi Santoso', 'PT Contoh Sejahtera', 'DTE/BNSP/2026/0001',
            'Asesor Kompetensi', '13/10/2026', '13/10/2029',
        ]], denganPetunjuk: true);
    }

    /** Seluruh data yang ada, dalam bentuk templat yang sama. */
    public function export(): string
    {
        $baris = CertificateHolder::query()
            ->orderBy('sort_order')->orderBy('id')
            ->get()
            ->map(fn (CertificateHolder $c) => [
                (string) $c->ujk_number,
                (string) $c->participant_name,
                (string) $c->company_name,
                (string) $c->certificate_number,
                (string) $c->qualification,
                $c->issued_at?->format('d/m/Y') ?? '',
                $c->expires_at?->format('d/m/Y') ?? '',
            ])->all();

        return $this->tulis($baris, denganPetunjuk: false);
    }

    /** @param array<int, array<int, string>> $baris */
    private function tulis(array $baris, bool $denganPetunjuk): string
    {
        $path = storage_path('app/tmp/sertifikat-'.Str::random(12).'.xlsx');
        @mkdir(dirname($path), 0775, true);

        $writer = new Writer;
        $writer->openToFile($path);

        $sheet = $writer->getCurrentSheet();
        $sheet->setName('Sertifikat');
        $sheet->setColumnWidth(16, 1);
        $sheet->setColumnWidth(30, 2, 3);
        $sheet->setColumnWidth(24, 4);
        $sheet->setColumnWidth(30, 5);
        $sheet->setColumnWidth(17, 6, 7);

        $judul = (new Style)->setFontBold()->setFontColor(Color::WHITE)->setBackgroundColor('0E2237');
        $writer->addRow(Row::fromValues(array_keys(self::KOLOM), $judul));

        foreach ($baris as $b) {
            $writer->addRow(Row::fromValues($b));
        }

        if ($denganPetunjuk) {
            $petunjuk = $writer->addNewSheetAndMakeItCurrent();
            $petunjuk->setName('Petunjuk');
            $petunjuk->setColumnWidth(100, 1);
            $tebal = (new Style)->setFontBold();

            foreach ([
                ['Cara mengisi templat sertifikat', true],
                ['', false],
                ['1. Isi data di lembar "Sertifikat". Baris contoh boleh ditimpa atau dihapus.', false],
                ['2. Jangan mengubah judul kolom di baris pertama. Urutan kolom boleh berubah.', false],
                ['3. Nama Peserta wajib diisi, dan hanya berisi huruf (tanpa angka).', false],
                ['4. No. Sertifikat harus unik. Mengunggah nomor yang sudah ada akan MEMPERBARUI datanya, bukan menggandakannya.', false],
                ['5. Tanggal ditulis HH/BB/TTTT, misalnya 13/10/2026. Sel bertipe tanggal di Excel juga diterima.', false],
                ['6. Tanggal Berakhir menentukan status Berlaku / Segera berakhir / Kedaluwarsa di situs. Kosongkan bila tidak berakhir.', false],
                ['7. Bila ada satu baris yang salah, tidak ada data yang disimpan. Perbaiki baris yang disebut, lalu unggah lagi.', false],
                ['8. Paling banyak '.number_format(self::MAKS_BARIS, 0, ',', '.').' baris per unggahan.', false],
            ] as [$teks, $isTebal]) {
                $writer->addRow(Row::fromValues([$teks], $isTebal ? $tebal : null));
            }

            // Lembar Sertifikat yang terbuka saat berkasnya dibuka.
            $writer->setCurrentSheet($sheet);
        }

        $writer->close();

        return $path;
    }

    /* ── Membaca ───────────────────────────────────────────────────────── */

    /**
     * @return array{ok: bool, dibuat: int, diperbarui: int, galat: array<int, string>}
     */
    public function import(string $path): array
    {
        try {
            $baris = $this->baca($path);
        } catch (Throwable $e) {
            return $this->gagal(['Berkas tidak bisa dibaca sebagai Excel (.xlsx). Pastikan berkasnya disimpan dari templat yang diunduh di halaman ini.']);
        }

        if ($baris === null) {
            return $this->gagal(['Baris pertama harus berisi judul kolom templat. Kolom "Nama Peserta" tidak ditemukan.']);
        }

        if ($baris === []) {
            return $this->gagal(['Berkas tidak berisi data. Isi minimal satu baris di bawah judul kolom.']);
        }

        if (count($baris) > self::MAKS_BARIS) {
            return $this->gagal(['Berkas berisi '.count($baris).' baris. Paling banyak '.self::MAKS_BARIS.' baris per unggahan; pecah berkasnya menjadi beberapa bagian.']);
        }

        [$data, $galat] = $this->periksa($baris);

        if ($galat) {
            return $this->gagal($galat);
        }

        // Batas tabel (CertificateHolder::MAX_RECORDS) dihitung dari baris BARU saja.
        $baru = collect($data)->filter(fn ($d) => $this->cariYangSama($d) === null)->count();
        if (CertificateHolder::recordCount() + $baru > CertificateHolder::maxRecords()) {
            return $this->gagal(['Impor ini akan menambah '.$baru.' sertifikat baru, melewati batas '.CertificateHolder::maxRecords().' sertifikat. Sisa slot: '.CertificateHolder::remainingSlots().'.']);
        }

        $dibuat = 0;
        $diperbarui = 0;

        DB::transaction(function () use ($data, &$dibuat, &$diperbarui) {
            foreach ($data as $d) {
                $ada = $this->cariYangSama($d, kunci: true);

                if ($ada) {
                    $ada->fill($d)->save();
                    $diperbarui++;
                } else {
                    CertificateHolder::create($d + ['is_active' => true]);
                    $dibuat++;
                }
            }
        });

        return ['ok' => true, 'dibuat' => $dibuat, 'diperbarui' => $diperbarui, 'galat' => []];
    }

    /**
     * Baris berisi data, sudah dipetakan ke nama kolom basis data, dengan nomor
     * baris Excel sebagai kunci. null bila judul kolomnya tidak dikenali.
     *
     * @return array<int, array<string, mixed>>|null
     */
    private function baca(string $path): ?array
    {
        $reader = new Reader;
        $reader->open($path);

        $peta = null;
        $hasil = [];

        try {
            foreach ($reader->getSheetIterator() as $sheet) {
                foreach ($sheet->getRowIterator() as $nomor => $row) {
                    $sel = $row->toArray();

                    if ($peta === null) {
                        $peta = $this->petakanJudul($sel);
                        if (! in_array('participant_name', $peta, true)) {
                            return null;
                        }

                        continue;
                    }

                    $isi = [];
                    foreach ($peta as $i => $kolom) {
                        $isi[$kolom] = $sel[$i] ?? null;
                    }

                    // Baris yang seluruhnya kosong dilewati, bukan dianggap galat.
                    if (collect($isi)->every(fn ($v) => $v === null || trim((string) ($v instanceof DateTimeInterface ? 'x' : $v)) === '')) {
                        continue;
                    }

                    $hasil[$nomor] = $isi;
                }

                break; // hanya lembar pertama
            }
        } finally {
            $reader->close();
        }

        return $peta === null ? null : $hasil;
    }

    /** Indeks kolom => kolom basis data, dicocokkan tanpa peduli huruf besar dan spasi. */
    private function petakanJudul(array $sel): array
    {
        $normal = fn ($s) => preg_replace('/[^a-z]/', '', mb_strtolower((string) $s));
        $kenal = [];
        foreach (self::KOLOM as $judul => $kolom) {
            $kenal[$normal($judul)] = $kolom;
        }
        // Nama lain yang wajar diketik admin.
        $kenal['peserta'] = 'participant_name';
        $kenal['nama'] = 'participant_name';
        $kenal['perusahaan'] = 'company_name';
        $kenal['nosertifikat'] ??= 'certificate_number';
        $kenal['nomorsertifikat'] = 'certificate_number';
        $kenal['noujk'] ??= 'ujk_number';

        $peta = [];
        foreach ($sel as $i => $judul) {
            $k = $normal($judul);
            if (isset($kenal[$k]) && ! in_array($kenal[$k], $peta, true)) {
                $peta[$i] = $kenal[$k];
            }
        }

        return $peta;
    }

    /**
     * @param  array<int, array<string, mixed>>  $baris
     * @return array{0: array<int, array<string, mixed>>, 1: array<int, string>}
     */
    private function periksa(array $baris): array
    {
        $data = [];
        $galat = [];
        $nomorTerpakai = [];

        foreach ($baris as $nomor => $isi) {
            $d = [];
            foreach (['ujk_number', 'participant_name', 'company_name', 'certificate_number', 'qualification'] as $k) {
                $v = $isi[$k] ?? null;
                $v = $v instanceof DateTimeInterface ? $v->format('d/m/Y') : trim(preg_replace('/\s+/u', ' ', (string) $v));
                $d[$k] = $v === '' ? null : $v;
            }

            $salah = [];
            foreach (['issued_at' => 'Tanggal Terbit', 'expires_at' => 'Tanggal Berakhir'] as $k => $label) {
                [$tgl, $ok] = $this->tanggal($isi[$k] ?? null);
                $d[$k] = $tgl;
                if (! $ok) {
                    $salah[] = "{$label} tidak dikenali (pakai HH/BB/TTTT, mis. 13/10/2026)";
                }
            }

            $v = Validator::make($d, [
                'participant_name' => ['required', 'string', 'max:200', new PersonName],
                'ujk_number' => ['nullable', 'string', 'max:50'],
                'company_name' => ['nullable', 'string', 'max:200'],
                'certificate_number' => ['nullable', 'string', 'max:100'],
                'qualification' => ['nullable', 'string', 'max:200'],
            ], [], [
                'participant_name' => 'Nama Peserta', 'ujk_number' => 'No. UJK', 'company_name' => 'Nama Perusahaan',
                'certificate_number' => 'No. Sertifikat', 'qualification' => 'Kualifikasi',
            ]);
            foreach ($v->errors()->all() as $pesan) {
                $salah[] = rtrim(str_replace('Kolom ', '', $pesan), '.');
            }

            if ($d['issued_at'] && $d['expires_at'] && $d['expires_at'] < $d['issued_at']) {
                $salah[] = 'Tanggal Berakhir lebih awal daripada Tanggal Terbit';
            }

            if ($d['certificate_number']) {
                $kunci = mb_strtolower($d['certificate_number']);
                if (isset($nomorTerpakai[$kunci])) {
                    $salah[] = "No. Sertifikat {$d['certificate_number']} sudah dipakai di baris {$nomorTerpakai[$kunci]}";
                } else {
                    $nomorTerpakai[$kunci] = $nomor;
                }
            }

            if ($salah) {
                $galat[] = "Baris {$nomor}: ".implode('; ', $salah).'.';
            } else {
                $data[$nomor] = $d;
            }
        }

        if (count($galat) > self::MAKS_GALAT_TAMPIL) {
            $sisa = count($galat) - self::MAKS_GALAT_TAMPIL;
            $galat = array_slice($galat, 0, self::MAKS_GALAT_TAMPIL);
            $galat[] = "…dan {$sisa} baris lain dengan galat.";
        }

        return [$data, $galat];
    }

    /**
     * Sertifikat yang sudah ada dan sama dengan baris ini.
     *
     * Dicocokkan lewat No. Sertifikat, yang memang unik. Baris tanpa nomor
     * dicocokkan lewat gabungan nama, No. UJK, dan kualifikasi — supaya
     * mengunggah ulang berkas yang sama tidak menggandakan peserta yang
     * sertifikatnya belum bernomor.
     */
    private function cariYangSama(array $d, bool $kunci = false): ?CertificateHolder
    {
        $q = CertificateHolder::query();

        if ($d['certificate_number']) {
            $q->where('certificate_number', $d['certificate_number']);
        } else {
            $q->whereNull('certificate_number')
                ->where('participant_name', $d['participant_name'])
                ->where('ujk_number', $d['ujk_number'])
                ->where('qualification', $d['qualification']);
        }

        return ($kunci ? $q->lockForUpdate() : $q)->first();
    }

    /**
     * Tanggal dari sel Excel. Menerima sel bertipe tanggal, angka seri Excel,
     * dan teks HH/BB/TTTT, HH-BB-TTTT, atau TTTT-BB-HH.
     *
     * @return array{0: ?string, 1: bool} [Y-m-d atau null, apakah sah]
     */
    private function tanggal(mixed $v): array
    {
        if ($v === null || (is_string($v) && trim($v) === '')) {
            return [null, true];
        }

        if ($v instanceof DateTimeInterface) {
            return [CarbonImmutable::instance($v)->toDateString(), true];
        }

        // Angka seri Excel: hari sejak 30 Desember 1899.
        if (is_int($v) || is_float($v) || (is_string($v) && preg_match('/^\d{4,5}(\.\d+)?$/', trim($v)))) {
            $n = (int) floor((float) $v);
            if ($n > 20000 && $n < 80000) {
                return [CarbonImmutable::create(1899, 12, 30)->addDays($n)->toDateString(), true];
            }

            return [null, false];
        }

        $s = trim((string) $v);

        foreach (['d/m/Y', 'd-m-Y', 'd.m.Y', 'Y-m-d', 'j/n/Y', 'j-n-Y'] as $format) {
            /* createFromFormat MELEMPAR InvalidFormatException untuk teks yang
               tidak cocok — bukan mengembalikan false. Tanpa try ini, satu sel
               tanggal yang salah ketik menjatuhkan seluruh impor alih-alih
               dilaporkan sebagai galat di baris itu. */
            try {
                $t = CarbonImmutable::createFromFormat('!'.$format, $s);
            } catch (Throwable) {
                continue;
            }

            // Dibandingkan balik ke teks aslinya: 31/02/2026 diterima PHP
            // sebagai 3 Maret, dan itu bukan tanggal yang dimaksud admin.
            if ($t && $t->format($format) === $s && $t->year >= 1990 && $t->year <= 2100) {
                return [$t->toDateString(), true];
            }
        }

        return [null, false];
    }

    /** @param array<int, string> $galat */
    private function gagal(array $galat): array
    {
        return ['ok' => false, 'dibuat' => 0, 'diperbarui' => 0, 'galat' => $galat];
    }
}
