<header class="bg-white border-b sticky top-0 z-40 px-8 py-4 flex justify-between items-center shadow-sm">
    <form action="{{ route('students.index') }}" method="GET" class="flex items-center gap-4 w-1/3">
        <div class="relative w-full">
            <i class="fas fa-search absolute right-3 top-3 text-gray-400"></i>

            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="{{ __('بحث سريع برقم الطالب أو الاسم...') }}"
                class="w-full pr-10 pl-4 py-2 bg-gray-100 rounded-lg focus:ring-2 focus:ring-kku-primary outline-none border-none transition-all">
        </div>
    </form>

    <div class="flex items-center gap-6">
        <div class="relative inline-block text-left px-4" id="langDropdownContainer">
            <div>
                <button type="button" onclick="toggleLangDropdown()"
                    class="flex items-center gap-2 bg-white border border-gray-200 px-4 py-2 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                    <i class="fas fa-globe text-kku-primary text-lg"></i>
                    <span>{{ app()->getLocale() == 'ar' ? 'العربية' : 'English' }}</span>
                    <i class="fas fa-chevron-down text-[10px] text-gray-400"></i>
                </button>
            </div>

            <div id="langDropdown"
                class="hidden absolute left-0 z-10 mt-2 w-40 origin-top-left rounded-xl bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none overflow-hidden">
                <div class="py-1">
                    <a href="{{ url('lang/ar') }}"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-kku-bg hover:text-kku-primary {{ app()->getLocale() == 'ar' ? 'bg-gray-50 font-bold' : '' }}">
                        <span class="text-lg">🇸🇦</span> العربية
                        @if (app()->getLocale() == 'ar')
                            <i class="fas fa-check mr-auto text-[10px]"></i>
                        @endif
                    </a>

                    <a href="{{ url('lang/en') }}"
                        class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-kku-bg hover:text-kku-primary {{ app()->getLocale() == 'en' ? 'bg-gray-50 font-bold' : '' }}">
                        <span class="text-lg">🇺🇸</span> English
                        @if (app()->getLocale() == 'en')
                            <i class="fas fa-check ml-auto text-[10px]"></i>
                        @endif
                    </a>
                </div>
            </div>
        </div>
        {{-- // Notifications Dropdown --}}
        <div class="relative" id="notificationContainer">
            <button onclick="toggleNotifications()"
                class="relative p-2 text-gray-500 hover:bg-gray-100 rounded-full transition-all focus:outline-none">
                <i class="far fa-bell text-xl"></i>
                <span
                    class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] flex items-center justify-center rounded-full border-2 border-white">
                    2
                </span>
            </button>

            <div id="notificationDropdown"
                class="hidden absolute mt-3 w-80 bg-white rounded-2xl shadow-xl border border-gray-100 z-50 overflow-hidden
                {{ app()->getLocale() == 'ar' ? 'left-0 origin-top-left' : 'right-0 origin-top-right' }}">

                <div class="p-4 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-sm text-gray-800">{{ __('الاشعارات') }}</h3>
                    <span class="text-[10px] px-2 py-0.5 bg-kku-primary/10 text-kku-primary rounded-full font-bold">
                        {{ app()->getLocale() == 'ar' ? '2 جديد' : '2 New' }}
                    </span>
                </div>

                <div class="max-h-96 overflow-y-auto">
                    <a href="#"
                        class="flex gap-3 p-4 hover:bg-red-50/30 transition-colors border-b border-gray-50 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                        <div
                            class="w-10 h-10 rounded-full bg-red-100 flex-shrink-0 flex items-center justify-center text-red-600">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-800 font-bold mb-1">{{ __('تحذير من المخاطر الأكاديمية') }}</p>
                            <p class="text-[11px] text-gray-500 leading-relaxed">
                                {{ __('انخفض المعدل التراكمي للطالب فيصل الشهري إلى') }} <span
                                    class="text-red-600 font-bold">1.85</span>
                            </p>
                            <span class="text-[9px] text-gray-400 mt-2 block">2m ago</span>
                        </div>
                    </a>

                    <a href="#"
                        class="flex gap-3 p-4 hover:bg-blue-50/30 transition-colors border-b border-gray-50 {{ app()->getLocale() == 'ar' ? 'text-right' : 'text-left' }}">
                        <div
                            class="w-10 h-10 rounded-full bg-blue-100 flex-shrink-0 flex items-center justify-center text-blue-600">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-800 font-bold mb-1">{{ __('طلب موعد جديد') }}</p>
                            <p class="text-[11px] text-gray-500 leading-relaxed">
                                {{ __('الطالب أحمد طلب موعداً جديداً.') }}</p>
                            <span class="text-[9px] text-gray-400 mt-2 block">1h ago</span>
                        </div>
                    </a>
                </div>

                <a href="#"
                    class="block p-3 text-center text-xs text-kku-primary font-bold bg-gray-50 hover:bg-gray-100">
                    {{ __('عرض جميع الإشعارات') }}
                </a>
            </div>
        </div>
        {{-- Not --}}
        <div class="text-left border-r pr-4">
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">الفصل الدراسي</p>
            <p class="text-sm font-bold text-kku-primary">الثاني 1447هـ</p>
        </div>

    </div>

    <script>
        function toggleLangDropdown() {
            const dropdown = document.getElementById('langDropdown');
            dropdown.classList.toggle('hidden');
        }

        // إغلاق القائمة عند الضغط في أي مكان خارجها
        window.onclick = function(event) {
            if (!event.target.closest('#langDropdownContainer')) {
                const dropdown = document.getElementById('langDropdown');
                if (!dropdown.classList.contains('hidden')) {
                    dropdown.classList.add('hidden');
                }
            }
        }

        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('hidden');

            // إغلاق قائمة اللغة إذا كانت مفتوحة (اختياري لترتيب الواجهة)
            const langDropdown = document.getElementById('langDropdown');
            if (langDropdown) langDropdown.classList.add('hidden');
        }

        // إغلاق عند الضغط خارجها
        window.addEventListener('click', function(e) {
            const container = document.getElementById('notificationContainer');
            const dropdown = document.getElementById('notificationDropdown');
            if (container && !container.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
    </script>

</header>
