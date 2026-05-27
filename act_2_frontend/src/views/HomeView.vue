<script setup lang="ts">
import { ref, onMounted } from 'vue'
import axios from 'axios'

const novedades = ref<any[]>([])

onMounted(async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/productos')
    // Tomamos únicamente los últimos 3 productos agregados
    novedades.value = response.data.slice(-3).reverse()
  } catch (error) {
    console.error('Error al cargar las novedades:', error)
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

    <div style="background: #eef2f5; padding: 60px 20px; text-align: center; border-bottom: 1px solid #e1e8ed;">
      <h1 style="font-size: 36px; margin-bottom: 10px; color: #2c3e50;">Bienvenido a nuestra plataforma</h1>
      <p style="font-size: 18px; color: #7f8c8d; margin-bottom: 25px;">Descubre los mejores productos con entregas a domicilio inmediatas.</p>
      <router-link to="/catalogo" style="background: #4ab3f4; color: white; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-weight: bold;">
        Ver Catálogo Completo
      </router-link>
    </div>

    <div style="max-width: 1000px; margin: 40px auto; padding: 0 20px;">
      <h2 style="text-align: center; margin-bottom: 30px; color: #2c3e50;">Últimas Novedades</h2>
      
      <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
        <div v-for="producto in novedades" :key="producto.id" style="background: white; border: 1px solid #e1e8ed; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
          <h3 style="margin-top: 0; color: #2c3e50;">{{ producto.nombre }}</h3>
          <p style="color: #7f8c8d; font-size: 14px; height: 40px; overflow: hidden;">{{ producto.descripcion || 'Sin descripción' }}</p>
          <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">
            <span style="font-size: 18px; color: #2c3e50; font-weight: bold;">${{ Number(producto.precio).toFixed(2) }}</span>
            <router-link :to="'/catalogo/' + producto.id" style="color: #4ab3f4; text-decoration: none; font-size: 14px;">
              Ver detalles →
            </router-link>
          </div>
        </div>
      </div>
      
      <div v-if="novedades.length === 0" style="text-align: center; color: #7f8c8d; margin-top: 20px;">
        Cargando productos del servidor...
      </div>
    </div>
  </div>
</template>