@extends('layouts.app')
@section('title', __('مشاهدة معلومات الطالب'))
@section('content')
    <div class="space-y-6">
        {{-- الهيدر العلوي --}}
        <div class="flex justify-between items-center bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div
                    class="w-16 h-16 rounded-2xl bg-kku-primary text-white flex items-center justify-center text-2xl font-bold shadow-lg">
                    {{ mb_substr($student->name_ar, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $student->name_ar }}</h2>
                    <p class="text-gray-400 text-sm">{{ $student->student_id }} | {{ $student->department->name_ar }}</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="openNoteModal()"
                    class="px-4 py-2 bg-kku-primary text-white rounded-xl text-sm font-bold shadow-md hover:bg-kku-dark transition-all">
                    <i class="fas fa-plus ml-1"></i> {{ __('إضافة ملاحظة إرشادية') }}
                </button>
                <button onclick="window.print()"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-200 transition-all">
                    <i class="fas fa-print ml-1"></i> {{ __('طباعة التقرير') }}
                </button>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">
            {{-- العمود الجانبي: المؤشرات --}}
            <div class="col-span-12 lg:col-span-4 space-y-6">
                {{-- كرت مؤشر الخطر --}}
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h4 class="font-bold text-gray-800 mb-4">{{ __('مؤشرات الأداء والخطر') }}</h4>
                    <div class="space-y-4">
                        <div
                            class="flex justify-between items-center p-3 {{ $student->gpa < 2 ? 'bg-red-50' : 'bg-green-50' }} rounded-2xl">
                            <span class="text-xs font-bold">{{ __('المعدل التراكمي') }}</span>
                            <span
                                class="text-sm font-black {{ $student->gpa < 2 ? 'text-red-600' : 'text-green-600' }}">{{ $student->gpa }}</span>
                        </div>

                        {{-- إضافة مؤشر معدل التخصص ذكياً --}}
                        @php
                            $majorCourses = $student->courses->where('level_type', 'تخصص');
                            $majorGpa =
                                $majorCourses->count() > 0
                                    ? round($majorCourses->avg('pivot.current_grade') / 20, 2)
                                    : 0;
                        @endphp
                        <div
                            class="flex justify-between items-center p-3 {{ $majorGpa < 2.5 ? 'bg-amber-50' : 'bg-indigo-50' }} rounded-2xl">
                            <span class="text-xs font-bold">{{ __('معدل مواد التخصص') }}</span>
                            <span
                                class="text-sm font-black {{ $majorGpa < 2.5 ? 'text-amber-600' : 'text-indigo-600' }}">{{ $majorGpa }}</span>
                        </div>

                        <div
                            class="flex justify-between items-center p-3 {{ $student->courses->sum('pivot.absences_count') > 15 ? 'bg-red-50' : 'bg-blue-50' }} rounded-2xl">
                            <span class="text-xs font-bold">{{ __('إجمالي الساعات الغائبة') }}</span>
                            <span
                                class="text-sm font-black {{ $student->courses->sum('pivot.absences_count') > 15 ? 'text-red-600' : 'text-blue-600' }}">
                                {{ $student->courses->sum('pivot.absences_count') }} {{ __('ساعة') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- كرت الساعات المجتازة --}}
                <div class="bg-kku-dark text-white p-6 rounded-3xl shadow-lg">
                    <p class="text-xs opacity-70">{{ __('الساعات المجتازة') }}</p>
                    <h3 class="text-3xl font-black mt-1">{{ $student->total_credits }} <span
                            class="text-sm font-normal">{{ __('ساعة') }}</span></h3>
                    <div class="w-full h-1.5 bg-white/10 rounded-full mt-4 overflow-hidden">
                        <div class="bg-kku-accent h-full"
                            style="width: {{ min(($student->total_credits / 140) * 100, 100) }}%"></div>
                    </div>
                </div>
            </div>

            {{-- العمود الرئيسي: الجداول --}}
            <div class="col-span-12 lg:col-span-8 space-y-6">
                {{-- جدول المقررات --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                        <h4 class="font-bold text-gray-800">{{ __('المقررات المسجلة والغيابات') }}</h4>
                        <span class="text-xs text-gray-400">{{ __('الفصل الدراسي الحالي') }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-right text-sm">
                            <thead class="bg-gray-50/50 text-[10px] font-bold text-gray-500 uppercase">
                                <tr>
                                    <th class="px-6 py-4 italic">المادة</th>
                                    <th class="px-6 py-4">النوع</th>
                                    <th class="px-6 py-4">الغياب</th>
                                    <th class="px-6 py-4">النسبة</th>
                                    <th class="px-6 py-4 text-center">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($student->courses as $course)
                                    @php
                                        $absences = $course->pivot->absences_count;
                                        $limit = 15; // حد الحرمان
                                        $percent = ($absences / $limit) * 100;
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-800">{{ $course->name }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $course->code }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span
                                                class="px-2 py-0.5 rounded-lg text-[10px] {{ $course->level_type == 'تخصص' ? 'bg-purple-50 text-purple-600' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $course->level_type }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 font-bold {{ $absences > 10 ? 'text-red-500' : 'text-gray-600' }}">
                                            {{ $absences }} {{ __('ساعة') }}
                                        </td>
                                        <td class="px-6 py-4 w-32">

                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                                    <div class="h-full {{ $percent >= 100 ? 'bg-red-600' : ($percent >= 60 ? 'bg-amber-500' : 'bg-green-500') }}"
                                                        style="width: {{ min($percent, 100) }}%"></div>
                                                </div>
                                                <span class="text-[10px] font-bold">
                                                    {{ round(min($percent, 100)) }}%
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @if ($percent >= 100)
                                                <span
                                                    class="px-2 py-1 rounded-md text-[9px] font-bold bg-red-100 text-red-600 border border-red-200">حرمان

                                                </span>
                                            @elseif($percent >= 60)
                                                <span
                                                    class="px-2 py-1 rounded-md text-[9px] font-bold bg-amber-50 text-amber-600 border border-amber-100">إنذار
                                                    نهائي</span>
                                            @else
                                                <span
                                                    class="px-2 py-1 rounded-md text-[9px] font-bold bg-green-50 text-green-600 border border-green-100">منتظم</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- السجل الإرشادي --}}
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                        <h4 class="font-bold text-gray-800">{{ __('السجل الإرشادي التاريخي') }}</h4>
                        <i class="fas fa-history text-gray-300"></i>
                    </div>
                    <div class="p-6 space-y-6">
                        @forelse($notes as $note)
                            <div class="relative pr-8 pb-6 border-r-2 border-gray-100 last:border-0 last:pb-0">
                                <div
                                    class="absolute right-[-7px] top-0 w-3 h-3 rounded-full bg-kku-primary border-2 border-white shadow-sm">
                                </div>
                                <div class="bg-gray-50 p-4 rounded-2xl hover:bg-gray-100 transition-colors">
                                    <div class="flex justify-between items-center mb-2">
                                        <span
                                            class="text-xs font-black text-kku-primary bg-white px-2 py-1 rounded-lg shadow-sm">{{ $note->type }}</span>
                                        <span
                                            class="text-[10px] text-gray-400 font-medium">{{ $note->created_at->diffForHumans() }}
                                            ({{ $note->created_at->format('Y/m/d') }})
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $note->content }}</p>
                                    <div class="mt-2 text-[10px] text-gray-400 italic text-left">
                                        بواسطة: {{ $note->user->name ?? 'المرشد الأكاديمي' }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-gray-400">
                                <p>{{ __('لا توجد سجلات إرشادية سابقة لهذا الطالب') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>





    {{-- المودل --}}
    <div id="noteModal" class="hidden fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeNoteModal()"
                aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-3xl text-right overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full animate-in fade-in zoom-in duration-300">
                <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-lg" id="modal-title">{{ __('إضافة ملاحظة إرشادية جديدة') }}
                    </h3>
                    <button onclick="closeNoteModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('notes.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf
                    <input type="hidden" name="student_id" value="{{ $student->id }}">

                    <div>
                        <label
                            class="block text-xs font-bold text-gray-500 mb-2 mr-1">{{ __('نوع الجلسة الإرشادية') }}</label>
                        <div class="relative">
                            <select name="type"
                                class="w-full p-3.5 bg-gray-50 border border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-kku-primary focus:bg-white outline-none appearance-none transition-all">
                                <option value="أكاديمية">{{ __('جلسة أكاديمية') }}</option>
                                <option value="سلوكية">جلسة سلوكية</option>
                                <option value="متابعة غياب">متابعة غياب</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                            <i
                                class="fas fa-chevron-down absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2 mr-1">{{ __('تفاصيل الملاحظة') }}</label>
                        <textarea name="content" rows="5" required
                            class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-kku-primary focus:bg-white outline-none transition-all resize-none"
                            placeholder="{{ __('اكتب هنا ما تم نقاشه مع الطالب والحلول المقترحة...') }}">{{ old('content') }}</textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit"
                            class="flex-1 py-4 bg-kku-primary text-white rounded-2xl font-bold shadow-lg shadow-kku-primary/20 hover:bg-kku-dark hover:-translate-y-0.5 transition-all duration-200">
                            {{ __('حفظ الملاحظة') }}
                        </button>
                        <button type="button" onclick="closeNoteModal()"
                            class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold hover:bg-gray-200 transition-all">
                            {{ __('إلغاء') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openNoteModal() {
            const modal = document.getElementById('noteModal');
            modal.classList.remove('hidden');
            // منع التمرير في الصفحة الخلفية
            document.body.style.overflow = 'hidden';
        }

        function closeNoteModal() {
            const modal = document.getElementById('noteModal');
            modal.classList.add('hidden');
            // إعادة تفعيل التمرير
            document.body.style.overflow = 'auto';
        }

        // إضافة مستمع لحدث لوحة المفاتيح (زر Esc)
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                closeNoteModal();
            }
        });
    </script>
@endsection
