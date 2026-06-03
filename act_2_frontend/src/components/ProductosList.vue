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
}

const productos = ref<Producto[]>([])
const imagen = ref<File | null>(null)
const preview = ref<string | null>(null)
const loading = ref(false)
const mensajeExito = ref('')
const mensajeError = ref('')

const form = reactive({
  nombre: '',
  descripcion: '',
  precio: '',
  stock: ''
})

const cargarProductos = async () => {
  loading.value = true
  try {
    const response = await axios.get('http://127.0.0.1:8000/api/productos')
    productos.value = response.data.data || response.data
  } catch (error) {
    console.error('Error al traer los productos:', error)
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

  // Validaciones en el frontend antes del envío
  if (!form.nombre.trim() || !form.precio || !form.stock) {
    mensajeError.value = 'Por favor, completa todos los campos obligatorios del formulario.'
    return
  }

  if (Number(form.precio) < 0 || Number(form.stock) < 0) {
    mensajeError.value = 'El precio y las existencias no pueden tomar valores negativos.'
    return
  }

  loading.value = true

  // Construcción del objeto FormData para transporte de binarios
  const fd = new FormData()
  fd.append('nombre', form.nombre)
  fd.append('descripcion', form.descripcion)
  fd.append('precio', form.precio)
  fd.append('stock', form.stock)
  if (imagen.value) {
    fd.append('imagen', imagen.value)
  }

  try {
    await axios.post('http://127.0.0.1:8000/api/productos', fd, {
      headers: { 'Content-Type': 'multipart/form-data' }
    })
    
    mensajeExito.value = 'El producto ha sido registrado correctamente en el catálogo.'
    
    // Limpieza del formulario y de las vistas previas
    form.nombre = ''
    form.descripcion = ''
    form.precio = ''
    form.stock = ''
    imagen.value = null
    preview.value = null
    
    // Recarga de la tabla y activación del auto-cierre del aviso
    await cargarProductos()
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
    await cargarProductos()
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
  cargarProductos()
})
</script>

<template>
  <div style="font-family: Arial, sans-serif; color: #2c3e50;">
    <h2 style="margin-bottom: 20px; border-bottom: 2px solid #e1e8ed; padding-bottom: 10px;">Gestión de Productos Completa</h2>

    <div v-if="mensajeExito" style="background: #e8f8f0; color: #1e7e34; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
      {{ mensajeExito }}
    </div>
    <div v-if="mensajeError" style="background: #fff3f3; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 20px; border: 1px solid #f5c6c6;">
      {{ mensajeError }}
    </div>

    <div v-if="loading" style="background: rgba(255,255,255,0.8); color: #3498db; text-align: center; padding: 10px; margin-bottom: 15px; font-weight: bold; border-radius: 4px;">
      Sincronizando información con el servidor Laravel...
    </div>

    <form @submit.prevent="guardarProducto" style="background: #f8f9fa; padding: 20px; border-radius: 6px; border: 1px solid #e1e8ed; margin-bottom: 35px; display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
      <div>
        <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Nombre del Artículo *</label>
        <input v-model="form.nombre" type="text" placeholder="Ej. Monitor Gamer" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" />
      </div>
      
      <div>
        <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Precio de Venta *</label>
        <input v-model="form.precio" type="number" step="0.01" placeholder="0.00" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" />
      </div>

      <div>
        <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Existencias en Almacén *</label>
        <input v-model="form.stock" type="number" placeholder="0" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" />
      </div>

      <div>
        <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Fotografía de Portada</label>
        <input type="file" accept="image/*" @change="onImageChange" style="width: 100%; padding: 6px; font-size: 13px;" />
      </div>

      <div style="grid-column: span 2;">
        <label style="display: block; margin-bottom: 5px; font-weight: bold; font-size: 14px;">Descripción General</label>
        <textarea v-model="form.descripcion" placeholder="Detalles técnicos o de presentación del producto..." rows="3" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; resize: vertical;"></textarea>
      </div>

      <div v-if="preview" style="grid-column: span 2; background: white; padding: 10px; border-radius: 4px; border: 1px dashed #ccc; text-align: center;">
        <span style="display: block; font-size: 12px; color: #7f8c8d; margin-bottom: 5px;">Vista previa de la imagen cargada:</span>
        <img :src="preview" alt="Preview de portada" style="max-width: 150px; max-height: 150px; object-fit: contain; border-radius: 4px;" />
      </div>

      <div style="grid-column: span 2; text-align: right; margin-top: 5px;">
        <button type="submit" :disabled="loading" style="background: #2ecc71; color: white; border: none; padding: 10px 20px; font-weight: bold; border-radius: 4px; cursor: pointer;">
          Guardar Nuevo Producto
        </button>
      </div>
    </form>

    <h3 style="margin-bottom: 15px; color: #34495e;">Listado de Productos en Sistema</h3>
    <table style="width: 100%; border-collapse: collapse; text-align: left; background: white; border: 1px solid #e1e8ed;">
      <thead>
        <tr style="background: #f1f2f6; border-bottom: 2px solid #e1e8ed;">
          <th style="padding: 10px; width: 80px;">Miniatura</th>
          <th style="padding: 10px;">Producto</th>
          <th style="padding: 10px;">Precio</th>
          <th style="padding: 10px;">Stock</th>
          <th style="padding: 10px; text-align: center;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="prod in productos" :key="prod.id" style="border-bottom: 1px solid #e1e8ed;">
          <td style="padding: 10px; text-align: center;">
            <img :src="prod.imagen_url || 'https://via.placeholder.com/50?text=Sin+Foto'" alt="Miniatura" style="width: 45px; height: 45px; object-fit: contain; border-radius: 4px; border: 1px solid #f1f2f6;" />
          </td>
          <td style="padding: 10px; font-weight: bold;">{{ prod.nombre }}</td>
          <td style="padding: 10px;">${{ Number(prod.precio).toFixed(2) }}</td>
          <td style="padding: 10px;">{{ prod.stock }} pzs</td>
          <td style="padding: 10px; text-align: center;">
            <button @click="eliminarProducto(prod.id)" style="background: #e74c3c; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;">
              Eliminar
            </button>
          </td>
        </tr>
        <tr v-if="productos.length === 0">
          <td colspan="5" style="text-align: center; padding: 20px; color: #7f8c8d;">No hay productos registrados en la base de datos.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>