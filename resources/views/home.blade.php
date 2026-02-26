@extends('layouts.app')

@section('title', 'الرئيسية')

@section('content')
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">مرحباً بك {{ Auth::user()->name }} مجدداً..</h1>
            <p class="text-gray-500 mt-1 text-lg">لديك <span class="text-kku-primary font-bold underline">12
                    طالباً</span> يحتاجون لمتابعة عاجلة هذا الأسبوع.</p>
        </div>
        <button onclick="toggleModal()"
            class="bg-kku-primary text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-green-900/20 hover:bg-kku-dark transition-all flex items-center gap-2">
            <i class="fas fa-plus"></i> إضافة تقرير إرشادي
        </button>
    </div>

    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 group hover:border-kku-primary transition-all">
            <div
                class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center text-kku-primary group-hover:bg-kku-primary group-hover:text-white transition-all">
                <i class="fas fa-user-graduate text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-400 text-sm font-bold">إجمالي الطلاب</p>
                <h3 class="text-2xl font-black">45</h3>
            </div>
        </div>

        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 group hover:border-red-500 transition-all">
            <div
                class="w-14 h-14 bg-red-50 rounded-2xl flex items-center justify-center text-red-500 group-hover:bg-red-500 group-hover:text-white transition-all">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-400 text-sm font-bold">متعثرين أكاديمياً</p>
                <h3 class="text-2xl font-black text-red-600">8</h3>
            </div>
        </div>

        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 group hover:border-amber-500 transition-all">
            <div
                class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 group-hover:bg-amber-500 group-hover:text-white transition-all">
                <i class="fas fa-clock text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-400 text-sm font-bold">طلبات معلقة</p>
                <h3 class="text-2xl font-black">14</h3>
            </div>
        </div>

        <div
            class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-5 group hover:border-blue-500 transition-all">
            <div
                class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500 group-hover:bg-blue-500 group-hover:text-white transition-all">
                <i class="fas fa-star text-2xl"></i>
            </div>
            <div>
                <p class="text-gray-400 text-sm font-bold">المتفوقين</p>
                <h3 class="text-2xl font-black text-blue-600">12</h3>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <section class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-list text-kku-primary"></i> طلاب بحاجة لمتابعة
                </h3>
                <a href="#" class="text-sm text-kku-primary font-bold hover:underline">عرض جميع الطلاب</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-bold">
                        <tr>
                            <th class="px-6 py-4">اسم الطالب</th>
                            <th class="px-6 py-4">الرقم الجامعي</th>
                            <th class="px-6 py-4">المعدل</th>
                            <th class="px-6 py-4">الحالة</th>
                            <th class="px-6 py-4 text-center">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-[10px]">
                                        ف ش</div>
                                    <span class="text-sm font-bold">فيصل الشهري</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">441102933</td>
                            <td class="px-6 py-4 font-bold text-red-500 italic">1.85</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-[10px] font-bold">إنذار
                                    ثاني</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button class="text-gray-400 hover:text-kku-primary"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center font-bold text-[10px]">
                                        س ع</div>
                                    <span class="text-sm font-bold">سعيد عسيري</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">442105520</td>
                            <td class="px-6 py-4 font-bold">3.40</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 bg-green-50 text-green-600 rounded-full text-[10px] font-bold">منتظم</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button class="text-gray-400 hover:text-kku-primary"><i class="fas fa-eye"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="space-y-6">
            <div class="bg-kku-dark text-white p-6 rounded-3xl shadow-lg relative overflow-hidden group">
                <i class="fas fa-quote-right absolute -right-4 -top-4 text-white/10 text-8xl"></i>
                <h4 class="font-bold mb-2">تنبيه النظام الذكي</h4>
                <p class="text-xs text-green-100 leading-relaxed opacity-80">تم رصد انخفاض حاد في معدل 5 طلاب
                    من الدفعة 441 بعد نتائج الاختبارات النصفية. يرجى مراجعة سجلاتهم.</p>
                <button
                    class="mt-4 w-full bg-white text-kku-dark py-2 rounded-xl text-sm font-bold hover:bg-kku-accent hover:text-white transition-colors">مراجعة
                    الآن</button>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-calendar-check text-amber-500"></i> مواعيد اليوم
                </h3>
                <div class="space-y-4">
                    <div class="flex gap-4 items-start border-r-2 border-amber-400 pr-3">
                        <div class="text-center shrink-0">
                            <p class="text-[10px] font-black text-gray-400 uppercase">10:30</p>
                            <p class="text-[10px] font-bold text-gray-400">AM</p>
                        </div>
                        <div>
                            <p class="text-sm font-bold">جلسة إرشادية (عن بعد)</p>
                            <p class="text-xs text-gray-400">الطالب: خالد علي</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
