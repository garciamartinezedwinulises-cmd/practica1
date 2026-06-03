<script setup lang="ts">
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import { useCarritoStore } from '../stores/carrito'
import CartIcon from '../components/CartIcon.vue'

const productos = ref<any[]>([])
const busqueda = ref('')
const carrito = useCarritoStore()
const loading = ref(false)

// Paginación reactiva de 10 artículos por página
const paginaActual = ref(1)
const itemsPorPagina = 10

const productosFiltrados = computed(() => {
  return productos.value.filter(p =>
    p.nombre.toLowerCase().includes(busqueda.value.toLowerCase())
  )
})

// Filtra el arreglo general para mostrar solo el bloque de la página activa
const productosPaginados = computed(() => {
  const inicio = (paginaActual.value - 1) * itemsPorPagina
  const fin = inicio + itemsPorPagina
  return productosFiltrados.value.slice(inicio, fin)
})

const totalPaginas = computed(() => {
  return Math.ceil(productosFiltrados.value.length / itemsPorPagina) || 1
})

// Regresa a la página 1 si el usuario escribe en el buscador
watch(busqueda, () => {
  paginaActual.value = 1
})

const cambiarPagina = (pagina: number) => {
  if (pagina >= 1 && pagina <= totalPaginas.value) {
    paginaActual.value = pagina
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }
}

onMounted(async () => {
  loading.value = true
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/productos')
    productos.value = response.data.data || response.data
  } catch (error) {
    console.error('Error al cargar el catálogo:', error)
  } finally {
    loading.value = false
  }
})
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

    <div style="max-width: 1000px; margin: 40px auto; padding: 0 20px; font-family: Arial, sans-serif;">
      <h2 style="color: #2c3e50; margin-bottom: 10px;">Nuestro Catálogo</h2>
      <p style="color: #7f8c8d; margin-bottom: 30px;">Explora la variedad de opciones disponibles en nuestra sucursal.</p>

      <div style="margin-bottom: 30px;">
        <input 
          type="text" 
          v-model="busqueda" 
          placeholder="Buscar producto por nombre..." 
          style="width: 100%; max-width: 400px; padding: 10px 15px; border: 1px solid #ccc; border-radius: 4px; font-size: 15px; box-sizing: border-box;"
        />
      </div>

      <div v-if="loading" style="text-align: center; color: #3498db; padding: 40px; font-size: 16px; font-weight: bold;">
        Obteniendo productos desde el servidor...
      </div>

      <div v-else>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
          <div v-for="producto in productosPaginados" :key="producto.id" style="background: white; border: 1px solid #e1e8ed; padding: 20px; border-radius: 8px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
              <div style="width: 100%; height: 160px; display: flex; align-items: center; justify-content: center; background: #fafafa; border-radius: 6px; margin-bottom: 15px; overflow: hidden; border: 1px solid #f1f2f6;">
                <img 
                  :src="producto.imagen_url || 'https://via.placeholder.com/150?text=Sin+Imagen'" 
                  :alt="producto.nombre"
                  style="max-width: 100%; max-height: 100%; object-fit: contain;"
                  @error="(e) => { (e.target as HTMLInputElement).src = 'https://via.placeholder.com/150?text=Sin+Imagen' }"
                />
              </div>

              <h3 style="margin-top: 0; color: #2c3e50; font-size: 18px;">{{ producto.nombre }}</h3>
              <p style="color: #7f8c8d; font-size: 14px; margin-bottom: 15px;">{{ producto.descripcion || 'Sin descripción' }}</p>
            </div>
            
            <div style="display: flex; flex-direction: column; gap: 12px; border-top: 1px solid #f2f5f8; padding-top: 15px;">
              <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 18px; color: #2c3e50; font-weight: bold;">${{ Number(producto.precio).toFixed(2) }}</span>
                <router-link :to="'/catalogo/' + producto.id" style="color: #4ab3f4; text-decoration: none; font-size: 14px;">
                  Ver detalles →
                </router-link>
              </div>
              
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

        <div v-if="totalPaginas > 1" style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 40px;">
          <button @click="cambiarPagina(paginaActual - 1)" :disabled="paginaActual === 1" style="padding: 8px 12px; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer;">
            Anterior
          </button>
          
          <span style="font-size: 14px; color: #2c3e50;">
            Página {{ paginaActual }} de {{ totalPaginas }}
          </span>

          <button @click="cambiarPagina(paginaActual + 1)" :disabled="paginaActual === totalPaginas" style="padding: 8px 12px; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer;">
            Siguiente
          </button>
        </div>
      </div>

      <div v-if="productosFiltrados.length === 0 && productos.length > 0 && !loading" style="text-align: center; color: #7f8c8d; margin-top: 40px; font-size: 16px;">
        No se encontraron productos que coincidan con tu búsqueda.
      </div>
    </div>
  </div>
</template>