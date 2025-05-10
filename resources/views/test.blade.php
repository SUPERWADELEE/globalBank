<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>錢包介面</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
<div class="p-4 bg-white rounded shadow max-w-md mx-auto">
    {{-- Header --}}
    <div class="flex items-center justify-between border-b pb-4 mb-4">
        <div class="font-bold text-xl">品牌 LOGO</div>
        <div class="flex items-center space-x-2">
            <div class="text-sm text-right">
                <div class="font-bold">Edward Yen</div>
                <div class="text-xs">VIP1</div>
            </div>
            <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="..." /></svg>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex space-x-2 mb-4 text-sm">
        <button class="px-3 py-1 bg-gray-200 rounded">我的錢包</button>
        <button class="px-3 py-1 bg-gray-100 rounded">即時匯率</button>
        <button class="px-3 py-1 bg-gray-100 rounded">交易紀錄</button>
    </div>

    {{-- Balance --}}
    <div class="border rounded p-4 bg-gray-50 mb-4">
        <div class="flex justify-between items-center mb-2">
            <span class="font-bold">總餘額</span>
            <button class="px-2 py-1 text-sm border rounded">刷新</button>
        </div>
        <div class="text-center text-2xl font-bold text-gray-800">13,669</div>
        <div class="text-center text-sm text-gray-500">USDT</div>
    </div>

    {{-- Wallet Items --}}
    <div class="space-y-4">
        {{-- USDT --}}
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <img src="/icons/usdt.png" class="w-5 h-5" alt="USDT">
                <span>USDT</span>
            </div>
            <div class="flex items-center space-x-2">
                <span>10,000</span>
                <button class="bg-yellow-200 text-sm px-3 py-1 rounded">發送</button>
                <button class="bg-yellow-200 text-sm px-3 py-1 rounded">接收</button>
            </div>
        </div>

        {{-- KRW --}}
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span>₩</span>
                <span>KRW</span>
            </div>
            <span>5,000,000</span>
        </div>

        {{-- JPY --}}
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span>¥</span>
                <span>JPY</span>
            </div>
            <span>20,000</span>
        </div>

        {{-- SGD --}}
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span>$</span>
                <span>SGD</span>
            </div>
            <span>0.00</span>
        </div>
    </div>

    {{-- Customer Service --}}
    <div class="mt-6 flex justify-center">
        <button class="flex items-center space-x-1 text-sm">
            <svg class="w-6 h-6" fill="black" viewBox="0 0 24 24"><path d="..." /></svg>
            <span>聯繫客服</span>
        </button>
    </div>
</div>
</body>
</html>