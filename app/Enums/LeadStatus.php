<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadStatus: string implements HasColor, HasLabel
{
    case New = 'new';
    case Contacted = 'contacted';
    case Proposal = 'proposal';
    case Won = 'won';
    case Lost = 'lost';

    public function getLabel(): string
    {
        return __("enums.lead_status.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::New => 'info',
            self::Contacted => 'warning',
            self::Proposal => 'primary',
            self::Won => 'success',
            self::Lost => 'danger',
        };
    }
}
