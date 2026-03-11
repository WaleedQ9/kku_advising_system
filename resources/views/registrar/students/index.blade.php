@extends('layouts.app')

@section('title', 'إدارة الطلاب - مسجل الطلاب')

@section('content')
    <div class="space-y-6">

        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">إدارة تسجيل الطلاب</h2>
                <p class="text-gray-500 text-sm">يمكنك البحث عن الطلاب وإلحاق المواد الدراسية لهم</p>
            </div>

            <div class="bg-kku-primary/10 px-4 py-2 rounded-2xl border border-kku-primary/20">
                <span class="text-kku-primary font-bold">{{ $students->total() }}</span>
                <span class="text-gray-600 text-xs mr-1">طالب مسجل في النظام</span>
            </div>
        </div>






        <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 mb-6">
            <form action="{{ route('registrar.students.index') }}" method="GET" class="space-y-5">

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-5">
                        <label class="block text-[10px] font-bold text-gray-400 mb-2 mr-1">البحث السريع</label>
                        <div class="relative">
                            <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="الاسم أو الرقم الجامعي..."
                                class="w-full pr-11 pl-4 py-3 bg-gray-50 border-transparent rounded-2xl text-sm focus:ring-2 focus:ring-kku-primary focus:bg-white transition-all outline-none">
                        </div>
                    </div>

                    <div class="md:col-span-4">
                        <label class="block text-[10px] font-bold text-gray-400 mb-2 mr-1">تصفية حسب القسم</label>
                        <select name="department_id"
                            class="w-full p-3 bg-gray-50 border-transparent rounded-2xl text-sm focus:ring-2 focus:ring-kku-primary outline-none appearance-none">
                            <option value="">كل الأقسام</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-12 md:col-span-3">
                        <label class="block text-[10px] font-bold text-gray-400 mb-2 mr-1">حالة الطالب</label>
                        <select name="status"
                            class="w-full p-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-kku-primary outline-none appearance-none">
                            <option value="">كل الحالات</option>
                            <option value="منتظم" {{ request('status') == 'منتظم' ? 'selected' : '' }}>منتظم</option>
                            <option value="متعثر" {{ request('status') == 'متعثر' ? 'selected' : '' }}>متعثر</option>
                            <option value="خريج" {{ request('status') == 'خريج' ? 'selected' : '' }}>خريج</option>
                        </select>
                    </div>

                    <div class="md:col-span-3 flex items-end">
                        <button type="submit"
                            class="w-full py-3 bg-kku-primary text-white rounded-2xl font-bold shadow-lg shadow-kku-primary/20 hover:bg-kku-dark transition-all">
                            تطبيق الفلاتر
                        </button>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-50 flex flex-wrap items-center justify-between gap-4">
                    <label
                        class="flex items-center gap-3 cursor-pointer group bg-amber-50/50 px-5 py-3 rounded-2xl border border-amber-100/50 hover:bg-amber-50 transition-all">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="new_students" value="1" onchange="this.form.submit()"
                                {{ request('new_students') ? 'checked' : '' }}
                                class="w-5 h-5 rounded-lg border-amber-300 text-amber-600 focus:ring-amber-500 transition-all">
                        </div>
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-amber-800">إظهار الطلاب الجدد فقط</span>
                            <span class="text-[10px] text-amber-600/70">الطلاب الذين لم يتم تسجيل أي ساعات لهم بعد</span>
                        </div>
                    </label>

                    @if (request()->anyFilled(['search', 'department_id', 'new_students', 'status']))
                        <a href="{{ route('registrar.students.index') }}"
                            class="text-xs font-bold text-red-400 hover:text-red-600 flex items-center gap-2 px-4 py-2 hover:bg-red-50 rounded-xl transition-all">
                            <i class="fas fa-times-circle"></i>
                            إلغاء كافة الفلاتر
                        </a>
                    @endif
                </div>
            </form>
        </div>
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-right">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="text-gray-500 text-xs font-bold uppercase">
                        <th class="px-6 py-4">الطالب</th>
                        <th class="px-6 py-4">القسم</th>
                        <th class="px-6 py-4">المعدل</th>
                        <th class="px-6 py-4">الساعات</th>
                        <th class="px-6 py-4">الحالة</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($students as $student)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-gray-100 text-gray-400 flex items-center justify-center font-bold">
                                        {{ mb_substr($student->name_ar, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-800">{{ $student->name_ar }}</div>
                                        <div class="text-[11px] text-gray-400">{{ $student->student_id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-medium text-gray-600">{{ $student->department->name_ar }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="text-sm font-black {{ $student->gpa == 0 ? 'text-gray-300' : 'text-kku-primary' }}">
                                    {{ number_format($student->gpa, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $student->total_credits }} ساعة
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 rounded-lg text-[10px] font-bold {{ $student->status == 'منتظم' ? 'bg-green-50 text-green-600' : 'bg-amber-50 text-amber-600' }}">
                                    {{ $student->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('registrar.students.enroll', $student->id) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl text-xs font-bold hover:bg-kku-primary hover:text-white hover:border-kku-primary transition-all shadow-sm">
                                    <i class="fas fa-plus-circle"></i>
                                    {{ __('تسجيل مادة') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                <i class="fas fa-user-slash text-4xl mb-3 block"></i>
                                <p>لم يتم العثور على طلاب مطابقين للبحث</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-6 border-t border-gray-50">
                {{ $students->links() }}
            </div>
        </div>

    </div>
@endsection
