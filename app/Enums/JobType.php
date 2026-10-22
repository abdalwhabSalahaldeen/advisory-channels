<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum JobType: string implements HasLabel
{
    case Program = 'program';
    case FullTime = 'full_time';
    case PartTime = 'part_time';
    case Internship = 'internship';

    public function getLabel(): string
    {
        return __("enums.job_type.{$this->value}");
    }
}
