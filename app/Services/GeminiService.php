<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GeminiService
{
    protected string $apiKey;
    protected string $model;
    protected string $baseUrl;
    protected int $timeout;

    /**
     * Fallback chain — kalau model primary overloaded, coba model berikutnya.
     * Diurutkan dari kualitas tertinggi ke paling ringan (paling jarang overload).
     */
    protected array $fallbackModels = [
        'gemini-2.5-flash',
        'gemini-2.5-flash-lite',
        'gemini-2.0-flash',
        'gemini-2.0-flash-lite',
    ];

    /** Maximum retry attempts per model before moving to fallback */
    protected int $maxRetries = 3;

    public function __construct()
    {
        $this->apiKey = config('gemini.api_key');
        $this->model = config('gemini.model');
        $this->baseUrl = config('gemini.base_url');
        $this->timeout = config('gemini.timeout');

        if (empty($this->apiKey)) {
            throw new RuntimeException('GEMINI_API_KEY is not configured in .env');
        }
    }

    /**
     * Generate a complete structured sales page from product input.
     */
    public function generateSalesPage(array $input): array
    {
        $prompt = $this->buildPrompt($input);
        $schema = $this->salesPageSchema();

        $response = $this->callGeminiWithFallback($prompt, $schema);

        return $this->validateStructure($response);
    }

    /**
     * Regenerate a specific section of the sales page.
     */
    public function regenerateSection(string $section, array $input, array $existingContent): array
    {
        $prompt = $this->buildSectionPrompt($section, $input, $existingContent);
        $schema = $this->sectionSchema($section);

        $result = $this->callGeminiWithFallback($prompt, $schema);

        $existingContent[$section] = $result[$section] ?? $result;
        return $existingContent;
    }

    /**
     * Build the main prompt for full sales page generation.
     */
    protected function buildPrompt(array $input): string
    {
        $features = is_array($input['features'])
            ? implode(', ', $input['features'])
            : $input['features'];

        return <<<PROMPT
You are an expert direct-response copywriter specializing in high-converting sales pages.
Generate a complete, persuasive, and structured sales page for the following product/service.

PRODUCT INPUT:
- Product/Service Name: {$input['product_name']}
- Description: {$input['description']}
- Key Features: {$features}
- Target Audience: {$input['target_audience']}
- Price: {$input['price']}
- Unique Selling Points: {$input['usp']}

REQUIREMENTS:
1. Write a COMPELLING headline (8-15 words) that hooks the target audience emotionally.
2. Write a sub-headline (15-25 words) that expands on the headline's promise.
3. Write a product_description (2-3 paragraphs) that tells a story and builds desire.
4. Generate 4-6 benefits (outcome-focused, NOT feature-focused). Each benefit must have a short title and a 1-2 sentence description.
5. Generate 4-6 features_breakdown items. These are concrete product capabilities with titles and explanations.
6. Generate 3 realistic social_proof testimonials. Use believable Indonesian or general names, relevant roles, and quotes (2-3 sentences) that reference specific outcomes.
7. Format pricing_display with the price, currency, and an optional note (e.g. "One-time payment" or "Billed monthly").
8. Write a strong cta_primary (3-6 words, action-oriented) and a softer cta_secondary (4-8 words).

TONE: Persuasive, confident, benefit-driven. Match the tone to the target audience.
LANGUAGE: Use the same language as the input product description. If input is in Bahasa Indonesia, generate in Bahasa Indonesia. If English, generate in English.

Return ONLY valid JSON matching the provided schema. Do not include markdown fences or explanatory text.
PROMPT;
    }

    /**
     * Build prompt for regenerating a single section.
     */
    protected function buildSectionPrompt(string $section, array $input, array $existing): string
    {
        $features = is_array($input['features'])
            ? implode(', ', $input['features'])
            : $input['features'];

        $context = json_encode($existing, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        return <<<PROMPT
You are an expert direct-response copywriter. Regenerate ONLY the "{$section}" section of a sales page.
Keep it consistent with the rest of the existing content.

PRODUCT:
- Name: {$input['product_name']}
- Description: {$input['description']}
- Features: {$features}
- Target Audience: {$input['target_audience']}
- Price: {$input['price']}
- USP: {$input['usp']}

EXISTING SALES PAGE (for consistency):
{$context}

Return a JSON object containing ONLY the "{$section}" key with its new value, matching the schema.
Make the new version meaningfully different and improved. Same language as the existing content.
PROMPT;
    }

    /**
     * Call Gemini with full fault tolerance:
     *   1. Try primary model with exponential backoff retry (3 attempts)
     *   2. If still failing, try each fallback model in turn
     *   3. If all models fail, throw a user-friendly error
     */
    protected function callGeminiWithFallback(string $prompt, array $schema): array
    {
        // Build the model queue: primary first, then all fallbacks not equal to primary
        $modelsToTry = array_values(array_unique(array_merge(
            [$this->model],
            array_diff($this->fallbackModels, [$this->model])
        )));

        $lastException = null;

        foreach ($modelsToTry as $modelName) {
            for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
                try {
                    $result = $this->callGemini($prompt, $schema, $modelName);

                    if ($modelName !== $this->model || $attempt > 1) {
                        Log::info('Gemini succeeded after retry/fallback', [
                            'model' => $modelName,
                            'attempt' => $attempt,
                        ]);
                    }

                    return $result;
                } catch (RuntimeException $e) {
                    $lastException = $e;
                    $isRetryable = $this->isRetryableError($e->getMessage());

                    Log::warning('Gemini attempt failed', [
                        'model' => $modelName,
                        'attempt' => $attempt,
                        'retryable' => $isRetryable,
                        'error' => $e->getMessage(),
                    ]);

                    // Non-retryable (bad API key, malformed request) → stop immediately
                    if (!$isRetryable) {
                        throw $e;
                    }

                    // Last attempt on this model → move to next model
                    if ($attempt === $this->maxRetries) {
                        break;
                    }

                    // Exponential backoff with jitter: 1s, 2s, 4s (+ random 0-500ms)
                    $delayMs = (int) ((pow(2, $attempt - 1) * 1000) + random_int(0, 500));
                    usleep($delayMs * 1000);
                }
            }
        }

        // All models and retries exhausted
        throw new RuntimeException(
            'Semua model AI sedang mengalami beban tinggi. Mohon tunggu 1-2 menit dan coba lagi. ' .
            '(Detail: ' . ($lastException?->getMessage() ?? 'unknown') . ')'
        );
    }

    /**
     * Single call to Gemini API for a specific model.
     */
    protected function callGemini(string $prompt, array $schema, ?string $modelName = null): array
    {
        $modelName = $modelName ?? $this->model;
        $url = "{$this->baseUrl}/models/{$modelName}:generateContent";

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.9,
                'topP' => 0.95,
                'maxOutputTokens' => 4096,
                'responseMimeType' => 'application/json',
                'responseSchema' => $schema,
            ],
        ];

        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'x-goog-api-key' => $this->apiKey,
            ])
            ->post($url, $payload);

        if ($response->failed()) {
            $status = $response->status();
            $errorMsg = $response->json('error.message') ?? $response->body();

            // Tag the error with the HTTP status so isRetryableError() can decide
            throw new RuntimeException("HTTP {$status}: {$errorMsg}");
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (empty($text)) {
            // Check for blocked content
            $finishReason = data_get($response->json(), 'candidates.0.finishReason');
            if ($finishReason === 'SAFETY') {
                throw new RuntimeException('Konten diblokir oleh safety filter Gemini. Coba ubah input Anda.');
            }
            throw new RuntimeException('EMPTY_RESPONSE: AI returned empty content');
        }

        $decoded = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Failed to decode Gemini JSON', [
                'model' => $modelName,
                'text' => substr($text, 0, 500),
            ]);
            throw new RuntimeException('INVALID_JSON: AI returned malformed JSON');
        }

        return $decoded;
    }

    /**
     * Determine if an error is transient and worth retrying.
     *   - 503 UNAVAILABLE (model overloaded) → RETRY
     *   - 500 INTERNAL → RETRY
     *   - 429 RESOURCE_EXHAUSTED (rate limit) → RETRY (with longer backoff)
     *   - 504 DEADLINE_EXCEEDED → RETRY
     *   - EMPTY_RESPONSE → RETRY (sometimes just a flake)
     *   - 400 INVALID_ARGUMENT → DO NOT RETRY (bad input)
     *   - 401/403 (auth) → DO NOT RETRY (fix API key first)
     */
    protected function isRetryableError(string $message): bool
    {
        $retryablePatterns = [
            'HTTP 503',
            'HTTP 500',
            'HTTP 504',
            'HTTP 429',
            'overloaded',
            'high demand',
            'UNAVAILABLE',
            'Deadline',
            'EMPTY_RESPONSE',
            'INVALID_JSON',
            'Connection timed out',
            'cURL error',
        ];

        foreach ($retryablePatterns as $pattern) {
            if (stripos($message, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * JSON schema for the full sales page (Gemini structured output).
     */
    protected function salesPageSchema(): array
    {
        return [
            'type' => 'OBJECT',
            'properties' => [
                'headline' => ['type' => 'STRING'],
                'sub_headline' => ['type' => 'STRING'],
                'product_description' => ['type' => 'STRING'],
                'benefits' => [
                    'type' => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'title' => ['type' => 'STRING'],
                            'description' => ['type' => 'STRING'],
                        ],
                        'required' => ['title', 'description'],
                    ],
                ],
                'features_breakdown' => [
                    'type' => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'title' => ['type' => 'STRING'],
                            'description' => ['type' => 'STRING'],
                        ],
                        'required' => ['title', 'description'],
                    ],
                ],
                'social_proof' => [
                    'type' => 'ARRAY',
                    'items' => [
                        'type' => 'OBJECT',
                        'properties' => [
                            'name' => ['type' => 'STRING'],
                            'role' => ['type' => 'STRING'],
                            'quote' => ['type' => 'STRING'],
                        ],
                        'required' => ['name', 'role', 'quote'],
                    ],
                ],
                'pricing_display' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'price' => ['type' => 'STRING'],
                        'currency' => ['type' => 'STRING'],
                        'note' => ['type' => 'STRING'],
                    ],
                    'required' => ['price', 'currency'],
                ],
                'cta_primary' => ['type' => 'STRING'],
                'cta_secondary' => ['type' => 'STRING'],
            ],
            'required' => [
                'headline', 'sub_headline', 'product_description',
                'benefits', 'features_breakdown', 'social_proof',
                'pricing_display', 'cta_primary', 'cta_secondary',
            ],
        ];
    }

    /**
     * Schema for a single section (used in regeneration).
     */
    protected function sectionSchema(string $section): array
    {
        $full = $this->salesPageSchema();
        $sectionSchema = $full['properties'][$section] ?? ['type' => 'STRING'];

        return [
            'type' => 'OBJECT',
            'properties' => [
                $section => $sectionSchema,
            ],
            'required' => [$section],
        ];
    }

    /**
     * Validate and normalize the structure, filling missing keys with safe defaults.
     */
    protected function validateStructure(array $content): array
    {
        return [
            'headline' => $content['headline'] ?? 'Transform Your Business Today',
            'sub_headline' => $content['sub_headline'] ?? '',
            'product_description' => $content['product_description'] ?? '',
            'benefits' => $content['benefits'] ?? [],
            'features_breakdown' => $content['features_breakdown'] ?? [],
            'social_proof' => $content['social_proof'] ?? [],
            'pricing_display' => $content['pricing_display'] ?? [
                'price' => '',
                'currency' => 'IDR',
                'note' => '',
            ],
            'cta_primary' => $content['cta_primary'] ?? 'Get Started Now',
            'cta_secondary' => $content['cta_secondary'] ?? 'Learn More',
        ];
    }
}
