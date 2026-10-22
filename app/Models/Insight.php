<?php

namespace App\Models;

use App\Enums\InsightType;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Thought leadership: articles, reports, guides and news.
 */
#[Fillable([
    'slug', 'type', 'title', 'excerpt', 'body', 'cover', 'attachment',
    'service_id', 'author_id', 'is_featured', 'published_at',
])]
class Insight extends Model
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
            'type' => InsightType::class,
            'title' => 'array',
            'excerpt' => 'array',
            'body' => 'array',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
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

    /**
     * @return BelongsTo<TeamMember, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class, 'author_id');
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->isPast();
    }

    /**
     * Estimated reading time of the body in the given (or current) locale.
     */
    public function readingMinutes(?string $locale = null): int
    {
        $words = str_word_count(strip_tags((string) $this->tr('body', $locale)), format: 0, characters: 'ءآأؤإئابةتثجحخدذرزسشصضطظعغفقكلمنهوىيًٌٍَُِّْ');

        return max(1, (int) ceil($words / 200));
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->whereNotNull('published_at')->where('published_at', '<=', now())->latest('published_at');
    }
}
