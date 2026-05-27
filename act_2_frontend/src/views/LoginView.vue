<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter, useRoute } from 'vue-router'

const email = ref('')
const password = ref('')
const errorMsg = ref('')
const loading = ref(false)

const authStore = useAuthStore()
const router = useRouter()
const route = useRoute()

const handleLogin = async () => {
  errorMsg.value = ''
  loading.value = true
  try {
    await authStore.login({ email: email.value, password: password.value })
    
    const redirectPath = (route.query.redirect as string) || '/admin'
    router.push(redirectPath)
  } catch (err: any) {
    errorMsg.value = err.response?.data?.message || 'Error al iniciar sesión'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; background: #fff; border-radius: 8px;">
    <h2>Iniciar Sesión</h2>
    <form @submit.prevent="handleLogin">
      <div style="margin-bottom: 15px;">
        <label style="display: block; margin-bottom: 5px;">Correo Electrónico:</label>
        <input type="email" v-model="email" required style="width: 100%; padding: 8px; box-sizing: border-box;" />
      </div>
      <div style="margin-bottom: 15px;">
        <label style="display: block; margin-bottom: 5px;">Contraseña:</label>
        <input type="password" v-model="password" required style="width: 100%; padding: 8px; box-sizing: border-box;" />
      </div>
      <p v-if="errorMsg" style="color: red; font-size: 14px;">{{ errorMsg }}</p>
      <button type="submit" :disabled="loading" style="background: #4ab3f4; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; width: 100%;">
        {{ loading ? 'Cargando...' : 'Entrar' }}
      </button>
    </form>
    <p style="margin-top: 15px; font-size: 14px; text-align: center;">
      ¿No tienes cuenta? <router-link to="/register">Regístrate aquí</router-link>
    </p>
  </div>
</template>