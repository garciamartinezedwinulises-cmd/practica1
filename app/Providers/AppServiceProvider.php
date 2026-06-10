<?php

namespace App\Providers;

use App\Models\Producto;
use App\Policies\ProductoPolicy;
use App\Models\User;
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
        Gate::policy(Producto::class, ProductoPolicy::class);

        Gate::define('crear-producto', function (User $user) {
            return in_array($user->rol, ['admin', 'editor']);
        });

        Gate::define('editar-producto', function (User $user) {
            return in_array($user->rol, ['admin', 'editor']);
        });

        Gate::define('eliminar-producto', function (User $user) {
            return $user->esAdmin();
        });
    }
}