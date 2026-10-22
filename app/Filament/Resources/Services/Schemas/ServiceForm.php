<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Filament\Support\Translations;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceForm
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
                                ->rows(2)
                                ->maxLength(300),
                            RichEditor::make("body.{$locale}")
                                ->label(__('admin.fields.body')),
                        ]),
                        Translations::list('highlights')
                            ->label(__('admin.fields.highlights')),
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
                        Select::make('icon')
                            ->label(__('admin.fields.icon'))
                            ->options(static::iconOptions()),
                        FileUpload::make('image')
                            ->label(__('admin.fields.image'))
                            ->image()
                            ->disk('public')
                            ->directory('services'),
                        TextInput::make('sort_order')
                            ->label(__('admin.fields.sort_order'))
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label(__('admin.fields.is_active'))
                            ->default(true),
                    ])
                    ->columnSpan(['lg' => 1]),
                Section::make(__('admin.sections.seo'))
                    ->schema([
                        Translations::tabs(fn (string $locale) => [
                            TextInput::make("meta_title.{$locale}")
                                ->label(__('admin.fields.meta_title'))
                                ->maxLength(70),
                            Textarea::make("meta_description.{$locale}")
                                ->label(__('admin.fields.meta_description'))
                                ->rows(2)
                                ->maxLength(160),
                        ]),
                    ])
                    ->collapsed()
                    ->columnSpanFull(),
            ])
            ->columns(['lg' => 3]);
    }

    /**
     * @return array<string, string>
     */
    public static function iconOptions(): array
    {
        return collect(['compass', 'network', 'refresh', 'layers', 'chart', 'shield'])
            ->mapWithKeys(fn (string $icon) => [$icon => __("admin.icons.{$icon}")])
            ->all();
    }
}
