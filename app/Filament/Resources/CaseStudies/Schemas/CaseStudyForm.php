<?php

namespace App\Filament\Resources\CaseStudies\Schemas;

use App\Filament\Support\RelationSelects;
use App\Filament\Support\Translations;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CaseStudyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.story'))
                    ->schema([
                        Translations::tabs(fn (string $locale) => [
                            Translations::sluggedTitle($locale),
                            TextInput::make("client.{$locale}")
                                ->label(__('admin.fields.client'))
                                ->maxLength(255),
                            TextInput::make("sector.{$locale}")
                                ->label(__('admin.fields.sector'))
                                ->maxLength(255),
                            Textarea::make("summary.{$locale}")
                                ->label(__('admin.fields.summary'))
                                ->rows(2)
                                ->maxLength(400),
                            Textarea::make("challenge.{$locale}")
                                ->label(__('admin.fields.challenge'))
                                ->rows(4),
                            Textarea::make("approach.{$locale}")
                                ->label(__('admin.fields.approach'))
                                ->rows(4),
                            Textarea::make("outcome.{$locale}")
                                ->label(__('admin.fields.outcome'))
                                ->rows(4),
                        ]),
                        static::metricsRepeater(),
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
                        RelationSelects::service(),
                        FileUpload::make('cover')
                            ->label(__('admin.fields.cover'))
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('case-studies'),
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

    /**
     * Headline numbers stored as [{"value": "35%", "label": {"ar": "...", "en": "..."}}, ...].
     */
    protected static function metricsRepeater(): Repeater
    {
        return Repeater::make('metrics')
            ->label(__('admin.fields.metrics'))
            ->schema([
                TextInput::make('value')
                    ->label(__('admin.fields.metric_value'))
                    ->required()
                    ->maxLength(20),
                ...collect(config('site.locales'))
                    ->map(fn (array $locale, string $key) => TextInput::make("label.{$key}")
                        ->label(__('admin.fields.metric_label').' — '.$locale['name'])
                        ->extraInputAttributes(['dir' => $locale['dir']])
                        ->required())
                    ->values()
                    ->all(),
            ])
            ->columns(3)
            ->maxItems(4)
            ->defaultItems(0)
            ->addActionLabel(__('admin.add_item'))
            ->reorderable()
            ->columnSpanFull();
    }
}
