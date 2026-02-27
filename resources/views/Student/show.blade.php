@extends('layouts.app')
@section('title', __('مشاهدة معلومات الطالب'))
@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
            <div class="flex items-center gap-4">
                <div
                    class="w-16 h-16 rounded-2xl bg-kku-primary text-white flex items-center justify-center text-2xl font-bold">
                    {{ mb_substr($student->name_ar, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $student->name_ar }}</h2>
                    <p class="text-gray-400 text-sm">{{ $student->student_id }} | {{ $student->major }}</p>
                </div>
            </div>
            <div class="flex gap-2">

                <button onclick="openNoteModal()"
                    class="px-4 py-2 bg-kku-primary text-white rounded-xl text-sm font-bold shadow-md hover:bg-kku-dark transition-all">
                    <i class="fas fa-plus ml-1"></i> {{ __('إضافة ملاحظة إرشادية') }}
                </button>
                <button
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-200 transition-all">
                    <i class="fas fa-print ml-1"></i> {{ __('طباعة التقرير') }}
                </button>
            </div>
        </div>

        <div class="grid grid-cols-12 gap-6">

            <div class="col-span-12 lg:col-span-4 space-y-6">
                {{-- كرت مؤشر الخطر (Risk Indicator) --}}
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h4 class="font-bold text-gray-800 mb-4">{{ __('مؤشرات الخطر') }}</h4>
                    <div class="space-y-4">
                        <div
                            class="flex justify-between items-center p-3 {{ $student->gpa < 2 ? 'bg-red-50' : 'bg-green-50' }} rounded-2xl">
                            <span class="text-xs font-bold">{{ __('المعدل التراكمي') }}</span>
                            <span
                                class="text-sm font-black {{ $student->gpa < 2 ? 'text-red-600' : 'text-green-600' }}">{{ $student->gpa }}</span>
                        </div>
                        <div
                            class="flex justify-between items-center p-3 {{ $student->absences_count > 10 ? 'bg-amber-50' : 'bg-blue-50' }} rounded-2xl">
                            <span class="text-xs font-bold">{{ __('إجمالي الغيابات') }}</span>
                            <span
                                class="text-sm font-black {{ $student->absences_count > 10 ? 'text-amber-600' : 'text-blue-600' }}">{{ $student->absences_count }}</span>
                        </div>
                    </div>
                </div>

                {{-- كرت الساعات المجتازة --}}
                <div class="bg-kku-dark text-white p-6 rounded-3xl shadow-lg">
                    <p class="text-xs opacity-70">{{ __('الساعات المجتازة') }}</p>
                    <h3 class="text-3xl font-black mt-1">{{ $student->total_credits }} <span
                            class="text-sm font-normal">{{ __('ساعة') }}</span></h3>
                    <div class="w-full h-1.5 bg-white/10 rounded-full mt-4 overflow-hidden">
                        <div class="bg-kku-accent h-full" style="width: {{ ($student->total_credits / 140) * 100 }}%">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-span-12 lg:col-span-8">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                        <h4 class="font-bold text-gray-800">{{ __('السجل الإرشادي') }}</h4>
                    </div>
                    <div class="p-6">
                        @forelse($notes as $note)
                            <div class="relative pr-8 pb-8 border-r border-gray-100 last:border-0">
                                <div class="absolute right-[-5px] top-0 w-2.5 h-2.5 rounded-full bg-kku-primary"></div>
                                <div class="bg-gray-50 p-4 rounded-2xl">
                                    <div class="flex justify-between mb-2">
                                        <span class="text-xs font-bold text-kku-primary">{{ $note->type }}</span>
                                        <span
                                            class="text-[10px] text-gray-400">{{ $note->created_at->format('Y-m-d') }}</span>
                                    </div>
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $note->content }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12 text-gray-400">
                                <i class="fas fa-history text-4xl mb-3 block"></i>
                                <p>{{ __('لا توجد سجلات إرشادية سابقة لهذا الطالب') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- المودل --}}

    <div id="noteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
        <div
            class="bg-white w-full max-w-lg rounded-3xl shadow-2xl overflow-hidden animate-in fade-in zoom-in duration-300">
            <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800">{{ __('إضافة ملاحظة إرشادية جديدة') }}</h3>
                <button onclick="closeNoteModal()" class="text-gray-400 hover:text-gray-600"><i
                        class="fas fa-times"></i></button>
            </div>

            <form action="{{ route('notes.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2">{{ __('نوع الجلسة') }}</label>
                    <select name="type"
                        class="w-full p-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-kku-primary outline-none">
                        <option value="أكاديمية">{{ __('جلسة أكاديمية') }}</option>
                        <option value="سلوكية">{{ __('جلسة سلوكية') }}</option>
                        <option value="متابعة غياب">{{ __('متابعة غياب') }}</option>
                        <option value="أخرى">{{ __('أخرى') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-2">{{ __('نص الملاحظة') }}</label>
                    <textarea name="content" rows="4" required
                        class="w-full p-3 bg-gray-50 border-none rounded-xl text-sm focus:ring-2 focus:ring-kku-primary outline-none"
                        placeholder="{{ __('اكتب تفاصيل الجلسة وما تم الاتفاق عليه مع الطالب...') }}"></textarea>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="submit"
                        class="flex-1 py-3 bg-kku-primary text-white rounded-xl font-bold shadow-lg hover:bg-kku-dark transition-all">
                        {{ __('حفظ الملاحظة') }}
                    </button>
                    <button type="button" onclick="closeNoteModal()"
                        class="flex-1 py-3 bg-gray-100 text-gray-500 rounded-xl font-bold hover:bg-gray-200 transition-all">
                        {{ __('إلغاء') }}
                    </button>
                </div>
            </form>
        </div>
    </div>


    <script>
        function openNoteModal() {
            document.getElementById('noteModal').classList.remove('hidden');
        }

        function closeNoteModal() {
            document.getElementById('noteModal').classList.add('hidden');
        }

        // إغلاق المودال عند الضغط خارجه
        window.onclick = function(event) {
            const modal = document.getElementById('noteModal');
            if (event.target == modal) {
                closeNoteModal();
            }
        }
    </script>
@endsection
