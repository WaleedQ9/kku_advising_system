@extends('layouts.app')
@section('title', 'لوحة تحكم رئيس القسم')

@section('content')
@php
    $chair = auth()->user();
    $hour  = now()->hour;
    $greeting = $hour < 12 ? 'صباح الخير' : ($hour < 17 ? 'مساء الخير' : 'مساء النور');
@endphp

{{-- ══════════ Greeting ══════════ --}}
<div class="relative bg-kku-dark rounded-3xl p-8 mb-8 overflow-hidden shadow-xl">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full -translate-x-32 -translate-y-32"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-kku-accent rounded-full translate-x-48 translate-y-48"></div>
    </div>
    <div class="relative flex flex-wrap justify-between items-center gap-6">
        <div>
            <p class="text-green-300 text-sm font-bold mb-1">
                <i class="fas fa-sun ml-2 text-yellow-300"></i>{{ $greeting }}،
            </p>
            <h1 class="text-white text-3xl font-black leading-tight">{{ $chair->name }}</h1>
            <p class="text-green-200/80 mt-2 text-sm">
                <i class="fas fa-building ml-1"></i>رئيس قسم {{ $chair->department->name_ar ?? '' }}
                · الفصل الدراسي الثاني 1447هـ
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('chair.report.print') }}"
               target="_blank"
               class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white px-5 py-3 rounded-xl font-bold transition">
                <i class="fas fa-print"></i> طباعة التقرير
            </a>
            <a href="{{ route('chair.report.csv') }}"
               class="inline-flex items-center gap-2 bg-kku-accent/80 hover:bg-kku-accent border border-kku-accent/30 text-white px-5 py-3 rounded-xl font-bold transition">
                <i class="fas fa-file-csv"></i> تصدير CSV
            </a>
        </div>
    </div>
</div>

{{-- ══════════ Stats Cards ══════════ --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                <i class="fas fa-users text-green-600"></i>
            </div>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $totalStudents }}</p>
        <p class="text-sm text-gray-500 mt-1">إجمالي الطلاب</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                <i class="fas fa-exclamation-triangle text-red-500"></i>
            </div>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $atRiskStudents }}</p>
        <p class="text-sm text-gray-500 mt-1">طلاب متعثرون</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                <i class="fas fa-chalkboard-teacher text-blue-500"></i>
            </div>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $advisors->count() }}</p>
        <p class="text-sm text-gray-500 mt-1">المرشدون الأكاديميون</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                <i class="fas fa-flag text-amber-500"></i>
            </div>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $totalActiveFlags }}</p>
        <p class="text-sm text-gray-500 mt-1">إنذارات نشطة</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- ══════════ Advisors Table ══════════ --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-black text-gray-800 text-lg">
                <i class="fas fa-chalkboard-teacher text-kku-primary ml-2"></i>المرشدون في القسم
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-gray-500 font-semibold">المرشد</th>
                        <th class="px-4 py-3 text-center text-gray-500 font-semibold">عدد الطلاب</th>
                        <th class="px-4 py-3 text-center text-gray-500 font-semibold">البريد</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($advisors as $advisor)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $advisor->name }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-kku-primary/10 text-kku-primary font-black text-sm">
                                {{ $advisor->students_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-400 text-xs">{{ $advisor->email }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="px-4 py-6 text-center text-gray-400">لا يوجد مرشدون</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ══════════ Risk Flags Summary ══════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h2 class="font-black text-gray-800 text-lg">
                <i class="fas fa-shield-alt text-red-500 ml-2"></i>ملخص مؤشرات الخطر
            </h2>
        </div>
        <div class="p-5 space-y-3">
            @forelse($activeFlags as $flag)
            <div class="flex items-center justify-between p-3 rounded-xl
                {{ $flag->severity === 'High' ? 'bg-red-50 border border-red-100' : 'bg-amber-50 border border-amber-100' }}">
                <div>
                    <p class="font-bold text-sm {{ $flag->severity === 'High' ? 'text-red-700' : 'text-amber-700' }}">
                        {{ $flag->type === 'Low_GPA' ? 'معدل منخفض' : 'غياب مرتفع' }}
                    </p>
                    <p class="text-xs {{ $flag->severity === 'High' ? 'text-red-400' : 'text-amber-400' }} mt-0.5">
                        {{ $flag->severity === 'High' ? 'حرج' : 'متوسط' }}
                    </p>
                </div>
                <span class="text-2xl font-black {{ $flag->severity === 'High' ? 'text-red-600' : 'text-amber-600' }}">
                    {{ $flag->total }}
                </span>
            </div>
            @empty
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-check-circle text-3xl text-green-400 mb-2"></i>
                <p>لا توجد إنذارات نشطة</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ══════════ At-Risk Students ══════════ --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-8">
    <div class="p-5 border-b border-gray-100">
        <h2 class="font-black text-gray-800 text-lg">
            <i class="fas fa-exclamation-triangle text-red-500 ml-2"></i>الطلاب المتعثرون في القسم
            <span class="text-sm font-normal text-gray-400 mr-2">({{ $atRiskList->count() }} طالب)</span>
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-right text-gray-500 font-semibold">الطالب</th>
                    <th class="px-4 py-3 text-center text-gray-500 font-semibold">المعدل</th>
                    <th class="px-4 py-3 text-right text-gray-500 font-semibold">المرشد</th>
                    <th class="px-4 py-3 text-right text-gray-500 font-semibold">الإنذارات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($atRiskList as $student)
                <tr class="hover:bg-red-50/30 transition">
                    <td class="px-4 py-3">
                        <p class="font-semibold text-gray-800">{{ $student->name_ar }}</p>
                        <p class="text-xs text-gray-400">{{ $student->student_id }}</p>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2 py-1 rounded-lg text-xs font-bold
                            {{ $student->gpa < 2.0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ number_format($student->gpa, 2) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-600">{{ $student->advisor?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1 flex-wrap">
                            @foreach($student->riskFlags as $flag)
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold
                                {{ $flag->severity === 'High' ? 'bg-red-100 text-red-600' : 'bg-amber-100 text-amber-600' }}">
                                {{ $flag->type === 'Low_GPA' ? 'معدل' : 'غياب' }}
                            </span>
                            @endforeach
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-4 py-6 text-center text-gray-400">لا يوجد طلاب متعثرون</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ══════════ Recent Notes ══════════ --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <h2 class="font-black text-gray-800 text-lg">
            <i class="fas fa-clipboard-list text-kku-primary ml-2"></i>آخر الملاحظات الإرشادية في القسم
        </h2>
    </div>
    <div class="divide-y divide-gray-50">
        @forelse($recentNotes as $note)
        <div class="p-4 hover:bg-gray-50/50 transition">
            <div class="flex items-start justify-between gap-3">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap mb-1">
                        <span class="font-semibold text-gray-800 text-sm">{{ $note->student?->name_ar }}</span>
                        <span class="text-gray-300">·</span>
                        <span class="text-xs text-gray-400">{{ $note->user?->name }}</span>
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold
                            {{ $note->note_type === 'Academic' ? 'bg-blue-100 text-blue-600' : 'bg-purple-100 text-purple-600' }}">
                            {{ $note->note_type === 'Academic' ? 'أكاديمي' : 'سلوكي' }}
                        </span>
                        @if($note->follow_up_required)
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-600">
                            متابعة
                        </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-600 truncate">{{ $note->content }}</p>
                </div>
                <span class="text-xs text-gray-400 whitespace-nowrap">
                    {{ $note->created_at->diffForHumans() }}
                </span>
            </div>
        </div>
        @empty
        <div class="p-8 text-center text-gray-400">لا توجد ملاحظات حديثة</div>
        @endforelse
    </div>
</div>
@endsection
