@extends('layouts.app')
@section('title', $student->name_ar)

@section('content')
<div class="space-y-6">

    {{-- ── Header ── --}}
    <div class="flex flex-wrap justify-between items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl {{ $student->hasRiskFlags() ? 'bg-red-500' : 'bg-kku-primary' }} text-white flex items-center justify-center text-2xl font-bold shadow-lg">
                {{ mb_substr($student->name_ar, 0, 1) }}
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    {{ $student->name_ar }}
                    @if($student->hasRiskFlags())
                        <span class="text-xs font-bold px-2 py-0.5 bg-red-100 text-red-600 rounded-full">
                            <i class="fas fa-exclamation-circle"></i> يحتاج متابعة
                        </span>
                    @endif
                </h2>
                <p class="text-gray-400 text-sm mt-0.5">
                    {{ $student->student_id }} | {{ $student->department->name_ar }}
                    @if($student->major) · {{ $student->major }} @endif
                </p>
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            <button onclick="openNoteModal()"
                class="px-4 py-2 bg-kku-primary text-white rounded-xl text-sm font-bold hover:bg-kku-dark transition-all">
                <i class="fas fa-plus ml-1"></i> ملاحظة إرشادية
            </button>
            <a href="{{ route('students.index') }}"
                class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-200 transition-all">
                <i class="fas fa-arrow-right ml-1"></i> قائمة الطلاب
            </a>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-6">

        {{-- ── العمود الجانبي ── --}}
        <div class="col-span-12 lg:col-span-4 space-y-5">

            {{-- مؤشرات الأداء --}}
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-800 mb-4">مؤشرات الأداء</h4>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 {{ $student->gpa < 2 ? 'bg-red-50' : 'bg-green-50' }} rounded-2xl">
                        <span class="text-xs font-bold text-gray-600">المعدل التراكمي</span>
                        <span class="text-lg font-black {{ $student->gpa < 2 ? 'text-red-600' : 'text-green-600' }}">
                            {{ number_format($student->gpa, 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-blue-50 rounded-2xl">
                        <span class="text-xs font-bold text-gray-600">الساعات المجتازة</span>
                        <span class="text-lg font-black text-blue-600">{{ $student->total_credits }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 {{ $student->courses->sum('pivot.absences_count') > 15 ? 'bg-red-50' : 'bg-gray-50' }} rounded-2xl">
                        <span class="text-xs font-bold text-gray-600">إجمالي الغيابات</span>
                        <span class="text-lg font-black {{ $student->courses->sum('pivot.absences_count') > 15 ? 'text-red-600' : 'text-gray-700' }}">
                            {{ $student->courses->sum('pivot.absences_count') }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-3 {{ $student->academic_status === 'Warning' ? 'bg-amber-50' : 'bg-gray-50' }} rounded-2xl">
                        <span class="text-xs font-bold text-gray-600">الحالة الأكاديمية</span>
                        <span class="text-sm font-bold {{ $student->academic_status === 'Warning' ? 'text-amber-600' : 'text-green-600' }}">
                            {{ $student->academic_status === 'Warning' ? 'تحذير' : 'منتظم' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- التنبيهات النشطة --}}
            @if($student->riskFlags->where('is_resolved', false)->isNotEmpty())
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-red-100">
                <h4 class="font-bold text-red-600 mb-4 flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i> تنبيهات نشطة
                </h4>
                <div class="space-y-2">
                    @foreach($student->riskFlags->where('is_resolved', false) as $flag)
                    <div class="flex items-center justify-between p-3 {{ $flag->severity === 'High' ? 'bg-red-50 border border-red-200' : 'bg-amber-50 border border-amber-200' }} rounded-xl">
                        <div>
                            <p class="text-sm font-bold {{ $flag->severity === 'High' ? 'text-red-700' : 'text-amber-700' }}">
                                {{ $flag->type === 'Low_GPA' ? 'معدل منخفض' : 'غيابات عالية' }}
                            </p>
                            <p class="text-[10px] text-gray-400">{{ $flag->severity === 'High' ? 'خطورة عالية' : 'خطورة متوسطة' }}</p>
                        </div>
                        <form method="POST" action="{{ route('flags.resolve', $flag->id) }}">
                            @csrf
                            <button type="submit" class="text-xs font-bold text-gray-400 hover:text-green-600 transition-colors">
                                <i class="fas fa-check-circle"></i> حل
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- سجل حذف المواد --}}
            @if($student->dropActions->isNotEmpty())
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-history text-kku-primary"></i> سجل حذف المواد
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full mr-auto">
                        {{ $student->dropActions->where('status','Completed')->count() }}/3
                    </span>
                </h4>
                <div class="space-y-2">
                    @foreach($student->dropActions as $drop)
                    <div class="p-3 rounded-xl border {{ $drop->status === 'Completed' ? 'bg-gray-50 border-gray-200' : 'bg-red-50 border-red-200' }}">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-bold text-gray-700">{{ $drop->course->name ?? '—' }}</p>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $drop->status === 'Completed' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $drop->status === 'Completed' ? 'مكتمل' : 'مرفوض' }}
                            </span>
                        </div>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $drop->created_at->format('Y/m/d') }}</p>
                        @if($drop->reason)
                            <p class="text-[11px] text-gray-500 mt-1 italic">{{ $drop->reason }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- ── العمود الرئيسي ── --}}
        <div class="col-span-12 lg:col-span-8 space-y-6">

            {{-- جدول المقررات مع زر حذف --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-book-open text-kku-primary"></i> المقررات المسجلة
                    </h4>
                    <span class="text-xs text-gray-400">
                        محاولات الحذف المتبقية:
                        <span class="font-black text-kku-primary">
                            {{ max(0, 3 - $student->dropActions->where('status','Completed')->count()) }}/3
                        </span>
                    </span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-right text-sm">
                        <thead class="bg-gray-50 text-[11px] font-bold text-gray-500 uppercase">
                            <tr>
                                <th class="px-5 py-3">المادة</th>
                                <th class="px-5 py-3">النوع</th>
                                <th class="px-5 py-3">الغياب</th>
                                <th class="px-5 py-3">نسبة الخطر</th>
                                <th class="px-5 py-3 text-center">الحالة</th>
                                <th class="px-5 py-3 text-center">حذف</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($student->courses as $course)
                            @php
                                $absences = $course->pivot->absences_count;
                                $limit    = 15;
                                $percent  = min(($absences / $limit) * 100, 100);
                                $canDrop  = $student->dropActions->where('status','Completed')->count() < 3;
                            @endphp
                            <tr class="hover:bg-gray-50/70 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-800">{{ $course->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono">{{ $course->code }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold {{ $course->level_type == 'تخصص' ? 'bg-purple-50 text-purple-600' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $course->level_type }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-bold {{ $absences > 10 ? 'text-red-500' : 'text-gray-600' }}">
                                    {{ $absences }}
                                </td>
                                <td class="px-5 py-4 w-36">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full {{ $percent >= 100 ? 'bg-red-600' : ($percent >= 60 ? 'bg-amber-500' : 'bg-green-500') }}"
                                                style="width:{{ $percent }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-bold w-8">{{ round($percent) }}%</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($percent >= 100)
                                        <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-red-100 text-red-600">حرمان</span>
                                    @elseif($percent >= 60)
                                        <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-amber-50 text-amber-600">إنذار</span>
                                    @else
                                        <span class="px-2 py-1 rounded-md text-[10px] font-bold bg-green-50 text-green-600">منتظم</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($canDrop)
                                        <button onclick="openDropModal({{ $course->id }}, '{{ addslashes($course->name) }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-lg text-[11px] font-bold hover:bg-red-500 hover:text-white transition-all">
                                            <i class="fas fa-trash-alt text-[10px]"></i> حذف
                                        </button>
                                    @else
                                        <span class="text-[10px] text-gray-300">استُنفدت المحاولات</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-gray-400 text-sm">لا توجد مواد مسجلة</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- السجل الإرشادي --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h4 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-history text-kku-primary"></i> السجل الإرشادي
                    </h4>
                    <span class="text-xs text-gray-400">{{ $notes->count() }} ملاحظة</span>
                </div>
                <div class="p-6 space-y-4">
                    @forelse($notes as $note)
                    <div class="relative pr-8 pb-5 border-r-2 border-gray-100 last:border-0 last:pb-0">
                        <div class="absolute right-[-6px] top-0 w-3 h-3 rounded-full bg-kku-primary border-2 border-white shadow-sm"></div>
                        <div class="bg-gray-50 p-4 rounded-2xl">
                            <div class="flex justify-between items-start gap-2 mb-2 flex-wrap">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-lg
                                        {{ ($note->note_type ?? $note->type) === 'Academic' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }}">
                                        {{ ($note->note_type ?? $note->type) === 'Academic' ? 'أكاديمية' : (($note->note_type ?? $note->type) === 'Behavioral' ? 'سلوكية' : ($note->note_type ?? $note->type)) }}
                                    </span>
                                    @if($note->title)
                                        <span class="text-xs font-bold text-gray-700">{{ $note->title }}</span>
                                    @endif
                                    @if($note->follow_up_required)
                                        <span class="text-[10px] font-bold text-amber-600">
                                            <i class="fas fa-flag text-[9px]"></i> متابعة
                                        </span>
                                    @endif
                                </div>
                                <span class="text-[10px] text-gray-400">{{ $note->created_at->format('Y/m/d') }}</span>
                            </div>
                            <p class="text-sm text-gray-700 leading-relaxed">{{ $note->content }}</p>
                            <p class="text-[10px] text-gray-400 mt-2 italic">بواسطة: {{ $note->user->name ?? 'المرشد الأكاديمي' }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-10 text-gray-400">
                        <i class="fas fa-comment-slash text-4xl mb-3 block opacity-30"></i>
                        <p class="text-sm">لا توجد سجلات إرشادية سابقة</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ══════════════ Modal: ملاحظة إرشادية ══════════════ --}}
<div id="noteModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeNoteModal()"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg z-10 overflow-hidden animate-modal-in">
        <div class="bg-kku-primary p-5 text-white flex justify-between items-center">
            <h3 class="font-bold text-lg">إضافة ملاحظة إرشادية</h3>
            <button onclick="closeNoteModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/20 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('notes.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="student_id" value="{{ $student->id }}">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">نوع الملاحظة</label>
                    <select name="note_type" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-kku-primary transition-all">
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
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">تفاصيل الملاحظة</label>
                <textarea name="content" rows="4" required minlength="10"
                    placeholder="اكتب ما تم مناقشته مع الطالب..."
                    class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-kku-primary transition-all resize-none"></textarea>
            </div>
            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl hover:bg-gray-50 transition-colors">
                <input type="checkbox" name="follow_up_required" value="1" class="w-4 h-4 rounded accent-kku-primary">
                <span class="text-sm text-gray-600">تحتاج متابعة لاحقة</span>
                <i class="fas fa-flag text-amber-500 text-xs mr-auto"></i>
            </label>
            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 py-3 bg-kku-primary text-white rounded-xl font-bold hover:bg-kku-dark transition-all">
                    <i class="fas fa-save ml-1"></i> حفظ الملاحظة
                </button>
                <button type="button" onclick="closeNoteModal()" class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition-all">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════ Modal: حذف مادة ══════════════ --}}
<div id="dropModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeDropModal()"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md z-10 overflow-hidden animate-modal-in">
        <div class="bg-red-500 p-5 text-white flex justify-between items-center">
            <div>
                <p class="text-xs opacity-75 mb-0.5">تأكيد حذف المادة</p>
                <h3 class="font-bold text-lg" id="dropCourseName">—</h3>
            </div>
            <button onclick="closeDropModal()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/20 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('drop.store', $student->id) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="course_id" id="dropCourseId">

            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex gap-3">
                <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 shrink-0"></i>
                <p class="text-sm text-amber-700">
                    سيتم حذف المادة من سجل الطالب وخصم ساعاتها. تأكد من صحة قرارك قبل المتابعة.
                    <span class="block mt-1 font-bold">
                        المحاولات المتبقية: {{ max(0, 3 - $student->dropActions->where('status','Completed')->count()) }}/3
                    </span>
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">سبب الحذف <span class="text-red-500">*</span></label>
                <textarea name="reason" rows="3" required minlength="10"
                    placeholder="اذكر سبب حذف المادة..."
                    class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-red-400 transition-all resize-none"></textarea>
            </div>

            <div class="flex gap-3 pt-1">
                <button type="submit"
                    class="flex-1 py-3 bg-red-500 text-white rounded-xl font-bold hover:bg-red-600 transition-all">
                    <i class="fas fa-trash-alt ml-1"></i> تأكيد الحذف
                </button>
                <button type="button" onclick="closeDropModal()"
                    class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition-all">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>


{{-- Flash --}}
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
// Note Modal
function openNoteModal()  { document.getElementById('noteModal').classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
function closeNoteModal() { document.getElementById('noteModal').classList.add('hidden');    document.body.style.overflow = ''; }

// Drop Modal
function openDropModal(courseId, courseName) {
    document.getElementById('dropCourseId').value  = courseId;
    document.getElementById('dropCourseName').textContent = courseName;
    document.getElementById('dropModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeDropModal() { document.getElementById('dropModal').classList.add('hidden'); document.body.style.overflow = ''; }

// Esc key
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') { closeNoteModal(); closeDropModal(); }
});

// Flash auto-hide
const flash = document.getElementById('flashMsg');
if (flash) setTimeout(() => flash.style.opacity = '0', 3500);
</script>

@endsection
