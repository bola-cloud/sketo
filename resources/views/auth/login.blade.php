<!DOCTYPE html>
<html class="loading" lang="ar" data-textdirection="rtl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <title>تسجيل الدخول - Sketo</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('logo.png')}}">
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&family=Inter:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Vendor CSS -->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/vendors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/app.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/custom-rtl.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/style-rtl.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/new-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/block-icon/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/fonts/line-awesome/css/line-awesome.min.css')}}">

    <style>
        body {
            background: var(--sidebar-bg-gradient) !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cairo', sans-serif !important;
        }

        .login-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .brand-logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .brand-logo {
            width: 80px;
            margin-bottom: 15px;
        }

        .brand-text {
            color: #1e293b;
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: var(--sketo-primary);
            box-shadow: 0 0 0 4px rgba(var(--primary-h), var(--primary-s), var(--primary-l), 0.1);
        }

        .btn-login {
            background: var(--sketo-primary);
            color: white;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(var(--primary-h), var(--primary-s), var(--primary-l), 0.3);
            border: none;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(var(--primary-h), var(--primary-s), var(--primary-l), 0.4);
        }

        .form-group label {
            font-weight: 600;
            color: #64748b;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .input-group-text {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-left: none;
            /* RTL specific */
            border-radius: 0 12px 12px 0;
            color: #94a3b8;
        }

        /* Fix for RTL input groups */
        .input-group>.form-control {
            border-radius: 12px 0 0 12px !important;
        }

        .alert-danger {
            background-color: #fef2f2;
            border: 1px solid #fee2e2;
            color: #ef4444;
            border-radius: 12px;
            font-size: 0.9rem;
            padding: 12px;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="brand-logo-container">
            <img src="{{asset('logo.png')}}" alt="Sketo Logo" class="brand-logo">
            <h2 class="brand-text">SKETO</h2>
            <p class="text-muted">تسجيل الدخول للوحة التحكم</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0 list-unstyled">
                    @foreach ($errors->all() as $error)
                        <li><i class="la la-exclamation-circle"></i> {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success mb-3">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group mb-3">
                <label for="email">البريد الإلكتروني</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="la la-user"></i></span>
                    </div>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required
                        autofocus placeholder="example@domain.com">
                </div>
            </div>

            <div class="form-group mb-4">
                <label for="password">كلمة المرور</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="la la-lock"></i></span>
                    </div>
                    <input type="password" class="form-control" id="password" name="password" required
                        placeholder="********">
                </div>
            </div>

            <div class="form-group d-flex justify-content-between align-items-center mb-4">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="remember_me" name="remember">
                    <label class="custom-control-label text-muted" for="remember_me">تذكرني</label>
                </div>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-primary text-sm font-weight-bold">نسيت كلمة
                        المرور؟</a>
                @endif
            </div>

            <button type="submit" class="btn btn-login">
                تسجيل الدخول <i class="la la-arrow-left mr-1"></i>
            </button>
        </form>

        <div class="text-center mt-4">
            <p class="text-muted text-sm">Design & Developed by <a href="https://wa.me/201555622169"
                    class="font-weight-bold text-primary">Eng: Bola Eshaq</a></p>
        </div>
    </div>

    <!-- Vendor JS -->
    <script src="{{asset('app-assets/vendors/js/vendors.min.js')}}" type="text/javascript"></script>
</body>

</html>