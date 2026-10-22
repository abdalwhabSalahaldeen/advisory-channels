<?php

namespace App\Models;

use App\Enums\JobType;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug', 'title', 'type', 'location', 'summary', 'description',
    'requirements', 'closes_at', 'sort_order', 'is_active',
])]
class JobOpening extends Model
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
            'type' => JobType::class,
            'location' => 'array',
            'summary' => 'array',
            'description' => 'array',
            'requirements' => 'array',
            'closes_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return HasMany<JobApplication, $this>
     */
    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function isOpen(): bool
    {
        return $this->is_active && ($this->closes_at === null || $this->closes_at->endOfDay()->isFuture());
    }

    #[Scope]
    protected function open(Builder $query): void
    {
        $query->where('is_active', true)
            ->where(fn (Builder $query) => $query->whereNull('closes_at')->orWhereDate('closes_at', '>=', today()))
            ->orderBy('sort_order')
            ->orderBy('id');
    }
}
