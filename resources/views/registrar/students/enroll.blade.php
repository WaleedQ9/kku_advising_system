@extends('layouts.app')

@section('title', 'تسجيل مادة للطالب')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-gray-800">تسجيل مادة جديدة</h3>
                    <p class="text-xs text-gray-400 mt-1">الطالب: {{ $student->name_ar }} ({{ $student->student_id }})</p>
                </div>
                <a href="{{ route('registrar.students.index') }}" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </a>
            </div>

            <form action="{{ route('registrar.students.store_enroll', $student->id) }}" method="POST" class="p-8 space-y-6">
                @csrf

                <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-2xl border border-blue-100">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-blue-600 shadow-sm">
                        <i class="fas fa-university"></i>
                    </div>
                    <div>
                        <p class="text-[10px] text-blue-400 font-bold uppercase tracking-wider">القسم التابع له الطالب</p>
                        <p class="text-sm font-bold text-blue-800">{{ $student->department->name_ar }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-3 mr-1">اختر المادة الدراسية</label>
                    <div class="relative">
                        <select name="course_id" required
                            class="w-full p-4 bg-gray-50 border border-gray-100 rounded-2xl text-sm focus:ring-2 focus:ring-kku-primary focus:bg-white outline-none appearance-none transition-all">
                            <option value="" disabled selected>-- اختر من المواد المتاحة --</option>
                            @foreach ($availableCourses as $course)
                                <option value="{{ $course->id }}">
                                    {{ $course->name }} ({{ $course->code }}) - {{ $course->credits }} ساعات
                                    [{{ $course->level_type }}]
                                </option>
                            @endforeach
                        </select>
                        <i
                            class="fas fa-chevron-down absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2 mr-1">تظهر هنا فقط مواد التخصص للطالب والمواد العامة التي لم
                        يسجلها بعد.</p>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit"
                        class="flex-1 py-4 bg-kku-primary text-white rounded-2xl font-bold shadow-lg shadow-kku-primary/20 hover:bg-kku-dark hover:-translate-y-0.5 transition-all">
                        تأكيد تسجيل المادة
                    </button>
                    <a href="{{ route('registrar.students.index') }}"
                        class="flex-1 py-4 bg-gray-100 text-gray-500 rounded-2xl font-bold text-center hover:bg-gray-200 transition-all">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
