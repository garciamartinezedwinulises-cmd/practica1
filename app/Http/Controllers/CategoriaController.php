<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Http\Resources\CategoriaResource;
use App\Http\Resources\ProductoResource;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        // Método index con eager loading para incluir los productos vinculados
        return CategoriaResource::collection(
            Categoria::with('productos')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        // Generamos el slug automáticamente basándonos en el nombre
        $categoria = Categoria::create([
            'nombre' => $request->nombre,
            'slug' => \Illuminate\Support\Str::slug($request->nombre),
            'descripcion' => $request->descripcion,
        ]);

        return new CategoriaResource($categoria);
    }

    public function show($id)
    {
        $categoria = Categoria::with('productos')->find($id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }
        return new CategoriaResource($categoria);
    }

    // Método personalizado para filtrar y retornar los productos de esta categoría
    public function productos(Categoria $categoria)
    {
        return ProductoResource::collection(
            $categoria->productos()->with('categoria')->get()
        );
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::find($id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $categoria->update([
            'nombre' => $request->nombre,
            'slug' => \Illuminate\Support\Str::slug($request->nombre),
            'descripcion' => $request->descripcion,
        ]);

        return new CategoriaResource($categoria);
    }

    public function destroy($id)
    {
        $categoria = Categoria::find($id);
        if (!$categoria) {
            return response()->json(['message' => 'Categoría no encontrada'], 404);
        }

        $categoria->delete();
        return response()->json(null, 204);
    }
}