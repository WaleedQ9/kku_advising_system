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

        <div class="bg-white p-4 rounded-3xl shadow-sm border border-gray-100">
            <form action="{{ route('registrar.students.index') }}" method="GET" class="flex gap-4">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="ابحث بالاسم أو الرقم الجامعي..."
                        class="w-full pr-11 pl-4 py-3 bg-gray-50 border-none rounded-2xl text-sm focus:ring-2 focus:ring-kku-primary outline-none transition-all">
                </div>
                <button type="submit"
                    class="px-8 py-3 bg-kku-primary text-white rounded-2xl font-bold hover:bg-kku-dark transition-all">
                    {{ __('بحث') }}
                </button>
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
