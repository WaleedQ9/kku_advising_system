<!DOCTYPE html>
@php $isRtl = app()->getLocale() === 'ar'; @endphp
<html lang="{{ app()->getLocale() }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('الملف الإرشادي للطالب') }} — {{ $student->name_ar }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            font-size: 13px;
            color: #1a1a1a;
            background: #fff;
            direction: {{ $isRtl ? 'rtl' : 'ltr' }};
        }

        /* ── Header ── */
        .header {
            background: #1a4731;
            color: white;
            padding: 24px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header-logo { font-size: 11px; opacity: 0.8; line-height: 1.8; }
        .header-title { text-align: center; }
        .header-title h1 { font-size: 18px; font-weight: 800; }
        .header-title p  { font-size: 11px; opacity: 0.75; margin-top: 4px; }
        .header-meta { text-align: {{ $isRtl ? 'left' : 'right' }}; font-size: 11px; opacity: 0.8; line-height: 1.8; }

        /* ── Student Info ── */
        .student-info {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            margin: 20px 32px;
        }
        .info-cell {
            padding: 14px 16px;
            border-{{ $isRtl ? 'left' : 'right' }}: 1px solid #e5e7eb;
        }
        .info-cell:last-child { border-{{ $isRtl ? 'left' : 'right' }}: none; }
        .info-cell label { font-size: 10px; color: #6b7280; font-weight: 700; display: block; margin-bottom: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-cell span  { font-size: 14px; font-weight: 800; color: #111; }

        /* ── Stats ── */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin: 0 32px 20px;
        }
        .stat-card {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px;
            text-align: center;
        }
        .stat-card .val { font-size: 22px; font-weight: 900; color: #1a4731; }
        .stat-card .lbl { font-size: 10px; color: #6b7280; margin-top: 4px; font-weight: 600; }
        .stat-card.warn .val { color: #dc2626; }
        .stat-card.ok   .val { color: #16a34a; }

        /* ── Section ── */
        .section { margin: 0 32px 20px; }
        .section-title {
            font-size: 12px;
            font-weight: 800;
            color: #1a4731;
            border-bottom: 2px solid #1a4731;
            padding-bottom: 6px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Table ── */
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th {
            background: #f3f4f6;
            padding: 8px 12px;
            text-align: center;
            font-weight: 700;
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #e5e7eb;
        }
        th:first-child { text-align: {{ $isRtl ? 'right' : 'left' }}; }
        td {
            padding: 9px 12px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
            text-align: center;
        }
        td:first-child { text-align: {{ $isRtl ? 'right' : 'left' }}; }
        tr:last-child td { border-bottom: none; }

        /* ── Badge ── */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
        }
        .badge-green  { background: #dcfce7; color: #15803d; }
        .badge-red    { background: #fee2e2; color: #dc2626; }
        .badge-amber  { background: #fef3c7; color: #d97706; }
        .badge-blue   { background: #dbeafe; color: #1d4ed8; }
        .badge-purple { background: #f3e8ff; color: #7c3aed; }

        /* ── Note ── */
        .note-item {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }
        .note-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
        .note-content { color: #374151; line-height: 1.6; font-size: 12px; }
        .note-footer  { font-size: 10px; color: #9ca3af; margin-top: 6px; }

        /* ── Risk ── */
        .risk-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 12px;
            border-radius: 6px;
            margin-bottom: 6px;
        }
        .risk-high   { background: #fee2e2; }
        .risk-medium { background: #fef3c7; }

        /* ── Footer ── */
        .footer {
            margin: 30px 32px 0;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
            color: #9ca3af;
        }
        .signature-box {
            border-top: 1px solid #9ca3af;
            width: 160px;
            padding-top: 6px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }

        /* Print */
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
            @page { margin: 15mm; size: A4; }
        }

        /* Print button */
        .print-bar {
            background: #1a4731;
            color: white;
            padding: 12px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            background: white;
            color: #1a4731;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: 800;
            font-size: 13px;
            cursor: pointer;
        }
        .btn-back {
            color: white;
            text-decoration: none;
            font-size: 12px;
            opacity: 0.8;
        }
        .btn-back:hover { opacity: 1; }
    </style>
</head>
<body>

{{-- ── شريط الطباعة (يختفي عند الطباعة) ── --}}
<div class="print-bar no-print">
    <a href="{{ route('students.show', $student->id) }}" class="btn-back">
        {{ $isRtl ? '←' : '→' }} {{ __('العودة لملف الطالب') }}
    </a>
    <button class="btn-print" onclick="window.print()">
        🖨️ {{ __('طباعة / حفظ PDF') }}
    </button>
</div>

{{-- ── Header ── --}}
<div class="header">
    <div class="header-logo">
        {{ __('جامعة الملك خالد') }}<br>
        {{ __('كلية علوم الحاسب') }}<br>
        {{ $student->department->name_ar }}
    </div>
    <div class="header-title">
        <h1>{{ __('الملف الإرشادي للطالب') }}</h1>
        <p>Academic Advising Profile</p>
    </div>
    <div class="header-meta">
        {{ __('رقم الملف') }}: {{ $student->student_id }}<br>
        {{ __('تاريخ الطباعة') }}: {{ now()->format('Y/m/d') }}<br>
        {{ __('المرشد') }}: {{ auth()->user()->name }}
    </div>
</div>

{{-- ── بيانات الطالب ── --}}
<div class="student-info">
    <div class="info-cell">
        <label>{{ __('الاسم') }}</label>
        <span>{{ $student->name_ar }}</span>
    </div>
    <div class="info-cell">
        <label>{{ __('الرقم الجامعي') }}</label>
        <span>{{ $student->student_id }}</span>
    </div>
    <div class="info-cell">
        <label>{{ __('القسم') }}</label>
        <span>{{ $student->department->name_ar }}</span>
    </div>
    <div class="info-cell">
        <label>{{ __('الحالة') }}</label>
        <span>{{ $student->status }} ({{ $student->academic_status === 'Warning' ? __('تحذير') : __('منتظم') }})</span>
    </div>
</div>

{{-- ── مؤشرات الأداء ── --}}
<div class="stats">
    <div class="stat-card {{ $student->gpa < 2 ? 'warn' : 'ok' }}">
        <div class="val">{{ number_format($student->gpa, 2) }}</div>
        <div class="lbl">{{ __('المعدل التراكمي') }}</div>
    </div>
    <div class="stat-card">
        <div class="val">{{ $student->total_credits }}</div>
        <div class="lbl">{{ __('الساعات المجتازة') }}</div>
    </div>
    <div class="stat-card">
        <div class="val">{{ $student->courses->count() }}</div>
        <div class="lbl">{{ __('المواد المسجلة') }}</div>
    </div>
    <div class="stat-card {{ $student->riskFlags->where('is_resolved',false)->isNotEmpty() ? 'warn' : 'ok' }}">
        <div class="val">{{ $student->riskFlags->where('is_resolved',false)->count() }}</div>
        <div class="lbl">{{ __('تنبيهات نشطة') }}</div>
    </div>
</div>

{{-- ── التنبيهات ── --}}
@if($student->riskFlags->where('is_resolved',false)->isNotEmpty())
<div class="section">
    <div class="section-title">⚠ {{ __('التنبيهات النشطة') }}</div>
    @foreach($student->riskFlags->where('is_resolved',false) as $flag)
    <div class="risk-item {{ $flag->severity === 'High' ? 'risk-high' : 'risk-medium' }}">
        <span style="font-weight:700;">
            {{ $flag->type === 'Low_GPA' ? __('معدل منخفض') : __('غيابات عالية') }}
        </span>
        <span class="badge {{ $flag->severity === 'High' ? 'badge-red' : 'badge-amber' }}">
            {{ $flag->severity === 'High' ? __('خطورة عالية') : __('خطورة متوسطة') }}
        </span>
    </div>
    @endforeach
</div>
@endif

{{-- ── المقررات ── --}}
@if($student->courses->isNotEmpty())
<div class="section">
    <div class="section-title">📚 {{ __('المقررات المسجلة') }}</div>
    <table>
        <colgroup>
            <col style="width:35%">
            <col style="width:12%">
            <col style="width:10%">
            <col style="width:10%">
            <col style="width:10%">
            <col style="width:13%">
        </colgroup>
        <thead>
            <tr>
                <th>{{ __('المادة') }}</th>
                <th>{{ __('الرمز') }}</th>
                <th>{{ __('الساعات') }}</th>
                <th>{{ __('الغيابات') }}</th>
                <th>{{ __('الدرجة') }}</th>
                <th>{{ __('الحالة') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($student->courses as $course)
            @php
                $abs     = $course->pivot->absences_count;
                $percent = $course->credits > 0 ? min(($abs / 15) * 100, 100) : 0;
            @endphp
            <tr>
                <td style="font-weight:700;">{{ $course->name }}</td>
                <td style="font-family:monospace; color:#6b7280;">{{ $course->code }}</td>
                <td>{{ $course->credits }}</td>
                <td style="color:{{ $abs > 10 ? '#dc2626' : '#374151' }}; font-weight:{{ $abs > 10 ? '700' : '400' }};">{{ $abs }}</td>
                <td>{{ $course->pivot->current_grade ?? '—' }}</td>
                <td>
                    @if($percent >= 100)
                        <span class="badge badge-red">{{ __('حرمان') }}</span>
                    @elseif($percent >= 60)
                        <span class="badge badge-amber">{{ __('إنذار') }}</span>
                    @else
                        <span class="badge badge-green">{{ __('منتظم') }}</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- ── السجل الإرشادي ── --}}
@if($student->advisingNotes->isNotEmpty())
<div class="section">
    <div class="section-title">📝 {{ __('السجل الإرشادي') }} ({{ $student->advisingNotes->count() }} {{ __('ملاحظة') }})</div>
    @foreach($student->advisingNotes as $note)
    <div class="note-item">
        <div class="note-header">
            <div style="display:flex; gap:8px; align-items:center;">
                <span class="badge {{ ($note->note_type ?? $note->type) === 'Academic' ? 'badge-blue' : 'badge-purple' }}">
                    {{ ($note->note_type ?? $note->type) === 'Academic' ? __('أكاديمية') : __('سلوكية') }}
                </span>
                @if($note->title)
                    <span style="font-weight:700; font-size:12px;">{{ $note->title }}</span>
                @endif
                @if($note->follow_up_required)
                    <span class="badge badge-amber">🚩 {{ __('متابعة مطلوبة') }}</span>
                @endif
            </div>
            <span style="font-size:10px; color:#9ca3af;">{{ $note->created_at->format('Y/m/d') }}</span>
        </div>
        <div class="note-content">{{ $note->content }}</div>
        <div class="note-footer">{{ __('بواسطة') }}: {{ $note->user->name ?? __('المرشد الأكاديمي') }}</div>
    </div>
    @endforeach
</div>
@endif

{{-- ── سجل حذف المواد ── --}}
@if($student->dropActions->isNotEmpty())
<div class="section">
    <div class="section-title">🗑 {{ __('سجل حذف المواد') }}</div>
    <table>
        <thead>
            <tr>
                <th>{{ __('المادة') }}</th>
                <th>{{ __('السبب') }}</th>
                <th>{{ __('الحالة') }}</th>
                <th>{{ __('تاريخ') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($student->dropActions as $drop)
            <tr>
                <td style="font-weight:700;">{{ $drop->course->name ?? '—' }}</td>
                <td style="color:#6b7280;">{{ $drop->reason ?? '—' }}</td>
                <td>
                    <span class="badge {{ $drop->status === 'Completed' ? 'badge-green' : 'badge-red' }}">
                        {{ $drop->status === 'Completed' ? __('مكتمل') : __('مرفوض') }}
                    </span>
                </td>
                <td style="color:#6b7280; font-size:11px;">{{ $drop->created_at->format('Y/m/d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- ── Footer ── --}}
<div class="footer">
    <div>
        <p>{{ __('نظام الإرشاد الأكاديمي') }} — {{ __('جامعة الملك خالد') }}</p>
        <p style="margin-top:4px;">{{ __('تم إنشاء هذا التقرير بتاريخ') }} {{ now()->format('Y/m/d H:i') }}</p>
    </div>
    <div style="text-align:{{ $isRtl ? 'left' : 'right' }};">
        <div class="signature-box">{{ __('توقيع المرشد الأكاديمي') }}</div>
    </div>
</div>

<script>
    // window.onload = () => window.print();
</script>

</body>
</html>
