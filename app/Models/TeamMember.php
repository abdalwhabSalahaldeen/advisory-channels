<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'position', 'bio', 'photo', 'linkedin_url', 'is_leadership', 'sort_order', 'is_active'])]
class TeamMember extends Model
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
            'name' => 'array',
            'position' => 'array',
            'bio' => 'array',
            'is_leadership' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<Insight, $this>
     */
    public function insights(): HasMany
    {
        return $this->hasMany(Insight::class, 'author_id');
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
