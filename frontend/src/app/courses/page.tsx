'use client'

import { useEffect, useState } from 'react'
import { useAuthStore } from '@/stores/auth-store'
import { courseService } from '@/services/api'
import type { Course } from '@/types'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { BookOpen, Plus, Edit, Trash2 } from 'lucide-react'
import toast from 'react-hot-toast'

export default function CoursesPage() {
  const { user } = useAuthStore()
  const [courses, setCourses] = useState<Course[]>([])
  const [isLoading, setIsLoading] = useState(true)

  useEffect(() => {
    loadCourses()
  }, [])

  const loadCourses = async () => {
    try {
      const data = await courseService.getAll()
      setCourses(data)
    } catch (error) {
      toast.error('Failed to load courses')
    } finally {
      setIsLoading(false)
    }
  }

  const handleDelete = async (id: string) => {
    if (!confirm('Are you sure you want to delete this course?')) return
    
    try {
      await courseService.delete(id)
      toast.success('Course deleted successfully')
      loadCourses()
    } catch (error) {
      toast.error('Failed to delete course')
    }
  }

  if (isLoading) {
    return <div className="flex items-center justify-center h-64">Loading courses...</div>
  }

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-3xl font-bold tracking-tight">Courses</h1>
          <p className="text-muted-foreground">Manage your school courses</p>
        </div>
        <button className="flex items-center rounded-md bg-primary px-4 py-2 text-primary-foreground hover:bg-primary/90">
          <Plus className="mr-2 h-4 w-4" />
          Add Course
        </button>
      </div>

      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        {courses.length === 0 ? (
          <Card className="col-span-full">
            <CardContent className="flex flex-col items-center justify-center py-12">
              <BookOpen className="h-12 w-12 text-gray-400 mb-4" />
              <p className="text-gray-500">No courses found</p>
              <p className="text-sm text-gray-400">Create your first course to get started</p>
            </CardContent>
          </Card>
        ) : (
          courses.map((course) => (
            <Card key={course.id} className="hover:shadow-md transition-shadow">
              <CardHeader>
                <div className="flex items-start justify-between">
                  <div>
                    <CardTitle className="text-lg">{course.name}</CardTitle>
                    <p className="text-sm text-muted-foreground">{course.code}</p>
                  </div>
                  <span
                    className={`rounded-full px-2 py-1 text-xs font-medium ${
                      course.status === 'published'
                        ? 'bg-green-100 text-green-800'
                        : course.status === 'draft'
                        ? 'bg-yellow-100 text-yellow-800'
                        : 'bg-gray-100 text-gray-800'
                    }`}
                  >
                    {course.status}
                  </span>
                </div>
              </CardHeader>
              <CardContent>
                <p className="text-sm text-gray-600 line-clamp-2 mb-4">
                  {course.description || 'No description available'}
                </p>
                <div className="flex items-center space-x-2">
                  <button className="flex items-center rounded-md bg-secondary px-3 py-1.5 text-sm text-secondary-foreground hover:bg-secondary/80">
                    <Edit className="mr-1 h-3 w-3" />
                    Edit
                  </button>
                  {(user?.role === 'admin' || user?.role === 'super_admin') && (
                    <button
                      onClick={() => handleDelete(course.id)}
                      className="flex items-center rounded-md bg-destructive/10 px-3 py-1.5 text-sm text-destructive hover:bg-destructive/20"
                    >
                      <Trash2 className="mr-1 h-3 w-3" />
                      Delete
                    </button>
                  )}
                </div>
              </CardContent>
            </Card>
          ))
        )}
      </div>
    </div>
  )
}
