# Technical Documentation
## AI Sales Page Generator

**Developer:** Khairul Umam Albi
**Submitted to:** PT Dakwah Digital Network
**Date:** April 2026
**Option Chosen:** Option B — AI Sales Page Generator

---

## 1. Approach & Design Philosophy

The goal of this application is to transform raw, unstructured product information into a complete, styled, and persuasive landing page — in under a minute. The system is built around **three core principles**:

1. **Structured AI Output** — Instead of asking the LLM for free-form text and then parsing it, I use Google Gemini's `responseSchema` feature to force a strict JSON structure. This guarantees that every generated page has the same shape (headline, benefits, testimonials, etc.) and can be rendered reliably by a fixed Blade template.
2. **Real Landing Page Aesthetic** — The preview is not a text preview with labels. It is a fully-styled landing page that a customer could publish tomorrow. I built three distinct themes (Modern, Minimalist, Bold) each with its own color system, typography weight, and button styling.
3. **Iterative Editing** — Generating a full page once is not enough. Users can regenerate any single section (just the headline, just the testimonials, etc.) without re-doing the whole page, so they can refine copy quickly.

---

## 2. Tech Stack

| Layer | Technology | Reason |
|---|---|---|
| Backend | Laravel 11 + PHP 8.3 | Fast to scaffold, strong ORM, built-in auth via Breeze |
| Database | MySQL 8 | Standard, free, easy to deploy on VPS |
| Frontend | Blade + Tailwind CSS (via Vite) | Server-rendered = fast initial load; Tailwind = design consistency |
| AI | Google Gemini 2.5 Flash | Free tier (15 RPM / 1500 RPD), native JSON schema support, fast response |
| Auth | Laravel Breeze (Blade stack) | Industry standard, registration/login/password reset out of the box |
| Deployment | VPS (Ubuntu 22.04) + Caddy reverse proxy + PHP-FPM | Same stack used in a previous production project; auto-HTTPS via Caddy |

---

## 3. System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        User (Browser)                        │
└────────────────────────┬────────────────────────────────────┘
                         │ HTTPS (Caddy)
┌────────────────────────▼────────────────────────────────────┐
│              Laravel Application (PHP-FPM)                   │
│                                                               │
│  ┌──────────────────┐     ┌─────────────────────────────┐   │
│  │ SalesPageController │──→ │  GeminiService              │   │
│  │ (CRUD + auth)    │     │  - buildPrompt()            │   │
│  │                  │     │  - callGemini()             │   │
│  │                  │     │  - validateStructure()      │   │
│  └────────┬─────────┘     └─────────────┬───────────────┘   │
│           │                             │ HTTPS              │
│  ┌────────▼─────────┐                   │                    │
│  │  MySQL           │         ┌─────────▼─────────────┐     │
│  │  sales_pages     │         │ Google Gemini API      │     │
│  │  users           │         │ (generativelanguage...)│     │
│  └──────────────────┘         └────────────────────────┘     │
└──────────────────────────────────────────────────────────────┘
```

### Request Flow — Creating a Sales Page

1. User fills the form at `/pages/create` (product name, description, features, audience, price, USP, template choice).
2. `POST /pages` → `SalesPageController@store` validates the input.
3. `GeminiService::generateSalesPage($input)` builds a structured prompt and calls the Gemini API with a `responseSchema` that enforces the exact JSON shape.
4. The response is validated, normalized (missing keys filled with safe defaults), and saved to `sales_pages.generated_content` as JSON.
5. User is redirected to `/pages/{id}` where the Blade partial `sales-page-render.blade.php` paints the full landing page using the chosen theme.

---

## 4. Database Schema

```sql
sales_pages
├── id              BIGINT PK
├── user_id         FK → users.id (cascade delete)
├── product_name    VARCHAR(255)
├── description     TEXT
├── features        JSON         -- array of feature strings
├── target_audience VARCHAR(500)
├── price           VARCHAR(100)
├── usp             TEXT
├── template        ENUM('modern','minimalist','bold')
├── generated_content JSON       -- full structured AI output
├── generated_at    TIMESTAMP
├── created_at, updated_at
└── INDEX (user_id, created_at)
```

The `generated_content` JSON column stores the structured page:

```json
{
  "headline": "...",
  "sub_headline": "...",
  "product_description": "...",
  "benefits": [{ "title": "...", "description": "..." }],
  "features_breakdown": [{ "title": "...", "description": "..." }],
  "social_proof": [{ "name": "...", "role": "...", "quote": "..." }],
  "pricing_display": { "price": "...", "currency": "...", "note": "..." },
  "cta_primary": "...",
  "cta_secondary": "..."
}
```

Storing it as JSON (rather than normalized tables) is deliberate: a sales page is a cohesive document, not a relational dataset. It keeps reads to one query and makes section-by-section regeneration a simple JSON key overwrite.

---

## 5. Prompt Engineering

The prompt is the core of this application. A few deliberate choices:

1. **Role priming** — "You are an expert direct-response copywriter" gives the model a clear voice and reduces generic marketing-speak.
2. **Benefit vs. feature distinction** — I explicitly instruct the model that benefits must be outcome-focused and features must be capability-focused. Without this, LLMs tend to blur the two.
3. **Testimonial realism** — I ask for "believable Indonesian or general names" and quotes that "reference specific outcomes." This produces testimonials that feel like real quotes rather than generic "Great product!" lines.
4. **Language matching** — The prompt tells the model to respond in the same language as the input. Indonesian product description → Indonesian sales page, automatically.
5. **Structured output via `responseSchema`** — Instead of parsing free-form text with regex (brittle), I define a JSON schema directly in the API call. Gemini returns valid JSON, every time.

---

## 6. Security Considerations

- **CSRF tokens** on all state-changing forms (Laravel default).
- **Ownership check** in the controller (`authorize()` method) — users can only view/edit/delete their own pages. Returns 403 otherwise.
- **Input validation** via Laravel's `$request->validate()` — all fields have length and type constraints.
- **API key** stored in `.env`, never committed to git, never exposed to the client.
- **Mass assignment protection** via `$fillable` on the model.
- **SQL injection prevention** via Eloquent's parameterized queries.
- **Password hashing** via Laravel Breeze (bcrypt by default).

---

## 7. Features Implemented

### Core (required by task)
- ✅ User authentication (register, login, logout) via Laravel Breeze
- ✅ Structured product input form
- ✅ AI-powered sales page generation via Google Gemini
- ✅ Full structured output: headline, sub-headline, description, benefits, features, social proof, pricing, CTA
- ✅ Saved pages (view, edit/regenerate, delete) with search
- ✅ Live preview as a real landing page layout

### Bonus Features
- ✅ **Export as standalone HTML** — single self-contained HTML file with Tailwind inlined via CDN
- ✅ **Multiple design templates** — Modern (gradient), Minimalist (clean B&W), Bold (vibrant)
- ✅ **Section-by-section regeneration** — regenerate only the headline, only the testimonials, etc., keeping the rest of the page intact

---

## 8. Error Handling

- **AI failure** — If Gemini returns an error or invalid JSON, the user sees a friendly flash message ("Gagal menghasilkan sales page: ...") and the form is re-populated with their input. The error is logged server-side for debugging.
- **Empty AI response** — Defensive defaults in `validateStructure()` ensure the page still renders even if the model omits a field.
- **Loading state** — The form button shows a spinner during generation (AI calls take 10–30 seconds), preventing double submissions.
- **404 / 403** — Unknown or unauthorized pages return appropriate HTTP codes.

---

## 9. Deployment Steps

See `README.md` for the full deployment guide. Summary:

1. Ubuntu 22.04 VPS with PHP 8.3, MySQL 8, Composer, Node.js 20
2. Clone project → `composer install --no-dev` → `npm run build`
3. Configure `.env` (DB credentials + `GEMINI_API_KEY`)
4. `php artisan migrate --force` and cache artisan commands
5. Add a Caddy site block with `php_fastcgi` pointing to PHP-FPM socket
6. `sudo systemctl reload caddy` → Caddy auto-issues a Let's Encrypt certificate

---

## 10. What I'd Build Next (with more time)

- **A/B testing** — generate 2–3 variations of a page and let users pick the best
- **Copy history / versioning** — keep every generation, not just the latest
- **Image generation** — use an image model (e.g., Imagen) to generate hero images
- **Analytics** — track which generated pages users export most often, feed that signal back into the prompt
- **Queue jobs** — move Gemini calls to a Laravel queue so the user isn't blocked for 30 seconds on the request-response cycle; notify when done

---

**Thank you for the opportunity to work on this task.**

Khairul Umam Albi
