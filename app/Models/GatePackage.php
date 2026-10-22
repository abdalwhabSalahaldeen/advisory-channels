<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A stage of the "Saudi Gate" market-entry journey (Assess, Start, Operate...).
 */
#[Fillable([
    'slug', 'title', 'tagline', 'description', 'deliverables',
    'is_premium', 'sort_order', 'is_active',
])]
class GatePackage extends Model
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
            'tagline' => 'array',
            'description' => 'array',
            'deliverables' => 'array',
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }
}
