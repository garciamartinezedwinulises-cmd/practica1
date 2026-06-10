<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'precio', 'stock', "categoria_id"];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    // Buscador protegido contra valores vacíos
    public function scopeBuscar($query, $termino) {
        return $query->when(!empty($termino), function ($q) use ($termino) {
            return $q->where(function ($inner) use ($termino) {
                $inner->where('nombre', 'LIKE', "%{$termino}%")
                      ->orWhere('descripcion', 'LIKE', "%{$termino}%");
            });
        });
    }

    // Filtro de categoría protegido contra strings vacíos
    public function scopeDeCategoria($query, $categoriaId) {
        return $query->when(($categoriaId !== null && $categoriaId !== ''), function ($q) use ($categoriaId) {
            return $q->where('categoria_id', $categoriaId);
        });
    }

    // Filtro de rango de precios protegido
    public function scopeRangoPrecio($query, $min, $max) {
        return $query
            ->when(($min !== null && $min !== ''), fn($q) => $q->where('precio', '>=', $min))
            ->when(($max !== null && $max !== ''), fn($q) => $q->where('precio', '<=', $max));
    }
}