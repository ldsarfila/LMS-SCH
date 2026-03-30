export interface User {
  id: string
  name: string
  email: string
  phone?: string
  avatar_path?: string
  gender?: 'male' | 'female'
  role: string
  school_id?: string
  status: 'active' | 'inactive' | 'suspended'
}

export interface School {
  id: string
  name: string
  code: string
  email?: string
  phone?: string
  address?: string
  city?: string
  province?: string
  logo_path?: string
  status: 'active' | 'inactive' | 'suspended'
}

export interface Course {
  id: string
  name: string
  code: string
  description?: string
  teacher_id: string
  school_id: string
  status: 'draft' | 'published' | 'archived'
  created_at: string
  updated_at: string
}

export interface Enrollment {
  id: string
  user_id: string
  course_id: string
  status: 'active' | 'completed' | 'dropped'
  progress_percentage: number
  enrolled_at: string
}

export interface Exam {
  id: string
  title: string
  course_id: string
  duration_minutes: number
  total_questions: number
  passing_score: number
  status: 'draft' | 'published' | 'closed'
  scheduled_at: string
}

export interface Assignment {
  id: string
  title: string
  course_id: string
  description: string
  due_date: string
  max_score: number
  status: 'draft' | 'published' | 'closed'
}

export interface DashboardStats {
  totalStudents: number
  totalTeachers: number
  totalCourses: number
  averagePerformance: number
}
