import { watch, reactive } from 'vue'
import { useRoute, useRouter } from 'vue-router'

export function useFiltros() {
  const route  = useRoute()
  const router = useRouter()

  // Inicializamos todas las propiedades para que TypeScript conozca su estructura
  const filtros = reactive({
    busqueda:     (route.query.busqueda as string) || '',
    categoria_id: (route.query.categoria as string) || '',
    precio_min:   (route.query.min as string) || '',
    precio_max:   (route.query.max as string) || '',
    pagina:       Number(route.query.p) || 1,
    orden:        (route.query.orden as string) || 'nombre', // <- Agregado para TypeScript
    dir:          (route.query.dir as string) || 'asc',    // <- Agregado para TypeScript
  })

  // Sincroniza los filtros con la URL del navegador
  watch(filtros, () => {
    router.push({ query: {
      busqueda:  filtros.busqueda   || undefined,
      categoria: filtros.categoria_id || undefined,
      min:       filtros.precio_min || undefined,
      max:       filtros.precio_max || undefined,
      p:         filtros.pagina > 1 ? filtros.pagina : undefined,
      orden:     filtros.orden !== 'nombre' ? filtros.orden : undefined,
      dir:       filtros.dir !== 'asc' ? filtros.dir : undefined,
    }})
  })

  return { filtros }
}