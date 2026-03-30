'use client'

import { useEffect } from 'react'
import { useRouter } from 'next/navigation'
import { useAuthStore } from '@/stores/auth-store'
import { LoginForm } from '@/components/auth/login-form'
import { LoadingSpinner } from '@/components/ui/loading-spinner'

export default function LoginPage() {
  const router = useRouter()
  const { isAuthenticated, isLoading, checkAuth } = useAuthStore()

  useEffect(() => {
    checkAuth()
  }, [checkAuth])

  if (isLoading) {
    return (
      <div className="flex h-screen items-center justify-center">
        <LoadingSpinner size="lg" />
      </div>
    )
  }

  if (isAuthenticated) {
    router.push('/dashboard')
    return null
  }

  return (
    <div className="flex min-h-screen items-center justify-center bg-gray-50 px-4 py-12 sm:px-6 lg:px-8">
      <div className="w-full max-w-md space-y-8">
        <div className="text-center">
          <h1 className="text-3xl font-bold tracking-tight text-gray-900">
            LMS Login
          </h1>
          <p className="mt-2 text-sm text-gray-600">
            Sign in to your Learning Management System account
          </p>
        </div>
        <LoginForm />
      </div>
    </div>
  )
}
