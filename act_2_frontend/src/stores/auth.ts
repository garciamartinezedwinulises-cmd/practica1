import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as any,
    token: localStorage.getItem('token') || null,
    permisos: { crear: false, editar: false, eliminar: false }
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    async login(credentials: any) {
      const response = await axios.post('http://127.0.0.1:8000/api/login', credentials)
      this.token = response.data.token
      localStorage.setItem('token', this.token as string)
      
      // SOLUCIÓN: Traemos los permisos calculados del servidor inmediatamente
      await this.fetchUser()
    },
    async register(data: any) {
      const response = await axios.post('http://127.0.0.1:8000/api/register', data)
      this.token = response.data.token
      localStorage.setItem('token', this.token as string)
      
      // SOLUCIÓN: Sincronizamos los permisos tras el registro
      await this.fetchUser()
    },
    async logout() {
      try {
        await axios.post('http://127.0.0.1:8000/api/logout')
      } catch (error) {
        console.error('Error al invalidar token en el servidor:', error)
      } finally {
        this.token = null
        this.user = null
        this.permisos = { crear: false, editar: false, eliminar: false }
        localStorage.removeItem('token')
      }
    },
    async fetchUser() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/me')
        this.user = response.data
        
        this.permisos = response.data.permisos
      } catch (error) {
        this.logout()
        throw error
      }
    }
  }
})