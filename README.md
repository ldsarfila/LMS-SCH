# Enterprise Learning Management System (LMS)

## 🏗️ System Architecture

### Tech Stack
- **Backend**: Laravel 12 + PHP 8.5+
- **Frontend**: Next.js 14 + TypeScript + TailwindCSS + ShadCN UI
- **Database**: MySQL 8.0+
- **Cache/Queue**: Redis
- **Authentication**: Laravel Sanctum (SPA)
- **RBAC**: Spatie Laravel Permission

### Architecture Pattern
- Modular Monolith with Clean Architecture
- API-First Design (RESTful JSON API)
- Multi-tenant Ready (School-based)
- Repository-Service-Controller Pattern

## 📁 Project Structure

```
/workspace
├── backend/                    # Laravel Backend
│   ├── app/
│   │   ├── Modules/           # Feature Modules
│   │   │   ├── Auth/         # Authentication & Authorization
│   │   │   ├── User/         # User Management
│   │   │   ├── School/       # Multi-tenant School Management
│   │   │   ├── Course/       # Course Management
│   │   │   ├── Lesson/       # Lesson & Content Management
│   │   │   ├── Assignment/   # Assignment System
│   │   │   ├── Cbt/          # Computer Based Test (Ujian Online)
│   │   │   ├── Grade/        # Grading System
│   │   │   ├── Attendance/   # Attendance System
│   │   │   └── Notification/ # Notification System
│   │   ├── Http/
│   │   ├── Models/
│   │   ├── Services/
│   │   └── Repositories/
│   ├── database/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── config/
│   ├── routes/
│   └── resources/
│
└── frontend/                   # Next.js Frontend
    ├── src/
    │   ├── app/               # App Router Pages
    │   ├── components/        # Reusable Components
    │   ├── lib/               # Utilities & Helpers
    │   ├── hooks/             # Custom Hooks
    │   ├── services/          # API Services
    │   ├── stores/            # State Management (Zustand)
    │   └── types/             # TypeScript Types
    └── public/
```

## 🎯 User Roles (RBAC)

1. **Super Admin** - Full system control, multi-school management
2. **School Admin** - School-level management (teachers, students, classes)
3. **Teacher** - Course creation, content upload, grading, exams
4. **Student** - Access courses, take exams, view grades
5. **Parent** - Monitor student progress

## 🔑 Core Features

### 1. Course Management
- CRUD operations for courses
- Course categories
- Enrollment system
- Progress tracking

### 2. Learning Content
- Video streaming support
- PDF/DOC/PPT uploads
- Rich text editor with MathJax
- Drip content scheduling

### 3. CBT System (Online Exams)
- Multiple question types (MCQ, True/False, Matching, Essay, etc.)
- Timer with auto-submit
- Random question ordering
- Basic anti-cheat measures
- Auto-grading for objective questions

### 4. Assignment System
- File upload submissions
- Deadline management
- Manual grading

### 5. Grading System
- Automatic & manual grading
- Report card generation
- Excel/PDF export

### 6. Attendance System
- QR code & manual attendance
- Export capabilities

### 7. Notification System
- Email notifications
- WhatsApp Business integration
- Event-based triggers

### 8. Dashboard Analytics
- Student performance charts
- Class statistics
- Real-time metrics

## 🚀 Quick Start

### Backend Setup
```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### Frontend Setup
```bash
cd frontend
npm install
cp .env.example .env
npm run dev
```

## 📊 Database Schema Overview

Key tables:
- users, roles, permissions (Spatie RBAC)
- schools (multi-tenant)
- courses, lessons, materials
- enrollments
- assignments, submissions
- exams, questions, answers
- grades
- attendance

## 🔐 Security Features

- XSS Protection
- SQL Injection Prevention
- CSRF Protection
- Rate Limiting
- File Upload Validation
- JWT/Sanctum Authentication

## 📈 Performance Optimization

- Redis Caching
- Database Indexing
- Lazy Loading
- Query Optimization
- CDN-ready Asset Storage

## 🌐 API Endpoints

Base URL: `/api/v1`

- `GET /courses` - List courses
- `POST /courses` - Create course
- `GET /courses/{id}` - Get course details
- `POST /auth/login` - Login
- `POST /auth/register` - Register
- `GET /users/me` - Current user profile

## 📝 License

Proprietary - Enterprise LMS Solution
