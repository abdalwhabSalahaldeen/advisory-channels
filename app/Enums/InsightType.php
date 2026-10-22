<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum InsightType: string implements HasColor, HasLabel
{
    case Article = 'article';
    case Report = 'report';
    case Guide = 'guide';
    case News = 'news';

    public function getLabel(): string
    {
        return __("enums.insight_type.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Article => 'primary',
            self::Report => 'warning',
            self::Guide => 'success',
            self::News => 'gray',
        };
    }
}
