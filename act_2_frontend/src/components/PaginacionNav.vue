<script setup lang="ts">

defineProps<{
  meta: {
    current_page?: number
    last_page?: number
    per_page?: number
    total?: number
  }
}>()

const emit = defineEmits<{
  (e: 'cambio-pagina', pagina: number): void
}>()

const irAPagina = (pagina: number) => {
  emit('cambio-pagina', pagina)
  // Desplazamiento suave hacia arriba para mejorar la experiencia de usuario
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
</script>

<template>
  <div v-if="meta && meta.last_page && meta.last_page > 1" style="display: flex; justify-content: center; align-items: center; gap: 8px; font-family: Arial, sans-serif;">
    
    <button 
      @click="irAPagina(1)" 
      :disabled="meta.current_page === 1"
      style="padding: 8px 12px; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer; font-weight: bold;"
      :style="{ opacity: meta.current_page === 1 ? '0.5' : '1', cursor: meta.current_page === 1 ? 'not-allowed' : 'pointer' }"
    >
      « Primera
    </button>

    <button 
      @click="irAPagina(meta.current_page! - 1)" 
      :disabled="meta.current_page === 1"
      style="padding: 8px 12px; border: 1px solid #ccc; background: white; border-radius: 4px; font-weight: bold;"
      :style="{ opacity: meta.current_page === 1 ? '0.5' : '1', cursor: meta.current_page === 1 ? 'not-allowed' : 'pointer' }"
    >
      ‹ Anterior
    </button>
    
    <span style="font-size: 14px; color: #2c3e50; padding: 0 10px; font-weight: bold;">
      Página {{ meta.current_page }} de {{ meta.last_page }}
    </span>

    <button 
      @click="irAPagina(meta.current_page! + 1)" 
      :disabled="meta.current_page === meta.last_page"
      style="padding: 8px 12px; border: 1px solid #ccc; background: white; border-radius: 4px; font-weight: bold;"
      :style="{ opacity: meta.current_page === meta.last_page ? '0.5' : '1', cursor: meta.current_page === meta.last_page ? 'not-allowed' : 'pointer' }"
    >
      Siguiente ›
    </button>

    <button 
      @click="irAPagina(meta.last_page)" 
      :disabled="meta.current_page === meta.last_page"
      style="padding: 8px 12px; border: 1px solid #ccc; background: white; border-radius: 4px; cursor: pointer; font-weight: bold;"
      :style="{ opacity: meta.current_page === meta.last_page ? '0.5' : '1', cursor: meta.current_page === meta.last_page ? 'not-allowed' : 'pointer' }"
    >
      Última »
    </button>

  </div>
</template>