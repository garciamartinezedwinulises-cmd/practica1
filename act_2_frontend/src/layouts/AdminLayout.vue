<script setup lang="ts">
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const authStore = useAuthStore()
const router = useRouter()

const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
  } catch (error) {
    console.error('Error al cerrar sesión:', error)
  }
}
</script>

<template>
  <div style="display: flex; min-height: 100vh; font-family: Arial, sans-serif;">
    <aside style="width: 250px; background: #2c3e50; color: white; padding: 20px; display: flex; flex-direction: column; justify-content: space-between;">
      <div>
        <h3 style="margin-top: 0; padding-bottom: 15px; border-bottom: 1px solid #34495e;">Panel Admin</h3>
        <p style="font-size: 14px; color: #bdc3c7;" v-if="authStore.user">
          Usuario: {{ authStore.user.name }}
        </p>
        <nav style="display: flex; flex-direction: column; gap: 10px; margin-top: 30px;">
          <router-link to="/admin" style="color: white; text-decoration: none; padding: 10px; background: #34495e; border-radius: 4px;">
            Inicio Panel
          </router-link>
          <router-link to="/admin/productos" style="color: white; text-decoration: none; padding: 10px; background: #34495e; border-radius: 4px;">
            Gestionar Productos
          </router-link>
          <router-link to="/" style="color: #4ab3f4; text-decoration: none; padding: 10px; margin-top: 20px; font-size: 14px;">
            ← Ir a vista pública
          </router-link>
        </nav>
      </div>

      <button @click="handleLogout" style="background: #e74c3c; color: white; border: none; padding: 10px; cursor: pointer; border-radius: 4px; width: 100%; font-weight: bold;">
        Cerrar Sesión
      </button>
    </aside>

    <main style="flex: 1; padding: 30px; background: #f8f9fa;">
      <RouterView />
    </main>
  </div>
</template>