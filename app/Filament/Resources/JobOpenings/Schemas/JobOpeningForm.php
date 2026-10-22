<?php

namespace App\Filament\Resources\JobOpenings\Schemas;

use App\Enums\JobType;
use App\Filament\Support\Translations;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobOpeningForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.content'))
                    ->schema([
                        Translations::tabs(fn (string $locale) => [
                            Translations::sluggedTitle($locale),
                            TextInput::make("location.{$locale}")
                                ->label(__('admin.fields.location'))
                                ->maxLength(255),
                            Textarea::make("summary.{$locale}")
                                ->label(__('admin.fields.summary'))
                                ->rows(2)
                                ->maxLength(400),
                            RichEditor::make("description.{$locale}")
                                ->label(__('admin.fields.description')),
                        ]),
                        Translations::list('requirements')
                            ->label(__('admin.fields.requirements')),
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
                        Select::make('type')
                            ->label(__('admin.fields.type'))
                            ->options(JobType::class)
                            ->required()
                            ->default(JobType::FullTime),
                        DatePicker::make('closes_at')
                            ->label(__('admin.fields.closes_at')),
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
