<?php

namespace App\Filament\Resources\Partners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label(__('admin.fields.name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('url')
                            ->label(__('admin.fields.url'))
                            ->url()
                            ->maxLength(255)
                            ->extraInputAttributes(['dir' => 'ltr']),
                        FileUpload::make('logo')
                            ->label(__('admin.fields.logo'))
                            ->image()
                            ->acceptedFileTypes(['image/svg+xml', 'image/png', 'image/webp', 'image/jpeg'])
                            ->disk('public')
                            ->directory('partners')
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('sort_order')
                            ->label(__('admin.fields.sort_order'))
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label(__('admin.fields.is_active'))
                            ->default(true)
                            ->inline(false),
                    ])
                    ->columns(['md' => 2])
                    ->columnSpanFull(),
            ]);
    }
}
