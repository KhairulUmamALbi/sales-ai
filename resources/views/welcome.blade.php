<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - Generate High-Converting Sales Pages with AI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-slate-50 via-white to-indigo-50 min-h-screen font-sans antialiased">
    <nav class="max-w-6xl mx-auto px-6 py-5 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center text-white font-bold">S</div>
            <span class="font-bold text-slate-900">SalesAI</span>
        </div>
        <div class="flex gap-3">
            @auth
                <a href="{{ route('pages.index') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 text-slate-700 hover:text-slate-900 transition">Login</a>
                <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Sign Up</a>
            @endauth
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-16 md:py-24">
        <div class="text-center max-w-3xl mx-auto">
            <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-700 text-sm font-medium rounded-full mb-5">
                Powered by Google Gemini AI
            </span>
            <h1 class="text-4xl md:text-6xl font-extrabold text-slate-900 leading-tight mb-6">
                Turn product ideas into<br>
                <span class="bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    high-converting sales pages
                </span>
            </h1>
            <p class="text-lg md:text-xl text-slate-600 mb-8">
                Describe your product. Our AI generates a complete landing page with
                headlines, benefits, testimonials, and calls-to-action — in seconds.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('register') }}"
                   class="px-7 py-3.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 transition">
                    Start Generating Free
                </a>
                <a href="{{ route('login') }}"
                   class="px-7 py-3.5 bg-white text-slate-900 font-semibold rounded-xl border border-slate-200 hover:border-slate-300 transition">
                    Log In
                </a>
            </div>
        </div>

        <div class="mt-20 grid md:grid-cols-3 gap-6">
            @foreach([
                ['⚡', 'Instant Generation', 'From input to full page in under 30 seconds.'],
                ['🎨', 'Multiple Templates', 'Modern, Minimalist, and Bold designs ready to use.'],
                ['📦', 'Export & Save', 'Download as HTML or regenerate sections anytime.'],
            ] as $feature)
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition">
                    <div class="text-3xl mb-3">{{ $feature[0] }}</div>
                    <h3 class="font-semibold text-slate-900 mb-1">{{ $feature[1] }}</h3>
                    <p class="text-slate-600 text-sm">{{ $feature[2] }}</p>
                </div>
            @endforeach
        </div>
    </main>

    <footer class="max-w-6xl mx-auto px-6 py-8 text-center text-slate-500 text-sm">
        Built with Laravel + Google Gemini · Khairul Umam Albi · 2026
    </footer>
</body>
</html>
