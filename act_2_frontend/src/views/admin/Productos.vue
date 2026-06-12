<script setup lang="ts">
import { onMounted, ref } from 'vue'
import axios from 'axios'
import { useAuthStore } from '../../stores/auth'
import AdminNotificaciones from '../../components/AdminNotificaciones.vue' // 📡 PRÁCTICA 12: Importación del escudo de alertas [cite: 108-110]

const authStore = useAuthStore()
const productos = ref<any[]>([])
const categorias = ref<any[]>([])
const loading = ref(false)

const mostrarModal = ref(false)
const esEdicion = ref(false)
const idSeleccionado = ref<number | null>(null)

const form = ref({
  nombre: '',
  descripcion: '',
  precio: 0,
  stock: 0,
  categoria_id: '' as string | number
})
const imagenFile = ref<any>(null)

const obtenerProductos = async () => {
  loading.value = true
  try {
    const { data } = await axios.get('http://127.0.0.1:8000/api/productos')
    productos.value = data.data || data
  } catch (error) {
    console.error('Error al cargar productos:', error)
  } finally {
    loading.value = false
  }
}

const obtenerCategorias = async () => {
  try {
    const { data } = await axios.get('http://127.0.0.1:8000/api/categorias')
    categorias.value = data.data || data
  } catch (error) {
    console.error('Error al cargar categorías:', error)
  }
}

const manejarImagen = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files && target.files.length > 0) {
    imagenFile.value = target.files[0]
  }
}

const abrirModalCrear = () => {
  esEdicion.value = false
  idSeleccionado.value = null
  form.value = { nombre: '', descripcion: '', precio: 0, stock: 0, categoria_id: '' }
  imagenFile.value = null
  mostrarModal.value = true
}

const abrirModalEditar = (producto: any) => {
  esEdicion.value = true
  idSeleccionado.value = producto.id
  form.value = {
    nombre: producto.nombre,
    descripcion: producto.descripcion || '',
    precio: producto.precio,
    stock: producto.stock,
    categoria_id: producto.categoria_id || ''
  }
  imagenFile.value = null
  mostrarModal.value = true
}

const guardarProducto = async () => {
  try {
    const formData = new FormData()
    formData.append('nombre', form.value.nombre)
    formData.append('descripcion', form.value.descripcion)
    formData.append('precio', form.value.precio.toString())
    formData.append('stock', form.value.stock.toString())
    formData.append('categoria_id', form.value.categoria_id.toString())
    
    if (imagenFile.value) {
      formData.append('imagen', imagenFile.value)
    }

    if (esEdicion.value && idSeleccionado.value) {
      formData.append('_method', 'PUT')
      await axios.post(`http://127.0.0.1:8000/api/productos/${idSeleccionado.value}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      alert('Producto actualizado con éxito.')
    } else {
      await axios.post('http://127.0.0.1:8000/api/productos', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })
      alert('Producto creado con éxito.')
    }
    
    mostrarModal.value = false
    obtenerProductos()
  } catch (error: any) {
    if (error.response && error.response.data.errors) {
      alert('Error de validación: ' + Object.values(error.response.data.errors).join(' '))
    } else {
      alert('No tienes los permisos requeridos para realizar esta acción.')
    }
  }
}

const eliminarProducto = async (id: number) => {
  if (confirm('¿Estás completamente seguro de eliminar este producto?')) {
    try {
      await axios.delete(`http://127.0.0.1:8000/api/productos/${id}`)
      alert('Producto eliminado con éxito.')
      obtenerProductos()
    } catch (error) {
      alert('No tienes permisos para eliminar este producto en el servidor.')
    }
  }
}

onMounted(() => {
  obtenerProductos()
  obtenerCategorias()
})
</script>

<template>
  <div style="padding: 20px; font-family: Arial, sans-serif;">
    
    <AdminNotificaciones />
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border-bottom: 2px solid #eee; padding-bottom: 15px;">
      <div>
        <h2 style="margin: 0; color: #2c3e50;">Panel de Gestión de Productos</h2>
        <p style="margin: 5px 0 0 0; color: #7f8c8d;">
          Usuario: <strong style="color: #333;">{{ authStore.user?.name }}</strong> 
        </p>
      </div>
      
      <div style="text-align: right;">
        <span style="font-size: 13px; color: #7f8c8d; display: block; margin-bottom: 4px;">Tu Rol de Acceso:</span>
        <span style="background: #34495e; color: white; padding: 6px 14px; border-radius: 20px; font-weight: bold; font-size: 14px; text-transform: uppercase; display: inline-block;">
          {{ authStore.user?.rol || 'cliente' }}
        </span>
      </div>
    </div>

    <div style="margin-bottom: 20px; text-align: right;">
      <button 
        v-can="'crear'" 
        @click="abrirModalCrear"
        style="background: #2ecc71; color: white; border: none; padding: 10px 20px; font-weight: bold; border-radius: 4px; cursor: pointer; font-size: 14px;"
      >
        + Nuevo Producto
      </button>
    </div>

    <div v-if="loading" style="text-align: center; color: #3498db; padding: 40px; font-weight: bold;">
      Cargando inventario...
    </div>

    <table v-else style="width: 100%; border-collapse: collapse; background: white; border: 1px solid #e1e8ed; border-radius: 6px; overflow: hidden;">
      <thead>
        <tr style="background: #f8f9fa; border-bottom: 2px solid #e1e8ed; text-align: left; color: #34495e;">
          <th style="padding: 12px 15px;">ID</th>
          <th style="padding: 12px 15px;">Nombre</th>
          <th style="padding: 12px 15px;">Categoría</th>
          <th style="padding: 12px 15px;">Descripción</th>
          <th style="padding: 12px 15px;">Precio</th>
          <th style="padding: 12px 15px;">Stock</th>
          <th style="padding: 12px 15px; text-align: center;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="p in productos" :key="p.id" style="border-bottom: 1px solid #e1e8ed; color: #2c3e50;">
          <td style="padding: 12px 15px; font-weight: bold; color: #7f8c8d;">#{{ p.id }}</td>
          <td style="padding: 12px 15px; font-weight: bold;">{{ p.nombre }}</td>
          <td style="padding: 12px 15px;">
            <span style="background: #e8f4fd; color: #1da1f2; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;">
              {{ p.categoria?.nombre || 'General' }}
            </span>
          </td>
          <td style="padding: 12px 15px; color: #555;">{{ p.descripcion || 'Sin descripción' }}</td>
          <td style="padding: 12px 15px; font-weight: bold;">${{ Number(p.precio).toFixed(2) }}</td>
          <td style="padding: 12px 15px;">{{ p.stock }} pzs</td>
          <td style="padding: 12px 15px; text-align: center;">
            <div style="display: flex; gap: 8px; justify-content: center;">
              
              <button 
                v-can="'editar'"
                @click="abrirModalEditar(p)"
                style="background: #f39c12; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;"
              >
                Editar
              </button>

              <button 
                v-can="'eliminar'"
                @click="eliminarProducto(p.id)"
                style="background: #e74c3c; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 13px; font-weight: bold;"
              >
                Eliminar
              </button>

            </div>
          </td>
        </tr>
        
        <tr v-if="productos.length === 0">
          <td colspan="7" style="text-align: center; color: #7f8c8d; padding: 30px;">
            No hay productos registrados en el inventario actual.
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="mostrarModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 999;">
      <div style="background: white; padding: 25px; border-radius: 8px; width: 450px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        <h3 style="margin-top: 0; color: #2c3e50; margin-bottom: 20px;">
          {{ esEdicion ? 'Modificar Producto' : 'Registrar Nuevo Producto' }}
        </h3>
        
        <form @submit.prevent="guardarProducto">
          <div style="margin-bottom: 12px;">
            <label style="display: block; margin-bottom: 4px; font-weight: bold; color: #34495e;">Nombre del Producto:</label>
            <input v-model="form.nombre" type="text" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
          </div>

          <div style="margin-bottom: 12px;">
            <label style="display: block; margin-bottom: 4px; font-weight: bold; color: #34495e;">Categoría:</label>
            <select v-model="form.categoria_id" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; background: white;">
              <option value="" disabled>-- Selecciona una categoría --</option>
              <option v-for="cat in categorias" :key="cat.id" :value="cat.id">
                {{ cat.nombre }}
              </option>
            </select>
          </div>

          <div style="margin-bottom: 12px;">
            <label style="display: block; margin-bottom: 4px; font-weight: bold; color: #34495e;">Descripción:</label>
            <textarea v-model="form.descripcion" rows="3" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; resize: vertical;"></textarea>
          </div>

          <div style="display: flex; gap: 12px; margin-bottom: 12px;">
            <div style="flex: 1;">
              <label style="display: block; margin-bottom: 4px; font-weight: bold; color: #34495e;">Precio ($):</label>
              <input v-model.number="form.precio" type="number" step="0.01" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>
            <div style="flex: 1;">
              <label style="display: block; margin-bottom: 4px; font-weight: bold; color: #34495e;">Stock Actual:</label>
              <input v-model.number="form.stock" type="number" required style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
            </div>
          </div>

          <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 4px; font-weight: bold; color: #34495e;">Imagen Ilustrativa:</label>
            <input @change="manejarImagen" type="file" accept="image/*" style="width: 100%; padding: 4px; box-sizing: border-box;">
            <small style="color: #95a5a6; display: block; margin-top: 2px;">Formatos permitidos: JPG, PNG, WEBP (Max 2MB)</small>
          </div>

          <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button type="button" @click="mostrarModal = false" style="background: #95a5a6; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">
              Cancelar
            </button>
            <button type="submit" style="background: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">
              {{ esEdicion ? 'Actualizar' : 'Guardar Producto' }}
            </button>
          </div>
        </form>
      </div>
    </div>

  </div>
</template>