<?php

namespace App\Filament\Resources\GatePackages\Schemas;

use App\Filament\Support\Translations;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GatePackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.content'))
                    ->schema([
                        Translations::tabs(fn (string $locale) => [
                            Translations::sluggedTitle($locale),
                            TextInput::make("tagline.{$locale}")
                                ->label(__('admin.fields.tagline'))
                                ->maxLength(255),
                            Textarea::make("description.{$locale}")
                                ->label(__('admin.fields.description'))
                                ->rows(4),
                        ]),
                        Translations::list('deliverables')
                            ->label(__('admin.fields.deliverables')),
                    ])
                    ->columnSpan(['lg' => 2]),
                Section::make(__('admin.sections.publishing'))
                    ->schema([
                        TextInput::make('slug')
                            ->label(__('admin.fields.slug'))
                            ->required()
                            ->alphaDash()
                            ->maxLength(255)
                            ->unique()
                            ->extraInputAttributes(['dir' => 'ltr']),
                        TextInput::make('sort_order')
                            ->label(__('admin.fields.sort_order'))
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_premium')
                            ->label(__('admin.fields.is_premium')),
                        Toggle::make('is_active')
                            ->label(__('admin.fields.is_active'))
                            ->default(true),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(['lg' => 3]);
    }
}
