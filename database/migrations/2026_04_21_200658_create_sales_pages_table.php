<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // User input (stored for re-generation)
            $table->string('product_name');
            $table->text('description');
            $table->json('features');              // array of feature strings
            $table->string('target_audience');
            $table->string('price')->nullable();
            $table->text('usp')->nullable();       // unique selling points
            $table->string('template')->default('modern'); // modern | minimalist | bold

            // AI-generated content (structured JSON)
            $table->json('generated_content')->nullable();
            // Shape:
            // {
            //   "headline": "...",
            //   "sub_headline": "...",
            //   "product_description": "...",
            //   "benefits": [{ "title": "...", "description": "..." }],
            //   "features_breakdown": [{ "title": "...", "description": "..." }],
            //   "social_proof": [{ "name": "...", "role": "...", "quote": "..." }],
            //   "pricing_display": { "price": "...", "currency": "...", "note": "..." },
            //   "cta_primary": "...",
            //   "cta_secondary": "..."
            // }

            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_pages');
    }
};
