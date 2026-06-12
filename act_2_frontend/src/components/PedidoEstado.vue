<script setup lang="ts">
import { onMounted, onUnmounted, ref } from 'vue'
import axios from 'axios'

const props = defineProps<{
  pedidoId: number | string
}>()

const emailListo = ref(false)
let intervalo: any = null

const verificarEstadoEmail = async () => {
  try {
    const { data } = await axios.get(`http://127.0.0.1:8000/api/pedidos/${props.pedidoId}`)
    
    emailListo.value = !!data.email_enviado_at 
    if (emailListo.value) {
      clearInterval(intervalo)
    }
  } catch (error) {
    console.error('Error al consultar el estado del envío:', error)
  }
}

onMounted(() => {
  intervalo = setInterval(verificarEstadoEmail, 3000)
})

onUnmounted(() => {
  if (intervalo) clearInterval(intervalo)
})
</script>

<template>
  <div style="padding: 25px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); text-align: center; max-width: 450px; margin: 30px auto; font-family: Arial, sans-serif;">
    
    <div v-if="!emailListo" class="estado procesando" style="color: #e67e22; font-size: 16px; font-weight: bold; padding: 15px; border: 1px dashed #e67e22; border-radius: 6px; background: #fdf6f0;">
      ⏳ Procesando tu pedido... 
      <span style="display: block; font-size: 13px; color: #7f8c8d; font-weight: normal; margin-top: 5px;">
        Guardando orden y preparando confirmación asíncrona.
      </span>
    </div>

    <div v-else class="estado listo" style="color: #27ae60; font-size: 16px; font-weight: bold; padding: 15px; border: 1px solid #27ae60; border-radius: 6px; background: #f4fbf7;">
      ✅ ¡Pedido confirmado! Revisa tu correo.
      <span style="display: block; font-size: 13px; color: #2c3e50; font-weight: normal; margin-top: 5px;">
        Tu notificación ha sido depositada en tu bandeja de Mailtrap.
      </span>
    </div>

  </div>
</template>