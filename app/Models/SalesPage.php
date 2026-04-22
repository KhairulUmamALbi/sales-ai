<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_name',
        'description',
        'features',
        'target_audience',
        'price',
        'usp',
        'template',
        'generated_content',
        'generated_at',
    ];

    protected $casts = [
        'features' => 'array',
        'generated_content' => 'array',
        'generated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper: get a specific section from generated content with fallback.
     */
    public function section(string $key, mixed $default = null): mixed
    {
        return data_get($this->generated_content, $key, $default);
    }
}
