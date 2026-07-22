<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Halaman Tidak Ditemukan — {{ config('app.name', 'WAS') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #F4F6F8;
            color: #111827;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .container {
            text-align: center;
            max-width: 480px;
        }
        .code {
            font-size: 8rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #2563EB, #1E3A8A);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.04em;
            margin-bottom: -1rem;
            opacity: 0.12;
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: #111827;
        }
        p {
            color: #6B7280;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        a {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: #2563EB;
            color: #fff;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 200ms ease;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.3);
        }
        a:hover {
            background: #1D4ED8;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
            transform: translateY(-1px);
        }
        a:active {
            transform: scale(0.97);
        }
        @media (prefers-reduced-motion: reduce) {
            a { transition: none; transform: none; }
            a:hover { transform: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="code">404</div>
        <h1>Halaman tidak ditemukan</h1>
        <p>Halaman yang Anda cari mungkin telah dipindahkan, dihapus, atau tidak pernah ada.</p>
        <a href="{{ url('/') }}">&larr; Kembali ke Dasbor</a>
    </div>
</body>
</html>
