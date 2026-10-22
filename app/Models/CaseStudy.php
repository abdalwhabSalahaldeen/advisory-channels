<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A client success story. Clients are usually described, not named
 * ("a semi-government entity"), as is common in consulting.
 */
#[Fillable([
    'slug', 'title', 'client', 'sector', 'service_id', 'summary', 'challenge',
    'approach', 'outcome', 'metrics', 'cover', 'sort_order', 'is_active',
])]
class CaseStudy extends Model
{
    use HasFactory, HasTranslations;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'title' => 'array',
            'client' => 'array',
            'sector' => 'array',
            'summary' => 'array',
            'challenge' => 'array',
            'approach' => 'array',
            'outcome' => 'array',
            'metrics' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return BelongsTo<Service, $this>
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
