<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'

const productos = ref<any[]>([])
const busqueda = ref('') 


const productosFiltrados = computed(() => {
  return productos.value.filter(p =>
    p.nombre.toLowerCase().includes(busqueda.value.toLowerCase()) 
  ) 
}) 

onMounted(async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/productos')
    productos.value = response.data
  } catch (error) {
    console.error('Error al cargar el catálogo:', error)
  }
})
</script>

<template>
  <div>
    <nav style="background: #333; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center;">
      <span style="font-weight: bold; font-size: 18px;">Mi Tienda Online</span>
      <div>
        <router-link to="/" style="color: white; margin-right: 15px; text-decoration: none;">Inicio</router-link>
        <router-link to="/catalogo" style="color: white; margin-right: 15px; text-decoration: none;">Catálogo</router-link>
        <router-link to="/login" style="color: #4ab3f4; text-decoration: none;">Administración</router-link>
      </div>
    </nav>

    <div style="max-width: 1000px; margin: 40px auto; padding: 0 20px;">
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

      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
        <div v-for="producto in productosFiltrados" :key="producto.id" style="background: white; border: 1px solid #e1e8ed; padding: 20px; border-radius: 8px; display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <h3 style="margin-top: 0; color: #2c3e50; font-size: 18px;">{{ producto.nombre }}</h3>
            <p style="color: #7f8c8d; font-size: 14px; margin-bottom: 15px;">{{ producto.descripcion || 'Sin descripción' }}</p>
          </div>
          <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #f2f5f8; padding-top: 15px;">
            <span style="font-size: 18px; color: #2c3e50; font-weight: bold;">${{ Number(producto.precio).toFixed(2) }}</span>
            <router-link :to="'/catalogo/' + producto.id" style="background: #4ab3f4; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px;">
              Detalles
            </router-link>
          </div>
        </div>
      </div>

      <div v-if="productosFiltrados.length === 0 && productos.length > 0" style="text-align: center; color: #7f8c8d; margin-top: 40px; font-size: 16px;">
        No se encontraron productos que coincidan con tu búsqueda.
      </div>
    </div>
  </div>
</template>