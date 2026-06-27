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

    public static function canCreate(): bool
    {
        return static::userCan();
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
