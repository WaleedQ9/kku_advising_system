<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير القسم — {{ auth()->user()->department->name_ar ?? '' }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; font-size: 13px; color: #1a1a1a; background: #fff; direction: rtl; }

        .header { background: #1a4731; color: white; padding: 24px 32px; display: flex; justify-content: space-between; align-items: center; }
        .header-title h1 { font-size: 18px; font-weight: 800; }
        .header-title p  { font-size: 11px; opacity: 0.75; margin-top: 4px; }
        .header-meta { text-align: left; font-size: 11px; opacity: 0.8; line-height: 1.8; }

        .body { padding: 24px 32px; }

        .summary-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px; margin-bottom: 24px; }
        .summary-card { border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; text-align: center; }
        .summary-card .num { font-size: 22px; font-weight: 900; color: #004d25; }
        .summary-card .lbl { font-size: 11px; color: #6b7280; margin-top: 2px; }

        .filters-bar { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 16px; font-size: 12px; color: #6b7280; margin-bottom: 20px; }

        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        thead { background: #004d25; color: white; }
        thead th { padding: 9px 12px; text-align: right; font-weight: 700; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        tbody td { padding: 8px 12px; border-bottom: 1px solid #f0f0f0; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .badge-red    { background: #fee2e2; color: #dc2626; }
        .badge-amber  { background: #fef3c7; color: #d97706; }
        .badge-green  { background: #dcfce7; color: #16a34a; }

        .footer { margin-top: 32px; padding-top: 12px; border-top: 1px solid #e5e7eb; font-size: 11px; color: #9ca3af; display: flex; justify-content: space-between; }

        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
        }
    </style>
</head>
<body>

<div class="header">
    <div>
        <p style="font-size:13px; font-weight:800;">جامعة الملك خالد</p>
        <p style="font-size:11px; opacity:.75;">كلية علوم الحاسب وتقنية المعلومات</p>
    </div>
    <div class="header-title">
        <h1>تقرير أداء القسم الأكاديمي</h1>
        <p>{{ auth()->user()->department->name_ar ?? '' }} · الفصل الدراسي الثاني 1447هـ</p>
    </div>
    <div class="header-meta">
        <div>تاريخ الإصدار: {{ $generatedAt }}</div>
        <div>أعده: {{ auth()->user()->name }}</div>
    </div>
</div>

<div class="body">

    {{-- Summary --}}
    <div class="summary-grid">
        <div class="summary-card">
            <div class="num">{{ $summary['total'] }}</div>
            <div class="lbl">إجمالي الطلاب</div>
        </div>
        <div class="summary-card">
            <div class="num" style="color:#dc2626;">{{ $summary['warning'] }}</div>
            <div class="lbl">طلاب متعثرون</div>
        </div>
        <div class="summary-card">
            <div class="num" style="color:#16a34a;">{{ $summary['regular'] }}</div>
            <div class="lbl">طلاب منتظمون</div>
        </div>
        <div class="summary-card">
            <div class="num">{{ $summary['avg_gpa'] }}</div>
            <div class="lbl">متوسط المعدل</div>
        </div>
        <div class="summary-card">
            <div class="num" style="color:#d97706;">{{ $summary['flags'] }}</div>
            <div class="lbl">إنذارات نشطة</div>
        </div>
    </div>

    {{-- Active Filters --}}
    @if(!empty(array_filter($filters ?? [])))
    <div class="filters-bar">
        <strong>الفلاتر المطبقة:</strong>
        @if(!empty($filters['advisor_id']))
            المرشد: {{ $advisors->find($filters['advisor_id'])?->name ?? $filters['advisor_id'] }} &nbsp;|&nbsp;
        @endif
        @if(!empty($filters['major']))
            التخصص: {{ $filters['major'] }} &nbsp;|&nbsp;
        @endif
        @if(!empty($filters['status']))
            الحالة: {{ $filters['status'] === 'Warning' ? 'متعثر' : 'منتظم' }}
        @endif
    </div>
    @endif

    {{-- Students Table --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>رقم الطالب</th>
                <th>الاسم</th>
                <th>التخصص</th>
                <th>المعدل</th>
                <th>الحالة</th>
                <th>المرشد</th>
                <th>إنذارات</th>
                <th>ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($students as $i => $student)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $student->student_id }}</td>
                <td>{{ $student->name_ar }}</td>
                <td>{{ $student->major ?? '—' }}</td>
                <td>
                    <span class="badge {{ $student->gpa < 2.0 ? 'badge-red' : ($student->gpa >= 3.5 ? 'badge-green' : 'badge-amber') }}">
                        {{ number_format($student->gpa, 2) }}
                    </span>
                </td>
                <td>
                    <span class="badge {{ $student->academic_status === 'Warning' ? 'badge-red' : 'badge-green' }}">
                        {{ $student->status }}
                    </span>
                </td>
                <td>{{ $student->advisor?->name ?? '—' }}</td>
                <td style="text-align:center;">{{ $student->active_flags_count ?: '—' }}</td>
                <td style="text-align:center;">{{ $student->advising_notes_count ?: '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <span>نظام الإرشاد الأكاديمي الذكي — جامعة الملك خالد</span>
        <span>عدد الطلاب في التقرير: {{ $summary['total'] }}</span>
    </div>
</div>

<div class="no-print" style="text-align:center; margin:20px;">
    <button onclick="window.print()"
        style="background:#004d25;color:white;border:none;padding:10px 28px;border-radius:8px;font-size:14px;cursor:pointer;font-family:inherit;">
        🖨️ طباعة التقرير
    </button>
</div>

</body>
</html>
