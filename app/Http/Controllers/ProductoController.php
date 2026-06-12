<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Http\Resources\ProductoResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use OpenApi\Attributes as OA; // 🔑 Importación obligatoria para los atributos

class ProductoController extends Controller
{
    #[OA\Get(path: '/api/v1/productos', summary: 'Obtener el catálogo completo de productos', tags: ['Productos'])]
    #[OA\Response(response: 200, description: 'Catálogo recuperado con éxito')]
    #[OA\Response(response: 500, description: 'Error interno del servidor')]
    public function index(Request $request)
    {
        $productos = Producto::with('categoria')
            ->buscar($request->busqueda)
            ->deCategoria($request->categoria_id)
            ->rangoPrecio($request->precio_min, $request->precio_max)
            ->orderBy($request->get('orden', 'nombre'), $request->get('dir', 'asc'))
            ->paginate($request->get('por_pagina', 15));
            
        return ProductoResource::collection($productos);
    }

    #[OA\Post(path: '/api/v1/productos', summary: 'Crear un nuevo producto', tags: ['Productos'], security: [['bearerAuth' => []]])]
    #[OA\Response(response: 201, description: 'Producto creado con éxito')]
    #[OA\Response(response: 401, description: 'No autenticado')]
    #[OA\Response(response: 403, description: 'No tienes los permisos requeridos')]
    #[OA\Response(response: 422, description: 'Error de validación')]
    public function store(Request $request)
    {
        Gate::authorize('create', Producto::class);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }
        
        $producto = Producto::create($data);
        return new ProductoResource($producto);
    }

    #[OA\Get(path: '/api/v1/productos/{id}', summary: 'Obtener el detalle de un producto', tags: ['Productos'])]
    #[OA\Parameter(name: 'id', in: 'path', description: 'ID del producto', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Producto encontrado con éxito')]
    #[OA\Response(response: 404, description: 'Producto no encontrado')]
    public function show($id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
        return new ProductoResource($producto);
    }

    #[OA\Put(path: '/api/v1/productos/{id}', summary: 'Actualizar un producto existente', tags: ['Productos'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'id', in: 'path', description: 'ID del producto', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Producto actualizado con éxito')]
    #[OA\Response(response: 404, description: 'Producto no encontrado')]
    #[OA\Response(response: 422, description: 'Error de validación')]
    public function update(Request $request, $id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        Gate::authorize('update', $producto);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'precio' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($data);
        return new ProductoResource($producto);
    }

    #[OA\Delete(path: '/api/v1/productos/{id}', summary: 'Eliminar un producto', tags: ['Productos'], security: [['bearerAuth' => []]])]
    #[OA\Parameter(name: 'id', in: 'path', description: 'ID del producto', required: true, schema: new OA\Schema(type: 'integer'))]
    #[OA\Response(response: 200, description: 'Producto eliminado con éxito')]
    #[OA\Response(response: 404, description: 'Producto no encontrado')]
    public function destroy($id)
    {
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        Gate::authorize('delete', $producto);
        
        $producto->delete();
        return response()->noContent();
    }
}