@extends('layouts.app')

@section('title', __('الطلاب'))

@section('content')

    <div class="grid grid-cols-12 gap-6 mx-auto ">

        <div class="col-span-12 lg:col-span-8 ">
            <section class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-white">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-users text-kku-primary"></i>
                        {{ __('قائمة الطلاب') }}
                    </h3>
                    <span class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                        {{ $students->count() }} {{ __('الطلاب') }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-right border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 text-gray-500 text-xs font-bold uppercase tracking-wider">
                                <th class="px-6 py-4 border-b border-gray-100">{{ __('معلومات الطالب') }}</th>
                                <th class="px-6 py-4 border-b border-gray-100">{{ __('التخصص') }}</th>
                                <th class="px-6 py-4 border-b border-gray-100">{{ __('المعدل') }}</th>
                                <th class="px-6 py-4 border-b border-gray-100">{{ __('الحالة') }}</th>
                                <th class="px-6 py-4 border-b border-gray-100 text-center">{{ __('الإجراءات') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($students as $student)
                                <tr class="hover:bg-gray-50/80 transition-all group">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-kku-primary/10 flex items-center justify-center text-kku-primary font-bold">
                                                {{ mb_substr($student->name_ar, 0, 1) }}
                                            </div>
                                            <div>
                                                <div
                                                    class="font-bold text-gray-800 group-hover:text-kku-primary transition-colors">
                                                    {{ app()->getLocale() == 'ar' ? $student->name_ar : $student->name_en ?? $student->name_ar }}
                                                </div>
                                                <div class="text-xs text-gray-400 font-mono">{{ $student->student_id }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $student->major }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span
                                                class="font-bold {{ $student->gpa < 2.0 ? 'text-red-500' : 'text-gray-700' }}">
                                                {{ number_format($student->gpa, 2) }}
                                            </span>
                                            <div class="w-16 h-1 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                                <div class="h-full {{ $student->gpa < 2.0 ? 'bg-red-500' : 'bg-green-500' }}"
                                                    style="width: {{ ($student->gpa / 5) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        @php
                                            $statusClasses = match ($student->status) {
                                                'منتظم' => 'bg-green-50 text-green-600 border-green-100',
                                                'متعثر' => 'bg-red-50 text-red-600 border-red-100',
                                                'خريج' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                default => 'bg-gray-50 text-gray-600 border-gray-100',
                                            };
                                        @endphp
                                        <span
                                            class="px-3 py-1 rounded-full text-[10px] font-bold border {{ $statusClasses }}">
                                            {{ app()->getLocale() == 'ar' ? $student->status : ($student->status == 'منتظم' ? 'Regular' : ($student->status == 'متعثر' ? 'Struggling' : 'Graduated')) }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <div class="flex justify-center gap-2">
                                            <a href="{{ route('students.show', $student->id) }}"
                                                class="p-2 text-gray-400 hover:text-kku-primary hover:bg-kku-primary/5 rounded-lg transition-all"
                                                title="{{ __('View Profile') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button onclick="openNoteModal({{ $student->id }})"
                                                class="p-2 text-gray-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-all">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                        <i class="fas fa-search mb-3 text-4xl block"></i>
                                        {{ __('No students found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    {{-- {{ $students->links() }} --}}
                    {{ $students->appends(request()->query())->links() }}
                </div>
            </section>
        </div>
        <div class="col-span-12 lg:col-span-4 space-y-6">

            {{-- كرت ملخص الحالات --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-kku-primary"></i>
                    {{ __('ملخص الحالات') }}
                </h4>
                <div class="space-y-4">
                    {{-- متخرج --}}
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('خريج') }}</span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-lg font-bold text-xs">
                            {{ $students->where('status', 'خريج')->count() }}
                        </span>
                    </div>
                    {{-- منتظم --}}
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">{{ __('منتظم') }}</span>
                        <span class="px-3 py-1 bg-green-50 text-green-600 rounded-lg font-bold text-xs">
                            {{ $students->where('status', 'منتظم')->count() }}
                        </span>
                    </div>
                    {{-- متعثر --}}
                    <div class="flex justify-between items-center">

                        <span class="text-sm text-gray-600">{{ __('متعثر') }}</span>

                        <span class="px-3 py-1 bg-red-50 text-red-600 rounded-lg font-bold text-xs">
                            {{ $students->where('status', 'متعثر')->count() }}
                        </span>
                    </div>

                </div>

            </div>


            <div class="bg-kku-dark text-white rounded-3xl p-6 shadow-lg relative overflow-hidden">
                <div class="relative z-10">
                    <h4 class="font-bold mb-2">{{ __('تنبيه النظام الذكي') }}</h4>
                    <p class="text-xs opacity-80 leading-relaxed">
                        {{ __('يوجد طالباً انخفض معدلهم بشكل ملحوظ هذا الفصل. يوصى بجدولة جلسة إرشادية عاجلة.') }}
                    </p>
                    <button
                        class="mt-4 w-full py-2 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-xs font-bold transition-all">
                        {{ __('مراجعة الحالات الحرجة') }}
                    </button>
                </div>

                <i class="fas fa-graduation-cap absolute -bottom-4 -left-4 text-white/10 text-6xl rotate-12"></i>
            </div>
        </div>


    </div>


@endsection
