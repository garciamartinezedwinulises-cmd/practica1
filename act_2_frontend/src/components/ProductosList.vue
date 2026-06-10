<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'

interface Producto {
  id: number
  nombre: string
  descripcion: string
  precio: number
  stock: number
  imagen_url: string | null
  categoria?: {
    id: number
    nombre: string
  } | null
}

interface Categoria {
  id: number
  nombre: string
}

const productos = ref<Producto[]>([])
const categories = ref<Categoria[]>([])
const categorias = ref<Categoria[]>([])
const imagen = ref<File | null>(null)
const preview = ref<string | null>(null)
const loading = ref(false)
const mensajeExito = ref('')
const mensajeError = ref('')

const form = reactive({
  nombre: '',
  descripcion: '',
  precio: '',
  stock: '',
  categoria_id: ''
})

const cargarDatosIniciales = async () => {
  loading.value = true
  try {
    const [resProductos, resCategorias] = await Promise.all([
      axios.get('http://127.0.0.1:8000/api/productos'),
      axios.get('http://127.0.0.1:8000/api/categorias')
    ])
    
    productos.value = resProductos.data.data || resProductos.data
    categorias.value = resCategorias.data.data || resCategorias.data
  } catch (error) {
    console.error('Error al sincronizar con el servidor:', error)
    mensajeError.value = 'No se pudieron cargar los catálogos base del sistema.'
  } finally {
    loading.value = false
  }
}

const onImageChange = (e: Event) => {
  const target = e.target as HTMLInputElement
  const file = target.files?.[0]
  if (!file) return
  imagen.value = file
  preview.value = URL.createObjectURL(file)
}

const guardarProducto = async () => {
  mensajeExito.value = ''
  mensajeError.value = ''

  if (!form.nombre.trim() || !form.precio || !form.stock) {
    mensajeError.value = 'Por favor, completa todos los campos obligatorios del formulario.'
    return
  }

  if (Number(form.precio) < 0 || Number(form.stock) < 0) {
    mensajeError.value = 'El precio y las existencias no pueden tomar valores negativos.'
    return
  }

  loading.value = true

  const fd = new FormData()
  fd.append('nombre', form.nombre)
  fd.append('descripcion', form.descripcion)
  fd.append('precio', form.precio)
  fd.append('stock', form.stock)
  
  if (form.categoria_id) {
    fd.append('categoria_id', form.categoria_id)
  }
  
  if (imagen.value) {
    fd.append('imagen', imagen.value)
  }

  try {
    await axios.post('http://127.0.0.1:8000/api/productos', fd, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    
    mensajeExito.value = 'El producto ha sido registrado correctamente con su categoría.'
    
    form.nombre = ''
    form.descripcion = ''
    form.precio = ''
    form.stock = ''
    form.categoria_id = ''
    imagen.value = null
    preview.value = null
    
    await cargarDatosIniciales()
    setTimeout(() => {
      mensajeExito.value = ''
    }, 3500)
  } catch (error: any) {
    mensajeError.value = error.response?.data?.message || 'Ocurrió un inconveniente al guardar el artículo.'
  } finally {
    loading.value = false
  }
}

const eliminarProducto = async (id: number) => {
  if (!confirm('¿Estás seguro de que deseas dar de baja este artículo?')) return
  
  loading.value = true
  try {
    await axios.delete(`http://127.0.0.1:8000/api/productos/${id}`)
    mensajeExito.value = 'Artículo removido con éxito.'
    await cargarDatosIniciales()
    setTimeout(() => {
      mensajeExito.value = ''
    }, 3500)
  } catch (error) {
    mensajeError.value = 'No se pudo eliminar el artículo seleccionado.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  cargarDatosIniciales()
})
</script>

<template>
  <div style="font-family: Arial, sans-serif; color: #2c3e50;">
    <h2 style="margin-bottom: 20px; border-bottom: 2px solid #e1e8ed; padding-bottom: 10px;">Gestión de Catálogo por Categorías</h2>

    <div v-if="mensajeExito" style="background: #e8f8f0; color: #1e7e34; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
      {{ mensajeExito }}
    </div>
    <div v-if="mensajeError" style="background: #fff3f3; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6c6;">
      {{ mensajeError }}
    </div>

    <div v-if="loading" style="background: rgba(255,255,255,0.8); color: #3498db; text-align: center; padding: 10px; margin-bottom: 15px; font-weight: bold; border-radius: 4px;">
      Sincronizando información...
    </div>

    <form @submit.prevent="guardarProducto" style="background: #f8f9fa; padding: 20px; border-radius: 6px; border: 1px solid #e1e8ed; margin-bottom: 35px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
      <div>
        <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Nombre del Artículo *</label>
        <input v-model="form.nombre" type="text" placeholder="Ej. Camiseta Deportiva" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" />
      </div>
      
      <div>
        <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Asignar Categoría</label>
        <select v-model="form.categoria_id" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; background: white; height: 35px;">
          <option value="">Sin categoría</option>
          <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
            {{ cat.nombre }}
          </option>
        </select>
      </div>

      <div>
        <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Precio de Venta *</label>
        <input v-model="form.precio" type="number" step="0.01" placeholder="0.00" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" />
      </div>

      <div>
        <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Existencias en Almacén *</label>
        <input v-model="form.stock" type="number" placeholder="0" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" />
      </div>

      <div style="grid-column: span 2;">
        <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Fotografía de Portada</label>
        <input type="file" accept="image/*" @change="onImageChange" style="width: 100%; padding: 6px; font-size: 13px;" />
      </div>

      <div style="grid-column: span 2;">
        <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Descripción General</label>
        <textarea v-model="form.descripcion" placeholder="Detalles del artículo..." rows="2" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; resize: vertical;"></textarea>
      </div>

      <div v-if="preview" style="grid-column: span 2; background: white; padding: 10px; border-radius: 4px; border: 1px dashed #ccc; text-align: center;">
        <img :src="preview" alt="Preview" style="max-width: 120px; max-height: 120px; object-fit: contain;" />
      </div>

      <div style="grid-column: span 2; text-align: right;">
        <button type="submit" :disabled="loading" style="background: #2ecc71; color: white; border: none; padding: 10px 20px; font-weight: bold; border-radius: 4px; cursor: pointer;">
          Guardar Nuevo Producto
        </button>
      </div>
    </form>

    <h3 style="margin-bottom: 15px; color: #34495e;">Listado General</h3>
    <table style="width: 100%; border-collapse: collapse; text-align: left; background: white; border: 1px solid #e1e8ed;">
      <thead>
        <tr style="background: #f1f2f6; border-bottom: 2px solid #e1e8ed;">
          <th style="padding: 10px; width: 70px;">Foto</th>
          <th style="padding: 10px;">Producto</th>
          <th style="padding: 10px;">Categoría</th>
          <th style="padding: 10px;">Precio</th>
          <th style="padding: 10px; text-align: center;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="prod in productos" :key="prod.id" style="border-bottom: 1px solid #e1e8ed;">
          <td style="padding: 10px; text-align: center;">
            <img :src="prod.imagen_url || 'https://via.placeholder.com/40?text=Sin+Foto'" alt="Miniatura" style="width: 40px; height: 40px; object-fit: contain; border-radius: 4px;" />
          </td>
          <td style="padding: 10px; font-weight: bold;">{{ prod.nombre }}</td>
          <td style="padding: 10px; color: #7f8c8d;">
            {{ prod.categoria?.nombre || 'General / Sin Categoría' }}
          </td>
          <td style="padding: 10px;">${{ Number(prod.precio).toFixed(2) }}</td>
          <td style="padding: 10px; text-align: center;">
            <button @click="eliminarProducto(prod.id)" style="background: #e74c3c; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;">
              Eliminar
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>