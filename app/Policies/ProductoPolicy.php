<?php

namespace App\Policies;

use App\Models\Producto;
use App\Models\User;

class ProductoPolicy
{
    // Admins y editores pueden crear productos
    public function create(User $user): bool {
        return in_array($user->rol, ['admin', 'editor']);
    }
    // Admins y editores pueden actualizar productos
    public function update(User $user, Producto $producto): bool {
        return in_array($user->rol, ['admin', 'editor']);
    }
    // Únicamente los administradores pueden borrar productos
    public function delete(User $user, Producto $producto): bool {
        return $user->esAdmin();
    }
}