# SchoolSphere — Product Requirements Document (PRD)

**Product name:** SchoolSphere  
**Proposed product title:** Smart School Management System  
**Document type:** Product Requirements Document  
**Version:** 2.0 — aligned to the supplied SchoolSphere specification  
**Status:** Working baseline for product and engineering  
**Platform:** Responsive web application built with Laravel

---

## 1. Executive Summary

SchoolSphere is a centralized school administration and academic management web application. It brings student and teacher information, academic-year enrollment, attendance, examinations, results, fee billing and payments, notices, and role-specific dashboards into one secure system.

The product addresses operational problems caused by paper registers, disconnected spreadsheets, duplicated data entry, inconsistent reporting, and unclear access to student or financial records. Administrators manage the school configuration and official records; teachers work within their assigned teaching scope; students view their own records; and parents/guardians view permitted records for linked children.

The first release prioritizes data correctness, authorization, and high-value workflows over advanced customization. In particular, academic history must survive progression between years, teachers must not edit unauthorized marks, guardians must not access unrelated children, and partial fee payments must produce reliable balances and auditable receipts.

## 2. Product Vision

Make everyday school administration more reliable and less time-consuming by giving each school stakeholder one clear, secure place to manage or review the information relevant to them.

### Product principles

1. **Correctness before decoration:** Financial balances, attendance counts, enrollment history, and grades must be reliable.
2. **Least privilege:** Every role sees only the screens and records required for its work.
3. **Historical integrity:** Academic and financial history is preserved instead of overwritten or cascaded away.
4. **One source of truth:** Reuse linked data rather than copying student, teacher, class, and academic-year values into unrelated tables.
5. **Progressive delivery:** Build a dependable core before adding advanced analytics and convenience features.
6. **Clear review workflows:** Important operations such as mark publication and financial corrections must be intentional and traceable.
7. **Accessible, responsive usability:** Common school workflows should remain clear on desktop, tablet, and mobile screens.

## 3. Problem Statement

School records are often spread across admission registers, attendance books, mark sheets, fee ledgers, and independent spreadsheets. The resulting process is slow and error-prone. The same student details may be entered repeatedly, older academic-year records may be overwritten, staff may struggle to identify unpaid balances, and preparing report cards or monthly summaries can require significant manual work.

Separate records also make it difficult to give parents transparent access while protecting the records of other students. A centralized system must therefore solve both the operational problem and the authorization problem.

## 4. Objectives

### Primary objectives

- Centralize student, teacher, guardian, class, section, subject, and academic-year records.
- Digitize attendance entry, correction, and summaries.
- Digitize examination setup, marks entry, grading, publication, and report-card access.
- Track invoices, line items, partial payments, receipts, and outstanding balances.
- Provide secure authentication and record-level, role-based authorization.
- Give administrators, teachers, students, and parents/guardians relevant dashboards.
- Produce reliable views and reports without manually reconciling separate records.

### Secondary objectives

- Provide a foundation for timetables, richer notices, analytics, PDF report cards, CSV exports, and expanded audit logs.
- Make future development easier through clear relationships, service boundaries, tests, and documented conventions.

### Non-goals for the initial release

Native mobile apps, payroll, library, transport, hostel, inventory, canteen, biometric attendance hardware, payment-gateway integration, multi-school tenancy (unless confirmed before schema design), and AI-driven high-stakes decisions are excluded from the first release.

## 5. Target Users and Personas

### 5.1 Administrator / school office

**Needs:** Maintain official records, configure academic years, assign teachers, monitor attendance, publish results, manage invoices and payments, and generate useful summaries.  
**Pain points:** Duplicate records, unclear balances, manual reporting, and difficulty controlling staff permissions.  
**Success:** Routine school information can be maintained in one place and critical changes remain traceable.

### 5.2 Teacher

**Needs:** See assigned sections and subjects, find their enrolled students, take attendance, enter marks, and review performance.  
**Pain points:** Repetitive attendance and mark-sheet work, uncertainty about current student lists, and lack of quick progress summaries.  
**Success:** A teacher can complete attendance and marks workflows without seeing or modifying unrelated classes or subjects.

### 5.3 Student

**Needs:** Review personal attendance, timetable when available, published results, announcements, and fee status.  
**Pain points:** Having to ask staff for routine information.  
**Success:** The student's portal provides accurate, understandable information without exposing other students' records.

### 5.4 Parent / guardian

**Needs:** Review linked children's attendance, published results, notices, and fee status.  
**Pain points:** Limited visibility and dependence on manual updates.  
**Success:** A guardian can switch between linked children and access permitted information while being prevented from viewing another family's information.

## 6. Product Scope and Priorities

### Priority 0 — Essential for initial release

1. Authentication, password reset, profile, and active account state.
2. Role-aware dashboards and navigation.
3. User, student, teacher, and guardian management.
4. Academic years, classes, sections, subjects, class-subject mappings, and teacher assignments.
5. Student enrollment history by academic year.
6. Attendance recording, correction controls, and summaries.
7. Examination setup, marks, grading, result review/publication, and report-card view.
8. Fee types, invoices, invoice items, partial payments, receipts, and balances.
9. Basic notices with role/class targeting.
10. Student portal and minimum viable parent/guardian portal.
11. Authorization and workflow tests for critical paths.

### Priority 1 — Important follow-up capabilities

- Timetable builder and role-specific views.
- Richer parent portal interactions.
- Expanded reports and export filters.
- In-app/email notifications beyond basic notices.

### Priority 2 — Advanced capabilities

- Academic analytics and trend dashboards.
- PDF report-card generation and bulk output.
- CSV reporting.
- Expanded audit-log viewer and exports.
- More configurable school settings and permission granularity.

Priority reflects product importance; implementation order must follow dependencies and protect core correctness.

## 7. Functional Requirements

Requirements use IDs so they can be referenced in issues and tests.

### 7.1 Authentication and access control

- **AUTH-01:** Users can sign in and sign out securely.
- **AUTH-02:** Users can recover access through the configured password-reset workflow.
- **AUTH-03:** Users can update only profile fields permitted for their role.
- **AUTH-04:** Administrators can activate or deactivate accounts.
- **AUTH-05:** Deactivated accounts cannot access protected areas.
- **AUTH-06:** A role-aware dashboard is shown after authentication.
- **AUTH-07:** Access checks are performed server-side on every protected route and record action.
- **AUTH-08:** Unauthorized users cannot retrieve records by editing path parameters, query parameters, submitted IDs, or export/download URLs.
- **AUTH-09:** Initial roles are `admin`, `teacher`, `student`, and `parent`; a dedicated permission package may be added later if role-level access is no longer sufficient.

### 7.2 Dashboard and navigation

- **DASH-01:** The administrator dashboard displays meaningful summaries such as student count, teacher count, current academic year, attendance summary, fee balances, and recent activity when those values are supported by data.
- **DASH-02:** Teacher dashboards show assigned sections/subjects and shortcuts to attendance and marks workflows.
- **DASH-03:** Student dashboards show only the signed-in student's personal records.
- **DASH-04:** Parent dashboards show only linked children and their permitted summaries.
- **DASH-05:** Empty states explain what to set up next; dashboard widgets do not display misleading zeroes when data is unavailable.
- **DASH-06:** Navigation is responsive and hides irrelevant navigation items, while the server remains responsible for authorization.

### 7.3 User, student, teacher, and guardian management

- **PEOPLE-01:** Authorized administrators can create, view, update, search, filter, and paginate users and profiles.
- **PEOPLE-02:** Email, admission number, and employee number are unique within their defined scope.
- **PEOPLE-03:** Student and teacher profiles reference their authentication user records.
- **PEOPLE-04:** Guardians can be linked to multiple students and students can be linked to multiple guardians.
- **PEOPLE-05:** The relationship record stores relationship type and whether the guardian is primary.
- **PEOPLE-06:** The authenticated parent account must map to the correct guardian profile; a free-standing guardian contact record alone does not authorize portal access.
- **PEOPLE-07:** Historical records are preserved when a person leaves the school; hard deletion is restricted when referenced by enrollments, results, invoices, payments, or audit records.
- **PEOPLE-08:** List screens support server-side search/filtering and pagination rather than loading every record into the browser.

### 7.4 Academic configuration

- **ACADEMIC-01:** Administrators can create academic years with start and end dates.
- **ACADEMIC-02:** The application prevents invalid date ranges and enforces one current academic year at a time.
- **ACADEMIC-03:** Administrators can create classes/grades and class sections for an academic year.
- **ACADEMIC-04:** Administrators can create subjects and map subjects to classes and academic years.
- **ACADEMIC-05:** Administrators can create teaching assignments that link teachers to a class section and class-subject mapping.
- **ACADEMIC-06:** The application rejects assignments that refer to incompatible classes, sections, subjects, or academic years.
- **ACADEMIC-07:** The interface distinguishes a class/grade from a section instance for a specific academic year.

### 7.5 Admission and enrollment history

- **ENROLL-01:** An authorized administrator can create a student admission record with a unique admission number.
- **ENROLL-02:** A student can be enrolled in a section for a defined academic year, with roll number and status.
- **ENROLL-03:** Enrollment records preserve which section a student belonged to in each academic year.
- **ENROLL-04:** Progression to a new class/year creates a new enrollment record rather than overwriting past enrollment.
- **ENROLL-05:** Duplicate or invalid active enrollments are prevented according to the school's confirmed enrollment policy.
- **ENROLL-06:** Attendance, results, and fee records reference stable student/enrollment identities appropriate to the workflow.

### 7.6 Attendance

- **ATT-01:** Authorized teachers can record attendance only for eligible students in assigned sections/subjects or approved daily attendance scope.
- **ATT-02:** The supported attendance statuses are `present`, `absent`, `late`, and `excused`.
- **ATT-03:** The application prevents duplicate entries for the same student/date/attendance session.
- **ATT-04:** Attendance correction is restricted to authorized roles, uses a defined time/status policy, and records a reason where required.
- **ATT-05:** Administrators can review attendance summaries by date range, class/section, and student.
- **ATT-06:** Student and guardian portals show only permitted attendance records.
- **ATT-07:** Attendance percentage calculations document their denominator and treatment of excused or cancelled sessions.
- **ATT-08:** The system records the responsible user and timestamps for important attendance changes.

### 7.7 Exams, marks, grading, and report cards

- **EXAM-01:** Administrators can create an exam linked to an academic year, including name, type, dates, and status.
- **EXAM-02:** Authorized teachers can enter marks only for assigned teaching scope and eligible enrolled students.
- **EXAM-03:** Marks are validated server-side against configured maximums and the relevant theory/practical rules.
- **EXAM-04:** The server calculates totals, percentages, and grades; client-provided totals and grades are not trusted.
- **EXAM-05:** Grading scales are configurable and include percentage boundaries, grade, grade point, and description.
- **EXAM-06:** Grading boundaries cannot overlap or leave unintended gaps unless the grading policy expressly permits them.
- **EXAM-07:** Results have a defined lifecycle such as draft, submitted/review, and published.
- **EXAM-08:** Only an authorized role can publish results; published results are visible to the relevant student and linked guardians.
- **EXAM-09:** Changes after publication follow an explicit correction/republishing workflow and are traceable.
- **EXAM-10:** The system prevents duplicate result rows for the same enrollment, exam, and subject combination.
- **EXAM-11:** Report-card views show a clear breakdown of marks and configured grades; PDF export may follow as Priority 2.

### 7.8 Fees, invoices, and payments

- **FEE-01:** Administrators can configure fee types and default amounts.
- **FEE-02:** An invoice belongs to a student and academic year and contains one or more invoice items.
- **FEE-03:** Invoice numbers and receipt numbers are unique.
- **FEE-04:** Payments can be made in multiple installments against an invoice.
- **FEE-05:** Each payment records amount, payment date, method, optional reference, receipt number, and receiving user.
- **FEE-06:** Amounts use decimal monetary types and are validated for precision and permissible ranges.
- **FEE-07:** Invoice total is the sum of invoice items; remaining balance is invoice total minus successful, non-reversed payments.
- **FEE-08:** Payment recording and invoice-status changes execute transactionally.
- **FEE-09:** The system handles repeated submission/retries safely so one user action does not accidentally create duplicate payments.
- **FEE-10:** Payment history and receipts are viewable by authorized users; students and guardians see only permitted fee summaries.
- **FEE-11:** Financial history is not hard-deleted. Corrections use a documented void, reversal, or adjustment workflow.
- **FEE-12:** Invoice statuses and rules for overpayment, discounts, waivers, due dates, and reversals are configurable only after school policy is confirmed.

### 7.9 Notices and notifications

- **NOTICE-01:** Authorized administrators can draft, publish, and expire notices.
- **NOTICE-02:** Notices can target all users, a specific role, or a class section using explicit target records.
- **NOTICE-03:** Users see notices only when the target, publication date, and expiry conditions match.
- **NOTICE-04:** The initial release may use an in-app notice list; richer delivery notifications may follow in Priority 1.
- **NOTICE-05:** Notice content is safely rendered to prevent stored cross-site scripting.

### 7.10 Timetable — Priority 1

- **TIME-01:** Administrators can define timetable entries for a section and teaching assignment.
- **TIME-02:** The system validates start/end times and prevents conflicting teacher, room, or section allocations according to school rules.
- **TIME-03:** Teachers, students, and parents can view relevant timetables when published.

### 7.11 Reports, exports, and analytics — Priority 2

- **REPORT-01:** Initial reports include attendance summaries, exam results, invoice balances, and payment history.
- **REPORT-02:** CSV exports are permission-checked and apply the same filters and record visibility as the on-screen report.
- **REPORT-03:** PDF report cards and printable receipts use a consistent school identity and approved data.
- **ANALYTICS-01:** Analytics may show attendance percentage, average examination performance, and trends over time.
- **ANALYTICS-02:** Students who may need support are flagged as a prompt for qualified staff review; the product must not present the flag as a definitive diagnosis or automated decision.
- **AUDIT-01:** Important changes record actor, action, entity reference, timestamp, and appropriate before/after values while excluding passwords and secrets.

## 8. Information Architecture

### Shared

- Sign in / password reset
- Profile and account menu
- Notices
- Help or guidance states

### Administrator navigation

- Dashboard
- People: Users, Students, Teachers, Guardians
- Academics: Academic Years, Classes, Sections, Subjects, Class-Subject Mappings, Teaching Assignments, Enrollments
- Attendance
- Examinations and Results
- Fees: Fee Types, Invoices, Payments, Receipts
- Notices
- Reports
- Audit Logs / Settings (advanced)

### Teacher navigation

- Dashboard
- My Teaching Assignments
- Attendance
- Exams and Marks
- Student Performance (assigned students only)
- Timetable (when enabled)
- Notices

### Student navigation

- Dashboard
- My Profile
- My Attendance
- My Timetable (when enabled)
- My Results / Report Cards
- My Fee Status
- Notices

### Parent / guardian navigation

- Dashboard
- Linked Children
- Child Attendance
- Published Results
- Fee Status
- Notices
- Timetable (when enabled)

Navigation visibility is for usability only; it is never a substitute for server-side access control.

## 9. Key User Flows

### 9.1 First-time school setup

1. Administrator signs in.
2. Creates the current academic year.
3. Adds grades/classes, sections, subjects, and mappings.
4. Creates teacher profiles and assigns teaching responsibilities.
5. Creates student profiles, guardian links, and academic-year enrollments.
6. Confirms role permissions and reviews counts from the dashboard.

### 9.2 Taking attendance

1. Teacher selects an assigned section or teaching assignment.
2. Teacher chooses the date/session.
3. The application loads the eligible roster for that session.
4. Teacher records a status for each student and submits.
5. The server validates access, dates, eligibility, duplicate constraints, and allowed statuses.
6. The application saves the session consistently and shows a summary.

### 9.3 Publishing results

1. Administrator creates the exam and grading policy.
2. Teacher enters marks for assigned subject/student combinations.
3. The server validates the marks and calculates derived values.
4. Results remain drafts until the review/publish step is completed.
5. An authorized user publishes the results.
6. Student and linked guardian portals show the published result; later corrections use an audited workflow.

### 9.4 Receiving a partial payment

1. Authorized staff opens an invoice and verifies the outstanding amount.
2. Staff enters the payment details.
3. Server validates amount, currency precision, invoice status, authorization, and duplicate/retry protection.
4. A database transaction stores the payment and keeps the invoice status consistent.
5. The system creates a receipt number and recalculates the remaining balance.
6. Staff can print the receipt and view the complete payment history.

### 9.5 Parent checks a child’s records

1. Parent signs in and chooses a linked child.
2. The server resolves the authenticated account to a guardian profile and confirms the relationship.
3. The parent sees permitted information for that child.
4. Attempts to access another student's ID, invoice, or report are rejected by policy checks regardless of the URL used.

## 10. Data Model and Relationships

This is the logical model for schema design. Exact column types, nullable fields, unique keys, and delete behavior must be decided in migrations and reviewed before implementation.

### 10.1 Identity and guardianship

| Entity | Purpose / core fields | Key rules |
|---|---|---|
| `users` | Authentication identity: `name`, `email`, `password`, `role`, `is_active`, `email_verified_at` | Unique email; hashed password; initial role values `admin`, `teacher`, `student`, `parent` |
| `students` | Student profile: `user_id`, `admission_number`, DOB, gender, address, joined date | Unique admission number; links to `users` |
| `teachers` | Teacher profile: `user_id`, `employee_number`, qualification, hire date, phone | Unique employee number; links to `users` |
| `guardians` | Guardian contact/profile data | A guardian can link to multiple students |
| `student_guardians` | `student_id`, `guardian_id`, relationship, `is_primary` | Unique student/guardian pair; supports many-to-many |
| `audit_logs` | Actor, action, entity type/ID, before/after values, timestamp | Exclude secrets; restrict access |
| `notifications` | User-scoped notification type/data/read timestamp | User-scoped access |

**Important design decision:** a `guardians` contact record is not automatically an authenticated parent. Associate the authenticated parent user to a guardian using an explicit, unique relationship such as `guardians.user_id` or a carefully constrained profile mapping. Confirm whether one login can represent more than one guardian before finalizing this relationship.

### 10.2 Academic setup

| Entity | Purpose / core fields | Key rules |
|---|---|---|
| `academic_years` | Name, start/end dates, current flag | Valid range; one current year at a time |
| `classes` | Grade/class name and description | Represents a grade/level, not a section for a particular year |
| `class_sections` | `class_id`, `academic_year_id`, section name, room, capacity | Specific section instance for an academic year |
| `subjects` | Code, name, description, credit hours | Unique code if school policy requires it |
| `class_subjects` | Class, subject, academic year | Maps a subject to a class for an academic year |
| `enrollments` | Student, class section, academic year, roll number, status | Preserves year-by-year history; prevent invalid duplicates |
| `teaching_assignments` | Teacher, class section, class-subject mapping | Assignment must be internally consistent |

### 10.3 Attendance and examination

| Entity | Purpose / core fields | Key rules |
|---|---|---|
| `attendances` | Enrollment, optional teaching assignment, date, status, remarks | Unique key depends on daily vs per-session attendance model |
| `exams` | Academic year, name, type, date range, status | Valid dates and lifecycle |
| `exam_results` | Enrollment, exam, class-subject, theory/practical/total, grade, remarks | Unique enrollment/exam/subject combination; derived values validated/calculated server-side |
| `grading_scales` | Minimum/maximum percentage, grade, grade point, remarks | No unintended overlapping bands; policy versioning may be required later |

### 10.4 Fees and payments

| Entity | Purpose / core fields | Key rules |
|---|---|---|
| `fee_types` | Name, description, default amount | Monetary amount uses decimal type |
| `fee_invoices` | Student, academic year, invoice number, issue/due date, status | Unique invoice number; financial history is retained |
| `fee_invoice_items` | Invoice, fee type, description, amount | Multiple items per invoice; totals are reproducible |
| `payments` | Invoice, receipt number, amount, date, method, reference, receiver | Unique receipt; preserve reversals/adjustments instead of deleting history |

### 10.5 Notices and timetable

| Entity | Purpose / core fields | Key rules |
|---|---|---|
| `announcements` | Title, body, creator, publish/expiry dates | Safely render content; lifecycle validated |
| `announcement_targets` | Announcement, target type, target ID | Explicit target validation; targets may be role or class section |
| `timetable_entries` | Section, teaching assignment, day, start/end, room | Validate overlaps and assignment compatibility |

### 10.6 Relationship summary

- A `User` has one `Student` profile or one `Teacher` profile for the initial single-role design; the authenticated parent account must map to a `Guardian` profile.
- A `Student` has many `Enrollments`; each `Enrollment` belongs to one `ClassSection` and academic year.
- A `ClassSection` belongs to a `SchoolClass` and an academic year, and has many enrollments.
- A `Teacher` has many `TeachingAssignments`; each assignment links a teacher to a section and class-subject mapping.
- An `Enrollment` has many attendance records and exam results.
- An `Exam` has many results; a result belongs to an enrollment and a class-subject mapping.
- A `FeeInvoice` has many invoice items and many payments.
- A `Student` can have many guardians; a guardian can be linked to many students.

Use `SchoolClass` as the Eloquent model name for the `classes` table to avoid conflict with the PHP `class` keyword.

## 11. Data Integrity and Business Rules

- Unique identifiers must be enforced with database constraints, not application validation alone.
- Foreign-key behavior must be deliberate. Historical academic and financial records must not be deleted by broad cascade rules.
- A student's enrollment for one academic year must remain available after they advance to the next class.
- Attendance uniqueness must match the adopted attendance model: daily attendance versus subject/session attendance.
- The school must define how `excused` and other attendance statuses affect percentages.
- Marks and grades are calculated and validated server-side.
- Grading scales must have defined inclusive/exclusive boundaries and rounding behavior.
- Results require deliberate review and publication states.
- Fees use fixed-precision decimal amounts, never floating-point arithmetic.
- Invoice balance is calculated from invoice items less successful, non-reversed payments; any cached balance must be maintained transactionally.
- Payment recording and reversal are transactional and safe against duplicate requests.
- No financial payment is silently deleted; use reversal or adjustment records and preserve receipt history.
- Sensitive operations include actor identity and timestamps in an audit trail.
- Logs must not store password hashes, session tokens, secrets, or unnecessary personal data.

## 12. UX and Visual Design Requirements

### Direction

Modern, clean, professional, information-dense without visual clutter. The interface should feel like a dependable school operations platform rather than a marketing website.

### Layout and navigation

- Responsive sidebar on desktop and compact collapsible navigation on smaller screens.
- Clear page title, breadcrumbs where useful, primary action, filters, and table content.
- Tables must support readable columns, horizontal overflow or responsive presentation, empty states, and pagination.
- Use consistent forms with labels, inline validation, required-field indicators, and clear save/cancel actions.
- Confirm destructive or high-impact actions; offer clear status feedback after save operations.
- Dashboards should prioritize tasks and exceptions, not use charts merely for decoration.

### Component consistency

Create reusable Blade layouts and components for buttons, input fields, selects, badges, cards, tables, modal/dialog patterns, alerts, pagination, and empty states. Use consistent spacing, type scale, border radius, and semantic status colors.

### Accessibility

- Semantic labels and controls.
- Keyboard navigability and visible focus indicators.
- Adequate text/background contrast.
- Do not rely on color alone to communicate status.
- Clear validation summaries and field-level errors.

## 13. Technical Requirements and Architecture

### Proposed baseline

- Laravel (a supported release compatible with the installed PHP version).
- PHP compatible with the selected Laravel release (the project setup plan targets PHP 8.3 or later where supported).
- MySQL with `utf8mb4`.
- Blade templates, Tailwind CSS, and Vite.
- Alpine.js for lightweight interactions when necessary.
- Laravel's supported authentication starter kit for standard authentication flows.
- Eloquent models and migrations, Form Requests, middleware, policies/gates, service classes, seeders/factories, PHPUnit or Pest (select one test runner consistently), and CI when available.

### Architecture rules

- Controllers coordinate requests, authorization, application services, and responses; avoid complex business logic in controllers.
- Policies and server-side checks protect individual records and actions.
- Use Form Requests for input validation and authorization where practical.
- Service classes handle multi-step domain operations such as payments, result publication, and attendance corrections.
- Use transactions for operations that must update related records atomically.
- Use database constraints and indexes alongside validation.
- Keep role/ownership checks out of the frontend-only layer; JavaScript visibility is not security.
- Use queues only when real asynchronous processing is needed; the initial product should not depend on unnecessary infrastructure.
- Keep `.env` credentials out of source control and provide a safe `.env.example`.

### Suggested folder structure

```text
app/
  Http/Controllers/{Auth,Admin,Teacher,Student,Parent}/
  Http/Middleware/
  Http/Requests/
  Models/
  Policies/
  Services/
database/{migrations,factories,seeders}/
resources/{css,js,views}/
  views/{layouts,components,auth,admin,teacher,student,parent,students,teachers,attendance,exams,fees,reports}/
routes/web.php
tests/{Feature,Unit}/
```

Create directories and classes incrementally as the design becomes concrete.

## 14. Non-Functional Requirements

### Security and privacy

- Authentication for protected pages and secure password hashing using Laravel conventions.
- CSRF protection, output escaping, validation, and safe file handling where files are introduced.
- Record-level authorization for teacher assignments and guardian-child relationships.
- Principle of least privilege for admin, teacher, student, and parent roles.
- No real student or guardian data in seeders, local test fixtures, demos, or repositories.
- Sensitive exports and direct-download routes require the same access checks as the UI.
- Define backup, retention, incident response, and access-review policies before production use.

### Data integrity

- Foreign keys, unique constraints, appropriate indexes, transactions, and explicit financial/academic correction workflows.
- Time- and actor-stamped logs for critical changes.

### Performance and scalability

- Paginate large lists and filter on the server.
- Avoid N+1 queries with eager loading where appropriate.
- Use indexes on high-use foreign keys and query filters.
- Use database aggregation for summary queries carefully and test with realistic volumes.
- Set explicit performance targets after the expected number of students, users, years, and concurrent sessions is known.

### Reliability and maintainability

- Automated tests for critical flows and authorization boundaries.
- Clear local setup instructions, migrations, seeders, and environment configuration.
- Consistent code style and reviewable commits.
- Production errors must not expose stack traces or secrets.

### Localization

- Confirm school timezone, currency, display date format, academic calendar, and language requirements before production. Dates should use a consistent storage standard and be localized for display rather than stored as ambiguous text.

## 15. Quality Assurance and Testing

### Required test groups

1. **Authentication:** login, logout, reset, inactive-account blocking.
2. **Role access:** each role is denied unrelated route groups.
3. **Teacher scope:** teacher can access assigned students/subjects but cannot edit another teacher's marks or take attendance outside assignment scope.
4. **Guardian isolation:** guardian can see linked children only; changing URL or submitted IDs cannot bypass isolation.
5. **Enrollment:** progression creates history; invalid duplicates are rejected.
6. **Attendance:** duplicate session entries are blocked; correction rules are enforced; summaries calculate correctly.
7. **Grading:** boundary values, maximum marks, rounding, missing marks, and grade calculation behave as configured.
8. **Publication:** unpublished results do not appear in student/parent portals.
9. **Fees:** multiple invoice items, partial payments, remaining balance, receipt uniqueness, failed/duplicate submission, and reversal behavior are correct.
10. **Data integrity:** delete/disable behavior preserves related historical records.
11. **UI validation:** required fields and error messages are useful; primary pages work across supported breakpoints.
12. **Reports and downloads:** access filters match on-screen authorization.

Use factories and fake/test data. Never use real students' sensitive records in automated test suites.

## 16. Success Metrics

Baseline the following metrics after pilot usage begins. Targets should be selected with school staff instead of inventing unsupported values in advance.

- **Adoption:** percentage of intended staff accounts active; percentage of active students represented accurately in the system.
- **Workflow completion:** attendance sessions recorded digitally; exams and results managed in the system; invoices and payments recorded digitally.
- **Efficiency:** time required to take attendance and generate report cards or fee summaries compared with the prior process.
- **Data quality:** duplicate admission numbers, invalid enrollments, duplicate attendance sessions, mark corrections after publication, and payment reconciliation discrepancies.
- **Access safety:** count of authorization failures detected in testing; zero known cross-student or cross-guardian data leaks.
- **Reliability:** test pass rate for critical workflows, error rate, and backup/restore verification.
- **User experience:** completion success and feedback from administrators, teachers, students, and parents.

## 17. Delivery Roadmap

### Week 1 — Foundation and design system

- Bootstrap Laravel and MySQL.
- Configure Git, Vite, Tailwind, the authentication starter kit, and reusable layouts/components.
- Build shared layout, navigation, form controls, table patterns, responsive shell, and initial dashboard placeholders.
- Review schema relationships and permissions before creating large migration batches.

### Week 2 — Authentication and access control

- Finalize role values and active-account logic.
- Build role-specific landing pages.
- Implement route middleware and policies.
- Add authorization feature tests early.

### Weeks 3–4 — Academic structure and people

- Build migrations, models, relationships, factories, and seeders.
- Implement academic years, classes, sections, subjects, mappings, and teaching assignments.
- Implement users, student/teacher/guardian profiles, enrollment history, search, filters, and pagination.

### Week 5 — Attendance

- Implement session model, roster, attendance entry, duplicate protection, correction rules, summaries, and tests.

### Week 6 — Examinations and results

- Implement exam setup, mark entry, grading scales, totals, review/publication states, student result views, and tests.

### Week 7 — Fees

- Implement fee types, invoices/items, payment service, partial payments, receipt numbers, balance calculations, and transaction tests.

### Week 8 — Portals and notices

- Finish student/parent portals, guardian-child access policies, notices, and security tests.
- Add timetable only if the core flows remain stable and there is capacity.

### Weeks 9–10 — Hardening and follow-up features

- Add prioritized reports, analytics, exports, audit-log tools, security review, performance checks, deployment configuration, backup plan, and README.

If delivery falls behind, prioritize reliable attendance, results, fees, and authorization over advanced charts and cosmetic features.

## 18. Risks and Mitigations

| Risk | Impact | Mitigation |
|---|---|---|
| Poor schema decisions | Historical data becomes hard to maintain | Review ER relationships and deletion policy before migrations are widely used |
| Weak record-level authorization | Student or financial data exposure | Policies, deny-by-default access, and tests for altered IDs and direct URLs |
| Incorrect fee calculations | Financial discrepancies and loss of trust | Decimal types, transactions, immutable payment history, reconciliation tests |
| Ambiguous grading rules | Incorrect grades or report cards | Agree on grading boundaries, missing-mark behavior, and rounding before implementation |
| Ambiguous attendance model | Duplicate or misleading attendance rates | Choose daily versus per-subject/session attendance and define excused-status rules |
| Overly broad MVP | Delayed usable release | Deliver dependency-first phases and defer advanced analytics, PDFs, and exports |
| Historical records accidentally deleted | Loss of audit/academic/financial history | Restrictive foreign keys, soft deletion/archive strategy, explicit correction workflows |
| Unclear guardian account mapping | Parent portal authorization flaws | Map authenticated users to guardian profiles explicitly; test multiple-child and unrelated-child cases |
| Local/production configuration leakage | Data exposure or deployment failure | `.env` outside Git, safe `.env.example`, production debug disabled, deployment checklist |

## 19. Open Product Decisions

These decisions should be resolved before building the relevant module:

1. **School tenancy:** Is SchoolSphere a single-school installation, or must it support many independent schools from the start?
2. **Attendance granularity:** Daily by section, or per subject/class session? How are excused and cancelled sessions counted?
3. **Grading policy:** What grade boundaries, grade points, maximum marks, rounding rules, and incomplete-result rules apply?
4. **Result workflow:** Can teachers edit after submission? Who approves and publishes? How are published corrections handled?
5. **Fees:** What currencies, discounts, waivers, late fees, overpayment, invoice statuses, payment methods, and reversal rules are required?
6. **Guardian identity:** Can one user account be linked to more than one guardian profile? Who verifies guardian-child associations?
7. **Dates and localization:** What timezone, display date format, language, currency, and calendar are required?
8. **Notifications:** Are email, SMS, or in-app notifications required for the initial pilot?
9. **Reports:** Which exact attendance, examination, and fee report layouts are required by the school?
10. **Data retention:** How long must student, guardian, financial, audit, and inactive-account records be retained?

## 20. Launch Readiness Checklist

### Product workflows

- [ ] Administrator can manage users, classes, sections, subjects, students, teachers, guardians, and enrollments.
- [ ] Teachers are restricted to assigned sections and subjects.
- [ ] Attendance can be recorded and duplicate entries are prevented.
- [ ] Configured grading produces correct results and publication is controlled.
- [ ] Invoices support multiple items and partial payments with correct balances.
- [ ] Receipts are unique and payment history is retained.
- [ ] Students and parents can view only authorized records.
- [ ] Notices follow role/section targeting rules.

### Engineering and operations

- [ ] Migrations and seeders run from a clean database.
- [ ] Critical authorization and business-logic tests pass.
- [ ] Database constraints, indexes, and transaction boundaries are reviewed.
- [ ] No secrets or production credentials are committed.
- [ ] `.env.example` and the README describe safe setup.
- [ ] Production debug mode is off and error pages do not expose secrets.
- [ ] Backups and restore procedure have been tested.
- [ ] Deployment, monitoring, account recovery, and data retention owners are identified.

## 21. Definition of Done

The initial release is ready when essential workflows work end to end; role and record-level authorization is tested; academic history and financial records preserve integrity; validation, error states, and responsive layouts are complete; the database can be rebuilt from migrations; critical tests pass; and setup, deployment, backup, and known-limitations documentation is available.
