<script setup lang="ts">
import { onMounted } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'
import axios from 'axios'
// @ts-ignore
import ProductosList from '../components/ProductosList.vue'

const authStore = useAuthStore()
const router = useRouter()

onMounted(async () => {
  if (!authStore.user) {
    try {
      const response = await axios.get('http://127.0.0.1:8000/api/me')
      authStore.user = response.data
    } catch (err) {
      authStore.logout()
      router.push('/login')
    }
  }
})

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (err) {
    console.error('Error al cerrar sesión', err)
  }
}
</script>

<template>
  <div>
    <nav style="background: #333; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center;">
      <span style="font-weight: bold;">Panel de Control</span>
      <div v-if="authStore.user">
        <span style="margin-right: 15px;">Bienvenido, {{ authStore.user.name }}</span>
        <button @submit.prevent @click="handleLogout" style="background: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 4px;">
          Cerrar Sesión
        </button>
      </div>
    </nav>

    <div style="padding: 20px;">
      <ProductosList />
    </div>
  </div>
</template>