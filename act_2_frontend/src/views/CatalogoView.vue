<script setup lang="ts">
import { ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import { useFiltros } from '../composables/useFiltros'
import { useCarritoStore } from '../stores/carrito'
import CartIcon from '../components/CartIcon.vue'
import FiltrosPanel from '../components/FiltrosPanel.vue'
import PaginacionNav from '../components/PaginacionNav.vue'

const route = useRoute()
const { filtros } = useFiltros()
const carrito = useCarritoStore()

const resultado = ref<any>({ data: [], meta: {} })
const cargando = ref(false)

// Función para solicitar los productos paginados y filtrados al backend
const cargarProductos = async () => {
  cargando.value = true
  try {
    const { data } = await axios.get('http://127.0.0.1:8000/api/productos', {
      params: {
        busqueda:    filtros.busqueda,
        categoria_id: filtros.categoria_id,
        precio_min:  filtros.precio_min,
        precio_max:  filtros.precio_max,
        page:        filtros.pagina,
        orden:       filtros.orden || 'nombre',
        dir:         filtros.dir || 'asc'
      }

    })
    resultado.value = data
  } catch (error) {
    console.error('Error al cargar los productos de la API:', error)
  } finally {
    cargando.value = false
  }
}

// Observa la URL (?busqueda=...&p=...) y recarga los datos automáticamente
watch(() => [filtros.orden, filtros.dir], cargarProductos)

</script>

<template>
  <div>
    <nav style="background: #333; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; font-family: Arial, sans-serif;">
      <span style="font-weight: bold; font-size: 18px;">Mi Tienda Online</span>
      <div style="display: flex; align-items: center; gap: 15px;">
        <router-link to="/" style="color: white; text-decoration: none;">Inicio</router-link>
        <router-link to="/catalogo" style="color: white; text-decoration: none;">Catálogo</router-link>
        <router-link to="/login" style="color: #4ab3f4; text-decoration: none;">Administración</router-link>
        <CartIcon />
      </div>
    </nav>

    <div style="max-width: 1200px; margin: 40px auto; padding: 0 20px; font-family: Arial, sans-serif; display: grid; grid-template-columns: 280px 1fr; gap: 30px;">
      
      <aside>
        <FiltrosPanel :filtros="filtros" />
      </aside>

      <main>
        <h2 style="color: #2c3e50; margin-top: 0; margin-bottom: 5px;">Catálogo Avanzado</h2>
        <p style="color: #7f8c8d; margin-bottom: 25px;">Resultados totales del servidor: {{ resultado.meta?.total || 0 }} artículos.</p>

        <div v-if="cargando" style="text-align: center; color: #3498db; padding: 60px; font-size: 16px; font-weight: bold;">
          Buscando en el servidor...
        </div>

        <div v-else>
          <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
            <div v-for="producto in resultado.data" :key="producto.id" style="background: white; border: 1px solid #e1e8ed; padding: 20px; border-radius: 8px; display: flex; flex-direction: column; justify-content: space-between;">
              <div>
                <div style="width: 100%; height: 140px; display: flex; align-items: center; justify-content: center; background: #fafafa; border-radius: 6px; margin-bottom: 15px; overflow: hidden; border: 1px solid #f1f2f6;">
                  <img 
                    :src="producto.imagen_url && producto.imagen_url !== 'http://127.0.0.1:8000/storage/' ? producto.imagen_url : 'data:image/svg+xml;utf8,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'150\' height=\'150\' viewBox=\'0 0 150 150\'><rect width=\'100%\' height=\'100%\' fill=\'%23f1f2f6\'/><text x=\'50%\' y=\'50%\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'Arial\' font-size=\'14\' fill=\'%237f8c8d\'>Sin Imagen</text></svg>'" 
                    :alt="producto.nombre"
                    style="max-width: 100%; max-height: 100%; object-fit: contain;"
                  />
                </div>

                <h3 style="margin-top: 0; color: #2c3e50; font-size: 16px; margin-bottom: 5px;">{{ producto.nombre }}</h3>
                <span style="font-size: 12px; background: #f1f2f6; padding: 3px 8px; border-radius: 10px; color: #7f8c8d; display: inline-block; margin-bottom: 10px;">
                  {{ producto.categoria?.nombre || 'General' }}
                </span>
                <p style="color: #7f8c8d; font-size: 13px; margin-bottom: 15px; line-height: 1.4;">{{ producto.descripcion || 'Sin descripción' }}</p>
              </div>
              
              <div style="display: flex; flex-direction: column; gap: 12px; border-top: 1px solid #f2f5f8; padding-top: 15px;">
                <span style="font-size: 18px; color: #2c3e50; font-weight: bold;">${{ Number(producto.precio).toFixed(2) }}</span>
                <button @click="carrito.agregar(producto)" style="background: #4ab3f4; color: white; border: none; padding: 10px; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%;">
                  <template v-if="carrito.cantidadDeProducto(producto.id) > 0">
                    En carrito ({{ carrito.cantidadDeProducto(producto.id) }})
                  </template>
                  <template v-else>
                    Agregar al carrito
                  </template>
                </button>
              </div>
            </div>
          </div>

          <div v-if="resultado.data?.length === 0" style="text-align: center; color: #7f8c8d; padding: 40px; font-size: 16px;">
            Ningún producto coincide con los filtros aplicados.
          </div>

          <div style="margin-top: 40px;">
            <PaginacionNav 
              :meta="resultado.meta" 
              @cambio-pagina="filtros.pagina = $event" 
            />
          </div>
        </div>
      </main>

    </div>
  </div>
</template>