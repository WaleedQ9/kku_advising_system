@php
    // تحديد الموقع بناءً على اتجاه اللغة
    // في العربية (RTL): أسفل اليمين (bottom-5 right-5)
    // في الإنجليزية (LTR): أسفل اليسار (bottom-5 left-5)
    $alignmentClasses = app()->getLocale() == 'ar' ? 'bottom-5 left-5' : 'bottom-5 right-5';
    $animationClass = app()->getLocale() == 'ar' ? 'animate-in slide-in-from-left' : 'animate-in slide-in-from-right';
@endphp

@if (session('success'))
    <div id="alert-success"
        class="fixed {{ $alignmentClasses }}  z-[200]  h-auto w-[450px] flex items-center p-4 mb-4 text-green-800 rounded-2xl bg-green-50 border border-green-100 shadow-xl {{ $animationClass }} duration-500"
        role="alert">
        <div
            class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-white rounded-lg shadow-sm">
            <i class="fas fa-check"></i>
        </div>
        <div class="mx-3 text-sm font-bold">{{ session('success') }}</div>
        <button type="button" onclick="document.getElementById('alert-success').remove()"
            class="ms-auto -mx-1.5 -my-1.5 text-green-500 rounded-lg p-1.5 hover:bg-green-100 inline-flex items-center justify-center h-8 w-8">
            <i class="fas fa-times"></i>
        </button>
    </div>
@endif

@if ($errors->any())
    <div id="alert-error"
        class="fixed {{ $alignmentClasses }} z-[200]  h-auto w-[450px] flex flex-col p-4 mb-4 text-red-800 rounded-2xl bg-red-50 border border-red-100 shadow-xl {{ $animationClass }} duration-500"
        role="alert">
        <div class="flex items-center mb-2 ">
            <div
                class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-white rounded-lg shadow-sm">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="mx-3 text-sm font-black">{{ __('حدث خطأ في الإدخال:') }}</div>
            <button type="button" onclick="document.getElementById('alert-error').remove()"
                class="ms-auto -mx-1.5 -my-1.5 text-red-500 rounded-lg p-1.5 hover:bg-red-100 inline-flex items-center justify-center h-8 w-8">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <ul class="mt-1.5 list-disc list-inside text-xs font-bold px-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<script>
    // إخفاء التنبيه تلقائياً بعد 5 ثوانٍ مع تأثير اختفاء سلس
    setTimeout(() => {
        ['alert-success', 'alert-error'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.style.transition = "all 0.6s ease-in-out";
                el.style.opacity = "0";
                el.style.transform = "translateY(20px) scale(0.9)"; // ينزل للأسفل ويصغر قليلاً
                setTimeout(() => el.remove(), 500);
            }
        });
    }, 5000);
</script>
