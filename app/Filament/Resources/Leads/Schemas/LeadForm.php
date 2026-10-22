<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Enums\LeadStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Leads come from the public site; admins only move them through the pipeline.
 */
class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.follow_up'))
                    ->schema([
                        Select::make('status')
                            ->label(__('admin.fields.status'))
                            ->options(LeadStatus::class)
                            ->required(),
                        Textarea::make('notes')
                            ->label(__('admin.fields.notes'))
                            ->rows(5),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
