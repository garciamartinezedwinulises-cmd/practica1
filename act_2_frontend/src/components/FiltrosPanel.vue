<script setup lang="ts">
import { ref, watch, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps<{
  filtros: any
}>()

interface Categoria {
  id: number
  nombre: string
}

const categorias = ref<Categoria[]>([])
const localBusqueda = ref(props.filtros.busqueda)
const ordenSeleccionado = ref('nombre-asc')
let timeoutId: any = null

// 1. Cargar las categorías directamente desde la API
onMounted(async () => {
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/categorias')
    categorias.value = response.data.data || response.data
  } catch (error) {
    console.error('Error al cargar categorías en el panel:', error)
  }
})

// 2. Implementación del Debounce de 300ms para evitar saturar el servidor
watch(localBusqueda, (newValue) => {
  if (timeoutId) clearTimeout(timeoutId)
  timeoutId = setTimeout(() => {
    props.filtros.busqueda = newValue
    props.filtros.pagina = 1 
  }, 300)
})

// Sincronizar el input local si los filtros se limpian externamente
watch(() => props.filtros.busqueda, (newValue) => {
  localBusqueda.value = newValue
})

// 3. Controlar las variables de ordenamiento para Laravel
watch(ordenSeleccionado, (val) => {
  const [orden, dir] = val.split('-')
  props.filtros.orden = orden
  props.filtros.dir = dir
  props.filtros.pagina = 1
})

// 4. Función para restablecer todos los valores a su estado original
const limpiarFiltros = () => {
  localBusqueda.value = ''
  props.filtros.busqueda = ''
  props.filtros.categoria_id = ''
  props.filtros.precio_min = ''
  props.filtros.precio_max = ''
  props.filtros.pagina = 1
  ordenSeleccionado.value = 'nombre-asc'
  props.filtros.orden = 'nombre'
  props.filtros.dir = 'asc'
}
</script>

<template>
  <div style="background: #f8f9fa; border: 1px solid #e1e8ed; padding: 20px; border-radius: 8px; font-family: Arial, sans-serif;">
    <h3 style="margin-top: 0; margin-bottom: 20px; color: #2c3e50; border-bottom: 2px solid #e1e8ed; padding-bottom: 8px;">Filtros</h3>

    <div style="margin-bottom: 20px;">
      <label style="display: block; margin-bottom: 6px; font-weight: bold; font-size: 13px; color: #555;">Buscar artículo</label>
      <input 
        type="text" 
        v-model="localBusqueda" 
        placeholder="Ej. Camisa, laptop..." 
        style="width: 100%; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px;"
      />
    </div>

    <div style="margin-bottom: 20px;">
      <label style="display: block; margin-bottom: 6px; font-weight: bold; font-size: 13px; color: #555;">Categoría</label>
      <select 
        v-model="props.filtros.categoria_id" 
        style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; background: white; font-size: 14px; height: 35px;"
      >
        <option value="">Todas las categorías</option>
        <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
          {{ cat.nombre }}
        </option>
      </select>
    </div>

    <div style="margin-bottom: 20px;">
      <label style="display: block; margin-bottom: 6px; font-weight: bold; font-size: 13px; color: #555;">Rango de Precio</label>
      <div style="display: flex; gap: 10px; align-items: center;">
        <input 
          type="number" 
          v-model="props.filtros.precio_min" 
          placeholder="Mín" 
          style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px;"
        />
        <span style="color: #7f8c8d;">-</span>
        <input 
          type="number" 
          v-model="props.filtros.precio_max" 
          placeholder="Máx" 
          style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; font-size: 14px;"
        />
      </div>
    </div>

    <div style="margin-bottom: 25px;">
      <label style="display: block; margin-bottom: 6px; font-weight: bold; font-size: 13px; color: #555;">Ordenar por</label>
      <select 
        v-model="ordenSeleccionado" 
        style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; background: white; font-size: 14px; height: 35px;"
      >
        <option value="nombre-asc">Nombre (A-Z)</option>
        <option value="precio-asc">Precio: Menor a Mayor</option>
        <option value="precio-desc">Precio: Mayor a Menor</option>
      </select>
    </div>

    <button 
      @click="limpiarFiltros" 
      style="width: 100%; background: #e74c3c; color: white; border: none; padding: 10px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 14px;"
    >
      Limpiar Filtros
    </button>
  </div>
</template>