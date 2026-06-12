<script setup lang="ts">
import { ref } from 'vue'
import axios from 'axios'
import { useCarritoStore } from '../stores/carrito'
import { useRouter } from 'vue-router'
import PedidoEstado from '../components/PedidoEstado.vue' // Importación correcta según tus carpetas

const carrito = useCarritoStore()
const router = useRouter()

// Variables reactivas para el control de la Práctica 11
const pedidoIdCreado = ref<number | null>(null)
const mostrarMonitoreo = ref(false)

const finalizarCompra = async () => {
  try {
  
    const itemsParaEnviar = carrito.items.map((item: any) => ({
      producto_id: item.id,
      cantidad: item.cantidad,
      precio: item.precio
    }))

   
    const { data } = await axios.post('http://127.0.0.1:8000/api/pedidos', {
      items: itemsParaEnviar
    })

   
    pedidoIdCreado.value = data.pedido_id
    mostrarMonitoreo.value = true

    // 4. Vaciamos tu store con tu método original
    carrito.vaciar()

  } catch (error) {
    console.error('Error al procesar la compra:', error)
    alert('Hubo un problema de conexión al procesar tu orden de compra.')
  }
}

const confirmarVaciar = () => {
  if (confirm('¿Estás seguro de que deseas remover todos los artículos del carrito?')) {
    carrito.vaciar()
  }
}
</script>

<template>
  <div>
    <nav style="background: #333; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; font-family: Arial, sans-serif;">
      <span style="font-weight: bold; font-size: 18px;">Mi Tienda Online</span>
      <div>
        <router-link to="/" style="color: white; margin-right: 15px; text-decoration: none;">Inicio</router-link>
        <router-link to="/catalogo" style="color: white; margin-right: 15px; text-decoration: none;">Catálogo</router-link>
        <router-link to="/login" style="color: #4ab3f4; text-decoration: none;">Administración</router-link>
      </div>
    </nav>

    <div style="max-width: 800px; margin: 40px auto; padding: 0 20px; font-family: Arial, sans-serif;">
      <h2>Tu Carrito de Compras</h2>

      <div v-if="mostrarMonitoreo && pedidoIdCreado">
        <PedidoEstado :pedidoId="pedidoIdCreado" />
        <div style="text-align: center; margin-top: 25px;">
          <button @click="router.push('/')" style="background: #3498db; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 14px;">
            Volver a la Página de Inicio
          </button>
        </div>
      </div>

      <div v-else-if="carrito.items.length === 0" style="text-align: center; padding: 40px; color: #7f8c8d; border: 1px dashed #ccc; border-radius: 8px;">
        <p style="font-size: 16px;">No has agregado ningún producto al carrito todavía.</p>
        <router-link to="/catalogo" style="color: #4ab3f4; text-decoration: none; font-weight: bold;">
          Volver al catálogo para explorar artículos
        </router-link>
      </div>

      <div v-else>
        <table style="width: 100%; border-collapse: collapse; text-align: left; margin-bottom: 30px;">
          <thead>
            <tr style="border-bottom: 2px solid #e1e8ed; background: #f8f9fa;">
              <th style="padding: 12px;">Producto</th>
              <th style="padding: 12px;">Precio Unitario</th>
              <th style="padding: 12px; text-align: center;">Cantidad</th>
              <th style="padding: 12px;">Subtotal</th>
              <th style="padding: 12px; text-align: center;">Quitar</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in carrito.items" :key="item.id" style="border-bottom: 1px solid #e1e8ed;">
              <td style="padding: 12px; color: #2c3e50; font-weight: bold;">{{ item.nombre }}</td>
              <td style="padding: 12px; color: #555;">${{ Number(item.precio).toFixed(2) }}</td>
              <td style="padding: 12px; text-align: center;">
                <button @click="carrito.cambiarCantidad(item.id, item.cantidad - 1)" style="padding: 3px 8px; cursor: pointer; border: 1px solid #ccc; background: white; border-radius: 3px;">-</button>
                <span style="margin: 0 12px; font-weight: bold; display: inline-block; min-width: 20px;">{{ item.cantidad }}</span>
                <button @click="carrito.cambiarCantidad(item.id, item.cantidad + 1)" style="padding: 3px 8px; cursor: pointer; border: 1px solid #ccc; background: white; border-radius: 3px;">+</button>
              </td>
              <td style="padding: 12px; color: #2c3e50; font-weight: bold;">
                ${{ (item.precio * item.cantidad).toFixed(2) }}
              </td>
              <td style="padding: 12px; text-align: center;">
                <button @click="carrito.quitar(item.id)" style="background: #e74c3c; color: white; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; font-weight: bold;">
                  ×
                </button>
              </td>
            </tr>
          </tbody>
        </table>

        <div style="display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; padding: 25px; border-radius: 8px; border: 1px solid #e1e8ed;">
          <div>
            <button @click="confirmarVaciar" style="background: #7f8c8d; color: white; border: none; padding: 10px 18px; border-radius: 4px; cursor: pointer; font-size: 14px;">
              Vaciar Carrito
            </button>
          </div>
          <div style="text-align: right;">
            <h3 style="margin: 0 0 15px 0; color: #2c3e50; font-size: 20px;">Total General: ${{ carrito.totalPrecio.toFixed(2) }}</h3>
            <button @click="finalizarCompra" style="background: #2ecc71; color: white; border: none; padding: 12px 24px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 15px;">
              Finalizar Compra
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>