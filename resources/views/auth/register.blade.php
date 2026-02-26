<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - نظام الإرشاد الأكاديمي</title>

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
                        'fade-in': 'fadeIn 0.6s ease-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0'
                            },
                            '100%': {
                                opacity: '1'
                            }
                        },
                        slideUp: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(20px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #004d25 0%, #006837 100%);
        }

        .login-card {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(67, 160, 71, 0.1);
            border-color: #43a047;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 77, 37, 0.3);
        }

        .error-message {
            animation: slideUp 0.3s ease-out;
        }
    </style>
</head>

<body class="font-sans text-gray-800 flex items-center justify-center min-h-screen py-12 px-4">
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-10 right-10 w-40 h-40 bg-white opacity-10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 left-10 w-60 h-60 bg-white opacity-5 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        <!-- Logo and Title -->
        <div class="text-center mb-8 animate-fade-in">
            <div class="flex justify-center mb-6">
                <div class="bg-white p-2 rounded-2xl shadow-lg flex items-center justify-center w-28 h-28">
                    <img src="{{ asset('img/logo.svg') }}" alt="شعار جامعة الملك خالد" class="w-24 h-24 object-contain">
                </div>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">نظام الإرشاد الأكاديمي</h1>
            <p class="text-white/80 text-sm">جامعة الملك خالد</p>
        </div>

        <!-- Login Card -->
        <div class="bg-white rounded-3xl login-card p-8 animate-slide-up">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">تسجيل الدخول</h2>
            <p class="text-gray-500 text-sm mb-8">أدخل بيانات حسابك للمتابعة</p>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope ml-2 text-kku-primary"></i>الاسم الكامل
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required
                        autocomplete="name" autofocus
                        class="input-focus w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 focus:bg-white transition-all outline-none @error('name') border-red-500 bg-red-50 @enderror"
                        placeholder="أدخل اسمك الكامل">

                    @error('name')
                        <p class="text-red-500 text-sm mt-2 error-message flex items-center">
                            <i class="fas fa-exclamation-circle ml-2"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-envelope ml-2 text-kku-primary"></i>البريد الإلكتروني
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        autocomplete="email" autofocus
                        class="input-focus w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 focus:bg-white transition-all outline-none @error('email') border-red-500 bg-red-50 @enderror"
                        placeholder="أدخل بريدك الإلكتروني">

                    @error('email')
                        <p class="text-red-500 text-sm mt-2 error-message flex items-center">
                            <i class="fas fa-exclamation-circle ml-2"></i>{{ $message }}
                        </p>
                    @enderror
                </div>
                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock ml-2 text-kku-primary"></i>كلمة المرور
                    </label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="input-focus w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 focus:bg-white transition-all outline-none @error('password') border-red-500 bg-red-50 @enderror"
                        placeholder="أدخل كلمة مرورك">

                    @error('password')
                        <p class="text-red-500 text-sm mt-2 error-message flex items-center">
                            <i class="fas fa-exclamation-circle ml-2"></i>{{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="password-confirm" class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-lock ml-2 text-kku-primary"></i>تاكيد كلمة المرور
                    </label>
                    <input id="password-confirm" type="password" name="password_confirmation" required
                        autocomplete="password_confirmation"
                        class="input-focus  mb-5 w-full px-4 py-3 border-2 border-gray-200 rounded-lg bg-gray-50 focus:bg-white transition-all outline-none @error('password_confirmation') border-red-500 bg-red-50 @enderror"
                        placeholder="تاكيد كلمة المرور">

                </div>
                <!-- Remember Me -->
                <div class="flex items-center justify-between mt-5">

                    <a href="{{ route('login') }}"
                        class="text-sm text-kku-primary hover:text-kku-light font-semibold transition">
                        هل لديك سحب حساب؟ قم بتسجيل الدخول
                    </a>
                </div>

                <!-- Login Button -->
                <button type="submit"
                    class="btn-login w-full bg-gradient-to-r from-kku-primary to-kku-light text-white font-bold py-3 rounded-lg transition-all duration-300 flex items-center justify-center gap-2 hover:shadow-lg">
                    تسجيل جديد
                </button>


            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-8 text-white/80 text-sm">
            <p>© 2025 جامعة الملك خالد - جميع الحقوق محفوظة</p>
        </div>
    </div>
</body>

</html>
