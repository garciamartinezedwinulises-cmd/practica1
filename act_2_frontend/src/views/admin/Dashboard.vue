<script setup lang="ts">
import { useAuthStore } from '../../stores/auth'

const authStore = useAuthStore()
</script>

<template>
  <div style="background: white; padding: 30px; border-radius: 8px; border: 1px solid #e1e8ed;">
    <h2 style="margin-top: 0; color: #2c3e50;">Bienvenido al Panel de Control</h2>
    <p style="color: #7f8c8d; line-height: 1.6;" v-if="authStore.user">
      Hola, {{ authStore.user.name }}. Desde esta sección puedes navegar hacia la pestaña de gestión para añadir nuevos elementos, modificar existencias o eliminar registros del catálogo general.
    </p>
  </div>
</template> 