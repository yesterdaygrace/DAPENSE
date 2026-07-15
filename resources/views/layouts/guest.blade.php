<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="WAS — Web Accounting System" />
    <meta property="og:title" content="WAS" />
    <meta property="og:description" content="Web Accounting System — Sistem akuntansi terintegrasi untuk pengelolaan keuangan perusahaan." />
    <meta property="og:type" content="website" />
    <title>{{ config('app.name', 'WAS') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full bg-background antialiased">
    <div class="flex min-h-screen">
        {{ $slot }}
    </div>
</body>
</html>
