<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="theme-color" content="#60A5FA">

    <title>{{ config('app.name', 'Wallet') }} - 我的錢包</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <!-- WebView 優化樣式 -->
    <style>
        /* 基礎 WebView 優化 */
        html, body {
            height: 100%;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* 防止 iOS 縮放和選擇 */
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }

        /* 確保按鈕和連結可以點擊 */
        button, a, label, input[type="checkbox"] {
            -webkit-user-select: none;
            user-select: none;
            touch-action: manipulation;
        }

        /* 確保安全區域適配 */
        .safe-area-inset {
            padding-top: env(safe-area-inset-top);
            padding-bottom: env(safe-area-inset-bottom);
            padding-left: env(safe-area-inset-left);
            padding-right: env(safe-area-inset-right);
        }

        /* 移除 iOS 預設樣式 */
        button {
            -webkit-appearance: none;
            border-radius: 0;
        }

        /* 確保滾動流暢 */
        .scroll-smooth {
            -webkit-overflow-scrolling: touch;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-50 safe-area-inset">
    <div class="min-h-screen">
        {{ $slot }}
    </div>

    @livewireScripts

    <!-- WebView 優化腳本 -->
    <script>
        // 防止雙擊縮放
        document.addEventListener('touchstart', function(event) {
            if (event.touches.length > 1) {
                event.preventDefault();
            }
        });

        let lastTouchEnd = 0;
        document.addEventListener('touchend', function(event) {
            const now = (new Date()).getTime();
            if (now - lastTouchEnd <= 300) {
                event.preventDefault();
            }
            lastTouchEnd = now;
        }, false);

        // 防止長按選擇
        document.addEventListener('selectstart', function(event) {
            event.preventDefault();
        });

        // 防止右鍵選單
        document.addEventListener('contextmenu', function(event) {
            event.preventDefault();
        });
    </script>
</body>
</html> 