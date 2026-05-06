# KKU Academic Advising System — Project Context for Claude Code

## Project Overview
**Name:** Enhancing User Experience of KKU Academic Advising System from Faculty Members' Perspective  
**University:** King Khalid University — College of Computer Science  
**Stack:** Laravel 12 + Blade + Tailwind CSS + MySQL 8 + Spatie Permissions  
**Repo:** https://github.com/WaleedQ9/kku_advising_system.git

---

## User Roles (from Documentation)

The documentation defines **3 roles** — currently only `advisor` is fully implemented:

| Role | Arabic | Spatie Role | Status |
|------|--------|-------------|--------|
| Academic Advisor | المرشد الأكاديمي | `advisor` | ✅ Implemented |
| Department Chair | رئيس القسم | `chair` | ❌ Missing |
| College Dean | عميد الكلية | `dean` | ❌ Missing |

> **Note:** `registrar` role exists in the codebase but is NOT in the documentation. It should be reviewed — either removed or mapped to `chair`.

### Role Permissions (from documentation):

**Academic Advisor:**
- View all students in their department (`department_id`)
- Search students by name or ID
- View student profile (GPA, attendance, courses, risk flags)
- Add/view advising notes (Academic/Behavioral, follow-up flag)
- Process course drop (max 3 attempts, inline policy validation)
- View/resolve risk flags
- Mark follow-ups as done

**Department Chair:**
- Everything the advisor can do PLUS:
- View all advisors' activity in their department
- Generate department-level reports (PDF/CSV)
- Monitor all students' risk indicators across the department
- Filter advising activity by semester, advisor, or major

**College Dean:**
- View-only access (no advising actions)
- College-wide academic performance reports
- GPA distribution across all departments
- Advising compliance summaries
- High-level dashboards (no student-level editing)

---

## Database Schema

### Existing Tables
```
users
  - id, name, email, password
  - employee_id          ← added (unique)
  - faculty_role         ← added (enum: Advisor, Chair, Dean) — NOT used for auth yet
  - department_id (FK)
  - phone

students
  - id, student_id (unique), name_ar, name_en
  - major                ← added
  - department_id (FK)
  - advisor_id (FK → users)
  - gpa (decimal)
  - total_credits
  - status               (منتظم / متعثر / خريج) — Arabic display
  - academic_status      ← added (Regular / Warning) — system logic

advising_notes
  - id, student_id (FK), user_id (FK)
  - title                ← added
  - note_type            ← added (enum: Academic, Behavioral)
  - type                 (original field — kept for backward compat)
  - content
  - follow_up_required   ← added (boolean)

risk_flags
  - id, student_id (FK)
  - type                 (enum: Low_GPA, High_Absence)
  - severity             (enum: High, Medium)
  - is_resolved          (boolean, default false)

drop_actions
  - id, student_id (FK), course_id (FK), advisor_id (FK → users)
  - status               (enum: Completed, Rejected)
  - reason
  - eligibility_check_result (JSON — policy snapshot)

courses
  - id, name, code, credits, level_type, department_id

course_student (pivot)
  - student_id, course_id
  - current_grade, absences_count

departments
  - id, name_ar, name_en, code
```

---

## What's Implemented ✅

### Backend
- `advisor` Spatie role with full student management
- `StudentsController` — index (by department), show, print
- `AdvisingNoteController` — store, markFollowUpDone
- `DropActionController` — check eligibility, store (executeDrop)
- `RiskFlagController` — index, resolve, scan (auto-generate flags)
- Models: Student, User, AdvisingNote, RiskFlag, DropAction, Course, Department

### Key Business Logic
- **RiskFlag::triggerAlert()** — auto-generates flags: GPA < 2.0 → Low_GPA, absences >= 4 → High_Absence
- **DropAction::validatePolicy()** — max 3 completed drops per student
- **DropAction::executeDrop()** — drops course, decrements credits, updates academic_status
- **Student::checkDropEligibility()**, **hasRiskFlags()**, **getAcademicProfile()**

### Frontend (Blade + Tailwind)
- `home.blade.php` — Dashboard with live stats, system alerts vs advisor follow-ups
- `Student/index.blade.php` — Accordion table, sidebar filters, column toggle, inline quick note
- `Student/show.blade.php` — Full profile, course drop modal, notes timeline
- `Student/print.blade.php` — Standalone print/PDF page
- `lang/en.json` — 199 translation keys, all strings wrapped with `__()`

---

## What's Missing ❌ (Priority Order)

### 1. Role System Fix (HIGH)
```php
// Current: only 'advisor' and 'registrar' (registrar not in docs)
// Needed:
- Add 'chair' Spatie role
- Add 'dean' Spatie role
- Remove or repurpose 'registrar' role
- Use faculty_role column in role assignment
- Add role middleware to routes
```

### 2. Department Chair Dashboard (HIGH)
- View all advisors in department with their student counts
- List all students in department (not just one advisor's)
- View all advising notes across department
- See department-wide risk flag summary
- Generate PDF/CSV reports filtered by: semester, advisor, major

### 3. College Dean Dashboard (MEDIUM)
- College-wide stats (all departments)
- GPA distribution charts
- Risk indicators summary per department
- Advising activity compliance report
- View-only — no editing capabilities

### 4. Reporting System (MEDIUM) — FR7
- PDF report: student list with GPA, attendance, risk flags
- CSV export: advising activity log
- Filters: by semester, major, advisor, department
- Already partially done in `Student/print.blade.php` — needs expansion

### 5. Appointment Management (LOW) — FR6
- Advisor schedules advising appointments
- Student can view upcoming appointments
- Conflict check (no double-booking)
- Not in current DB schema — needs new `appointments` table

### 6. Notifications & Alerts (MEDIUM) — FR5
- In-platform notifications (already partially done via RiskFlags)
- Email notifications for risk alerts
- Notification when follow-up is added

### 7. Registrar Role Cleanup
- Either: rename `registrar` → `chair` and build Chair features on top
- Or: remove registrar entirely if not in scope

---

## Routes Reference
```php
// Advisor (auth + role:advisor)
GET  /home                              → home (dashboard)
GET  /students                          → students.index
GET  /students/{student}               → students.show
GET  /students/{student}/print         → students.print
POST /notes                            → notes.store
POST /notes/{note}/follow-up-done      → notes.followup.done
GET  /students/{student}/drop/{course}/check → drop.check
POST /students/{student}/drop          → drop.store
GET  /students/{student}/flags         → flags.index
POST /flags/{riskFlag}/resolve         → flags.resolve
POST /flags/scan                       → flags.scan

// Registrar (needs review)
GET  /registrar/dashboard
GET  /registrar/students
GET  /registrar/students/{student}/enroll
POST /registrar/students/{student}/enroll
```

---

## Functional Requirements from Documentation

| FR | Feature | Priority | Status |
|----|---------|----------|--------|
| FR1 | Unified Advisor Dashboard | High | ✅ Done |
| FR2 | Student Search & Quick Access | High | ✅ Done |
| FR3 | Inline Policy Validation (drop eligibility) | High | ✅ Done |
| FR4 | Advising Notes & Case History | Medium | ✅ Done |
| FR5 | Notifications & Alerts | Medium | ⚠️ Partial (RiskFlags only) |
| FR6 | Appointment Management | Low | ❌ Missing |
| FR7 | Reporting & Analytics | Medium | ⚠️ Partial (print only) |
| FR8 | SIS Data Integration | High | ⚠️ Simulated with seeders |
| FR9 | Role-Based Access Control | Medium | ⚠️ Partial (advisor only) |

---

## Immediate Next Steps (Suggested Order)

1. **Fix roles:** Add `chair` and `dean` Spatie roles, update seeders and middleware
2. **Chair dashboard:** Department overview, all advisors, reports
3. **Dean dashboard:** College-wide view-only dashboard
4. **Reports:** PDF/CSV export for chair and dean
5. **Notifications:** Email alerts for risk flags

---

## Code Conventions
- Controllers live in `app/Http/Controllers/Advisor/` or `app/Http/Controllers/Registrar/`
- Views in `resources/views/Student/`, `resources/views/registrar/`, `resources/views/home.blade.php`
- All Arabic strings wrapped with `__()`
- Translation file: `lang/en.json`
- Authorization: always check `auth()->user()->department_id === $student->department_id`
- Colors: `kku-primary` (green), `kku-dark` (dark green), `kku-accent` (gold) — defined in tailwind config
