<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { getProductos, deleteProducto } from '../services/productoService';
// @ts-ignore
import ProductoForm from './ProductoForm.vue';

const listaProductos = ref<any[]>([]);
const productoParaEditar = ref<any>(null);

async function obtenerLista() {
  try {
    const respuesta = await getProductos();
    listaProductos.value = respuesta.data || respuesta;
  } catch (error) {
    alert('No se pudo conectar con el servidor para obtener los productos.');
  }
}

async function removerProducto(id: number) {
  if (confirm('¿Estás seguro de que deseas eliminar este producto?')) {
    try {
      await deleteProducto(id);
      obtenerLista();
      if (productoParaEditar.value && productoParaEditar.value.id === id) {
        cancelarEdicion();
      }
    } catch (error) {
      alert('Error al intentar eliminar el producto.');
    }
  }
}

function seleccionarProducto(producto: any) {
  productoParaEditar.value = producto;
}

function cancelarEdicion() {
  productoParaEditar.value = null;
}

onMounted(() => {
  obtenerLista();
});
</script>

<template>
  <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
    <h2>Catálogo de Productos</h2>
    
    <ProductoForm 
      :productoEdit="productoParaEditar" 
      @saved="obtenerLista" 
      @cancel="cancelarEdicion" 
    />

    <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
      <thead>
        <tr style="background-color: #f2f2f2;">
          <th style="padding: 8px;">ID</th>
          <th style="padding: 8px;">Nombre</th>
          <th style="padding: 8px;">Descripción</th>
          <th style="padding: 8px;">Precio</th>
          <th style="padding: 8px;">Stock</th>
          <th style="padding: 8px;">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="producto in listaProductos" :key="producto.id">
          <td style="padding: 8px;">{{ producto.id }}</td>
          <td style="padding: 8px;">{{ producto.nombre }}</td>
          <td style="padding: 8px;">{{ producto.descripcion || 'Sin descripción' }}</td>
          <td style="padding: 8px;">${{ Number(producto.precio).toFixed(2) }}</td>
          <td style="padding: 8px;">{{ producto.stock }}</td>
          <td style="padding: 8px;">
            <button @click="seleccionarProducto(producto)" style="margin-right: 5px; cursor: pointer;">Editar</button>
            <button @click="removerProducto(producto.id)" style="cursor: pointer; background-color: #ffcccc; border: 1px solid #cc0000;">Eliminar</button>
          </td>
        </tr>
        <tr v-if="listaProductos.length === 0">
          <td colspan="6" style="padding: 8px; text-align: center;">No hay productos disponibles.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>