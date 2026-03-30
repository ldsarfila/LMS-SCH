'use client'

import Link from 'next/link'
import { usePathname } from 'next/navigation'
import { useAuthStore } from '@/stores/auth-store'
import { cn } from '@/lib/utils'
import {
  LayoutDashboard,
  BookOpen,
  Users,
  GraduationCap,
  FileText,
  ClipboardCheck,
  Award,
  Calendar,
  Bell,
  Settings,
} from 'lucide-react'

const navigation = [
  { name: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
  { name: 'Courses', href: '/courses', icon: BookOpen },
  { name: 'Students', href: '/students', icon: Users },
  { name: 'Teachers', href: '/teachers', icon: GraduationCap },
  { name: 'Exams', href: '/exams', icon: FileText },
  { name: 'Assignments', href: '/assignments', icon: ClipboardCheck },
  { name: 'Grades', href: '/grades', icon: Award },
  { name: 'Attendance', href: '/attendance', icon: Calendar },
]

export function Sidebar() {
  const pathname = usePathname()
  const { user } = useAuthStore()

  return (
    <div className="flex w-64 flex-col bg-white border-r">
      <div className="flex h-16 items-center border-b px-6">
        <Link href="/dashboard" className="flex items-center space-x-2">
          <GraduationCap className="h-6 w-6 text-primary" />
          <span className="text-lg font-bold">LMS</span>
        </Link>
      </div>
      
      <nav className="flex-1 space-y-1 p-4 overflow-y-auto">
        {navigation.map((item) => {
          const isActive = pathname === item.href || pathname.startsWith(`${item.href}/`)
          return (
            <Link
              key={item.name}
              href={item.href}
              className={cn(
                'flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors',
                isActive
                  ? 'bg-primary text-primary-foreground'
                  : 'text-gray-700 hover:bg-gray-100'
              )}
            >
              <item.icon className="mr-3 h-5 w-5" />
              {item.name}
            </Link>
          )
        })}
      </nav>

      <div className="border-t p-4 space-y-1">
        <Link
          href="/notifications"
          className="flex items-center rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
        >
          <Bell className="mr-3 h-5 w-5" />
          Notifications
        </Link>
        <Link
          href="/settings"
          className="flex items-center rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100"
        >
          <Settings className="mr-3 h-5 w-5" />
          Settings
        </Link>
      </div>
    </div>
  )
}
