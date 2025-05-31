<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Livewire Styles --}}
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen">
        {{-- Navigation --}}
        <nav class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <h1 class="text-xl font-bold text-gray-900">{{ config('app.name', 'Laravel') }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        {{-- Main Content --}}
        <main class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                {{ $slot }}
            </div>
        </main>
    </div>

    {{-- Livewire Scripts --}}
    @livewireScripts
</body>
</html> 