# SchoolSphere — Smart School Management System

A web-based School Management System built with **Laravel 12**, **PHP 8.2**, **MySQL (XAMPP)**, **Tailwind CSS v4**, and **Blade Templates**. Designed to centralize school administration, academic structures, student and teacher records, attendance, examinations, fee management, timetables, and reporting.

---

## 1. Technology Stack

* **Backend:** PHP 8.2.12 & Laravel 12.x
* **Database:** MySQL / MariaDB 10.4 (via XAMPP)
* **Frontend:** Laravel Blade, Tailwind CSS v4 (`@tailwindcss/vite`), Vanilla JS
* **Build System:** Vite 7.x & npm 11.x
* **Testing:** PHPUnit 11.x

---

## 2. Documentation Index

Detailed architectural and database design specifications are maintained in the [`docs/`](./docs) directory:

* [System Architecture & Route Plan](./docs/architecture.md): MVC design, security strategy, authorization matrix, and full route hierarchy.
* [Database ERD & Schema Specification](./docs/erd.md): Entity relationship diagram, foreign keys, unique constraints, and schema tables.
* [MVP Specification](./smsapp/mvp.md): Core functional requirements and minimum viable product scope.
* [Product Requirements (PRD)](./smsapp/prd.md): Product vision, user stories, and comprehensive feature specification.

---

## 3. Project Setup & Local Run

### Prerequisites
* Windows 11 with XAMPP (Apache & MySQL running on default port `3306`)
* PHP 8.2.12 with `pdo_mysql`, `mbstring`, `openssl`, and `gd` enabled
* Composer 2.x
* Node.js v24.x & npm 11.x

### Quickstart
1. Clone or open the repository in VS Code / PowerShell.
2. Verify environment configuration:
   ```powershell
   Copy-Item .env.example .env # if .env does not exist
   php artisan key:generate
   ```
3. Verify MySQL connection (`DB_DATABASE=schoolsphere`):
   ```powershell
   php artisan migrate:status
   ```
4. Build frontend assets:
   ```powershell
   npm run build
   ```
5. Run automated baseline tests:
   ```powershell
   php artisan test
   ```
6. Start local development servers:
   ```powershell
   # Terminal 1: Laravel Backend
   php artisan serve

   # Terminal 2: Vite HMR (Development)
   npm run dev
   ```

---

## 4. Development Phase Tracker

| Phase | Description | Status |
| :--- | :--- | :--- |
| **Phase 0** | Project Audit & Baseline Verification | **Completed** |
| **Phase 1** | Architecture, ERD & Design System | **Completed** |
| **Phase 2** | Frontend Foundation & Dashboard Layout | Up Next |
| **Phase 3** | Authentication & Role-Based Access | Not started |
| **Phase 4** | Academic Structure (Classes, Sections, Subjects) | Not started |
| **Phase 5** | Student, Teacher & Guardian Management | Not started |
| **Phase 6** | Attendance Management | Not started |
| **Phase 7** | Examinations & Results | Not started |
| **Phase 8** | Fees & Payment Processing | Not started |
| **Phase 9** | Timetables & School Announcements | Not started |
| **Phase 10**| Reports, Exports & Audit Logs | Not started |
| **Phase 11**| Integration, Security Hardening & QA | Not started |
| **Phase 12**| Final Documentation & Delivery | Not started |

---

## 5. Security & Data Integrity Highlights

* **Role Isolation:** Built-in server-side authorization ensuring teachers, students, and guardians can only access explicitly assigned resources.
* **Account Status Validation:** Inactive accounts are immediately denied entry without destroying historical data.
* **Relational Integrity:** Foreign keys, composite unique constraints, and database transactions for all multi-step financial and academic operations.
