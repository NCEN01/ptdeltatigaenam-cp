<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        /* Catatan aktivitas panel admin. Dipasang sebagai satu pendengar
           berpola, bukan trait di tiap model: ada tiga puluh model yang bisa
           disunting admin, dan model yang ditambahkan nanti akan diam-diam
           tidak tercatat kalau pemasangannya harus diingat satu per satu.
           Penyaring dan alasannya ada di App\Support\ActivityRecorder. */
        \App\Support\ActivityRecorder::listen();

        // Super admins implicitly have every permission.
        Gate::before(function ($user, string $ability) {
            if (method_exists($user, 'hasRole') && $user->hasRole('super_admin')) {
                return true;
            }

            return null;
        });

        // Enforce strong password defaults in production
        \Illuminate\Validation\Rules\Password::defaults(function () {
            $rule = \Illuminate\Validation\Rules\Password::min(8);

            return $this->app->isProduction()
                ? $rule->letters()->numbers()->symbols()->mixedCase()
                : $rule;
        });
    }
}
