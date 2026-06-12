<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import echo from '../plugins/echo'
interface PedidoAlerta {
  id: number
  total: number | string
  cliente: string
  items: number
  created_at: string
}

interface StockAlerta {
  producto_id: number
  nombre: string
  stock_actual: number
}

const pedidosNuevos = ref<PedidoAlerta[]>([])
const alertasStock = ref<StockAlerta[]>([])

onMounted(() => {
  echo.private('admin-panel')
    
    .listen('NuevoPedidoRecibido', (e: PedidoAlerta) => {
      pedidosNuevos.value.unshift(e) 
      setTimeout(() => {
        pedidosNuevos.value.pop()
      }, 10000)
    })
    
    .listen('StockBajoAlerta', (e: StockAlerta) => {
      const existe = alertasStock.value.some(a => a.producto_id === e.producto_id)
      if (!existe) {
        alertasStock.value.unshift(e)
      }
    })
})

onUnmounted(() => {
  echo.leave('admin-panel')
})
</script>

<template>
  <div class="notificaciones-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 360px; font-family: Arial, sans-serif;">
    
    <TransitionGroup name="toast">
      <div v-for="p in pedidosNuevos" :key="p.id" class="toast-card" style="background: #2ecc71; color: white; padding: 15px; border-radius: 6px; margin-bottom: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-weight: bold; font-size: 14px; border-left: 5px solid #27ae60;">
        🛒 ¡Nuevo pedido #{{ p.id }}! <br>
        <span style="font-weight: normal; font-size: 12px; display: block; margin-top: 4px; color: #f4fbf7;">
          Cliente: {{ p.cliente }} | Total: ${{ Number(p.total).toFixed(2) }} <br>
          Hora: {{ p.created_at }}
        </span>
      </div>
    </TransitionGroup>

    <div v-for="a in alertasStock" :key="a.producto_id" class="alerta-card" style="background: #e74c3c; color: white; padding: 12px; border-radius: 6px; margin-top: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); font-weight: bold; font-size: 13px; border-left: 5px solid #c0392b;">
      ⚠️ ALERTA DE INVENTARIO <br>
      <span style="font-weight: normal; font-size: 12px; display: block; margin-top: 3px; color: #fdedec;">
        El producto <strong>{{ a.nombre }}</strong> bajó a {{ a.stock_actual }} unidades. ¡Requiere reabastecimiento! [cite: 15, 135]
      </span>
    </div>

  </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.5s ease;
}
.toast-enter-from {
  opacity: 0;
  transform: translateX(50px) scale(0.9);
}
.toast-leave-to {
  opacity: 0;
  transform: translateY(-20px) scale(0.9);
}
</style>