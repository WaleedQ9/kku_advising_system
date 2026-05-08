# KKU Academic Advising System — Project Context

## نظرة عامة
**الاسم:** تحسين تجربة المستخدم لنظام الإرشاد الأكاديمي في جامعة الملك خالد من منظور أعضاء هيئة التدريس  
**الجامعة:** جامعة الملك خالد — كلية علوم الحاسب  
**المكدس التقني:** Laravel 12 + Blade + Tailwind CSS + MySQL 8 + Spatie Permissions  
**المستودع:** https://github.com/WaleedQ9/kku_advising_system.git

---

## الأدوار (Roles)

| الدور | الاسم العربي | Spatie Role | الحالة |
|-------|-------------|-------------|--------|
| Academic Advisor | المرشد الأكاديمي | `advisor` | ✅ مكتمل |
| Department Chair | رئيس القسم | `chair` | ✅ مكتمل |
| College Dean | عميد الكلية | `dean` | ✅ مكتمل |

### صلاحيات كل دور:

**المرشد الأكاديمي (advisor):**
- عرض طلاب قسمه فقط (`department_id`)
- بحث بالاسم أو الرقم الجامعي
- عرض الملف الكامل للطالب (معدل، حضور، مواد، تنبيهات)
- إضافة/عرض الملاحظات الإرشادية (أكاديمية/سلوكية، خيار المتابعة)
- تنفيذ حذف المواد (الحد الأقصى 3 محاولات، التحقق الآني من الشروط)
- عرض وحل التنبيهات (risk flags)
- تعليم المتابعات كمكتملة

**رئيس القسم (chair):**
- لوحة تحكم تعرض جميع المرشدين في القسم مع إحصائياتهم
- ملخص طلاب القسم بالكامل (إجمالي، منتظم، متعثر، مع إنذارات)
- عرض آخر الملاحظات الإرشادية عبر القسم
- طباعة تقرير HTML لطلاب القسم
- تصدير CSV لبيانات الطلاب
- فلترة التقارير بـ: المرشد، التخصص، الحالة

**عميد الكلية (dean):**
- لوحة تحكم شاملة لجميع الأقسام (عرض فقط)
- إحصائيات معدلات التخرج وتوزيع GPA
- ملخص تنبيهات الخطر لكل قسم
- جدول مقارنة أداء الأقسام
- لا يملك صلاحية تعديل أي بيانات

---

## بنية قاعدة البيانات

```
users
  - id, name, email, password
  - employee_id (unique)
  - faculty_role (enum: Advisor, Chair, Dean)
  - department_id (FK)
  - phone

students
  - id, student_id (unique), name_ar, name_en
  - major
  - department_id (FK)
  - advisor_id (FK → users)
  - gpa (decimal)
  - total_credits
  - status               (منتظم / متعثر / خريج)
  - academic_status      (Regular / Warning)

advising_notes
  - id, student_id (FK), user_id (FK)
  - title
  - note_type            (enum: Academic, Behavioral)
  - content
  - follow_up_required   (boolean)

risk_flags
  - id, student_id (FK)
  - type                 (enum: Low_GPA, High_Absence)
  - severity             (enum: High, Medium)
  - is_resolved          (boolean, default false)

drop_actions
  - id, student_id (FK), course_id (FK), advisor_id (FK → users)
  - status               (enum: Completed, Rejected)
  - reason
  - eligibility_check_result (JSON)

courses
  - id, name, code, credits
  - level_type           (عام / تخصص)
  - requirement_type     (اجباري / اختياري)
  - department_id (FK, null for general courses)

course_student (pivot)
  - student_id, course_id
  - current_grade, absences_count

departments
  - id, name_ar, name_en, code
```

---

## بيانات الاختبار (Seeders)

### الأقسام (4 أقسام)
| الكود | الاسم العربي |
|-------|-------------|
| CS | علوم الحاسب |
| CYS | الأمن السيبراني |
| IS | نظم المعلومات |
| CEN | هندسة الحاسب |

### المواد (20 مادة)
- **5 عامة إجبارية** (GEN101–105): برمجة، رياضيات، إنجليزي، مهارات الحاسب، منطق
- **3 عامة اختيارية** (GEN201–203): أخلاقيات، ريادة أعمال، تفكير نقدي
- **3 مواد لكل قسم** (2 تخصص إجباري + 1 تخصص اختياري)

### توزيع الساعات للطالب
- 3 عامة إجبارية (8 ساعات) + 2 تخصص إجباري (6 ساعات) + اختياريات = **15–24 ساعة**

### المستخدمون
- 4 مرشدين (advisor) — واحد لكل قسم
- 4 رؤساء أقسام (chair) — واحد لكل قسم
- 1 عميد (dean)

### الطلاب (40 طالباً)
- 10 طلاب لكل قسم
- أسماء عربية حقيقية
- ~3 طلاب متعثرون لكل قسم (gpa < 2.0)
- الطلاب المتعثرون: يملكون تنبيهات Low_GPA + High_Absence بعد تشغيل Scan

---

## المنطق الأساسي (Business Logic)

### RiskFlag::triggerAlert()
```php
// يُشغَّل من flags.scan
if ($gpa < 2.0)          → Low_GPA   (High severity)
if (totalAbsences >= 4)  → High_Absence (High severity)
```

### DropAction::validatePolicy()
```php
// الحد الأقصى 3 حذف مكتمل لكل طالب
$completedDrops = DropAction::where('student_id', $id)
    ->where('status', 'Completed')->count();
if ($completedDrops >= 3) → رفض
```

### absencesFromGpa() في StudentCourseSeeder
```php
if ($gpa >= 2.0) return 0;   // منتظم → لا إنذار غياب
if ($gpa >= 1.7) return 2;   // متعثر متوسط
return 4;                     // متعثر شديد → يُطلق High_Absence
```

---

## الملفات الرئيسية

### Controllers
```
app/Http/Controllers/
├── HomeController.php               ← الصفحة الرئيسية (يوجّه بحسب الدور)
├── Advisor/
│   ├── StudentsController.php       ← index, show, print
│   ├── AdvisingNoteController.php   ← store, markFollowUpDone
│   ├── DropActionController.php     ← check, store
│   └── RiskFlagController.php       ← index, resolve, scan
├── Chair/
│   ├── DashboardController.php      ← لوحة رئيس القسم
│   └── ReportController.php         ← print, exportCsv
└── Dean/
    └── DashboardController.php      ← لوحة العميد
```

### Views
```
resources/views/
├── home.blade.php                   ← لوحة المرشد
├── Student/
│   ├── index.blade.php              ← قائمة الطلاب (accordion، فلاتر، toggle أعمدة)
│   ├── show.blade.php               ← ملف الطالب الكامل
│   └── print.blade.php              ← صفحة طباعة مستقلة
├── chair/
│   ├── dashboard.blade.php          ← لوحة رئيس القسم
│   └── report-print.blade.php       ← طباعة تقرير القسم
├── dean/
│   └── dashboard.blade.php          ← لوحة العميد
└── layouts/app.blade.php
```

### Routes
```php
// مشترك
GET  /home                               → home (يوجّه بحسب الدور)

// Advisor (auth + role:advisor)
GET  /students                           → students.index
GET  /students/{student}                 → students.show
GET  /students/{student}/print           → students.print
POST /notes                              → notes.store
POST /notes/{note}/follow-up-done        → notes.followup.done
GET  /students/{student}/drop/{course}/check → drop.check
POST /students/{student}/drop            → drop.store
GET  /students/{student}/flags           → flags.index
POST /flags/{riskFlag}/resolve           → flags.resolve
POST /flags/scan                         → flags.scan

// Chair (auth + role:chair) — prefix: /chair
GET  /chair/dashboard                    → chair.dashboard
GET  /chair/report/print                 → chair.report.print
GET  /chair/report/csv                   → chair.report.csv

// Dean (auth + role:dean) — prefix: /dean
GET  /dean/dashboard                     → dean.dashboard
```

---

## المتطلبات الوظيفية (Functional Requirements)

| FR | الميزة | الأولوية | الحالة |
|----|--------|----------|--------|
| FR1 | لوحة تحكم المرشد الموحدة | عالية | ✅ مكتمل |
| FR2 | بحث الطلاب والوصول السريع | عالية | ✅ مكتمل |
| FR3 | التحقق الآني من شروط الحذف | عالية | ✅ مكتمل |
| FR4 | الملاحظات الإرشادية وسجل الحالات | متوسطة | ✅ مكتمل |
| FR5 | التنبيهات والإشعارات | متوسطة | ⚠️ جزئي (RiskFlags فقط، لا إيميل) |
| FR6 | إدارة المواعيد | منخفضة | ❌ غير مطبق |
| FR7 | التقارير والتحليلات | متوسطة | ✅ HTML print + CSV |
| FR8 | تكامل بيانات SIS | عالية | ⚠️ محاكى بـ Seeders |
| FR9 | التحكم بالوصول (RBAC) | متوسطة | ✅ مكتمل (advisor, chair, dean) |

---

## ما تبقى (اختياري)

### FR5 — إشعارات البريد الإلكتروني
- إرسال إيميل للمرشد عند توليد تنبيه جديد
- يتطلب: `php artisan make:notification RiskFlagAlert`
- يتطلب: إعداد SMTP في `.env`

### FR6 — إدارة المواعيد
- يتطلب جدول جديد `appointments`
- المرشد يجدول موعداً، الطالب يرى مواعيده
- التحقق من تعارض المواعيد

---

## اتفاقيات الكود

- Controllers في `app/Http/Controllers/{Role}/`
- Views في `resources/views/{role}/`
- جميع النصوص العربية مغلفة بـ `__()`
- ملف الترجمة: `lang/en.json`
- التحقق من الصلاحية: `auth()->user()->department_id === $student->department_id`
- الألوان: `kku-primary` (أخضر)، `kku-dark` (أخضر داكن)، `kku-accent` (ذهبي) — في `tailwind.config.js`
- لا PDF بـ dompdf — يُستخدم HTML print فقط (`window.print()`)
