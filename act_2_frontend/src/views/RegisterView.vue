<script setup lang="ts">
import { ref } from 'vue'
import { useAuthStore } from '../stores/auth'
import { useRouter } from 'vue-router'

const name = ref('')
const email = ref('')
const password = ref('')
const password_confirmation = ref('')
const errorMsg = ref('')
const loading = ref(false)

const authStore = useAuthStore()
const router = useRouter()

const handleRegister = async () => {
  errorMsg.value = ''
  if (password.value !== password_confirmation.value) {
    errorMsg.value = 'Las contraseñas no coinciden.'
    return
  }
  loading.value = true
  try {
    await authStore.register({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: password_confirmation.value
    })
    router.push('/dashboard')
  } catch (err: any) {
    errorMsg.value = err.response?.data?.message || 'Error en el registro'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div style="max-width: 400px; margin: 50px auto; padding: 20px; border: 1px solid #ccc; background: #fff; border-radius: 8px;">
    <h2>Registro de Usuario</h2>
    <form @submit.prevent="handleRegister">
      <div style="margin-bottom: 15px;">
        <label style="display: block; margin-bottom: 5px;">Nombre:</label>
        <input type="text" v-model="name" required style="width: 100%; padding: 8px; box-sizing: border-box;" />
      </div>
      <div style="margin-bottom: 15px;">
        <label style="display: block; margin-bottom: 5px;">Correo Electrónico:</label>
        <input type="email" v-model="email" required style="width: 100%; padding: 8px; box-sizing: border-box;" />
      </div>
      <div style="margin-bottom: 15px;">
        <label style="display: block; margin-bottom: 5px;">Contraseña:</label>
        <input type="password" v-model="password" required style="width: 100%; padding: 8px; box-sizing: border-box;" />
      </div>
      <div style="margin-bottom: 15px;">
        <label style="display: block; margin-bottom: 5px;">Confirmar Contraseña:</label>
        <input type="password" v-model="password_confirmation" required style="width: 100%; padding: 8px; box-sizing: border-box;" />
      </div>
      <p v-if="errorMsg" style="color: red; font-size: 14px;">{{ errorMsg }}</p>
      <button type="submit" :disabled="loading" style="background: #28a745; color: white; border: none; padding: 10px 15px; cursor: pointer; border-radius: 4px; width: 100%;">
        {{ loading ? 'Registrando...' : 'Registrarme' }}
      </button>
    </form>
    <p style="margin-top: 15px; font-size: 14px; text-align: center;">
      ¿Ya tienes cuenta? <router-link to="/login">Inicia sesión</router-link>
    </p>
  </div>
</template>