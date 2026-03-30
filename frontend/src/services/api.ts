import axios from 'axios'
import type { User, DashboardStats, Course, School } from '@/types'

const API_URL = process.env.API_URL || 'http://localhost:8000/api/v1'

const apiClient = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

// Request interceptor to add auth token
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token')
    if (token) {
      config.headers.Authorization = `Bearer ${token}`
    }
    return config
  },
  (error) => Promise.reject(error)
)

// Response interceptor to handle errors
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token')
      localStorage.removeItem('user')
      window.location.href = '/login'
    }
    return Promise.reject(error)
  }
)

export const authService = {
  login: async (email: string, password: string) => {
    const response = await apiClient.post('/auth/login', { email, password })
    return response.data
  },

  logout: async () => {
    await apiClient.post('/auth/logout')
  },

  me: async (): Promise<User> => {
    const response = await apiClient.get('/users/me')
    return response.data
  },
}

export const dashboardService = {
  getStats: async (): Promise<DashboardStats> => {
    const response = await apiClient.get('/dashboard/stats')
    return response.data
  },
}

export const courseService = {
  getAll: async (): Promise<Course[]> => {
    const response = await apiClient.get('/courses')
    return response.data
  },

  getById: async (id: string): Promise<Course> => {
    const response = await apiClient.get(`/courses/${id}`)
    return response.data
  },

  create: async (data: Partial<Course>): Promise<Course> => {
    const response = await apiClient.post('/courses', data)
    return response.data
  },

  update: async (id: string, data: Partial<Course>): Promise<Course> => {
    const response = await apiClient.put(`/courses/${id}`, data)
    return response.data
  },

  delete: async (id: string): Promise<void> => {
    await apiClient.delete(`/courses/${id}`)
  },
}

export const schoolService = {
  getAll: async (): Promise<School[]> => {
    const response = await apiClient.get('/schools')
    return response.data
  },

  getById: async (id: string): Promise<School> => {
    const response = await apiClient.get(`/schools/${id}`)
    return response.data
  },
}

export default apiClient
