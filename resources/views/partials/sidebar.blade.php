    <aside class="w-72 bg-kku-dark h-screen sticky top-0 text-white hidden lg:flex flex-col shadow-2xl z-50">
        <div class="p-6 text-center border-b border-white/10">
            <img src="{{ asset('img/logo.svg') }}" alt="KKU"
                class="w-20 mx-auto mb-4 bg-white rounded-xl p-1 shadow-lg">
            <h2 class="font-bold text-lg tracking-wide">بوابة المرشد الأكاديمي</h2>
            <p class="text-xs text-green-200 opacity-70">جامعة الملك خالد</p>
        </div>

        <nav class="flex-1 mt-6 px-3 space-y-2">
            <a href="{{ route('home') }}"
                class="sidebar-item flex items-center gap-3 p-3 rounded-lg transition-all
                         {{ request()->routeIs('home') ? 'bg-white/20 border-r-4 border-kku-accent font-bold' : 'hover:bg-white/10 opacity-80' }}">
                <i class="fas fa-th-large w-6"></i>
                <span>{{ __('الرئيسية') }}</span>
            </a>
            <a href="{{ route('students.index') }}"
                class="sidebar-item flex items-center gap-3 p-3 rounded-lg transition-all {{ request()->routeIs('students.*') ? 'bg-white/20 border-r-4 border-kku-accent font-bold' : 'hover:bg-white/10 opacity-80' }}">
                <i class="fas fa-users w-6"></i> <span>{{ __('قائمة الطلاب') }}</span>
            </a>
            <a href="#" class="sidebar-item flex items-center gap-3 p-3 rounded-lg transition-all">
                <i class="fas fa-file-signature w-6"></i> <span>الطلبات الأكاديمية</span>
                <span class="mr-auto bg-red-500 text-[10px] px-2 py-0.5 rounded-full">5</span>
            </a>
            <a href="#" class="sidebar-item flex items-center gap-3 p-3 rounded-lg transition-all">
                <i class="fas fa-chart-pie w-6"></i> <span>إحصائيات الدفعة</span>
            </a>
            <a href="#" class="sidebar-item flex items-center gap-3 p-3 rounded-lg transition-all text-gray-300">
                <i class="fas fa-calendar-alt w-6"></i> <span>جدول المواعيد</span>
            </a>
        </nav>

        <div class="p-4 bg-white/5 m-4 rounded-xl border border-white/10">
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full bg-kku-accent flex items-center justify-center font-bold text-kku-dark">
                    م</div>
                <div class="overflow-hidden">
                    <p class="text-[15px] font-bold truncate  ">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-green-300">كلية علوم الحاسب</p>
                </div>
                <a href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                    class="mr-auto hover:text-red-400 transition-colors" title="تسجيل الخروج">
                    <i class="fas fa-sign-out-alt fa-flip-horizontal"></i>
                </a>

                {{-- نموذج مخفي لإرسال طلب تسجيل الخروج --}}
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </aside>
