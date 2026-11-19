<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel Admin · BeasiswaKu')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="{{ asset('js/admin.js') }}" defer></script>
</head>
<body class="admin-body" data-admin-login-url="{{ route('admin.login') }}">
    @if(session('success'))<div class="flash admin-flash" role="status">{{ session('success') }}</div>@endif
    @yield('content')
</body>
</html>
