<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('app.platform.account_suspended') }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&family=Inter:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --bg: #f8fafc;
        }

        body {
            font-family:
                {{ app()->getLocale() == 'ar' ? "'Tajawal', sans-serif" : "'Inter', sans-serif" }}
            ;
            background-color: var(--bg);
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #1e293b;
        }

        .container {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 2rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.05);
            max-width: 500px;
            width: 90%;
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: #fff7ed;
            color: #f97316;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 1.5rem;
        }

        h1 {
            font-weight: 700;
            margin-bottom: 1rem;
        }

        p {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 1rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2);
        }

        .support {
            margin-top: 2rem;
            font-size: 0.9rem;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="icon-box">
            <i class="la la-ban"></i>
        </div>
        <h1>{{ __('app.platform.account_suspended') }}</h1>
        <p>{{ __('app.platform.account_suspended_msg') }}</p>

        <a href="mailto:support@sketo.com" class="btn">
            <i class="la la-envelope mr-1"></i> {{ __('app.platform.contact_support') }}
        </a>

        <div class="support">
            {{ __('app.platform.platform_name') }} &copy; {{ date('Y') }}
        </div>

        <div class="mt-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    style="background: none; border: none; color: #ef4444; cursor: pointer; text-decoration: underline; font-size: 0.9rem;">
                    {{ __('app.platform.logout') }}
                </button>
            </form>
        </div>
    </div>
</body>

</html>