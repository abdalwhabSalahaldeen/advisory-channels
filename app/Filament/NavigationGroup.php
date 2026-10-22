<?php

namespace App\Filament;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup implements HasLabel
{
    case Content;
    case Inbox;
    case Settings;

    public function getLabel(): string
    {
        return __('admin.navigation.'.strtolower($this->name));
    }
}
