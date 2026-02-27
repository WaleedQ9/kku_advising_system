<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - نظام الإرشاد الأكاديمي</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif']
                    },
                    colors: {
                        kku: {
                            dark: '#004d25',
                            primary: '#006837',
                            light: '#43a047',
                            accent: '#c5a017',
                            gold: '#D4AF37',
                            bg: '#f8fafc'
                        }
                    },
                    animation: {
                        'modal-in': 'fadeIn 0.3s ease-out forwards',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'scale(0.95)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'scale(1)'
                            },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .sidebar-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-right: 4px solid #c5a017;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-thumb {
            background: #006837;
            border-radius: 10px;
        }

        .modal-overlay {
            background: rgba(90, 90, 90, 0.4);
            backdrop-filter: blur(4px);
        }
    </style>
</head>

<body class="bg-kku-bg font-sans text-gray-800 flex">


    {{-- @include('partials.sidebar') --}}
    @include('partials.sidebar')
    {{-- @include('partials.sidebar') --}}



    <main class="flex-1 min-h-screen">
        {{-- @include('partials.header') --}}

        @include('partials.header')
        {{-- @include('partials.header') --}}


        <div class="p-8 space-y-8">
            {{-- @yield('content') --}}

            @yield('content')
            {{-- @yield('content') --}}
        </div>

    </main>


    <div id="reportModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
        <div onclick="toggleModal()" class="modal-overlay absolute inset-0"></div>

        <div class="bg-white w-full max-w-xl rounded-3xl shadow-2xl relative z-10 overflow-hidden animate-modal-in">
            <div class="bg-kku-primary p-6 text-white flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i class="fas fa-file-alt text-2xl text-kku-accent"></i>
                    <h3 class="font-bold text-xl">تقرير إرشادي جديد</h3>
                </div>
                <button onclick="toggleModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-white/20 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form id="reportForm" class="p-8 space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">رقم الطالب</label>
                        <input type="text" placeholder="مثال: 44100..."
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 outline-none focus:border-kku-primary transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">نوع الجلسة</label>
                        <select
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 outline-none focus:border-kku-primary">
                            <option>جلسة حضورية</option>
                            <option>جلسة عن بعد (Zoom)</option>
                            <option>استشارة سريعة</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">موضوع التقرير</label>
                    <select
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 outline-none focus:border-kku-primary">
                        <option>تدني المعدل الأكاديمي</option>
                        <option>مشاكل في تسجيل المواد</option>
                        <option>استفسار عن الخطة الدراسية</option>
                        <option>أخرى</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">تفاصيل التوصيات</label>
                    <textarea rows="4" placeholder="اكتب ملاحظاتك وتوصياتك للطالب هنا..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl p-3 outline-none focus:border-kku-primary"></textarea>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="saveReport()"
                        class="flex-1 bg-kku-primary text-white py-3 rounded-xl font-bold hover:bg-kku-dark transition-all">
                        حفظ التقرير
                    </button>
                    <button type="button" onclick="toggleModal()"
                        class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-xl font-bold hover:bg-gray-200 transition-all">
                        إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

<script>
    function toggleModal() {
        const modal = document.getElementById('reportModal');
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden'; // منع السكرول عند فتح المودل
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    }


    // إغلاق المودل عند الضغط على مفتاح Esc
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            const modal = document.getElementById('reportModal');
            if (!modal.classList.contains('hidden')) toggleModal();
        }
    });
</script>

</html>
