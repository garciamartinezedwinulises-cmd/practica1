<?php

namespace App\Events;

use App\Models\Pedido;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevoPedidoRecibido implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // Recibimos el modelo del pedido que se acaba de guardar
    public function __construct(public Pedido $pedido) {}

    // Canal privado: Solo los usuarios autorizados en el panel podrán escuchar [cite: 39]
    public function broadcastOn(): array {
        return [new PrivateChannel('admin-panel')];
    }

    // Estructura de datos sanitizada que viaja directo a Vue 3 [cite: 43]
    public function broadcastWith(): array {
        return [
            'id'         => $this->pedido->id,
            'total'      => $this->pedido->total,
            'cliente'    => $this->pedido->user->name,
            'items'      => $this->pedido->items->count(),
            'created_at' => $this->pedido->created_at->format('H:i:s'),
        ];
    }
}