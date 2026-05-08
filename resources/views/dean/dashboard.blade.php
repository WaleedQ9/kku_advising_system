@extends('layouts.app')
@section('title', __('لوحة تحكم عميد الكلية'))

@section('content')
@php
    $dean  = auth()->user();
    $hour  = now()->hour;
    $greeting = $hour < 12 ? __('صباح الخير') : ($hour < 17 ? __('مساء الخير') : __('مساء النور'));
@endphp

{{-- ══════════ Greeting ══════════ --}}
<div class="relative bg-kku-dark rounded-3xl p-8 mb-8 overflow-hidden shadow-xl">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-64 h-64 bg-white rounded-full -translate-x-32 -translate-y-32"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-kku-accent rounded-full translate-x-48 translate-y-48"></div>
    </div>
    <div class="relative">
        <p class="text-green-300 text-sm font-bold mb-1">
            <i class="fas fa-sun ml-2 text-yellow-300"></i>{{ $greeting }}،
        </p>
        <h1 class="text-white text-3xl font-black leading-tight">{{ $dean->name }}</h1>
        <p class="text-green-200/80 mt-2 text-sm">
            <i class="fas fa-university ml-1"></i>{{ __('عميد الكلية') }} · {{ __('الفصل الدراسي الثاني 1447هـ') }}
        </p>
        <p class="mt-3 inline-flex items-center gap-2 bg-white/10 border border-white/20 text-white/80 px-4 py-2 rounded-xl text-sm">
            <i class="fas fa-eye"></i> {{ __('صلاحية العرض فقط — لا يمكنك تعديل بيانات الطلاب') }}
        </p>
    </div>
</div>

{{-- ══════════ College-wide Stats ══════════ --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center mb-3">
            <i class="fas fa-users text-green-600"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $totalStudents }}</p>
        <p class="text-sm text-gray-500 mt-1">{{ __('إجمالي طلاب الكلية') }}</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center mb-3">
            <i class="fas fa-exclamation-triangle text-red-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $atRiskStudents }}</p>
        <p class="text-sm text-gray-500 mt-1">{{ __('طلاب متعثرون') }}</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center mb-3">
            <i class="fas fa-chalkboard-teacher text-blue-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $totalAdvisors }}</p>
        <p class="text-sm text-gray-500 mt-1">{{ __('المرشدون الأكاديميون') }}</p>
    </div>
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center mb-3">
            <i class="fas fa-flag text-amber-500"></i>
        </div>
        <p class="text-2xl font-black text-gray-800">{{ $totalFlags }}</p>
        <p class="text-sm text-gray-500 mt-1">{{ __('إنذارات نشطة') }}</p>
    </div>
</div>

{{-- ══════════ GPA Distribution ══════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h2 class="font-black text-gray-800 text-lg">
                <i class="fas fa-chart-bar text-kku-primary ml-2"></i>{{ __('توزيع المعدلات — الكلية') }}
            </h2>
        </div>
        <div class="p-5 space-y-3">
            @foreach($gpaBuckets as $label => $count)
            @php
                $pct = $totalStudents > 0 ? round($count / $totalStudents * 100) : 0;
                $color = match(true) {
                    str_contains($label, '4.5') => 'bg-green-500',
                    str_contains($label, '3.5') => 'bg-blue-500',
                    str_contains($label, '2.5') => 'bg-amber-400',
                    str_contains($label, '2.0') => 'bg-orange-400',
                    default => 'bg-red-500',
                };
            @endphp
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600 font-semibold">{{ $label }}</span>
                    <span class="text-gray-400">{{ $count }} {{ __('طالب') }} ({{ $pct }}%)</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5">
                    <div class="{{ $color }} h-2.5 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ══════════ Departments Summary ══════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100">
            <h2 class="font-black text-gray-800 text-lg">
                <i class="fas fa-building text-kku-primary ml-2"></i>{{ __('ملخص الأقسام') }}
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-right text-gray-500 font-semibold">{{ __('القسم') }}</th>
                        <th class="px-4 py-3 text-center text-gray-500 font-semibold">{{ __('الطلاب') }}</th>
                        <th class="px-4 py-3 text-center text-gray-500 font-semibold">{{ __('متعثرون') }}</th>
                        <th class="px-4 py-3 text-center text-gray-500 font-semibold">{{ __('متوسط المعدل') }}</th>
                        <th class="px-4 py-3 text-center text-gray-500 font-semibold">{{ __('إنذارات') }}</th>
                        <th class="px-4 py-3 text-center text-gray-500 font-semibold">{{ __('ملاحظات') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($departments as $dept)
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-4 py-3 font-semibold text-gray-800">{{ $dept->name_ar }}</td>
                        <td class="px-4 py-3 text-center font-bold text-gray-700">{{ $dept->students_count }}</td>
                        <td class="px-4 py-3 text-center">
                            @if($dept->at_risk_count > 0)
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-600">
                                {{ $dept->at_risk_count }}
                            </span>
                            @else
                            <span class="text-green-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded-lg text-xs font-bold
                                {{ $dept->avg_gpa < 2.0 ? 'bg-red-100 text-red-700' : ($dept->avg_gpa >= 3.5 ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700') }}">
                                {{ $dept->avg_gpa }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-500">
                            {{ isset($flagsByDept[$dept->id]) ? $flagsByDept[$dept->id]->count() : '—' }}
                        </td>
                        <td class="px-4 py-3 text-center text-gray-500">
                            {{ isset($notesByDept[$dept->id]) ? $notesByDept[$dept->id]->count() : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ══════════ Advising Compliance ══════════ --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-5 border-b border-gray-100">
        <h2 class="font-black text-gray-800 text-lg">
            <i class="fas fa-clipboard-check text-kku-primary ml-2"></i>{{ __('الالتزام بالإرشاد الأكاديمي — تقرير موجز') }}
        </h2>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($departments as $dept)
            @php
                $deptNotes    = isset($notesByDept[$dept->id]) ? $notesByDept[$dept->id]->count() : 0;
                $deptFlags    = isset($flagsByDept[$dept->id]) ? $flagsByDept[$dept->id]->count() : 0;
                $compliancePct = $dept->students_count > 0
                    ? min(100, round($deptNotes / $dept->students_count * 100))
                    : 0;
            @endphp
            <div class="border border-gray-100 rounded-xl p-4">
                <p class="font-bold text-gray-700 mb-3 text-sm">{{ $dept->name_ar }}</p>
                <div class="flex items-center gap-3 mb-2">
                    <div class="flex-1 bg-gray-100 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $compliancePct >= 70 ? 'bg-green-500' : ($compliancePct >= 40 ? 'bg-amber-400' : 'bg-red-500') }}"
                             style="width:{{ $compliancePct }}%"></div>
                    </div>
                    <span class="text-sm font-bold text-gray-600">{{ $compliancePct }}%</span>
                </div>
                <div class="flex justify-between text-xs text-gray-400">
                    <span>{{ $deptNotes }} {{ __('ملاحظة إرشادية') }}</span>
                    <span>{{ $deptFlags }} {{ __('إنذار نشط') }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
