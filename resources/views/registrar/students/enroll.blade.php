@extends('layouts.app')

@section('title', 'إدارة جدول الطالب')

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div
                    class="w-16 h-16 bg-kku-primary/10 text-kku-primary rounded-2xl flex items-center justify-center text-2xl font-bold">
                    {{ mb_substr($student->name_ar, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $student->name_ar }}</h2>
                    <p class="text-sm text-gray-500">{{ $student->student_id }} | {{ $student->department->name_ar }}</p>
                </div>
            </div>
            <div class="text-left">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">إجمالي الساعات المسجلة</p>
                <p class="text-3xl font-black text-kku-primary">{{ $student->total_credits }} <span
                        class="text-sm">ساعة</span></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                    <div class="p-5 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2">
                            <i class="fas fa-plus-circle text-kku-primary"></i>
                            تسجيل مادة جديدة
                        </h3>
                    </div>
                    <form action="{{ route('registrar.students.store_enroll', $student->id) }}" method="POST"
                        class="p-6 space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-500 mb-2 mr-1">اختر المادة</label>
                            <select name="course_id" required
                                class="w-full p-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-kku-primary outline-none appearance-none cursor-pointer">
                                <option value="" disabled selected>-- المواد المتاحة --</option>
                                @foreach ($availableCourses as $course)
                                    <option value="{{ $course->id }}">
                                        {{ $course->code }} - {{ $course->name }} ({{ $course->credits }} س)
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="w-full py-4 bg-kku-primary text-white rounded-2xl font-bold shadow-lg shadow-kku-primary/20 hover:bg-kku-dark transition-all">
                            إضافة للجدول
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 border-b border-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">المواد المسجلة في الترم الحالي</h3>
                        <span class="bg-blue-50 text-blue-600 text-[10px] font-bold px-3 py-1 rounded-full">
                            {{ $registeredCourses->count() }} مواد
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-right">
                            <thead class="bg-gray-50 text-[10px] font-bold text-gray-400 uppercase">
                                <tr>
                                    <th class="px-6 py-4">المادة</th>
                                    <th class="px-6 py-4">القسم المالك</th>
                                    <th class="px-6 py-4">النوع</th>
                                    <th class="px-6 py-4 text-center">الساعات</th>
                                    <th class="px-6 py-4"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($registeredCourses as $course)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-bold text-gray-800">{{ $course->name }}</div>
                                            <div class="text-[10px] text-gray-400">{{ $course->code }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-xs text-gray-500">{{ $course->department->name_ar }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1">
                                                <span
                                                    class="px-2 py-0.5 rounded text-[9px] font-bold w-fit {{ $course->level_type == 'تخصص' ? 'bg-green-50 text-green-600' : 'bg-blue-50 text-blue-600' }}">
                                                    {{ $course->level_type }}
                                                </span>
                                                <span
                                                    class="px-2 py-0.5 rounded text-[9px] font-bold w-fit {{ $course->requirement_type == 'اجباري' ? 'bg-red-50 text-red-600' : 'bg-amber-50 text-amber-600' }}">
                                                    {{ $course->requirement_type }}
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold text-gray-700">
                                            {{ $course->credits }}
                                        </td>
                                        <td class="px-6 py-4 text-left">
                                            <button class="text-gray-300 hover:text-red-500 transition-colors"
                                                title="حذف المادة">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="text-gray-300 mb-2"><i class="fas fa-calendar-times text-4xl"></i>
                                            </div>
                                            <p class="text-sm text-gray-400">لا توجد مواد مسجلة لهذا الطالب حالياً</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>

                </div>

            </div>
            <a href="http://127.0.0.1:8000/registrar/students?search=&department_id={{ $student->department->id }}"
                class="flex-1 bg-gray-200 text-gray-600 py-3 text-center rounded-xl font-bold hover:bg-gray-200 transition-all">
                العودة إلى قائمة الطلاب
            </a>

        </div>

    </div>

@endsection
