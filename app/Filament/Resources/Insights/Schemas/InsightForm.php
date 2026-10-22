<?php

namespace App\Filament\Resources\Insights\Schemas;

use App\Enums\InsightType;
use App\Filament\Support\RelationSelects;
use App\Filament\Support\Translations;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class InsightForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.content'))
                    ->schema([
                        Translations::tabs(fn (string $locale) => [
                            Translations::sluggedTitle($locale),
                            Textarea::make("excerpt.{$locale}")
                                ->label(__('admin.fields.excerpt'))
                                ->rows(3)
                                ->maxLength(400),
                            RichEditor::make("body.{$locale}")
                                ->label(__('admin.fields.body')),
                        ]),
                    ])
                    ->columnSpan(['lg' => 2]),
                Group::make([
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
                                ->options(InsightType::class)
                                ->required()
                                ->default(InsightType::Article),
                            DateTimePicker::make('published_at')
                                ->label(__('admin.fields.published_at'))
                                ->helperText(__('admin.fields.published_at_help'))
                                ->seconds(false),
                            Toggle::make('is_featured')
                                ->label(__('admin.fields.is_featured')),
                            RelationSelects::service(),
                            RelationSelects::author(),
                        ]),
                    Section::make(__('admin.sections.media'))
                        ->schema([
                            FileUpload::make('cover')
                                ->label(__('admin.fields.cover'))
                                ->image()
                                ->imageEditor()
                                ->disk('public')
                                ->directory('insights'),
                            FileUpload::make('attachment')
                                ->label(__('admin.fields.attachment'))
                                ->acceptedFileTypes(['application/pdf'])
                                ->maxSize(20 * 1024)
                                ->disk('public')
                                ->directory('insights/reports')
                                ->downloadable()
                                ->openable(),
                        ]),
                ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(['lg' => 3]);
    }
}
