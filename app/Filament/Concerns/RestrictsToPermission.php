<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Gate a Filament resource behind a single permission name.
 * Resources set `protected static ?string $accessPermission = '...'`.
 * Super admins bypass via the Gate::before rule in AppServiceProvider.
 */
trait RestrictsToPermission
{
    /**
     * Resources override this to declare the gating permission name.
     */
    protected static function accessPermission(): ?string
    {
        return static::$accessPermission ?? null;
    }

    protected static function userCan(): bool
    {
        $permission = static::accessPermission();

        if ($permission === null) {
            return true;
        }

        return (bool) auth()->user()?->can($permission);
    }

    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return static::userCan() && parent::shouldRegisterNavigation($parameters);
    }

    public static function canViewAny(): bool
    {
        return static::userCan();
    }

    public static function canView(Model $record): bool
    {
        return static::userCan();
    }

    /**
     * Dua alasan sebuah resource bisa menolak pembuatan baris baru: izin
     * perannya tidak mengizinkan, atau tabelnya sudah penuh.
     *
     * Keduanya diputuskan di satu tempat karena keduanya menjawab pertanyaan
     * yang sama — "boleh menambah atau tidak" — dan Filament memanggil satu
     * metode ini untuk dua hal sekaligus: menyembunyikan tombol "Buat" di
     * daftar, dan menolak rute /create kalau alamatnya diketik langsung.
     *
     * Batasnya tidak ditulis di sini maupun di resource-nya, melainkan dibaca
     * dari modelnya lewat App\Models\Concerns\HasRecordLimit. Model adalah
     * tempat batas itu juga ditegakkan saat penyimpanan, jadi angkanya tidak
     * pernah ada dua salinan yang bisa berbeda.
     */
    public static function canCreate(): bool
    {
        if (! static::userCan()) {
            return false;
        }

        return static::hasRoomForMoreRecords();
    }

    /** true bila modelnya tidak berbatas, atau berbatas dan masih ada ruang. */
    protected static function hasRoomForMoreRecords(): bool
    {
        $model = static::$model ?? null;

        if (! is_string($model) || ! method_exists($model, 'hasRoomForMore')) {
            return true;
        }

        return $model::hasRoomForMore();
    }

    /**
     * Lencana "2/2" di sebelah nama menu untuk resource yang berbatas.
     *
     * Tanpa ini, satu-satunya tanda bahwa tabelnya sudah penuh adalah tombol
     * "Buat" yang hilang — dan tombol yang hilang terbaca seperti izin yang
     * dicabut atau panel yang rusak, bukan seperti kuota yang habis.
     */
    public static function getNavigationBadge(): ?string
    {
        $model = static::$model ?? null;

        if (! is_string($model) || ! method_exists($model, 'maxRecords')) {
            return null;
        }

        return $model::recordCount().'/'.$model::maxRecords();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $model = static::$model ?? null;

        if (! is_string($model) || ! method_exists($model, 'maxRecords')) {
            return null;
        }

        return match (true) {
            $model::remainingSlots() === 0 => 'warning',
            $model::remainingSlots() <= 2 => 'info',
            default => 'gray',
        };
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        $model = static::$model ?? null;

        if (! is_string($model) || ! method_exists($model, 'maxRecords')) {
            return null;
        }

        return $model::remainingSlots() === 0
            ? 'Kuota penuh. Hapus atau sunting data yang sudah ada untuk menggantinya.'
            : 'Tersisa '.$model::remainingSlots().' dari '.$model::maxRecords().' slot.';
    }

    public static function canEdit(Model $record): bool
    {
        return static::userCan();
    }

    public static function canDelete(Model $record): bool
    {
        return static::userCan();
    }

    public static function canDeleteAny(): bool
    {
        return static::userCan();
    }
}
