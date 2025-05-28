<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }} - 會員登入</title>
    @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- 或你實際用的 CSS --}}
    @livewireStyles
    <script src="https://kit.fontawesome.com/xxxxxx.js" crossorigin="anonymous"></script> {{-- Icon 套件 --}}
</head>
<body>
    {{ $slot }}
    @livewireScripts
</body>
</html>