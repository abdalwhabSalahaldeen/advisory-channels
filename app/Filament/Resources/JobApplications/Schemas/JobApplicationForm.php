<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use App\Enums\ApplicationStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Applications come from the public site; admins only move them through the hiring pipeline.
 */
class JobApplicationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.follow_up'))
                    ->schema([
                        Select::make('status')
                            ->label(__('admin.fields.status'))
                            ->options(ApplicationStatus::class)
                            ->required(),
                        Textarea::make('notes')
                            ->label(__('admin.fields.notes'))
                            ->rows(5),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
