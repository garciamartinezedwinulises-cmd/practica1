import axios from 'axios'

const API_URL = 'http://127.0.0.1:8000/api/productos'

export const getProductos = async () => {
  const response = await axios.get(API_URL)
  return response.data
}

export const createProducto = async (producto: any) => {
  const response = await axios.post(API_URL, producto)
  return response.data
}

export const updateProducto = async (id: number, producto: any) => {
  const response = await axios.put(`${API_URL}/${id}`, producto)
  return response.data
}

export const deleteProducto = async (id: number) => {
  const response = await axios.delete(`${API_URL}/${id}`)
  return response.data
}