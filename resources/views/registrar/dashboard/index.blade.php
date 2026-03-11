@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <div class="flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">مرحباً بك {{ Auth::user()->name }} مجدداً..</h1>
                <p class="text-gray-500 mt-1 text-lg">لديك <span class="text-kku-primary font-bold underline">12
                        طالباً</span> يحتاجون لمتابعة عاجلة هذا الأسبوع.</p>
                <p class="text-sm text-gray-500">إليك نظرة عامة على الحالة الأكاديمية الحالية</p>

            </div>
            <button onclick="toggleModal()"
                class="bg-kku-primary text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-green-900/20 hover:bg-kku-dark transition-all flex items-center gap-2">
                <i class="fas fa-plus"></i> {{ __('إضافة تقرير إرشادي') }}
            </button>
        </div>


        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all flex items-center gap-4 group">
                <div
                    class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition-all">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase">إجمالي الطلاب</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $stats['total_students'] }}</h3>
                </div>
            </div>

            <a href="{{ route('registrar.students.index', ['new_students' => 1]) }}"
                class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all flex items-center gap-4 group">
                <div
                    class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-amber-500 group-hover:text-white transition-all">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase">بانتظار الجداول</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $stats['new_students'] }}</h3>
                </div>
            </a>

            <a href="{{ route('registrar.students.index', ['status' => 'at_risk']) }}"
                class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition-all flex items-center gap-4 group">
                <div
                    class="w-12 h-12 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-xl group-hover:bg-red-600 group-hover:text-white transition-all">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-bold uppercase">تحت الإنذار</p>
                    <h3 class="text-2xl font-black text-gray-800">{{ $stats['at_risk'] }}</h3>
                </div>
            </a>
        </div>

        <h3 class="text-lg font-bold text-gray-700 flex items-center gap-2 mt-10">
            <i class="fas fa-university text-kku-primary"></i> إحصائيات الأقسام
        </h3>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            @foreach ($departmentsStats as $dStat)
                <div
                    class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden transition-all hover:border-kku-primary/30">
                    <div class="relative z-10">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <span
                                    class="text-[10px] font-bold text-kku-primary bg-kku-primary/5 px-2 py-1 rounded-lg uppercase tracking-wider">{{ $dStat['code'] }}</span>
                                <h4 class="font-bold text-gray-800 mt-1">{{ $dStat['name'] }}</h4>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="bg-gray-50 p-3 rounded-2xl">
                                <p class="text-[9px] text-gray-400 font-bold mb-1">متوسط المعدل</p>
                                <p class="text-lg font-black text-gray-700">{{ $dStat['avg_gpa'] }}</p>
                            </div>
                            <div class="bg-gray-50 p-3 rounded-2xl">
                                <p class="text-[9px] text-gray-400 font-bold mb-1">الطلاب</p>
                                <p class="text-lg font-black text-gray-700">{{ $dStat['count'] }}</p>
                            </div>
                        </div>

                        <a href="{{ route('registrar.students.index', ['department_id' => $dStat['id'], 'new_students' => 1]) }}"
                            class="w-full py-3 bg-amber-50 text-amber-700 rounded-2xl text-xs font-bold flex items-center justify-center gap-2 hover:bg-amber-100 transition-all border border-amber-100">
                            <i class="fas fa-plus-circle"></i>
                            تسجيل جداول ({{ $dStat['new'] }}) طالب
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
