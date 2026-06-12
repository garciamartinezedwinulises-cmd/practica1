<?php

namespace App\Http\Controllers\Api\V2;

// 🛡️ Importamos el controlador original de la V1 para heredar todos sus métodos CRUD 
use App\Http\Controllers\ProductoController as V1ProductoController;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends V1ProductoController {

    public function index(Request $request) {

        $query = Producto::with('categoria');

        if ($request->q) {
            $query->whereFullText(['nombre', 'descripcion'], $request->q);
        }

        return response()->json($query->paginate(15));
    }
}