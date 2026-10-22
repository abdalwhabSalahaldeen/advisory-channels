<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A long-form legal page (privacy policy, terms & conditions) looked up by slug.
 */
#[Fillable(['slug', 'title', 'body'])]
class Doc extends Model
{
    use HasFactory, HasTranslations;

    public const PRIVACY_POLICY = 'privacy-policy';

    public const TERMS = 'terms-and-conditions';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'title' => 'array',
            'body' => 'array',
        ];
    }
}
