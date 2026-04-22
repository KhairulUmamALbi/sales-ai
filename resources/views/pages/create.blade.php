@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('pages.index') }}" class="text-sm text-slate-500 hover:text-slate-700 mb-3 inline-flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to pages
        </a>
        <h1 class="text-2xl font-bold text-slate-900">{{ $page ? 'Edit & Regenerate' : 'Create Sales Page' }}</h1>
        <p class="text-slate-600 mt-1">
            Berikan detail produk, lalu biarkan AI membuat sales page yang persuasif untuk Anda.
        </p>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST"
          action="{{ $page ? route('pages.update', $page) : route('pages.store') }}"
          id="generateForm"
          class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 space-y-5">
        @csrf
        @if ($page)
            @method('PUT')
        @endif

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Product / Service Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="product_name" required
                   value="{{ old('product_name', $page?->product_name) }}"
                   placeholder="e.g. Kursus Digital Marketing Mastery"
                   class="w-full px-3.5 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Description <span class="text-red-500">*</span>
            </label>
            <textarea name="description" required rows="4"
                      placeholder="Jelaskan produk Anda secara singkat: apa itu, untuk apa, dan masalah apa yang diselesaikan."
                      class="w-full px-3.5 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('description', $page?->description) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Key Features <span class="text-red-500">*</span>
                <span class="text-slate-400 font-normal">(comma-separated)</span>
            </label>
            <textarea name="features" required rows="3"
                      placeholder="e.g. 12 modul video, akses seumur hidup, mentoring mingguan, sertifikat resmi"
                      class="w-full px-3.5 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('features', $page ? implode(', ', $page->features) : '') }}</textarea>
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Target Audience <span class="text-red-500">*</span>
                </label>
                <input type="text" name="target_audience" required
                       value="{{ old('target_audience', $page?->target_audience) }}"
                       placeholder="e.g. UMKM pemula di Indonesia"
                       class="w-full px-3.5 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Price
                </label>
                <input type="text" name="price"
                       value="{{ old('price', $page?->price) }}"
                       placeholder="e.g. Rp 499.000"
                       class="w-full px-3.5 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                Unique Selling Points (USP)
            </label>
            <textarea name="usp" rows="3"
                      placeholder="Apa yang membuat produk Anda berbeda dari kompetitor?"
                      class="w-full px-3.5 py-2.5 border border-slate-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('usp', $page?->usp) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Design Template</label>
            <div class="grid grid-cols-3 gap-3">
                @foreach([
                    ['modern', 'Modern', 'bg-gradient-to-br from-indigo-500 to-purple-600'],
                    ['minimalist', 'Minimalist', 'bg-gradient-to-br from-slate-700 to-slate-900'],
                    ['bold', 'Bold', 'bg-gradient-to-br from-orange-500 to-red-600'],
                ] as $tpl)
                    <label class="cursor-pointer">
                        <input type="radio" name="template" value="{{ $tpl[0] }}"
                               {{ old('template', $page?->template ?? 'modern') === $tpl[0] ? 'checked' : '' }}
                               class="sr-only peer">
                        <div class="border-2 border-slate-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 rounded-xl p-3 text-center transition">
                            <div class="w-full h-16 rounded-lg mb-2 {{ $tpl[2] }}"></div>
                            <div class="text-sm font-medium text-slate-900">{{ $tpl[1] }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex gap-3">
            <button type="submit" id="submitBtn"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 transition disabled:opacity-60 disabled:cursor-not-allowed">
                <span id="btnLabel" class="inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    {{ $page ? 'Regenerate with AI' : 'Generate with AI' }}
                </span>
                <span id="btnLoading" class="hidden items-center gap-2">
                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Generating... (10–30s)
                </span>
            </button>
            @if ($page)
                <a href="{{ route('pages.show', $page) }}"
                   class="px-6 py-3 bg-slate-100 text-slate-700 font-medium rounded-xl hover:bg-slate-200">
                    Cancel
                </a>
            @endif
        </div>
    </form>
</div>

<script>
document.getElementById('generateForm').addEventListener('submit', function() {
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('btnLabel').classList.add('hidden');
    document.getElementById('btnLoading').classList.remove('hidden');
    document.getElementById('btnLoading').classList.add('inline-flex');
});
</script>
@endsection
