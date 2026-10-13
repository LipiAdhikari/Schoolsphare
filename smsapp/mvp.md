# SchoolSphere — MVP Specification

**Product name:** SchoolSphere  
**Proposed title:** Smart School Management System  
**Document type:** Minimum Viable Product (MVP) specification  
**Status:** Product baseline for Laravel implementation  
**Primary platform:** Responsive web application

---

## 1. MVP Overview

SchoolSphere is a web-based school administration and academic management system that centralizes student and teacher records, academic setup, attendance, examinations, results, fee invoices, payments, notices, and role-specific dashboards.

The MVP replaces disconnected registers and spreadsheets with one relational database and controlled workflows. Each signed-in user sees only the functions and records permitted for their role.

### 1.1 Problem

Schools may manage admissions, attendance, marks, fee payments, and notices in separate registers or spreadsheets. This causes repeated data entry, inconsistent records, slow report preparation, weak visibility into balances and academic progress, and difficulty controlling who can view or modify sensitive information.

### 1.2 MVP solution

Deliver one secure Laravel application with:

- Centralized student, teacher, guardian, class, section, subject, and academic-year records.
- Role-specific access for administrators, teachers, students, and parents/guardians.
- Attendance recording and attendance summaries.
- Examination setup, marks entry, grading, and report cards.
- Fee invoices, line items, partial payments, receipts, and balances.
- Basic notices and dashboards.
- Search, filters, pagination, validation, audit-friendly records, and automated tests for critical permissions and workflows.

## 2. MVP Goals and Success Criteria

### Goals

1. Reduce dependence on separate paper and spreadsheet records.
2. Keep a reliable history of student enrollment by academic year.
3. Make attendance and examination data easier to record, review, and report.
4. Calculate invoice balances accurately, including partial payments.
5. Prevent users from viewing or changing records outside their permissions.
6. Provide a foundation that can be extended without redesigning core relationships.

### MVP acceptance criteria

- An administrator can configure academic years, classes, sections, subjects, users, students, teachers, and enrollments.
- A teacher can work only with assigned classes/sections and subjects.
- Duplicate attendance entries for the same defined attendance session are blocked.
- Marks and final grades follow configured grading rules; results cannot be published accidentally.
- An invoice may contain multiple items and receive multiple partial payments; its balance is calculated accurately.
- Students can access only their own attendance, results, notices, and fee status.
- Parents/guardians can access only students explicitly linked to them and only information permitted by the school.
- Deactivating an account blocks access without deleting historical academic or financial records.
- Critical user journeys are covered by automated feature tests.
- The application is responsive and has installation/setup documentation.

## 3. Users and Permissions

### Administrator

Can manage the school configuration and operational records: users, roles, academic years, classes, sections, subjects, class-subject mappings, teacher assignments, students, guardians, enrollments, attendance oversight, examinations, grading scales, results, fee types, invoices, payments, notices, and reports.

### Teacher

Can view assigned teaching assignments and associated enrolled students; take or correct attendance within the allowed correction workflow; enter marks for assigned subjects/exams; review student performance; and view relevant notices. A teacher must not enter or change marks for a subject/class assignment they do not own.

### Student

Can view their own profile, current and historical enrollments where permitted, attendance summaries, timetable when enabled, published results/report cards, notices, and fee invoice/payment status. Students cannot edit official academic or financial records.

### Parent/Guardian

Can view the attendance, published results, notices, and fee status of linked children. The guardian portal is read-only for official records. Access must be checked server-side against the student-guardian relationship on every request, including URL IDs, exports, and downloads.

### Authorization requirements

- Login is not sufficient authorization. Every protected action must also verify role, record ownership/relationship, and assignment scope.
- Enforce access using Laravel middleware, policies, gates, form requests, and service-layer checks where appropriate.
- Never trust role names, student IDs, teacher IDs, or invoice IDs submitted by a client without authorization checks.
- Account activation and authentication status must be checked before sensitive actions.
- Prefer deny-by-default behavior for protected routes.

## 4. MVP Modules and Scope

Priority labels describe product importance. The implementation order may be adjusted to respect dependencies.

### 4.1 Essential modules — required for MVP

#### Authentication and account security

- Login and logout.
- Password reset through the selected Laravel authentication starter kit.
- Authenticated profile view/update for fields users are allowed to edit.
- Active/inactive account control.
- Role-based landing page.
- Secure password hashing, CSRF protection, request validation, and session security using Laravel conventions.

#### Administration dashboard

- Summary cards for total students, teachers, classes/sections, attendance, outstanding fees, and recent activity where data is available.
- Lists or simple charts that help administrators identify operational issues.
- Values must come from database queries and must respect the viewer's permissions.

#### User, student, teacher, and guardian management

- Create and manage user accounts.
- Assign one initial application role: `admin`, `teacher`, `student`, or `parent`.
- Activate/deactivate accounts.
- Maintain student admission numbers and teacher employee numbers with unique constraints.
- Link student and teacher profiles to user accounts.
- Maintain guardian records and student-guardian relationships, including relationship type and primary guardian.
- Search, filter, paginate, and validate records.
- Prefer deactivation/archiving to hard deletion when records have historical dependencies.

#### Academic setup

- Create academic years with start and end dates.
- Mark at most one academic year as current at a time.
- Create classes/grades and sections for a given academic year.
- Register subjects and map subjects to classes/academic years.
- Assign teachers to a class section and subject combination.
- Validate dates, capacities, assignment consistency, and duplicate mappings.

#### Student admission and enrollment history

- Create a student profile and admission number.
- Enroll a student in a section for a specific academic year.
- Store roll number, status, and enrollment history.
- Prevent overlapping or duplicate active enrollments under the configured school rules.
- Changing the student's class next year should create a new enrollment, not overwrite the previous year's enrollment.

#### Attendance

- Record attendance against an enrollment and a defined class/subject assignment or daily attendance session.
- Supported statuses: `present`, `absent`, `late`, `excused`.
- Prevent duplicate attendance records for the same student, date, and attendance session.
- Allow authorized correction with a reason and an audit trail for material changes.
- Provide daily, monthly, and per-student summaries; display attendance percentages with a documented denominator and treatment of `excused` records.

#### Examinations and report cards

- Create exams linked to an academic year, with name/type, date range, and status.
- Record theory and practical marks where relevant, plus remarks.
- Restrict mark entry and correction to authorized teachers and administrators.
- Configure grading scales with minimum/maximum percentage, grade, grade point, and description.
- Calculate totals and grades consistently on the server; do not trust client-submitted totals or grades.
- Validate maximum marks, decimal precision, completeness, and duplicate result entries.
- Keep results in a draft/review state until an authorized user publishes them.
- Allow students and linked guardians to view published results only.

#### Fees and payments

- Configure fee types and default amounts.
- Create student invoices for an academic year with one or more invoice items.
- Use unique invoice and receipt numbers.
- Record one or more payments against an invoice, including partial payments.
- Store payment amount, date, method, reference, receiving user, and receipt number.
- Calculate balance as invoice total minus successful payments; do not use floating-point arithmetic for money.
- Update payment and invoice status safely inside a database transaction.
- Provide payment history, current balance, and a printable receipt view.
- Do not hard-delete financial transactions. Define correction, reversal, or void procedures that preserve history.

#### Basic notices

- Administrators can create and publish notices.
- Notices can target all users, a role, or a class section using an explicit targeting structure.
- Users see only notices that apply to them and are within their publication/expiry window.

#### Student and parent read-only portals

- Student home page with own attendance, published results, notices, and fee status.
- Parent home page showing only linked children and their permitted summaries.
- Every record lookup must enforce the student/guardian relationship server-side.

### 4.2 Important follow-up scope

These should be planned in the early product roadmap. The product can start with simple versions if schedule permits, but they must not displace correctness of the essential modules.

- Timetable builder and role-specific timetable views.
- Richer parent portal and child switching.
- More detailed report filters and exports.
- Email or in-app notification delivery beyond basic notices.

### 4.3 Advanced scope — after the core MVP is reliable

- Academic analytics: attendance rates, average exam results, trend comparisons, and students needing teacher review.
- PDF report cards and bulk report-card generation.
- CSV exports for authorized reports.
- More configurable dashboards and management reports.
- Comprehensive audit-log viewer and filtered audit exports.
- Advanced permission configuration, attachments, and school-wide settings.

Academic risk signals must be explainable and presented as prompts for a teacher to review, not as definitive predictions or automated judgments about a student.

## 5. Core Data Model

The list below is the target domain model; implement it incrementally and avoid generating all migrations before relationships and deletion rules are reviewed.

### Authentication and identity

- `users`: `id`, `name`, `email`, `password`, `role`, `is_active`, `email_verified_at`, timestamps.
- `students`: `id`, `user_id`, `admission_number`, `date_of_birth`, `gender`, `address`, `joined_at`, timestamps.
- `teachers`: `id`, `user_id`, `employee_number`, `qualification`, `hire_date`, `phone`, timestamps.
- `guardians`: `id`, `name`, `phone`, `email`, `address`, `occupation`, timestamps.
- `student_guardians`: `student_id`, `guardian_id`, `relationship`, `is_primary`, timestamps.
- `audit_logs`: `id`, `user_id`, `action`, `entity_type`, `entity_id`, `old_values`, `new_values`, `created_at`.
- `notifications`: `id`, `user_id`, `type`, `data`, `read_at`, timestamps.

### Academic setup and enrollment

- `academic_years`: `id`, `name`, `start_date`, `end_date`, `is_current`, timestamps.
- `classes`: `id`, `name`, `description`, timestamps.
- `class_sections`: `id`, `class_id`, `academic_year_id`, `name`, `room_number`, `capacity`, timestamps.
- `subjects`: `id`, `code`, `name`, `description`, `credit_hours`, timestamps.
- `class_subjects`: `id`, `class_id`, `subject_id`, `academic_year_id`, timestamps.
- `enrollments`: `id`, `student_id`, `class_section_id`, `academic_year_id`, `roll_number`, `status`, timestamps.
- `teaching_assignments`: `id`, `teacher_id`, `class_section_id`, `class_subject_id`, timestamps.

### Attendance and examinations

- `attendances`: `id`, `enrollment_id`, `teaching_assignment_id` (nullable only if school-wide daily attendance is supported), `attendance_date`, `status`, `remarks`, timestamps.
- `exams`: `id`, `academic_year_id`, `name`, `exam_type`, `start_date`, `end_date`, `status`, timestamps.
- `exam_results`: `id`, `enrollment_id`, `exam_id`, `class_subject_id`, `theory_marks`, `practical_marks`, `total_marks`, `grade`, `remarks`, timestamps.
- `grading_scales`: `id`, `min_percentage`, `max_percentage`, `grade`, `grade_point`, `remarks`, timestamps.

### Fees and payments

- `fee_types`: `id`, `name`, `description`, `default_amount`, timestamps.
- `fee_invoices`: `id`, `student_id`, `academic_year_id`, `invoice_number`, `issue_date`, `due_date`, `status`, timestamps.
- `fee_invoice_items`: `id`, `fee_invoice_id`, `fee_type_id`, `description`, `amount`, timestamps.
- `payments`: `id`, `fee_invoice_id`, `receipt_number`, `amount`, `payment_date`, `method`, `reference`, `received_by`, timestamps.

### Timetable and notices

- `timetable_entries`: `id`, `class_section_id`, `teaching_assignment_id`, `day_of_week`, `start_time`, `end_time`, `room_number`, timestamps.
- `announcements`: `id`, `title`, `body`, `created_by`, `published_at`, `expires_at`, timestamps.
- `announcement_targets`: `id`, `announcement_id`, `target_type`, `target_id`, timestamps.

## 6. Data Integrity Rules

1. Use foreign keys and deliberate `restrict`, `nullOnDelete`, or soft-delete/archive behavior. Do not cascade-delete payment or academic history by default.
2. Unique constraints: `users.email`, `students.admission_number`, `teachers.employee_number`, `fee_invoices.invoice_number`, `payments.receipt_number`, and the relevant composite keys.
3. Add suitable indexes for commonly filtered columns such as academic year, section, student, date, status, and invoice number.
4. Store monetary values using `DECIMAL`, not floating-point types.
5. Store marks with an agreed decimal precision and validate them against configured maximums.
6. Enforce at most one current academic year through application validation and, where supported, a robust database-level strategy.
7. Use transactions for payment recording/reversal, result publication, and other multi-step business operations.
8. Define the attendance uniqueness key based on whether attendance is daily or per subject/session. Do not create an ambiguous unique constraint.
9. Calculate balances from invoice items and successful payments, or maintain a transactionally consistent cached value. A displayed balance must never be manually editable.
10. Preserve enrollment history across academic years.
11. Record who made important changes and when; avoid logging passwords, secrets, or unnecessary sensitive data.

## 7. Recommended Laravel Implementation

### Baseline stack

- Laravel with a supported PHP version compatible with the selected Laravel release.
- MySQL with `utf8mb4` character set and an appropriate collation.
- Blade views, Tailwind CSS, and Vite for the initial interface.
- Alpine.js only where lightweight client-side interaction is useful; add heavier frontend tooling only for a demonstrated requirement.
- Laravel authentication starter kit, middleware, policies, form requests, Eloquent models, migrations, seeders, factories, feature tests, and service classes for multi-step business logic.

### Architecture principles

- Keep controllers small: validate/authorize, call application logic, return a response.
- Use Form Request classes for validation and authorization where appropriate.
- Use policies/gates for record-level access.
- Use service classes for complex operations such as recording payments, publishing results, and attendance corrections.
- Use database transactions for multi-record operations.
- Avoid putting business rules only in Blade templates or JavaScript.
- Use `SchoolClass` as the Eloquent model name for the `classes` table to avoid PHP keyword confusion.
- Add indexes and database constraints as well as validation; validation alone does not prevent race-condition duplicates.

### Suggested folders (create as needed, not all at once)

```text
app/
  Http/Controllers/
    Auth/
    Admin/
    Teacher/
    Student/
    Parent/
  Http/Middleware/
  Http/Requests/
  Models/
  Policies/
  Services/
  Notifications/
  Providers/
database/
  migrations/
  factories/
  seeders/
resources/
  css/
  js/
  views/
    layouts/
    components/
    auth/
    admin/
    teacher/
    student/
    parent/
    students/
    teachers/
    attendance/
    exams/
    fees/
    reports/
routes/
  web.php
tests/
  Feature/
  Unit/
```

## 8. MVP User Journeys

### Administrator sets up a school

1. Sign in and open the administrator dashboard.
2. Create an academic year, classes, sections, and subjects.
3. Create teacher accounts and profiles.
4. Create student profiles and admission numbers.
5. Link guardians to students.
6. Enroll students in sections for the selected academic year.
7. Map class subjects and assign teachers.
8. Verify that dashboards and menus reflect the configured data.

### Teacher records attendance

1. Sign in and select an assigned class section or teaching assignment.
2. Select the attendance date/session.
3. Mark each eligible enrolled student.
4. Submit the attendance form.
5. The server validates assignment scope and duplicate rules before saving.
6. The teacher can view a summary and, when allowed, submit a correction with a reason.

### Teacher submits results

1. Open an assigned subject and exam.
2. Enter marks within the defined range.
3. Save as draft.
4. Review completeness and calculated totals/grades.
5. Submit for review or allow an authorized administrator to publish.
6. Student and guardian portals show results only after publication.

### Administrator records a partial fee payment

1. Open the student's invoice and review its items and current balance.
2. Enter a payment amount, method, date, and optional reference.
3. The server checks that the amount is valid and that the invoice can accept payment.
4. A database transaction stores the payment and updates the invoice status consistently.
5. The application issues a unique receipt and shows the remaining balance.
6. The receipt and payment history remain available for audit.

### Parent views a child's records

1. Sign in and select a linked child.
2. The application verifies the guardian-child relationship server-side.
3. The parent views permitted attendance, published results, notices, and fee status.
4. Attempts to substitute another child's ID or access another family's invoice return an authorization failure.

## 9. Delivery Sequence

### Phase 0 — Foundation

- Create Laravel project and configure local MySQL.
- Configure authentication starter kit, layout, navigation, reusable components, formatting, and responsive shell.
- Set up Git, environment templates, and test baseline.

### Phase 1 — Identity and academic structure

- Roles and active account handling.
- Academic years, classes, sections, subjects, class-subject mappings, teacher assignments.
- Students, teachers, guardians, and enrollments.
- Seeders/factories and authorization tests.

### Phase 2 — Attendance

- Attendance workflow, uniqueness rules, correction rules, summaries, and tests.

### Phase 3 — Examinations

- Exam setup, mark entry, grading configuration, draft/review/publish workflow, student result view, and tests.

### Phase 4 — Fees

- Fee types, invoices/items, payments, partial payment handling, receipts, balances, transaction handling, and tests.

### Phase 5 — Portals and notices

- Student and guardian dashboards, child relationships, notices, and strict access tests.

### Phase 6 — Follow-up capabilities

- Timetable, advanced analytics, PDF report cards, CSV exports, expanded audit-log tools, and deployment hardening.

## 10. Definition of Done

A feature is complete only when:

- Validation and authorization are implemented server-side.
- Relevant database constraints and indexes exist.
- Success, empty, loading (when applicable), and error states are handled.
- The interface works on mobile, tablet, and desktop breakpoints.
- Critical business logic has automated tests.
- Related dashboards and totals remain consistent.
- Historical records are not accidentally overwritten or deleted.
- Documentation is updated where setup or behavior changes.

## 11. Explicitly Out of Scope for Initial MVP

- Native Android/iOS apps.
- Payment gateway integration or online card payments.
- Payroll, HR leave management, library, transport, hostel, inventory, and canteen modules.
- Biometric-device integration.
- Automated high-stakes student ranking or predictive decisions.
- Multi-school tenancy, unless it is confirmed as a product requirement before schema design.
- AI chatbot or generative AI functions.

## 12. Open Decisions Before Production

- Is attendance daily by class or per subject/session? The schema and unique key depend on this.
- What is the school's official grading scale and rounding policy?
- Which attendance statuses count in the attendance-percentage denominator?
- Can teachers edit submitted marks, and what review/approval path is required?
- What are the official invoice statuses, payment reversal rules, and receipt-number format?
- Are parent accounts separate from guardian records, or can a guardian have a `users` account? The parent portal requires a stable link between the authenticated account and guardian profile.
- Is SchoolSphere for one school only, or must the database support multiple schools?
- What local calendar, date format, currency, and timezone should be configured? For Nepal-based deployment, confirm school policy for the Nepali calendar and whether all dates are stored in Gregorian format with localized display.
