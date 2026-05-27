<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductoSeeder extends Seeder
{
    public function run(): void
    {
        Producto::create([
            'nombre' => 'Teclado Mecánico',
            'descripcion' => 'Teclado RGB con switches red',
            'precio' => 1250.00,
            'stock' => 15
        ]);

        Producto::create([
            'nombre' => 'Monitor 24 pulgadas',
            'descripcion' => 'Monitor Full HD 144Hz',
            'precio' => 3499.99,
            'stock' => 8
        ]);
    }
}