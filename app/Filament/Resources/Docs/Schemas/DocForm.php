<?php

namespace App\Filament\Resources\Docs\Schemas;

use App\Filament\Support\Translations;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DocForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Translations::tabs(fn (string $locale) => [
                            TextInput::make("title.{$locale}")
                                ->label(__('admin.fields.title'))
                                ->required()
                                ->maxLength(255),
                            RichEditor::make("body.{$locale}")
                                ->label(__('admin.fields.body')),
                        ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
