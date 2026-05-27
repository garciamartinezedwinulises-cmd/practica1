<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'

// Declaramos que recibimos el parámetro ID directamente como una propiedad [cite: 42, 69]
const props = defineProps({
  id: {
    type: String,
    required: true
  }
})

const producto = ref<any>(null)
const errorCarga = ref(false)
const router = useRouter()

onMounted(async () => {
  try {
    const response = await axios.get(`http://127.0.0.1:8000/api/productos/${props.id}`)
    producto.value = response.data
  } catch (error) {
    errorCarga.value = true
    console.error('Error al obtener el producto:', error)
  }
})


const volver = () => {
  router.back()
}
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

    <div style="max-width: 600px; margin: 50px auto; padding: 0 20px;">
      <button @click="volver" style="background: none; border: none; color: #4ab3f4; cursor: pointer; font-size: 15px; margin-bottom: 20px; padding: 0;">
        ← Volver al catálogo [cite: 70]
      </button>

      <div v-if="producto" style="background: white; border: 1px solid #e1e8ed; padding: 30px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
        <h2 style="margin-top: 0; color: #2c3e50; border-bottom: 1px solid #f2f5f8; padding-bottom: 15px;">{{ producto.nombre }}</h2> [cite: 69]
        
        <div style="margin: 20px 0;">
          <h4 style="color: #7f8c8d; margin-bottom: 5px; font-size: 13px; text-transform: uppercase;">Descripción del producto</h4>
          <p style="color: #2c3e50; font-size: 16px; line-height: 1.5; margin-top: 0;">{{ producto.descripcion || 'Este producto no cuenta con una descripción detallada todavía.' }}</p> [cite: 69]
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; background: #f8fbc9; padding: 15px; border-radius: 6px; margin-top: 25px;">
          <div>
            <span style="display: block; color: #7f8c8d; font-size: 13px;">Precio al público</span>
            <span style="font-size: 22px; font-weight: bold; color: #2c3e50;">${{ Number(producto.precio).toFixed(2) }}</span> [cite: 69]
          </div>
          <div>
            <span style="display: block; color: #7f8c8d; font-size: 13px;">Disponibilidad en almacén</span>
            <span style="font-size: 22px; font-weight: bold; color: #2c3e50;">{{ producto.stock }} piezas</span> [cite: 69]
          </div>
        </div>
      </div>

      <div v-else-if="errorCarga" style="text-align: center; background: #fff3f3; border: 1px solid #f5c6c6; padding: 20px; border-radius: 6px; color: #a12424;">
        El producto solicitado no existe o no se encuentra disponible en este momento.
      </div>

      <div v-else style="text-align: center; color: #7f8c8d; padding-top: 30px;">
        Consultando la ficha técnica del producto...
      </div>
    </div>
  </div>
</template>