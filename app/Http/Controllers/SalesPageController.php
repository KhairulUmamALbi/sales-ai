<?php

namespace App\Http\Controllers;

use App\Models\SalesPage;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SalesPageController extends Controller
{
    public function __construct(protected GeminiService $gemini)
    {
    }

    /**
     * History / list page with search.
     */
    public function index(Request $request)
    {
        $search = $request->string('search')->trim();

        $pages = SalesPage::where('user_id', $request->user()->id)
            ->when($search->isNotEmpty(), function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('product_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('target_audience', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('pages.index', [
            'pages' => $pages,
            'search' => $search,
        ]);
    }

    /**
     * Show the creation form.
     */
    public function create()
    {
        return view('pages.create', ['page' => null]);
    }

    /**
     * Generate a new sales page via AI and save.
     */
    public function store(Request $request)
    {
        $data = $this->validateInput($request);

        try {
            $generated = $this->gemini->generateSalesPage($data);
        } catch (\Throwable $e) {
            Log::error('Sales page generation failed', ['error' => $e->getMessage()]);
            return back()
                ->withInput()
                ->with('error', 'Gagal menghasilkan sales page: ' . $e->getMessage());
        }

        $page = SalesPage::create([
            'user_id' => $request->user()->id,
            'product_name' => $data['product_name'],
            'description' => $data['description'],
            'features' => $data['features'],
            'target_audience' => $data['target_audience'],
            'price' => $data['price'],
            'usp' => $data['usp'],
            'template' => $data['template'],
            'generated_content' => $generated,
            'generated_at' => now(),
        ]);

        return redirect()
            ->route('pages.show', $page)
            ->with('success', 'Sales page berhasil dibuat! 🎉');
    }

    /**
     * Show live preview of a saved page.
     */
    public function show(Request $request, SalesPage $page)
    {
        $this->authorize($request, $page);
        return view('pages.preview', ['page' => $page]);
    }

    /**
     * Show edit form (allows re-generation).
     */
    public function edit(Request $request, SalesPage $page)
    {
        $this->authorize($request, $page);
        return view('pages.create', ['page' => $page]);
    }

    /**
     * Update and re-generate the page.
     */
    public function update(Request $request, SalesPage $page)
    {
        $this->authorize($request, $page);
        $data = $this->validateInput($request);

        try {
            $generated = $this->gemini->generateSalesPage($data);
        } catch (\Throwable $e) {
            Log::error('Sales page re-generation failed', ['error' => $e->getMessage()]);
            return back()
                ->withInput()
                ->with('error', 'Gagal meregenerate sales page: ' . $e->getMessage());
        }

        $page->update([
            'product_name' => $data['product_name'],
            'description' => $data['description'],
            'features' => $data['features'],
            'target_audience' => $data['target_audience'],
            'price' => $data['price'],
            'usp' => $data['usp'],
            'template' => $data['template'],
            'generated_content' => $generated,
            'generated_at' => now(),
        ]);

        return redirect()
            ->route('pages.show', $page)
            ->with('success', 'Sales page berhasil diregenerate! 🔄');
    }

    /**
     * Regenerate only a specific section (BONUS feature).
     */
    public function regenerateSection(Request $request, SalesPage $page)
    {
        $this->authorize($request, $page);

        $validSections = [
            'headline', 'sub_headline', 'product_description',
            'benefits', 'features_breakdown', 'social_proof',
            'pricing_display', 'cta_primary', 'cta_secondary',
        ];

        $section = $request->input('section');
        if (!in_array($section, $validSections, true)) {
            return back()->with('error', 'Section tidak valid.');
        }

        try {
            $input = [
                'product_name' => $page->product_name,
                'description' => $page->description,
                'features' => $page->features,
                'target_audience' => $page->target_audience,
                'price' => $page->price,
                'usp' => $page->usp,
            ];

            $updated = $this->gemini->regenerateSection(
                $section,
                $input,
                $page->generated_content ?? []
            );

            $page->update([
                'generated_content' => $updated,
                'generated_at' => now(),
            ]);

            return back()->with('success', "Section '{$section}' berhasil diregenerate!");
        } catch (\Throwable $e) {
            Log::error('Section regeneration failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal regenerate section: ' . $e->getMessage());
        }
    }

    /**
     * Delete a sales page.
     */
    public function destroy(Request $request, SalesPage $page)
    {
        $this->authorize($request, $page);
        $page->delete();

        return redirect()
            ->route('pages.index')
            ->with('success', 'Sales page dihapus.');
    }

    /**
     * Export sales page as standalone HTML (BONUS feature).
     */
    public function exportHtml(Request $request, SalesPage $page)
    {
        $this->authorize($request, $page);

        $html = view('pages.export', ['page' => $page])->render();

        $filename = str($page->product_name)->slug() . '-sales-page.html';

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // -----------------------------------------------------------------

    /**
     * Validate + normalize form input.
     */
    protected function validateInput(Request $request): array
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'features' => ['required', 'string', 'max:2000'],
            'target_audience' => ['required', 'string', 'max:500'],
            'price' => ['nullable', 'string', 'max:100'],
            'usp' => ['nullable', 'string', 'max:2000'],
            'template' => ['nullable', 'in:modern,minimalist,bold'],
        ]);

        // Convert comma-separated features to array
        $validated['features'] = collect(explode(',', $validated['features']))
            ->map(fn ($f) => trim($f))
            ->filter()
            ->values()
            ->all();

        $validated['template'] = $validated['template'] ?? 'modern';
        $validated['price'] = $validated['price'] ?? '';
        $validated['usp'] = $validated['usp'] ?? '';

        return $validated;
    }

    /**
     * Simple ownership check.
     */
    protected function authorize(Request $request, SalesPage $page): void
    {
        abort_if($page->user_id !== $request->user()->id, 403);
    }
}
