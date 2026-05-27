import { defineStore } from 'pinia'

export const useCarritoStore = defineStore('carrito', {
  state: () => ({
    items: JSON.parse(localStorage.getItem('carrito') || '[]') as any[]
  }),

  getters: {
    totalItems: (state) => state.items.reduce((s, i) => s + i.cantidad, 0),
    totalPrecio: (state) => state.items.reduce((s, i) => s + i.precio * i.cantidad, 0),
    cantidadDeProducto: (state) => (id: number) =>
      state.items.find(i => i.id === id)?.cantidad || 0,
  },

  actions: {
    agregar(producto: any) {
      const existe = this.items.find(i => i.id === producto.id)
      if (existe) {
        existe.cantidad++
      } else {
        this.items.push({ ...producto, cantidad: 1 })
      }
      this.guardarLocalStorage()
    },
    quitar(id: number) {
      this.items = this.items.filter(i => i.id !== id)
      this.guardarLocalStorage()
    },
    cambiarCantidad(id: number, cantidad: number) {
      const producto = this.items.find(i => i.id === id)
      if (producto) {
        producto.cantidad = cantidad
        if (producto.cantidad <= 0) {
          this.quitar(id)
        }
      }
      this.guardarLocalStorage()
    },
    vaciar() {
      this.items = []
      localStorage.removeItem('carrito')
    },
    guardarLocalStorage() {
      localStorage.setItem('carrito', JSON.stringify(this.items))
    }
  }
})