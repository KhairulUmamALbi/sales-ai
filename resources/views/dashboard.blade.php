@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
            <p class="text-slate-600 mt-1">Welcome back, {{ auth()->user()->name }}!</p>
        </div>
    </div>

    {{-- Welcome card --}}
    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-slate-900">Hello, there! 👋</h3>
                <p class="mt-1 text-sm text-slate-600">
                    You're logged in and ready to manage your sales pages.
                </p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('pages.create') }}"
                   class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition">
                    Create Page
                </a>
                <a href="{{ route('pages.index') }}"
                   class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition">
                    View Pages
                </a>
            </div>
        </div>
    </div>

    {{-- Stats cards --}}
    @php
        $totalPages = \App\Models\SalesPage::where('user_id', auth()->id())->count();
        $recentPages = \App\Models\SalesPage::where('user_id', auth()->id())
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        $templates = \App\Models\SalesPage::where('user_id', auth()->id())
            ->distinct('template')
            ->count('template');
    @endphp

    <div class="grid gap-5 md:grid-cols-3">
        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Total Pages</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $totalPages }}</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Created This Week</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $recentPages }}</p>
        </div>

        <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
            <p class="text-sm font-medium text-slate-500">Templates Used</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $templates }} / 3</p>
        </div>
    </div>

</div>
@endsection
