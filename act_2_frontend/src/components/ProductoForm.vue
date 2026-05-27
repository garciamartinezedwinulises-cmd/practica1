<script setup>
import { ref, watch } from 'vue';
import { createProducto, updateProducto } from '../services/productoService';

const props = defineProps({
  productoEdit: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['saved', 'cancel']);

const form = ref({
  nombre: '',
  descripcion: '',
  precio: 0,
  stock: 0
});

const mensajeExito = ref('');
const mensajeError = ref('');

watch(() => props.productoEdit, (nuevoValor) => {
  if (nuevoValor) {
    form.value = { ...nuevoValor };
  } else {
    reiniciarFormulario();
  }
}, { immediate: true });

function reiniciarFormulario() {
  form.value = { nombre: '', descripcion: '', precio: 0, stock: 0 };
}

async function enviarFormulario() {
  mensajeExito.value = '';
  mensajeError.value = '';
  
  try {
    if (props.productoEdit) {
      await updateProducto(props.productoEdit.id, form.value);
      mensajeExito.value = 'El producto se actualizó correctamente.';
    } else {
      await createProducto(form.value);
      mensajeExito.value = 'El producto se creó correctamente.';
      reiniciarFormulario();
    }
    emit('saved');
  } catch (error) {
    mensajeError.value = 'Ocurrió un error al procesar la solicitud. Verifica los datos.';
  }
}
</script>

<template>
  <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #ccc; border-radius: 5px;">
    <h3>{{ productoEdit ? 'Editar Producto' : 'Agregar Nuevo Producto' }}</h3>
    
    <p v-if="mensajeExito" style="color: green;">{{ mensajeExito }}</p>
    <p v-if="mensajeError" style="color: red;">{{ mensajeError }}</p>

    <form @submit.prevent="enviarFormulario">
      <div style="margin-bottom: 10px;">
        <label>Nombre: </label>
        <input v-model="form.nombre" type="text" required style="width: 100%; padding: 5px;" />
      </div>
      
      <div style="margin-bottom: 10px;">
        <label>Descripción: </label>
        <textarea v-model="form.descripcion" style="width: 100%; padding: 5px;"></textarea>
      </div>
      
      <div style="margin-bottom: 10px;">
        <label>Precio: </label>
        <input v-model.number="form.precio" type="number" step="0.01" required style="width: 100%; padding: 5px;" />
      </div>
      
      <div style="margin-bottom: 10px;">
        <label>Stock: </label>
        <input v-model.number="form.stock" type="number" required style="width: 100%; padding: 5px;" />
      </div>

      <button type="submit" style="padding: 5px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer;">
        {{ productoEdit ? 'Actualizar' : 'Guardar' }}
      </button>
      
      <button v-if="productoEdit" type="button" @click="emit('cancel')" style="margin-left: 10px; padding: 5px 15px; background-color: #f44336; color: white; border: none; cursor: pointer;">
        Cancelar
      </button>
    </form>
  </div>
</template>