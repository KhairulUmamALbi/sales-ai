@php
    $c = $page->generated_content ?? [];
    $template = $page->template ?? 'modern';

    // Template color schemes
    $themes = [
        'modern' => [
            'hero_bg' => 'bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500',
            'hero_text' => 'text-white',
            'accent' => 'text-indigo-600',
            'btn_primary' => 'bg-white text-indigo-600 hover:bg-indigo-50',
            'btn_secondary' => 'bg-transparent border-2 border-white text-white hover:bg-white/10',
            'card_bg' => 'bg-white',
            'section_bg' => 'bg-slate-50',
            'price_bg' => 'bg-gradient-to-br from-indigo-600 to-purple-600',
        ],
        'minimalist' => [
            'hero_bg' => 'bg-white border-b border-slate-200',
            'hero_text' => 'text-slate-900',
            'accent' => 'text-slate-900',
            'btn_primary' => 'bg-slate-900 text-white hover:bg-slate-800',
            'btn_secondary' => 'bg-transparent border-2 border-slate-300 text-slate-700 hover:bg-slate-50',
            'card_bg' => 'bg-white border border-slate-200',
            'section_bg' => 'bg-white',
            'price_bg' => 'bg-slate-900',
        ],
        'bold' => [
            'hero_bg' => 'bg-gradient-to-br from-orange-500 via-red-500 to-pink-600',
            'hero_text' => 'text-white',
            'accent' => 'text-orange-600',
            'btn_primary' => 'bg-yellow-400 text-slate-900 hover:bg-yellow-300',
            'btn_secondary' => 'bg-black/20 border-2 border-white text-white hover:bg-black/30',
            'card_bg' => 'bg-white',
            'section_bg' => 'bg-orange-50',
            'price_bg' => 'bg-gradient-to-br from-orange-500 to-red-600',
        ],
    ];
    $t = $themes[$template];
@endphp

{{-- ===== HERO ===== --}}
<section class="{{ $t['hero_bg'] }} {{ $t['hero_text'] }} py-20 md:py-28 px-6">
    <div class="max-w-4xl mx-auto text-center">
        @if ($page->price)
            <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur text-sm font-medium rounded-full mb-5">
                ⭐ Untuk {{ $page->target_audience }}
            </span>
        @endif
        <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-5">
            {{ $c['headline'] ?? 'Transform Your Business Today' }}
        </h1>
        <p class="text-lg md:text-xl opacity-90 max-w-2xl mx-auto mb-8">
            {{ $c['sub_headline'] ?? '' }}
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="#pricing" class="px-7 py-3.5 {{ $t['btn_primary'] }} font-bold rounded-xl shadow-lg transition">
                {{ $c['cta_primary'] ?? 'Get Started' }}
            </a>
            <a href="#benefits" class="px-7 py-3.5 {{ $t['btn_secondary'] }} font-semibold rounded-xl transition">
                {{ $c['cta_secondary'] ?? 'Learn More' }}
            </a>
        </div>
    </div>
</section>

{{-- ===== PRODUCT DESCRIPTION ===== --}}
<section class="{{ $t['section_bg'] }} py-16 md:py-20 px-6">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-10">
            <span class="inline-block text-sm font-semibold {{ $t['accent'] }} uppercase tracking-wider mb-3">
                About {{ $page->product_name }}
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900">
                Dibuat untuk {{ $page->target_audience }}
            </h2>
        </div>
        <div class="prose prose-lg text-slate-700 max-w-none leading-relaxed">
            @foreach(explode("\n", $c['product_description'] ?? '') as $paragraph)
                @if(trim($paragraph))
                    <p class="mb-4">{{ trim($paragraph) }}</p>
                @endif
            @endforeach
        </div>
    </div>
</section>

{{-- ===== BENEFITS ===== --}}
@if (!empty($c['benefits']))
<section id="benefits" class="bg-white py-16 md:py-20 px-6">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-12">
            <span class="inline-block text-sm font-semibold {{ $t['accent'] }} uppercase tracking-wider mb-3">
                Why Choose Us
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-3">
                Manfaat yang Anda Dapatkan
            </h2>
        </div>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($c['benefits'] as $i => $benefit)
                <div class="{{ $t['card_bg'] }} rounded-2xl p-6 shadow-sm hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl {{ $t['price_bg'] }} text-white flex items-center justify-center font-bold text-lg mb-4">
                        {{ $i + 1 }}
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 mb-2">{{ $benefit['title'] ?? '' }}</h3>
                    <p class="text-slate-600 leading-relaxed">{{ $benefit['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== FEATURES BREAKDOWN ===== --}}
@if (!empty($c['features_breakdown']))
<section class="{{ $t['section_bg'] }} py-16 md:py-20 px-6">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <span class="inline-block text-sm font-semibold {{ $t['accent'] }} uppercase tracking-wider mb-3">
                What's Included
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900">
                Fitur Lengkap untuk Hasil Maksimal
            </h2>
        </div>
        <div class="grid md:grid-cols-2 gap-5">
            @foreach($c['features_breakdown'] as $feature)
                <div class="{{ $t['card_bg'] }} rounded-2xl p-6 flex gap-4 shadow-sm">
                    <div class="flex-shrink-0 w-10 h-10 rounded-lg bg-green-100 text-green-600 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 mb-1">{{ $feature['title'] ?? '' }}</h3>
                        <p class="text-slate-600 text-sm leading-relaxed">{{ $feature['description'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== SOCIAL PROOF ===== --}}
@if (!empty($c['social_proof']))
<section class="bg-white py-16 md:py-20 px-6">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <span class="inline-block text-sm font-semibold {{ $t['accent'] }} uppercase tracking-wider mb-3">
                Testimonials
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900">
                Apa Kata Mereka
            </h2>
        </div>
        <div class="grid md:grid-cols-3 gap-5">
            @foreach($c['social_proof'] as $testimonial)
                <div class="bg-slate-50 rounded-2xl p-6">
                    <div class="flex gap-0.5 text-yellow-400 mb-3">
                        @for($i = 0; $i < 5; $i++)
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-slate-700 italic mb-4 leading-relaxed">"{{ $testimonial['quote'] ?? '' }}"</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full {{ $t['price_bg'] }} text-white flex items-center justify-center font-bold">
                            {{ strtoupper(substr($testimonial['name'] ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-semibold text-slate-900 text-sm">{{ $testimonial['name'] ?? '' }}</div>
                            <div class="text-xs text-slate-500">{{ $testimonial['role'] ?? '' }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== PRICING + CTA ===== --}}
<section id="pricing" class="{{ $t['section_bg'] }} py-16 md:py-20 px-6">
    <div class="max-w-2xl mx-auto">
        <div class="{{ $t['card_bg'] }} rounded-3xl p-8 md:p-10 text-center shadow-xl">
            <span class="inline-block text-sm font-semibold {{ $t['accent'] }} uppercase tracking-wider mb-3">
                Special Offer
            </span>
            <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-6">
                Mulai Sekarang
            </h2>
            <div class="{{ $t['price_bg'] }} text-white rounded-2xl p-8 mb-6">
                <div class="text-sm opacity-80 mb-1">{{ $c['pricing_display']['currency'] ?? 'IDR' }}</div>
                <div class="text-5xl md:text-6xl font-extrabold mb-2">
                    {{ $c['pricing_display']['price'] ?? $page->price }}
                </div>
                @if (!empty($c['pricing_display']['note']))
                    <div class="text-sm opacity-80">{{ $c['pricing_display']['note'] }}</div>
                @endif
            </div>
            <a href="#" class="block w-full px-7 py-4 {{ $template === 'minimalist' ? 'bg-slate-900 text-white hover:bg-slate-800' : 'bg-indigo-600 text-white hover:bg-indigo-700' }} font-bold rounded-xl text-lg shadow-lg transition mb-3">
                {{ $c['cta_primary'] ?? 'Get Started Now' }}
            </a>
            <p class="text-sm text-slate-500">{{ $c['cta_secondary'] ?? '' }}</p>
        </div>
    </div>
</section>

{{-- ===== FOOTER ===== --}}
<footer class="bg-slate-900 text-slate-400 py-8 px-6 text-center text-sm">
    © {{ date('Y') }} {{ $page->product_name }} · Generated by SalesAI
</footer>
