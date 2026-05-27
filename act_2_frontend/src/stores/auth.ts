import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null as any,
    token: localStorage.getItem('token') || (null as string | null),
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    async login(credentials: any) {
      const response = await axios.post('http://127.0.0.1:8000/api/login', credentials)
      this.token = response.data.token
      this.user = response.data.user
      localStorage.setItem('token', this.token as string)
    },
    async register(data: any) {
      const response = await axios.post('http://127.0.0.1:8000/api/register', data)
      this.token = response.data.token
      this.user = response.data.user
      localStorage.setItem('token', this.token as string)
    },
    async logout() {
      await axios.post('http://127.0.0.1:8000/api/logout')
      this.token = null
      this.user = null
      localStorage.removeItem('token')
    },
    async fetchUser() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/me')
        this.user = response.data
      } catch (error) {
        this.logout()
        throw error
      }
    }
  }
})