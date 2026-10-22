<?php

namespace App\Filament\Resources\Faqs\Schemas;

use App\Filament\Support\Translations;
use App\Models\FaqCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class FaqForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.content'))
                    ->schema([
                        Translations::tabs(fn (string $locale) => [
                            TextInput::make("question.{$locale}")
                                ->label(__('admin.fields.question'))
                                ->required()
                                ->maxLength(255),
                            Textarea::make("answer.{$locale}")
                                ->label(__('admin.fields.answer'))
                                ->required()
                                ->rows(5),
                        ]),
                    ])
                    ->columnSpan(['lg' => 2]),
                Section::make(__('admin.sections.publishing'))
                    ->schema([
                        Select::make('faq_category_id')
                            ->label(__('admin.fields.category'))
                            ->options(fn (): array => static::categoryOptions())
                            ->placeholder(__('admin.no_category')),
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
     * @return array<int, string>
     */
    public static function categoryOptions(): array
    {
        return FaqCategory::query()
            ->orderBy('sort_order')
            ->get()
            ->mapWithKeys(fn (FaqCategory $category): array => [$category->getKey() => $category->tr('name')])
            ->all();
    }
}
