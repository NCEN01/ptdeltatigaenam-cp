<?php

namespace App\Models;

use App\Models\Concerns\HasAutoSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasAutoSlug, HasFactory, HasTranslations;

    /** Kolom sumber slug otomatis (App\Models\Concerns\HasAutoSlug). */
    protected string $slugFrom = 'title';

    protected $guarded = ['id'];

    /**
     * price_label ikut di sini karena satuan harga tidak selalu "peserta":
     * headhunter dan konsultasi dijual per penugasan, bukan per kursi.
     */
    public array $translatable = [
        'title', 'short_description', 'description', 'duration',
        'price_label', 'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_active' => 'boolean',
            'discount_original_price' => 'decimal:2',
            'is_purchasable' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Display-only discount: `price` is the real (charged) price; when active,
     * `discount_original_price` is a higher "before" price shown struck-through.
     */
    public function hasDiscount(): bool
    {
        return $this->discount_active
            && $this->discount_original_price !== null
            && (float) $this->discount_original_price > (float) $this->price
            && (float) $this->price > 0;
    }

    public function discountPercent(): int
    {
        if (! $this->hasDiscount()) {
            return 0;
        }

        $before = (float) $this->discount_original_price;
        $now = (float) $this->price;

        return (int) round((($before - $now) / $before) * 100);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ServiceActivity::class)->orderBy('sort_order');
    }

    /** Pengajar yang mengampu program ini, dari pustaka mentor bersama. */
    public function mentors(): BelongsToMany
    {
        return $this->belongsToMany(Mentor::class)
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    /**
     * Benefit khusus program ini. Untuk menampilkannya di halaman, pakai
     * resolvedBenefits() — ia yang tahu kapan harus jatuh ke daftar bawaan.
     */
    public function benefits(): HasMany
    {
        return $this->hasMany(ProgramBenefit::class)->orderBy('sort_order');
    }

    /** Benefit yang berlaku: milik program ini bila ada, kalau tidak bawaannya. */
    public function resolvedBenefits()
    {
        return ProgramBenefit::forService($this);
    }

    /**
     * Keterangan cara pelaksanaan: luring, daring, atau bauran. Dibaca dari
     * kolom mode yang memang sudah ada sejak awal, dan dipusatkan di sini
     * supaya kartu daftar dan halaman rinciannya tidak menuliskan padanan
     * katanya masing-masing lalu menyimpang.
     */
    public function modeLabel(?string $locale = null): ?string
    {
        $locale ??= app()->getLocale();
        $isId = $locale === 'id';

        return match ($this->mode) {
            'offline' => $isId ? 'Tatap Muka' : 'In person',
            'online' => $isId ? 'Daring' : 'Online',
            'hybrid' => $isId ? 'Bauran' : 'Hybrid',
            default => null,
        };
    }

    /**
     * Jadwal terdekat yang belum lewat. Dipakai kartu daftar untuk menyebut
     * kapan dan di mana — tanpa ini kartunya hanya menyebut harga, dan
     * pertanyaan pertama pembaca justru "kapan".
     */
    public function nextSchedule(): ?ServiceSchedule
    {
        return $this->schedules
            ->where('is_active', true)
            ->filter(fn ($s) => $s->start_date && $s->start_date->endOfDay()->isFuture())
            ->sortBy('start_date')
            ->first();
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(ServiceSchedule::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Sisa kursi angkatan terdekat, atau null bila tidak ada angkatan berkuota.
     *
     * Kuota kini milik angkatan (service_schedules), bukan milik layanan:
     * kursi memang habis per angkatan, dan dua penghitung untuk hal yang sama
     * sebelumnya bertambah bersamaan setiap kali ada pembayaran lunas.
     */
    public function getAvailableSeatsAttribute(): ?int
    {
        return $this->nextSchedule()?->seats_available;
    }
}
