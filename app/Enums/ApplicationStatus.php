<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ApplicationStatus: string implements HasColor, HasLabel
{
    case New = 'new';
    case Reviewing = 'reviewing';
    case Shortlisted = 'shortlisted';
    case Rejected = 'rejected';
    case Hired = 'hired';

    public function getLabel(): string
    {
        return __("enums.application_status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'info',
            self::Reviewing => 'warning',
            self::Shortlisted => 'primary',
            self::Rejected => 'danger',
            self::Hired => 'success',
        };
    }
}
