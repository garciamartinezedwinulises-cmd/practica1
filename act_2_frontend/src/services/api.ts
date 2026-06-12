import axios from 'axios'

// Leemos las variables de entorno configuradas [cite: 115, 117]
const API_URL = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000'
const API_VERSION = import.meta.env.VITE_API_VERSION || 'v1'

const api = axios.create({
  baseURL: `${API_URL}/api/${API_VERSION}`, // Genera automáticamente: http://127.0.0.1:8000/api/v1 [cite: 117]
})

// Inyectamos automáticamente el token de Sanctum si el usuario ya inició sesión
api.interceptors.request.use((config) => {
  const token = localStorage.getItem('token') // O como recuperes tu token de Pinia/localStorage
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

export default api // [cite: 120]