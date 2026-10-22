<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use App\Filament\Support\Translations;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.content'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('admin.fields.name'))
                            ->required()
                            ->maxLength(255),
                        Translations::tabs(fn (string $locale) => [
                            TextInput::make("position.{$locale}")
                                ->label(__('admin.fields.position'))
                                ->maxLength(255),
                            Textarea::make("quote.{$locale}")
                                ->label(__('admin.fields.quote'))
                                ->required()
                                ->rows(4),
                        ]),
                    ])
                    ->columnSpan(['lg' => 2]),
                Section::make(__('admin.sections.publishing'))
                    ->schema([
                        FileUpload::make('avatar')
                            ->label(__('admin.fields.avatar'))
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('testimonials'),
                        TextInput::make('sort_order')
                            ->label(__('admin.fields.sort_order'))
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label(__('admin.fields.is_active'))
                            ->default(true),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(['lg' => 3]);
    }
}
