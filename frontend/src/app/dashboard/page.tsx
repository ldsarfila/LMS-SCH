'use client'

import { useEffect, useState } from 'react'
import { useRouter } from 'next/navigation'
import { useAuthStore } from '@/stores/auth-store'
import { LoadingSpinner } from '@/components/ui/loading-spinner'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Users, BookOpen, GraduationCap, TrendingUp } from 'lucide-react'

export default function DashboardPage() {
  const router = useRouter()
  const { user, isAuthenticated, isLoading, checkAuth, fetchDashboardStats } = useAuthStore()
  const [stats, setStats] = useState<any>(null)

  useEffect(() => {
    checkAuth()
  }, [checkAuth])

  useEffect(() => {
    if (isAuthenticated && user) {
      fetchDashboardStats().then(setStats)
    }
  }, [isAuthenticated, user, fetchDashboardStats])

  if (isLoading) {
    return (
      <div className="flex h-screen items-center justify-center">
        <LoadingSpinner size="lg" />
      </div>
    )
  }

  if (!isAuthenticated) {
    router.push('/login')
    return null
  }

  const statCards = [
    {
      title: 'Total Students',
      value: stats?.totalStudents ?? 0,
      icon: Users,
      color: 'text-blue-600',
      bgColor: 'bg-blue-100',
    },
    {
      title: 'Total Courses',
      value: stats?.totalCourses ?? 0,
      icon: BookOpen,
      color: 'text-green-600',
      bgColor: 'bg-green-100',
    },
    {
      title: 'Total Teachers',
      value: stats?.totalTeachers ?? 0,
      icon: GraduationCap,
      color: 'text-purple-600',
      bgColor: 'bg-purple-100',
    },
    {
      title: 'Average Performance',
      value: `${stats?.averagePerformance ?? 0}%`,
      icon: TrendingUp,
      color: 'text-orange-600',
      bgColor: 'bg-orange-100',
    },
  ]

  return (
    <div className="space-y-6">
      <div>
        <h1 className="text-3xl font-bold tracking-tight">Dashboard</h1>
        <p className="text-muted-foreground">
          Welcome back, {user?.name}! Here&apos;s what&apos;s happening with your school today.
        </p>
      </div>

      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        {statCards.map((card) => (
          <Card key={card.title}>
            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
              <CardTitle className="text-sm font-medium">{card.title}</CardTitle>
              <card.icon className={`h-4 w-4 ${card.color}`} />
            </CardHeader>
            <CardContent>
              <div className="text-2xl font-bold">{card.value}</div>
              <p className="text-xs text-muted-foreground">
                +20.1% from last month
              </p>
            </CardContent>
          </Card>
        ))}
      </div>

      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
        <Card className="col-span-4">
          <CardHeader>
            <CardTitle>Recent Activities</CardTitle>
          </CardHeader>
          <CardContent>
            <p className="text-muted-foreground">No recent activities to display.</p>
          </CardContent>
        </Card>
        <Card className="col-span-3">
          <CardHeader>
            <CardTitle>Quick Actions</CardTitle>
          </CardHeader>
          <CardContent className="space-y-2">
            <button className="w-full rounded-md bg-primary px-4 py-2 text-primary-foreground hover:bg-primary/90">
              Create New Course
            </button>
            <button className="w-full rounded-md bg-secondary px-4 py-2 text-secondary-foreground hover:bg-secondary/80">
              Add Student
            </button>
            <button className="w-full rounded-md bg-secondary px-4 py-2 text-secondary-foreground hover:bg-secondary/80">
              Schedule Exam
            </button>
          </CardContent>
        </Card>
      </div>
    </div>
  )
}
