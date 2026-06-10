import { useAuthStore } from '../stores/auth'

// Función auxiliar para evaluar la visibilidad en cualquier momento
const verificarPermiso = (el: HTMLElement, binding: any) => {
  const auth = useAuthStore()
  const permiso = binding.value

  
  if (auth.permisos && auth.permisos[permiso as keyof typeof auth.permisos]) {
    el.style.display = '' 
  } else {
    el.style.display = 'none' 
  }
}

export const vCan = {
  mounted(el: HTMLElement, binding: any) {
    verificarPermiso(el, binding)
  },
  
  updated(el: HTMLElement, binding: any) {
    verificarPermiso(el, binding)
  }
}