@extends('layouts.app')
@section('title', __('قائمة الطلاب'))

@section('content')
@php
    $advisor          = auth()->user();
    $collection       = $students->getCollection();
    $total            = $students->total();
    $regular          = $collection->where('status','منتظم')->count();
    $atRisk           = $collection->where('status','متعثر')->count();
    $graduated        = $collection->where('status','خريج')->count();
    $avgGpa           = $collection->avg('gpa');
    $followUpCount    = $collection->filter(fn($s) => $s->advisingNotes->where('follow_up_required',true)->isNotEmpty())->count();
    $flaggedCount     = $collection->filter(fn($s) => $s->riskFlags->where('is_resolved',false)->isNotEmpty())->count();

    $filterChips = [
        ''       => ['الكل',  $total,     'bg-kku-primary text-white', 'bg-gray-100 text-gray-600'],
        'منتظم'  => ['منتظم', $regular,   'bg-green-500 text-white',   'bg-green-50 text-green-700'],
        'متعثر'  => ['خطر',   $atRisk,    'bg-red-500 text-white',     'bg-red-50 text-red-600'],
        'خريج'   => ['خريج',  $graduated, 'bg-blue-500 text-white',    'bg-blue-50 text-blue-600'],
    ];

    $cols = [
        'col-major'   => 'التخصص',
        'col-level'   => 'المستوى',
        'col-attend'  => 'الحضور',
        'col-gpa'     => 'المعدل',
        'col-credits' => 'الساعات',
        'col-status'  => 'الحالة',
    ];
@endphp

<div class="flex gap-0 h-[calc(100vh-88px)] -mx-6 -mb-6 overflow-hidden">

    {{-- ══ Sidebar ══ --}}
    <div class="w-56 shrink-0 bg-white border-l border-gray-100 flex flex-col py-4 overflow-y-auto">
        <p class="px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ __('القوائم') }}</p>

        <a href="{{ route('students.index') }}"
            class="flex items-center gap-3 px-4 py-2.5 mx-2 rounded-xl text-sm font-bold transition-all
            {{ !request('status') && !request('followup') ? 'bg-kku-primary/10 text-kku-primary' : 'text-gray-600 hover:bg-gray-50' }}">
            <i class="fas fa-users w-4 text-center"></i>
            {{ __('طلابي') }}
            <span class="mr-auto text-xs font-black px-2 py-0.5 rounded-full
                {{ !request('status') && !request('followup') ? 'bg-kku-primary text-white' : 'bg-gray-200 text-gray-600' }}">
                {{ $total }}
            </span>
        </a>

        <a href="{{ route('students.index', ['status'=>'متعثر']) }}"
            class="flex items-center gap-3 px-4 py-2.5 mx-2 rounded-xl text-sm font-bold transition-all
            {{ request('status')==='متعثر' ? 'bg-red-50 text-red-600' : 'text-gray-600 hover:bg-gray-50' }}">
            <i class="fas fa-exclamation-triangle w-4 text-center {{ request('status')==='متعثر' ? 'text-red-500' : 'text-gray-400' }}"></i>
            {{ __('حالات خطر') }}
            @if($atRisk > 0)
                <span class="mr-auto text-xs font-black bg-red-500 text-white px-2 py-0.5 rounded-full">{{ $atRisk }}</span>
            @endif
        </a>

        <a href="{{ route('students.index', ['followup'=>1]) }}"
            class="flex items-center gap-3 px-4 py-2.5 mx-2 rounded-xl text-sm font-bold transition-all
            {{ request('followup') ? 'bg-amber-50 text-amber-700' : 'text-gray-600 hover:bg-gray-50' }}">
            <i class="fas fa-flag w-4 text-center {{ request('followup') ? 'text-amber-500' : 'text-gray-400' }}"></i>
            {{ __('متابعات مفتوحة') }}
            @if($followUpCount > 0)
                <span class="mr-auto text-xs font-black bg-amber-400 text-white px-2 py-0.5 rounded-full">{{ $followUpCount }}</span>
            @endif
        </a>

        <div class="mx-4 my-3 border-t border-gray-100"></div>
        <p class="px-4 text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ __('الإجراءات') }}</p>

        <form method="POST" action="{{ route('flags.scan') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-6 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50 transition-all text-right">
                <i class="fas fa-search-plus w-4 text-center text-gray-400"></i>
                {{ __('فحص التنبيهات') }}
            </button>
        </form>
    </div>

    {{-- ══ Main ══ --}}
    <div class="flex-1 flex flex-col overflow-hidden bg-gray-50/50">

        {{-- Toolbar --}}
        <div class="flex items-center gap-3 px-5 py-3 bg-white border-b border-gray-100 shrink-0 flex-wrap">
            <div class="flex items-center gap-2 flex-wrap">
                @foreach($filterChips as $val => [$label, $count, $activeClass, $inactiveClass])
                    @php $isActive = request('status', '') === $val && !request('followup'); @endphp
                    <a href="{{ route('students.index', $val ? ['status'=>$val] : []) }}"
                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $isActive ? $activeClass : $inactiveClass }}">
                        {{ __($label) }} <span class="opacity-80">({{ $count }})</span>
                    </a>
                @endforeach
            </div>
            <div class="mr-auto relative" id="colToggleWrapper">
                <button onclick="toggleColPanel()"
                    class="flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-200 transition-all">
                    <i class="fas fa-sliders-h"></i> {{ __('إخفاء/إظهار الأعمدة') }}
                </button>
                <div id="colPanel" class="hidden absolute left-0 top-10 z-50 bg-white border border-gray-200 rounded-2xl shadow-xl p-4 w-52 space-y-2">
                    <p class="text-[10px] font-black text-gray-400 uppercase mb-2">{{ __('الأعمدة المرئية') }}</p>
                    @foreach($cols as $col => $label)
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative w-8 h-4 shrink-0" onclick="toggleColumn('{{ $col }}', this)">
                                <input type="checkbox" checked class="col-toggle sr-only" data-col="{{ $col }}">
                                <div class="toggle-track w-8 h-4 rounded-full bg-kku-primary transition-colors"></div>
                                <div class="toggle-thumb absolute top-0.5 right-0.5 w-3 h-3 bg-white rounded-full shadow transition-transform"></div>
                            </div>
                            <span class="text-xs text-gray-700 group-hover:text-kku-primary transition-colors">{{ __($label) }}</span>
                        </label>
                    @endforeach
                    <hr class="my-2">
                    <button onclick="resetColumns()" class="text-xs text-gray-400 hover:text-kku-primary w-full text-right">
                        <i class="fas fa-undo ml-1"></i> {{ __('إعادة تعيين') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Stats Bar --}}
        <div class="flex items-center gap-6 px-5 py-2.5 bg-white border-b border-gray-100 shrink-0 text-xs">
            <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-kku-primary"></div><span class="text-gray-500">{{ __('إجمالي') }}:</span><strong>{{ $total }}</strong></div>
            <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-green-500"></div><span class="text-gray-500">{{ __('منتظم') }}:</span><strong class="text-green-700">{{ $regular }}</strong></div>
            <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-amber-400"></div><span class="text-gray-500">{{ __('مراقبة') }}:</span><strong class="text-amber-700">{{ $flaggedCount }}</strong></div>
            <div class="flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-red-500"></div><span class="text-gray-500">{{ __('خطر') }}:</span><strong class="text-red-600">{{ $atRisk }}</strong></div>
            <div class="flex items-center gap-2 mr-auto"><div class="w-2 h-2 rounded-full bg-yellow-400"></div><span class="text-gray-500">{{ __('متوسط المعدل') }}:</span><strong>{{ number_format($avgGpa, 2) }}</strong></div>
        </div>

        {{-- Table --}}
        <div class="flex-1 overflow-y-auto">

            {{-- Header --}}
            <div class="grid text-[10px] font-black text-gray-400 uppercase bg-gray-50 border-b border-gray-100 px-5 py-2.5 sticky top-0 z-10"
                style="grid-template-columns:40px 30% 20% repeat(6, 1fr)">
                <div></div>
                <div class="flex items-center">{{ __('الطالب') }}</div>
                <div class="col-major flex items-center justify-center">{{ __('التخصص') }}</div>
                <div class="col-level flex items-center justify-center">{{ __('المستوى') }}</div>
                <div class="col-attend flex items-center justify-center">{{ __('الحضور') }}</div>
                <div class="col-gpa flex items-center justify-center">{{ __('المعدل') }}</div>
                <div class="col-status flex items-center justify-center">{{ __('الحالة') }}</div>
                <div></div>
            </div>

            {{-- Rows --}}
            @foreach($students as $student)
            @php
                $activeFlags   = $student->riskFlags->where('is_resolved', false);
                $hasWarning    = $activeFlags->isNotEmpty();
                $hasFollowUp   = $student->advisingNotes->where('follow_up_required', true)->isNotEmpty();
                $totalAbsences = $student->courses->sum('pivot.absences_count');
                $totalSessions = $student->courses->count() * 15;
                $attendPct     = $totalSessions > 0 ? max(0, min(100, round((($totalSessions - $totalAbsences) / $totalSessions) * 100))) : 100;
                $gpaColor      = $student->gpa >= 3.75 ? 'text-green-600' : ($student->gpa >= 2.5 ? 'text-amber-600' : 'text-red-600');
                $attendColor   = $attendPct >= 85 ? 'text-green-600' : ($attendPct >= 70 ? 'text-amber-600' : 'text-red-600');
                $rowBorder     = $hasWarning ? 'border-r-[3px] border-r-red-400' : ($hasFollowUp ? 'border-r-[3px] border-r-amber-400' : '');
                $statusCls     = match($student->status) { 'منتظم'=>'bg-green-50 text-green-700','متعثر'=>'bg-red-50 text-red-700','خريج'=>'bg-blue-50 text-blue-700',default=>'bg-gray-100 text-gray-600' };
                $statusLabel   = match($student->status) { 'متعثر'=>'⚠ خطر', default=>$student->status };
                $gradientColor = $hasWarning ? '#ef4444,#be123c' : ($student->gpa>=3.75 ? '#10b981,#059669' : ($student->gpa>=2.5 ? '#f59e0b,#f97316' : '#8b5cf6,#6d28d9'));
                $initials      = mb_substr($student->name_ar,0,1) . mb_substr(explode(' ',$student->name_ar)[1]??'',0,1);
                $dropsDone     = $student->dropActions->where('status','Completed')->count();
            @endphp

            <div class="student-row border-b border-gray-100 transition-colors duration-200 {{ $rowBorder }}" id="row-{{ $student->id }}">

                {{-- Main Row --}}
                <div class="grid items-center px-5 py-3 hover:bg-gray-50/80 transition-colors cursor-pointer group"
                    style="grid-template-columns:40px 30% 20% repeat(6, 1fr)"
                    onclick="toggleExpand({{ $student->id }})">

                    <div class="text-gray-300 group-hover:text-kku-primary transition-colors">
                        <i class="fas fa-chevron-down text-sm expand-icon-{{ $student->id }} transition-transform duration-200"></i>
                    </div>

                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-9 h-9 rounded-xl shrink-0 flex items-center justify-center text-sm font-black text-white shadow-sm"
                            style="background:linear-gradient(135deg,{{ $gradientColor }})">
                            {{ $initials }}
                        </div>
                        <div class="min-w-0">
                            <div class="font-bold text-gray-800 text-sm truncate">{{ $student->name_ar }}</div>
                            <div class="text-[10px] text-gray-400 font-mono">#{{ $student->student_id }}</div>
                        </div>
                        @if($hasWarning)
                            <span class="text-[9px] bg-red-100 text-red-600 px-1.5 py-0.5 rounded font-bold shrink-0">⚠</span>
                        @elseif($hasFollowUp)
                            <span class="text-[9px] bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded font-bold shrink-0">🔔</span>
                        @endif
                    </div>

                    <div class="col-major flex flex-col items-center justify-center text-center min-w-0">
                        <div class="text-xs font-bold text-gray-700 truncate">{{ $student->department->name_ar }}</div>
                        <div class="text-[10px] text-gray-400 truncate">{{ $student->major ?? '—' }}</div>
                    </div>

                    <div class="col-level flex items-center justify-center">
                        <span class="text-xs font-bold text-gray-600">{{ $student->total_credits > 0 ? ceil($student->total_credits/18) : 1 }}</span>
                    </div>

                    <div class="col-attend flex items-center justify-center">
                        <span class="text-xs font-bold {{ $attendColor }}">{{ $attendPct }}%</span>
                    </div>

                    <div class="col-gpa flex items-center justify-center">
                        <span class="text-sm font-black {{ $gpaColor }}">{{ number_format($student->gpa,2) }}</span>
                    </div>

                    <div class="col-status flex items-center justify-center">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-[10px] font-bold {{ $statusCls }}">{{ __($statusLabel) }}</span>
                    </div>

                    <div class="flex items-center justify-center gap-2" onclick="event.stopPropagation()">
                        <button onclick="openQuickNote({{ $student->id }},'{{ addslashes($student->name_ar) }}')"
                            class="flex flex-col items-center gap-0.5 px-2.5 py-1.5 rounded-lg bg-kku-primary/10 text-kku-primary hover:bg-kku-primary hover:text-white transition-all">
                            <i class="fas fa-plus text-xs"></i>
                            <span class="text-[9px] font-bold leading-none">{{ __('ملاحظة') }}</span>
                        </button>
                        <a href="{{ route('students.show',$student->id) }}"
                            class="flex flex-col items-center gap-0.5 px-2.5 py-1.5 rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-600 hover:text-white transition-all">
                            <i class="fas fa-user text-xs"></i>
                            <span class="text-[9px] font-bold leading-none">{{ __('الملف') }}</span>
                        </a>
                        <a href="{{ route('students.print',$student->id) }}" target="_blank"
                            class="flex flex-col items-center gap-0.5 px-2.5 py-1.5 rounded-lg bg-gray-800 text-white hover:bg-black transition-all">
                            <i class="fas fa-print text-xs"></i>
                            <span class="text-[9px] font-bold leading-none">{{ __('طباعة') }}</span>
                        </a>
                    </div>
                </div>

                {{-- Expanded Panel --}}
                <div id="expanded-{{ $student->id }}" class="hidden border-t border-gray-100 bg-kku-primary/[0.02] p-5">
                    <div class="grid grid-cols-3 gap-4 items-stretch">

                        {{-- المقررات --}}
                        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                            <p class="text-[11px] font-black text-gray-500 mb-3 flex items-center gap-1.5">
                                <i class="fas fa-book text-kku-primary"></i> {{ __('المقررات المسجلة') }}
                            </p>
                            @if($student->courses->isEmpty())
                                <p class="text-xs text-gray-400">{{ __('لا توجد مواد مسجلة') }}</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($student->courses->take(4) as $c)
                                    @php $ap = min(100,($c->pivot->absences_count/15)*100); $ac=$ap>=100?'text-red-600':($ap>=60?'text-amber-600':'text-green-600'); @endphp
                                    <div class="flex items-center justify-between text-xs gap-2">
                                        <span class="text-gray-700 truncate flex-1">{{ $c->name }}</span>
                                        <span class="shrink-0 text-[10px] font-mono">{{ $c->credits }}س</span>
                                        <span class="shrink-0 font-bold {{ $ac }}">{{ 100-round($ap) }}%</span>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="mt-3 pt-2 border-t border-gray-100">
                                    <span class="text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded font-bold">
                                        {{ __('صلاحية الحذف') }}: {{ 3-$dropsDone }} {{ __('محاولات متبقية') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- الملاحظات --}}
                        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex flex-col">
                            <p class="text-[11px] font-black text-gray-500 mb-3 flex items-center gap-1.5 shrink-0">
                                <i class="fas fa-clipboard text-kku-primary"></i>
                                {{ __('جميع الملاحظات') }}
                                <span class="mr-auto bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded text-[10px]">{{ $student->advisingNotes->count() }}</span>
                            </p>
                            @if($student->advisingNotes->isEmpty())
                                <p class="text-xs text-gray-400">{{ __('لا توجد ملاحظات') }}</p>
                            @else
                                <div class="flex-1 space-y-2 overflow-y-auto">
                                    @foreach($student->advisingNotes->sortByDesc('created_at') as $note)
                                    <div class="text-xs border-b border-gray-50 pb-2 last:border-0 last:pb-0">
                                        <p class="text-gray-700 leading-relaxed line-clamp-2">{{ $note->content }}</p>
                                        <p class="text-[9px] text-gray-400 mt-1">
                                            @if($note->follow_up_required)
                                                <span class="text-amber-600 font-bold">🔔 {{ __('متابعة') }}</span> ·
                                            @else
                                                <span class="text-green-600 font-bold">✅ {{ __('مكتمل') }}</span> ·
                                            @endif
                                            {{ $note->created_at->format('d M Y') }} · {{ $note->user->name ?? __('المرشد') }}
                                        </p>
                                    </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- ملاحظة سريعة --}}
                        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm flex flex-col gap-3">
                            <p class="text-[11px] font-black text-gray-500 flex items-center gap-1.5">
                                <i class="fas fa-pen text-kku-primary"></i> {{ __('إضافة ملاحظة سريعة') }}
                            </p>
                            <form action="{{ route('notes.store') }}" method="POST" class="space-y-2">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                <select name="note_type" class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg outline-none focus:border-kku-primary bg-gray-50">
                                    <option value="Academic">{{ __('أكاديمي') }}</option>
                                    <option value="Behavioral">{{ __('سلوكي') }}</option>
                                </select>
                                <textarea name="content" rows="3" required minlength="10"
                                    placeholder="{{ __('اكتب ملاحظتك هنا...') }}"
                                    class="w-full px-2 py-1.5 text-xs border border-gray-200 rounded-lg outline-none focus:border-kku-primary bg-gray-50 resize-none"></textarea>
                                <label class="flex items-center gap-2 text-[10px] text-gray-500 cursor-pointer">
                                    <input type="checkbox" name="follow_up_required" value="1" class="accent-kku-primary">
                                    {{ __('تحتاج متابعة') }}
                                </label>
                                <button type="submit" class="w-full py-1.5 bg-kku-primary text-white rounded-lg text-xs font-bold hover:bg-kku-dark transition-all">
                                    <i class="fas fa-save ml-1"></i> {{ __('حفظ الملاحظة') }}
                                </button>
                            </form>
                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-gray-100">
                                <div class="bg-gray-50 rounded-xl p-2 text-center border border-gray-100">
                                    <div class="text-[9px] text-gray-400 mb-0.5">{{ __('الساعات المكتملة') }}</div>
                                    <div class="text-base font-black text-kku-primary">{{ $student->total_credits }}</div>
                                </div>
                                <div class="bg-gray-50 rounded-xl p-2 text-center border border-gray-100">
                                    <div class="text-[9px] text-gray-400 mb-0.5">{{ __('عدد الملاحظات') }}</div>
                                    <div class="text-base font-black text-blue-600">{{ $student->advisingNotes->count() }}</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            @endforeach

            @if($students->isEmpty())
            <div class="py-20 text-center text-gray-400">
                <i class="fas fa-search text-4xl mb-3 block opacity-20"></i>
                <p class="font-bold">{{ __('لا يوجد طلاب مطابقون') }}</p>
            </div>
            @endif

            <div class="px-5 py-3 bg-white border-t border-gray-100">
                {{ $students->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Quick Note Modal --}}
<div id="quickNoteModal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="closeQuickNote()"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg z-10 overflow-hidden">
        <div class="bg-kku-primary p-5 text-white flex justify-between items-center">
            <div>
                <p class="text-xs opacity-70 mb-0.5">{{ __('ملاحظة إرشادية سريعة') }}</p>
                <h3 class="font-bold text-lg" id="quickNoteStudentName">—</h3>
            </div>
            <button onclick="closeQuickNote()" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/20">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form action="{{ route('notes.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="student_id" id="quickNoteStudentId">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('نوع الملاحظة') }}</label>
                    <select name="note_type" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-kku-primary">
                        <option value="Academic">{{ __('أكاديمية') }}</option>
                        <option value="Behavioral">{{ __('سلوكية') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('العنوان') }}</label>
                    <input type="text" name="title" placeholder="{{ __('اختياري') }}"
                        class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-kku-primary">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 mb-1.5">{{ __('تفاصيل الملاحظة') }}</label>
                <textarea name="content" rows="4" required minlength="10"
                    placeholder="{{ __('اكتب تفاصيل الجلسة وما تم الاتفاق عليه مع الطالب...') }}"
                    class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm outline-none focus:border-kku-primary resize-none"></textarea>
            </div>
            <label class="flex items-center gap-3 cursor-pointer p-3 rounded-xl hover:bg-gray-50">
                <input type="checkbox" name="follow_up_required" value="1" class="w-4 h-4 accent-kku-primary">
                <span class="text-sm text-gray-600">{{ __('تحتاج متابعة لاحقة') }}</span>
                <i class="fas fa-flag text-amber-500 text-xs mr-auto"></i>
            </label>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 py-3 bg-kku-primary text-white rounded-xl font-bold hover:bg-kku-dark transition-all">
                    <i class="fas fa-save ml-1"></i> {{ __('حفظ الملاحظة') }}
                </button>
                <button type="button" onclick="closeQuickNote()" class="flex-1 py-3 bg-gray-100 text-gray-600 rounded-xl font-bold">
                    {{ __('إلغاء') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.toggle-track { transition: background .2s; }
.toggle-thumb { transition: transform .2s; }
</style>

<script>
function toggleExpand(id) {
    // أغلق جميع الصفوف أولاً
    document.querySelectorAll('.student-row').forEach(row => {
        const rid = row.id.replace('row-', '');
        const panel = document.getElementById('expanded-' + rid);
        const icon  = document.querySelector('.expand-icon-' + rid);
        if (panel) panel.classList.add('hidden');
        if (icon)  icon.style.transform = '';
        row.style.background = '';
        row.style.borderRight = '';
    });

    // افتح الصف المطلوب فقط (toggle — لو كان مفتوح اقفله)
    const panel = document.getElementById('expanded-' + id);
    const icon  = document.querySelector('.expand-icon-' + id);
    const row   = document.getElementById('row-' + id);
    const wasOpen = panel.dataset.open === '1';

    if (!wasOpen) {
        panel.classList.remove('hidden');
        panel.dataset.open = '1';
        icon.style.transform = 'rotate(-90deg)';
        row.style.background = '#f0fdf4';
        row.style.borderRight = '4px solid #16a34a';
        row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        panel.dataset.open = '0';
    }
}
function openQuickNote(id, name) {
    document.getElementById('quickNoteStudentId').value = id;
    document.getElementById('quickNoteStudentName').textContent = name;
    document.getElementById('quickNoteModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeQuickNote() {
    document.getElementById('quickNoteModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeQuickNote(); });

const COL_KEY = 'kku_cols_v2';
function toggleColumn(col, wrapper) {
    const cb = wrapper.querySelector('input');
    cb.checked = !cb.checked;
    wrapper.querySelector('.toggle-track').style.background = cb.checked ? '' : '#d1d5db';
    wrapper.querySelector('.toggle-thumb').style.transform  = cb.checked ? '' : 'translateX(16px)';
    document.querySelectorAll('.' + col).forEach(el => el.style.display = cb.checked ? '' : 'none');
    const p = {}; document.querySelectorAll('.col-toggle').forEach(c => p[c.dataset.col] = c.checked);
    localStorage.setItem(COL_KEY, JSON.stringify(p));
}
function resetColumns() {
    document.querySelectorAll('.col-toggle').forEach(cb => {
        cb.checked = true;
        const w = cb.closest('[onclick]');
        if (w) { w.querySelector('.toggle-track').style.background=''; w.querySelector('.toggle-thumb').style.transform=''; }
        document.querySelectorAll('.' + cb.dataset.col).forEach(el => el.style.display = '');
    });
    localStorage.removeItem(COL_KEY);
}
function toggleColPanel() { document.getElementById('colPanel').classList.toggle('hidden'); }
document.addEventListener('click', e => { if (!e.target.closest('#colToggleWrapper')) document.getElementById('colPanel').classList.add('hidden'); });
document.addEventListener('DOMContentLoaded', () => {
    const saved = localStorage.getItem(COL_KEY);
    if (!saved) return;
    const p = JSON.parse(saved);
    document.querySelectorAll('.col-toggle').forEach(cb => {
        if (p[cb.dataset.col] === false) {
            cb.checked = false;
            const w = cb.closest('[onclick]');
            if (w) { w.querySelector('.toggle-track').style.background='#d1d5db'; w.querySelector('.toggle-thumb').style.transform='translateX(16px)'; }
            document.querySelectorAll('.' + cb.dataset.col).forEach(el => el.style.display = 'none');
        }
    });
});
</script>

@endsection
