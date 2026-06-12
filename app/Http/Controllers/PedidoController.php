<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Producto;
use App\Jobs\EnviarConfirmacionPedido;
use App\Events\NuevoPedidoRecibido;
use App\Events\StockBajoAlerta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PedidoController extends Controller {
    
    public function store(Request $request) {
        $pedido = DB::transaction(function () use ($request) {
            $total = collect($request->items)->sum(fn($i) => $i['precio'] * $i['cantidad']);

            $p = Pedido::create([
                'user_id' => auth()->id(), 
                'total'   => $total,
            ]);

            foreach ($request->items as $item) {
                $p->items()->create([
                    'producto_id'     => $item['producto_id'],
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $item['precio'] 
                ]);

                $producto = Producto::find($item['producto_id']);
                $producto->decrement('stock', $item['cantidad']);
                
                if ($producto->stock <= 5) {
                    broadcast(new StockBajoAlerta($producto, $producto->stock));
                }
            }

            return $p;
        });

        broadcast(new NuevoPedidoRecibido($pedido))->toOthers();

        EnviarConfirmacionPedido::dispatch($pedido)->delay(now()->addSeconds(5));

        return response()->json(['pedido_id' => $pedido->id], 201);
    }

    public function show($id) {
        
        $pedido = Pedido::findOrFail($id);
        return response()->json($pedido);
    }
}