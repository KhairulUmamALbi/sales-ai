<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SalesAI') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 min-h-screen">
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('pages.index') }}" class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center text-white font-bold">S</div>
                        <span class="font-bold text-slate-900">SalesAI</span>
                    </a>
                    <div class="hidden md:flex ml-10 gap-1">
                        <a href="{{ route('dashboard') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('pages.index') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('pages.index') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900' }}">
                            My Pages
                        </a>
                        <a href="{{ route('pages.create') }}"
                           class="px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('pages.create') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:text-slate-900' }}">
                            Create New
                        </a>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="hidden sm:block text-sm text-slate-600">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-slate-600 hover:text-red-600 transition">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <main>
        @yield('content')
    </main>
</body>
</html>
