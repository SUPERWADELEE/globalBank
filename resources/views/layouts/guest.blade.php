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

    <title>{{ config('app.name', 'Wallet') }} - {{ __('auth.login') }}</title>

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
        }

        /* 確保輸入框可以選擇文字 */
        input, textarea {
            -webkit-user-select: text;
            user-select: text;
        }

        /* WebView 輸入框優化 */
        .webview-input {
            font-size: 16px !important;
            -webkit-appearance: none;
            border-radius: 0;
        }

        /* WebView 按鈕優化 */
        .webview-button {
            -webkit-appearance: none;
            border-radius: 0;
            touch-action: manipulation;
        }

        /* 防止 iOS Safari 自動縮放 */
        input[type="text"], 
        input[type="password"], 
        input[type="email"], 
        input[type="number"], 
        textarea, 
        select {
            font-size: 16px !important;
            -webkit-appearance: none;
            border-radius: 0;
        }

        /* 防止 Android WebView 自動填充樣式 */
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px white inset !important;
            -webkit-text-fill-color: #374151 !important;
        }

        /* 移除 iOS 預設樣式 */
        input[type="submit"], 
        input[type="button"], 
        button {
            -webkit-appearance: none;
            border-radius: 0;
        }

        /* 確保安全區域適配 */
        .safe-area-inset {
            padding-top: env(safe-area-inset-top);
            padding-bottom: env(safe-area-inset-bottom);
            padding-left: env(safe-area-inset-left);
            padding-right: env(safe-area-inset-right);
        }

        /* 防止橫屏時的問題 */
        @media screen and (orientation: landscape) {
            .webview-input,
            input[type="text"], 
            input[type="password"] {
                font-size: 16px !important;
            }
        }

        /* Android WebView 優化 */
        @media screen and (-webkit-min-device-pixel-ratio: 0) {
            .webview-input,
            input[type="text"], 
            input[type="password"] {
                background-color: transparent;
            }
        }

        /* 確保在 WebView 中的觸摸體驗 */
        button, a, label {
            touch-action: manipulation;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased bg-white safe-area-inset">
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
            if (event.target.tagName !== 'INPUT' && event.target.tagName !== 'TEXTAREA') {
                event.preventDefault();
            }
        });

        // iOS 鍵盤處理
        if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
            document.addEventListener('focusin', function(e) {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    setTimeout(function() {
                        e.target.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 300);
                }
            });
        }
    </script>
</body>
</html> 