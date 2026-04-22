@extends('layouts.app')

@section('content')
{{-- Admin toolbar (fixed) --}}
<div class="sticky top-16 z-20 bg-white border-b border-slate-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <a href="{{ route('pages.index') }}" class="text-sm text-slate-500 hover:text-slate-700 inline-flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back
            </a>
            <span class="text-sm text-slate-400">|</span>
            <span class="text-sm font-medium text-slate-900">{{ $page->product_name }}</span>
            <span class="hidden sm:inline px-2 py-0.5 bg-slate-100 text-slate-600 text-xs rounded-full">
                {{ ucfirst($page->template) }}
            </span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pages.edit', $page) }}"
               class="px-3 py-1.5 text-sm bg-slate-100 text-slate-700 rounded-lg hover:bg-slate-200">
                Edit
            </a>
            <a href="{{ route('pages.export', $page) }}"
               class="px-3 py-1.5 text-sm bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 inline-flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Export HTML
            </a>
        </div>
    </div>

    {{-- Section regeneration bar (bonus feature) --}}
    {{-- <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-3">
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span class="text-slate-500 font-medium mr-1">🔄 Regenerate section:</span>
            @foreach([
                'headline' => 'Headline',
                'sub_headline' => 'Sub-headline',
                'product_description' => 'Description',
                'benefits' => 'Benefits',
                'features_breakdown' => 'Features',
                'social_proof' => 'Testimonials',
                'cta_primary' => 'CTA',
            ] as $key => $label)
                <form method="POST" action="{{ route('pages.regenerate-section', $page) }}" class="inline">
                    @csrf
                    <input type="hidden" name="section" value="{{ $key }}">
                    <button type="submit"
                            class="px-2.5 py-1 bg-white border border-slate-200 rounded-md hover:border-indigo-300 hover:bg-indigo-50 hover:text-indigo-700 transition">
                        {{ $label }}
                    </button>
                </form>
            @endforeach
        </div>
    </div> --}}
</div>

{{-- The actual rendered sales page --}}
<div class="bg-white">
    @include('pages.partials.sales-page-render', ['page' => $page])
</div>
@endsection
