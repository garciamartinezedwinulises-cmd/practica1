<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriaProductoSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = ['Electrónica', 'Ropa', 'Hogar', 'Deportes'];

        foreach ($categorias as $nombre) {
            // Revisa si ya existe el slug antes de intentar registrarlo
            $cat = Categoria::firstOrCreate(
                ['slug' => Str::slug($nombre)],
                ['nombre' => $nombre]
            );

            for ($i = 1; $i <= 15; $i++) {
                Producto::create([
                    'nombre' => "Producto " . $nombre . " " . $i,
                    'descripcion' => "Descripción detallada de prueba para el artículo " . $i . " dentro de " . $nombre,
                    'precio' => rand(150, 2500) / 10,
                    'stock' => rand(5, 40),
                    'categoria_id' => $cat->id,
                    'imagen' => null
                ]);
            }
        }
    }
}