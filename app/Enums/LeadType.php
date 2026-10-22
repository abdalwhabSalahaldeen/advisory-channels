<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum LeadType: string implements HasColor, HasLabel
{
    case ServiceRequest = 'service_request';
    case SaudiGate = 'saudi_gate';
    case Contact = 'contact';

    public function getLabel(): string
    {
        return __("enums.lead_type.{$this->value}");
    }

    public function getColor(): string
    {
        return match ($this) {
            self::ServiceRequest => 'primary',
            self::SaudiGate => 'warning',
            self::Contact => 'gray',
        };
    }
}
