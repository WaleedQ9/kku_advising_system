@extends('layouts.app')
@section('title', __('الرئيسية'))

@section('content')
@php
    $advisor        = auth()->user();
    $allStudents    = \App\Models\Student::where('department_id', $advisor->department_id);
    $totalStudents  = $allStudents->count();
    $atRisk         = (clone $allStudents)->where('academic_status', 'Warning')->count();
    $topPerformers  = (clone $allStudents)->where('gpa', '>=', 3.75)->count();
    $followUp       = \App\Models\AdvisingNote::whereHas('student', fn($q) => $q->where('department_id', $advisor->department_id))
                        ->where('follow_up_required', true)->count();
    $activeFlags    = \App\Models\RiskFlag::whereHas('student', fn($q) => $q->where('department_id', $advisor->department_id))
                        ->where('is_resolved', false)->count();
    $recentNotes    = \App\Models\AdvisingNote::whereHas('student', fn($q) => $q->where('department_id', $advisor->department_id))
                        ->with(['student','user'])->latest()->take(5)->get();
    $flaggedStudents = \App\Models\Student::where('department_id', $advisor->department_id)
                        ->whereHas('riskFlags', fn($q) => $q->where('is_resolved', false))
                        ->with(['riskFlags' => fn($q) => $q->where('is_resolved', false)])
                        ->take(5)->get();
    $followUpStudents = \App\Models\Student::where('department_id', $advisor->department_id)
                        ->whereHas('advisingNotes', fn($q) => $q->where('follow_up_required', true))
                        ->with(['advisingNotes' => fn($q) => $q->where('follow_up_required', true)->latest()->take(1)])
                        ->take(5)->get();
    $hour = now()->hour;
    $greeting = $hour < 12 ? 'صباح الخير' : ($hour < 17 ? 'مساء الخير' : 'مساء النور');
@endphp

{{-- ══════════ Greeting ══════════ --}}
<div class="relative bg-kku-dark rounded-3xl p-8 mb-8 overflow-hidden shadow-xl">
    {{-- خلفية زخرفية --}}
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full -translate-x-32 -translate-y-32"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-kku-accent rounded-full translate-x-48 translate-y-48"></div>
    </div>
    <div class="relative flex flex-wrap justify-between items-center gap-6">
        <div>
            <p class="text-green-300 text-sm font-bold mb-1">
                <i class="fas fa-sun ml-2 text-yellow-300"></i>{{ $greeting }}،
            </p>
            <h1 class="text-white text-3xl font-black leading-tight">
                {{ $advisor->name }}
            </h1>
            <p class="text-green-200/80 mt-2 text-sm">
                {{ $advisor->department->name_ar ?? 'قسمك' }} ·
                الفصل الدراسي الثاني 1447هـ
            </p>
            @if($followUp > 0)
            <div class="mt-4 inline-flex items-center gap-2 bg-amber-400/20 border border-amber-400/30 text-amber-300 px-4 py-2 rounded-xl text-sm font-bold">
                <i class="fas fa-flag animate-pulse"></i>
                لديك {{ $followUp }} {{ __('طلاب بحاجة لمتابعة') }}
            </div>
            @endif
        </div>
        <div class="flex gap-3">
            <a href="{{ route('students.index') }}"
                class="flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white px-5 py-3 rounded-2xl text-sm font-bold transition-all">
                <i class="fas fa-users"></i> {{ __('قائمة الطلاب') }}
            </a>
            <button onclick="document.getElementById('quickScanForm').submit()"
                class="flex items-center gap-2 bg-kku-accent hover:bg-yellow-400 text-kku-dark px-5 py-3 rounded-2xl text-sm font-bold transition-all shadow-lg">
                <i class="fas fa-search"></i> {{ __('فحص التنبيهات') }}
            </button>
            <form id="quickScanForm" method="POST" action="{{ route('flags.scan') }}" class="hidden">@csrf</form>
        </div>
    </div>
</div>

{{-- ══════════ Stats ══════════ --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">

    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 group hover:border-kku-primary hover:shadow-md transition-all">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 bg-kku-primary/10 rounded-2xl flex items-center justify-center group-hover:bg-kku-primary transition-all">
                <i class="fas fa-users text-kku-primary text-lg group-hover:text-white transition-all"></i>
            </div>
            <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded-lg">{{ __('إجمالي') }}</span>
        </div>
        <p class="text-3xl font-black text-gray-800">{{ $totalStudents }}</p>
        <p class="text-xs text-gray-400 mt-1 font-bold">{{ __('إجمالي الطلاب') }}</p>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 group hover:border-red-400 hover:shadow-md transition-all">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center group-hover:bg-red-500 transition-all">
                <i class="fas fa-exclamation-triangle text-red-500 text-lg group-hover:text-white transition-all"></i>
            </div>
            @if($atRisk > 0)
            <span class="text-[10px] font-bold text-red-600 bg-red-50 px-2 py-1 rounded-lg animate-pulse">{{ __('تنبيه') }}</span>
            @endif
        </div>
        <p class="text-3xl font-black {{ $atRisk > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $atRisk }}</p>
        <p class="text-xs text-gray-400 mt-1 font-bold">{{ __('متعثرين أكاديمياً') }}</p>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 group hover:border-amber-400 hover:shadow-md transition-all">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center group-hover:bg-amber-400 transition-all">
                <i class="fas fa-flag text-amber-500 text-lg group-hover:text-white transition-all"></i>
            </div>
            @if($followUp > 0)
            <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2 py-1 rounded-lg">{{ __('يحتاجون متابعة') }}</span>
            @endif
        </div>
        <p class="text-3xl font-black {{ $followUp > 0 ? 'text-amber-600' : 'text-gray-800' }}">{{ $followUp }}</p>
        <p class="text-xs text-gray-400 mt-1 font-bold">{{ __('طلبات معلقة') }}</p>
    </div>

    <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 group hover:border-green-400 hover:shadow-md transition-all">
        <div class="flex justify-between items-start mb-4">
            <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center group-hover:bg-green-500 transition-all">
                <i class="fas fa-star text-green-500 text-lg group-hover:text-white transition-all"></i>
            </div>
            <span class="text-[10px] font-bold text-green-700 bg-green-50 px-2 py-1 rounded-lg">GPA ≥ 3.75</span>
        </div>
        <p class="text-3xl font-black text-green-600">{{ $topPerformers }}</p>
        <p class="text-xs text-gray-400 mt-1 font-bold">{{ __('المتفوقين') }}</p>
    </div>
</div>

{{-- ══════════ Main Grid ══════════ --}}
<div class="grid grid-cols-12 gap-6">

    {{-- ── تنبيهات النظام ── --}}
    <div class="col-span-12 lg:col-span-4">
        <div class="bg-white rounded-3xl shadow-sm border border-red-100 overflow-hidden h-full">
            <div class="p-5 border-b border-red-50 flex justify-between items-center bg-red-50/50">
                <h3 class="font-bold text-red-700 flex items-center gap-2 text-sm">
                    <i class="fas fa-robot"></i> تنبيهات النظام
                    @if($flaggedStudents->count())
                    <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-bold">
                        {{ $flaggedStudents->count() }}
                    </span>
                    @endif
                </h3>
                <span class="text-[10px] text-red-400 font-bold">{{ __('معدل / غياب') }}</span>
            </div>

            @if($flaggedStudents->isEmpty())
            <div class="py-12 text-center text-gray-400">
                <i class="fas fa-check-circle text-3xl mb-2 block text-green-400 opacity-60"></i>
                <p class="text-xs font-bold text-gray-500">{{ __('لا توجد تنبيهات نشطة') }}</p>
            </div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($flaggedStudents as $st)
                <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-red-50/30 transition-colors">
                    <div class="w-9 h-9 rounded-xl bg-red-100 text-red-600 flex items-center justify-center font-black text-xs shrink-0">
                        {{ mb_substr($st->name_ar, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-800 text-xs truncate">{{ $st->name_ar }}</p>
                        <div class="flex gap-1 mt-0.5 flex-wrap">
                            @foreach($st->riskFlags as $flag)
                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded
                                {{ $flag->severity === 'High' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $flag->type === 'Low_GPA' ? 'معدل منخفض' : 'غيابات' }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="font-black text-sm {{ $st->gpa < 2 ? 'text-red-500' : 'text-gray-600' }}">
                            {{ number_format($st->gpa, 2) }}
                        </span>
                        <a href="{{ route('students.show', $st->id) }}"
                            class="p-1.5 bg-kku-primary/10 text-kku-primary rounded-lg hover:bg-kku-primary hover:text-white transition-all">
                            <i class="fas fa-arrow-left text-[10px]"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ── متابعات المرشد ── --}}
    <div class="col-span-12 lg:col-span-3">
        <div class="bg-white rounded-3xl shadow-sm border border-amber-100 overflow-hidden h-full">
            <div class="p-5 border-b border-amber-50 flex justify-between items-center bg-amber-50/50">
                <h3 class="font-bold text-amber-700 flex items-center gap-2 text-sm">
                    <i class="fas fa-flag"></i> متابعات المرشد
                    @if($followUpStudents->count())
                    <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-bold">
                        {{ $followUpStudents->count() }}
                    </span>
                    @endif
                </h3>
                <span class="text-[10px] text-amber-400 font-bold">{{ __('يدوي') }}</span>
            </div>

            @if($followUpStudents->isEmpty())
            <div class="py-12 text-center text-gray-400">
                <i class="fas fa-check text-3xl mb-2 block text-green-400 opacity-60"></i>
                <p class="text-xs font-bold text-gray-500">{{ __('لا توجد متابعات معلقة') }}</p>
            </div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($followUpStudents as $st)
                @php $note = $st->advisingNotes->first(); @endphp
                <div class="flex items-center gap-3 px-5 py-3.5 hover:bg-amber-50/30 transition-colors">
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center font-black text-xs shrink-0">
                        {{ mb_substr($st->name_ar, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-gray-800 text-xs truncate">{{ $st->name_ar }}</p>
                        @if($note?->title)
                        <p class="text-[9px] text-amber-600 truncate mt-0.5">{{ $note->title }}</p>
                        @else
                        <p class="text-[9px] text-gray-400 truncate mt-0.5">{{ Str::limit($note?->content, 30) }}</p>
                        @endif
                    </div>
                    <a href="{{ route('students.show', $st->id) }}"
                        class="p-1.5 bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-500 hover:text-white transition-all shrink-0">
                        <i class="fas fa-arrow-left text-[10px]"></i>
                    </a>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- ── العمود الجانبي ── --}}
    <div class="col-span-12 lg:col-span-5 space-y-5">

        {{-- نشاط حديث --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-history text-kku-primary"></i>
                    {{ __('آخر الملاحظات الإرشادية') }}
                </h3>
            </div>
            @if($recentNotes->isEmpty())
            <div class="py-10 text-center text-gray-400 text-sm">{{ __('لا توجد ملاحظات بعد') }}</div>
            @else
            <div class="divide-y divide-gray-50">
                @foreach($recentNotes as $note)
                <div class="px-5 py-3 flex gap-3 hover:bg-gray-50/70 transition-colors">
                    <div class="w-8 h-8 rounded-lg shrink-0 flex items-center justify-center text-[10px] font-black
                        {{ ($note->note_type ?? $note->type) === 'Academic' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600' }}">
                        {{ mb_substr($note->student->name_ar ?? '?', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center">
                            <p class="text-xs font-bold text-gray-800 truncate">{{ $note->student->name_ar }}</p>
                            <span class="text-[9px] text-gray-400 shrink-0 mr-2">{{ $note->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[11px] text-gray-500 truncate mt-0.5">{{ $note->content }}</p>
                        @if($note->follow_up_required)
                        <span class="text-[9px] text-amber-600 font-bold">
                            <i class="fas fa-flag text-[8px]"></i> {{ __('متابعة مطلوبة') }}
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- توزيع الطلاب --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-kku-primary"></i>
                {{ __('ملخص الحالات') }}
            </h3>
            @php
                $regularCount = \App\Models\Student::where('department_id', $advisor->department_id)->where('status','منتظم')->count();
                $atRiskCount  = \App\Models\Student::where('department_id', $advisor->department_id)->where('status','متعثر')->count();
                $gradCount    = \App\Models\Student::where('department_id', $advisor->department_id)->where('status','خريج')->count();
                $total        = $totalStudents ?: 1;
            @endphp
            <div class="space-y-3">
                @foreach([
                    ['منتظم',  $regularCount, 'bg-green-500', 'text-green-600', 'bg-green-50'],
                    ['متعثر',  $atRiskCount,  'bg-red-500',   'text-red-600',   'bg-red-50'],
                    ['خريج',   $gradCount,    'bg-blue-500',  'text-blue-600',  'bg-blue-50'],
                ] as [$label, $count, $bar, $text, $bg])
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <span class="text-xs font-bold text-gray-600">{{ __($label) }}</span>
                        <span class="text-xs font-black {{ $text }}">{{ $count }}</span>
                    </div>
                    <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $bar }} rounded-full transition-all"
                            style="width: {{ $total > 0 ? round(($count/$total)*100) : 0 }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- تنبيه التنبيهات النشطة --}}
        @if($activeFlags > 0)
        <div class="bg-gradient-to-br from-red-500 to-red-600 text-white rounded-3xl p-6 shadow-lg relative overflow-hidden">
            <i class="fas fa-exclamation-triangle absolute -bottom-3 -left-3 text-white/10 text-7xl"></i>
            <div class="relative">
                <h4 class="font-bold mb-1 flex items-center gap-2">
                    <i class="fas fa-bell animate-pulse"></i>
                    {{ $activeFlags }} {{ __('تنبيهات نشطة') }}
                </h4>
                <p class="text-xs text-red-100 leading-relaxed mb-4">
                    {{ __('يوجد طلاب يحتاجون تدخلاً عاجلاً. راجع قائمة الطلاب وافحص التنبيهات.') }}
                </p>
                <a href="{{ route('students.index') }}"
                    class="inline-flex items-center gap-2 bg-white text-red-600 px-4 py-2 rounded-xl text-xs font-black hover:bg-red-50 transition-all">
                    <i class="fas fa-arrow-left"></i> {{ __('عرض جميع الطلاب') }}
                </a>
            </div>
        </div>
        @else
        <div class="bg-gradient-to-br from-kku-dark to-green-900 text-white rounded-3xl p-6 shadow-lg relative overflow-hidden">
            <i class="fas fa-graduation-cap absolute -bottom-3 -left-3 text-white/10 text-7xl"></i>
            <div class="relative">
                <h4 class="font-bold mb-1 flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-300"></i> {{ __('لا توجد تنبيهات نشطة') }}
                </h4>
                <p class="text-xs text-green-200/80 leading-relaxed mb-4">
                    {{ __('جميع طلاب قسمك ضمن المعدل الطبيعي. استمر في المتابعة الدورية.') }}
                </p>
                <form method="POST" action="{{ route('flags.scan') }}">
                    @csrf
                    <button class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all">
                        <i class="fas fa-sync"></i> {{ __('فحص الآن') }}
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
