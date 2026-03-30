import { create } from 'zustand'
import { persist } from 'zustand/middleware'
import type { User, DashboardStats } from '@/types'
import { authService, dashboardService } from '@/services/api'

interface AuthState {
  user: User | null
  isAuthenticated: boolean
  isLoading: boolean
  token: string | null
  
  // Actions
  login: (email: string, password: string) => Promise<void>
  logout: () => Promise<void>
  checkAuth: () => Promise<void>
  fetchDashboardStats: () => Promise<DashboardStats | null>
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set, get) => ({
      user: null,
      isAuthenticated: false,
      isLoading: true,
      token: null,

      login: async (email: string, password: string) => {
        set({ isLoading: true })
        try {
          const data = await authService.login(email, password)
          localStorage.setItem('auth_token', data.token)
          set({
            user: data.user,
            isAuthenticated: true,
            token: data.token,
            isLoading: false,
          })
        } catch (error) {
          set({ isLoading: false })
          throw error
        }
      },

      logout: async () => {
        try {
          await authService.logout()
        } catch (error) {
          console.error('Logout error:', error)
        } finally {
          localStorage.removeItem('auth_token')
          set({
            user: null,
            isAuthenticated: false,
            token: null,
          })
        }
      },

      checkAuth: async () => {
        const token = localStorage.getItem('auth_token')
        if (!token) {
          set({ 
            user: null, 
            isAuthenticated: false, 
            isLoading: false,
            token: null 
          })
          return
        }

        try {
          const user = await authService.me()
          set({
            user,
            isAuthenticated: true,
            token,
            isLoading: false,
          })
        } catch (error) {
          localStorage.removeItem('auth_token')
          set({ 
            user: null, 
            isAuthenticated: false, 
            isLoading: false,
            token: null 
          })
        }
      },

      fetchDashboardStats: async () => {
        try {
          const stats = await dashboardService.getStats()
          return stats
        } catch (error) {
          console.error('Failed to fetch dashboard stats:', error)
          return null
        }
      },
    }),
    {
      name: 'auth-storage',
      partialize: (state) => ({ 
        token: state.token,
        isAuthenticated: state.isAuthenticated,
        user: state.user 
      }),
    }
  )
)

// Provider component for client-side only initialization
import { useEffect } from 'react'

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const checkAuth = useAuthStore((state) => state.checkAuth)
  
  useEffect(() => {
    checkAuth()
  }, [checkAuth])

  return <>{children}</>
}
