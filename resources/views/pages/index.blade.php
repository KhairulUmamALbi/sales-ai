@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">My Sales Pages</h1>
            <p class="text-slate-600 mt-1">{{ $pages->total() }} {{ Str::plural('page', $pages->total()) }} generated</p>
        </div>
        <a href="{{ route('pages.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 shadow-sm transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Create New
        </a>
    </div>

    <form method="GET" action="{{ route('pages.index') }}" class="mb-6">
        <div class="relative max-w-md">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Search by product name, description, or audience..."
                   class="w-full pl-10 pr-4 py-2.5 border border-slate-200 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            <svg class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </form>

    @if ($pages->isEmpty())
        <div class="bg-white rounded-2xl border border-dashed border-slate-300 py-16 px-6 text-center">
            <div class="text-5xl mb-3">📝</div>
            <h3 class="font-semibold text-slate-900 mb-1">
                {{ $search->isNotEmpty() ? 'No pages match your search' : 'No sales pages yet' }}
            </h3>
            <p class="text-slate-600 text-sm mb-5">
                {{ $search->isNotEmpty() ? 'Try a different keyword.' : 'Create your first AI-generated sales page now.' }}
            </p>
            @if ($search->isEmpty())
                <a href="{{ route('pages.create') }}" class="inline-block px-5 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                    Create First Page
                </a>
            @endif
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ($pages as $page)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 hover:shadow-lg hover:border-indigo-200 transition">
                    <div class="flex items-start justify-between mb-3">
                        <span class="inline-block px-2.5 py-1 text-xs font-medium rounded-full
                            {{ $page->template === 'modern' ? 'bg-indigo-50 text-indigo-700' : '' }}
                            {{ $page->template === 'minimalist' ? 'bg-slate-100 text-slate-700' : '' }}
                            {{ $page->template === 'bold' ? 'bg-orange-50 text-orange-700' : '' }}">
                            {{ ucfirst($page->template) }}
                        </span>
                        <span class="text-xs text-slate-400">{{ $page->created_at->diffForHumans() }}</span>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-1 line-clamp-1">{{ $page->product_name }}</h3>
                    <p class="text-sm text-slate-600 mb-4 line-clamp-2">
                        {{ $page->section('headline', $page->description) }}
                    </p>
                    <div class="flex gap-2">
                        <a href="{{ route('pages.show', $page) }}"
                           class="flex-1 px-3 py-2 text-center bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                            Preview
                        </a>
                        <a href="{{ route('pages.edit', $page) }}"
                           class="px-3 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('pages.destroy', $page) }}"
                              onsubmit="return confirm('Hapus sales page ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M8 7V4a1 1 0 011-1h6a1 1 0 011 1v3"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $pages->links() }}
        </div>
    @endif
</div>
@endsection
