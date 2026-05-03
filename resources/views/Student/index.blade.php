@extends('layouts.app')
@section('title', __('قائمة الطلاب'))

@section('content')

{{-- ───────────────────── Toolbar ───────────────────── --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">

    {{-- بحث --}}
    <form method="GET" action="{{ route('students.index') }}" class="flex items-center gap-3 flex-1 min-w-[260px]">
        <div class="relative flex-1 max-w-sm">
            <i class="fas fa-search absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="بحث بالاسم أو الرقم..."
                class="w-full pr-9 pl-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-kku-primary outline-none">
        </div>
        <button type="submit" class="px-4 py-2.5 bg-kku-primary text-white rounded-xl text-sm font-bold hover:bg-kku-dark transition-all">بحث</button>
        @if(request('search'))
            <a href="{{ route('students.index') }}" class="px-3 py-2.5 bg-gray-100 text-gray-500 rounded-xl text-sm hover:bg-gray-200 transition-all">
                <i class="fas fa-times"></i>
            </a>
        @endif
    </form>

    <div class="flex items-center gap-2">
        {{-- فحص التنبيهات --}}
        <form method="POST" action="{{ route('flags.scan') }}">
            @csrf
            <button type="submit"
                class="flex items-center gap-2 px-4 py-2.5 bg-amber-50 border border-amber-200 text-amber-700 rounded-xl text-sm font-bold hover:bg-amber-100 transition-all"
                title="فحص تنبيهات جميع الطلاب">
                <i class="fas fa-radar"></i> فحص التنبيهات
            </button>
        </form>

        {{-- إظهار/إخفاء الأعمدة --}}
        <div class="relative" id="colToggleWrapper">
            <button onclick="toggleColPanel()"
                class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-50 transition-all">
                <i class="fas fa-columns text-kku-primary"></i> الأعمدة
            </button>
            <div id="colPanel"
                class="hidden absolute left-0 top-12 z-50 bg-white border border-gray-200 rounded-2xl shadow-xl p-4 w-52 space-y-2">
                <p class="text-[10px] font-black text-gray-400 uppercase mb-3">إظهار / إخفاء</p>
                @foreach([
                    'col-major'   => 'التخصص',
                    'col-gpa'     => 'المعدل',
                    'col-credits' => 'الساعات',
                    'col-status'  => 'الحالة',
                    'col-flags'   => 'التنبيهات',
                    'col-notes'   => 'الملاحظات',
                ] as $col => $label)
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" checked class="col-toggle w-4 h-4 rounded accent-kku-primary"
                        data-col="{{ $col }}" onchange="toggleColumn('{{ $col }}', this.checked)">
                    <span class="text-sm text-gray-700 group-hover:text-kku-primary transition-colors">{{ $label }}</span>
                </label>
                @endforeach
                <hr class="my-2">
                <button onclick="resetColumns()" class="text-xs text-gray-400 hover:text-kku-primary transition-colors w-full text-right">
                    <i class="fas fa-undo ml-1"></i> إعادة تعيين
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ───────────────────── Layout رئيسي ───────────────────── --}}
<div class="grid grid-cols-12 gap-6">

    {{-- ══════════════ الجدول الرئيسي ══════════════ --}}
    <div class="col-span-12 lg:col-span-8">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">

            {{-- Header الجدول --}}
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-users text-kku-primary"></i> طلابي
                </h3>
                <span class="text-xs font-bold text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
                    {{ $students->total() }} طالب
                </span>
            </div>

            {{-- الجدول --}}
            <div class="overflow-x-auto">
                <table class="w-full text-right text-sm" id="studentsTable">
                    <thead class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase">
                        <tr>
                            <th class="px-5 py-3">الطالب</th>
                            <th class="px-5 py-3 col-major">التخصص</th>
                            <th class="px-5 py-3 col-gpa">المعدل</th>
                            <th class="px-5 py-3 col-credits">الساعات</th>
                            <th class="px-5 py-3 col-status">الحالة</th>
                            <th class="px-5 py-3 col-flags">التنبيهات</th>
                            <th class="px-5 py-3 col-notes text-center">الملاحظات</th>
                            <th class="px-5 py-3 text-center">إجراء سريع</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($students as $student)
                        @php
                            $activeFlags = $student->riskFlags->where('is_resolved', false);
                            $hasWarning  = $activeFlags->isNotEmpty();
                            $latestNotes = $student->advisingNotes->take(3);
                        @endphp
                        <tr class="hover:bg-gray-50/70 transition-all group {{ $hasWarning ? 'border-r-4 border-r-red-400' : '' }}">

                            {{-- اسم الطالب --}}
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl {{ $hasWarning ? 'bg-red-100 text-red-600' : 'bg-kku-primary/10 text-kku-primary' }} flex items-center justify-center font-black text-sm shrink-0">
                                        {{ mb_substr($student->name_ar, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 leading-tight">{{ $student->name_ar }}</div>
                                        <div class="text-[11px] text-gray-400 font-mono">{{ $student->student_id }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- التخصص --}}
                            <td class="px-5 py-4 col-major">
                                <div class="text-sm text-gray-600">{{ $student->department->name_ar }}</div>
                                <div class="text-[10px] text-gray-400">{{ $student->major ?? '—' }}</div>
                            </td>

                            {{-- المعدل --}}
                            <td class="px-5 py-4 col-gpa">
                                <span class="font-black text-base {{ $student->gpa < 2.0 ? 'text-red-500' : ($student->gpa >= 3.5 ? 'text-green-600' : 'text-gray-700') }}">
                                    {{ number_format($student->gpa, 2) }}
                                </span>
                                <div class="w-14 h-1 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                    <div class="h-full {{ $student->gpa < 2.0 ? 'bg-red-500' : 'bg-kku-primary' }}"
                                        style="width:{{ ($student->gpa / 5) * 100 }}%"></div>
                                </div>
                            </td>

                            {{-- الساعات --}}
                            <td class="px-5 py-4 col-credits">
                                <span class="font-bold text-gray-700">{{ $student->total_credits }}</span>
                                <span class="text-[10px] text-gray-400"> ساعة</span>
                            </td>

                            {{-- الحالة --}}
                            <td class="px-5 py-4 col-status">
                                @php
                                    $sc = match($student->status) {
                                        'منتظم' => 'bg-green-50 text-green-700 border-green-200',
                                        'متعثر' => 'bg-red-50 text-red-700 border-red-200',
                                        'خريج'  => 'bg-blue-50 text-blue-700 border-blue-200',
                                        default => 'bg-gray-50 text-gray-600 border-gray-200',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $sc }}">
                                    {{ $student->status }}
                                </span>
                            </td>

                            {{-- التنبيهات --}}
                            <td class="px-5 py-4 col-flags">
                                @if($activeFlags->isEmpty())
                                    <span class="text-gray-300 text-xs">—</span>
                                @else
                                    <div class="flex flex-col gap-1">
                                        @foreach($activeFlags as $flag)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold
                                            {{ $flag->severity === 'High' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                            <i class="fas fa-exclamation-circle text-[9px]"></i>
                                            {{ $flag->type === 'Low_GPA' ? 'معدل منخفض' : 'غيابات عالية' }}
                                        </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            {{-- الملاحظات (preview + expand) --}}
                            <td class="px-5 py-4 col-notes text-center">
                                @if($latestNotes->isEmpty())
                                    <span class="text-[10px] text-gray-300">لا توجد ملاحظات</span>
                                @else
                                    <button onclick="toggleNotes({{ $student->id }})"
                                        class="inline-flex items-center gap-1 text-[11px] font-bold text-kku-primary hover:underline">
                                        <i class="fas fa-comment-dots"></i>
                                        {{ $student->advisingNotes->count() }} ملاحظة
                                    </button>
                                @endif
                            </td>

                            {{-- إجراء سريع: زر ملاحظة --}}
                            <td class="px-5 py-4 text-center">
                                <button onclick="openQuickNote({{ $student->id }}, '{{ addslashes($student->name_ar) }}')"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-kku-primary/10 text-kku-primary rounded-lg text-[11px] font-bold hover:bg-kku-primary hover:text-white transition-all">
                                    <i class="fas fa-plus text-[10px]"></i> ملاحظة
                                </button>
                            </td>
                        </tr>

                        {{-- ── صف الملاحظات القابل للطي ── --}}
                        <tr id="notes-row-{{ $student->id }}" class="hidden bg-kku-primary/5">
                            <td colspan="8" class="px-6 py-4">
                                <div class="text-xs font-black text-gray-500 mb-3 flex items-center gap-2">
                                    <i class="fas fa-history text-kku-primary"></i>
                                    السجل الإرشادي — {{ $student->name_ar }}
                                </div>
                                @if($student->advisingNotes->isEmpty())
                                    <p class="text-xs text-gray-400">لا توجد ملاحظات.</p>
                                @else
                                <div class="space-y-2 max-h-56 overflow-y-auto pl-2">
                                    @foreach($student->advisingNotes->sortByDesc('created_at') as $note)
                                    <div class="bg-white rounded-xl p-3 border border-gray-100 flex gap-3">
                                        <span class="shrink-0 px-2 py-0.5 rounded-md text-[10px] font-bold h-fit
                                            {{ ($note->note_type ?? $note->type) === 'Academic' || ($note->note_type ?? $note->type) === 'أكاديمية'
                                                ? 'bg-blue-50 text-blue-600'
                                                : 'bg-purple-50 text-purple-600' }}">
                                            {{ ($note->note_type ?? $note->type) === 'Academic' ? 'أكاديمية' : (($note->note_type ?? $note->type) === 'Behavioral' ? 'سلوكية' : ($note->note_type ?? $note->type)) }}
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            @if($note->title)
                                                <p class="text-xs font-bold text-gray-700 mb-0.5">{{ $note->title }}</p>
                                            @endif
                                            <p class="text-xs text-gray-600 leading-relaxed">{{ $note->content }}</p>
                                            <p class="text-[10px] text-gray-400 mt-1">
                                                {{ $note->created_at->format('Y/m/d') }} —
                                                {{ $note->user->name ?? 'المرشد' }}
                                                @if($note->follow_up_required)
                                                    <span class="mr-2 text-amber-600 font-bold"><i class="fas fa-flag text-[9px]"></i> متابعة مطلوبة</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center text-gray-400">
                                <i class="fas fa-search text-4xl mb-3 block opacity-30"></i>
                                لا يوجد طلاب مطابقون
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $students->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    {{-- ══════════════ الشريط الجانبي ══════════════ --}}
    <div class="col-span-12 lg:col-span-4 space-y-5">

        {{-- ملخص الحالات --}}
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-kku-primary"></i> ملخص الحالات
            </h4>
            <div class="space-y-3">
                @foreach(['منتظم' => ['bg-green-50','text-green-700','fa-check-circle'], 'متعثر' => ['bg-red-50','text-red-600','fa-exclamation-circle'], 'خريج' => ['bg-blue-50','text-blue-600','fa-graduation-cap']] as $s => $cls)
                <div class="flex items-center justify-between p-3 {{ $cls[0] }} rounded-xl">
                    <span class="text-sm font-bold {{ $cls[1] }} flex items-center gap-2">
                        <i class="fas {{ $cls[2] }}"></i> {{ $s }}
                    </span>
                    <span class="font-black text-lg {{ $cls[1] }}">
                        {{ $students->getCollection()->where('status', $s)->count() }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- التنبيهات النشطة --}}
        @php $flaggedStudents = $students->getCollection()->filter(fn($s) => $s->riskFlags->where('is_resolved',false)->isNotEmpty()); @endphp
        @if($flaggedStudents->isNotEmpty())
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-red-100">
            <h4 class="font-bold text-red-600 mb-4 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                تنبيهات نشطة ({{ $flaggedStudents->count() }})
            </h4>
            <div class="space-y-2 max-h-48 overflow-y-auto">
                @foreach($flaggedStudents as $fs)
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div>
                        <p class="text-sm font-bold text-gray-800">{{ $fs->name_ar }}</p>
                        <p class="text-[10px] text-gray-400">{{ $fs->student_id }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        @foreach($fs->riskFlags->where('is_resolved',false) as $f)
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md
                            {{ $f->severity === 'High' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $f->type === 'Low_GPA' ? 'معدل' : 'غياب' }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- تنبيه النظام --}}
        <div class="bg-kku-dark text-white rounded-3xl p-6 shadow-lg relative overflow-hidden">
            <i class="fas fa-graduation-cap absolute -bottom-4 -left-4 text-white/10 text-6xl rotate-12"></i>
            <div class="relative">
                <h4 class="font-bold mb-2 flex items-center gap-2">
                    <i class="fas fa-robot text-kku-accent"></i> النظام الذكي
                </h4>
                <p class="text-xs opacity-80 leading-relaxed">
                    اضغط "فحص التنبيهات" لتوليد تنبيهات تلقائية لطلابك بناءً على المعدل والغيابات.
                </p>
                <form method="POST" action="{{ route('flags.scan') }}" class="mt-4">
                    @csrf
                    <button class="w-full py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-xs font-bold transition-all">
                        <i class="fas fa-search ml-1"></i> فحص الآن
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════
     Modal: ملاحظة سريعة
══════════════════════════════════════════════════════ --}}
<div id="quickNoteModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeQuickNote()"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg z-10 overflow-hidden animate-modal-in">

        {{-- Header --}}
        <div class="bg-kku-primary p-5 text-white flex justify-between items-center">
            <div>
                <p class="text-xs opacity-70 mb-0.5">ملاحظة إرشادية سريعة</p>
                <h3 class="font-bold text-lg" id="quickNoteStudentName">—</h3>
            </div>
            <button onclick="closeQuickNote()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/20 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Form --}}
        <form action="{{ route('notes.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="student_id" id="quickNoteStudentId">

            {{-- النوع والعنوان --}}
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">نوع الملاحظة</label>
                    <select name="note_type"
                        class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-kku-primary transition-all">
                        <option value="Academic">أكاديمية</option>
                        <option value="Behavioral">سلوكية</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">العنوان (اختياري)</label>
                    <input type="text" name="title" placeholder="مثال: متابعة غياب"
                        class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-kku-primary transition-all">
                </div>
            </div>

            {{-- المحتوى --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">تفاصيل الملاحظة</label>
                <textarea name="content" rows="4" required minlength="10"
                    placeholder="اكتب ما تم مناقشته مع الطالب..."
                    class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-kku-primary transition-all resize-none"></textarea>
            </div>

            {{-- متابعة مطلوبة --}}
            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl hover:bg-gray-50 transition-colors">
                <input type="checkbox" name="follow_up_required" value="1"
                    class="w-4 h-4 rounded accent-kku-primary">
                <span class="text-sm text-gray-600">تحتاج متابعة لاحقة</span>
                <i class="fas fa-flag text-amber-500 text-xs mr-auto"></i>
            </label>

            {{-- Buttons --}}
            <div class="flex gap-3 pt-1">
                <button type="submit"
                    class="flex-1 py-3 bg-kku-primary text-white rounded-xl font-bold hover:bg-kku-dark transition-all shadow-lg shadow-kku-primary/20">
                    <i class="fas fa-save ml-1"></i> حفظ الملاحظة
                </button>
                <button type="button" onclick="closeQuickNote()"
                    class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition-all">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>


{{-- Flash messages --}}
@if(session('success'))
<div id="flashMsg" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[200] bg-kku-primary text-white px-6 py-3 rounded-2xl shadow-xl text-sm font-bold flex items-center gap-3">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div id="flashMsg" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[200] bg-red-500 text-white px-6 py-3 rounded-2xl shadow-xl text-sm font-bold flex items-center gap-3">
    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
</div>
@endif


<script>
// ── Quick Note Modal ──────────────────────────────
function openQuickNote(studentId, studentName) {
    document.getElementById('quickNoteStudentId').value = studentId;
    document.getElementById('quickNoteStudentName').textContent = studentName;
    const modal = document.getElementById('quickNoteModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeQuickNote() {
    document.getElementById('quickNoteModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeQuickNote(); });

// ── Notes Row Toggle ──────────────────────────────
function toggleNotes(studentId) {
    const row = document.getElementById('notes-row-' + studentId);
    row.classList.toggle('hidden');
}

// ── Column Toggle ─────────────────────────────────
const COL_KEY = 'kku_advisor_cols';

function toggleColumn(colClass, visible) {
    document.querySelectorAll('.' + colClass).forEach(el => {
        el.style.display = visible ? '' : 'none';
    });
    saveColumnPrefs();
}

function resetColumns() {
    document.querySelectorAll('.col-toggle').forEach(cb => {
        cb.checked = true;
        toggleColumn(cb.dataset.col, true);
    });
    localStorage.removeItem(COL_KEY);
}

function saveColumnPrefs() {
    const prefs = {};
    document.querySelectorAll('.col-toggle').forEach(cb => {
        prefs[cb.dataset.col] = cb.checked;
    });
    localStorage.setItem(COL_KEY, JSON.stringify(prefs));
}

function loadColumnPrefs() {
    const saved = localStorage.getItem(COL_KEY);
    if (!saved) return;
    const prefs = JSON.parse(saved);
    document.querySelectorAll('.col-toggle').forEach(cb => {
        const visible = prefs[cb.dataset.col] !== false;
        cb.checked = visible;
        toggleColumn(cb.dataset.col, visible);
    });
}

// ── Col Panel Toggle ──────────────────────────────
function toggleColPanel() {
    document.getElementById('colPanel').classList.toggle('hidden');
}
document.addEventListener('click', e => {
    if (!e.target.closest('#colToggleWrapper')) {
        document.getElementById('colPanel').classList.add('hidden');
    }
});

// ── Flash Auto-hide ───────────────────────────────
const flash = document.getElementById('flashMsg');
if (flash) setTimeout(() => flash.style.opacity = '0', 3000);

// ── Init ──────────────────────────────────────────
document.addEventListener('DOMContentLoaded', loadColumnPrefs);
</script>

@endsection
