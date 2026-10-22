<?php

namespace App\Filament\Resources\TeamMembers\Schemas;

use App\Filament\Support\Translations;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.content'))
                    ->schema([
                        Translations::tabs(fn (string $locale) => [
                            TextInput::make("name.{$locale}")
                                ->label(__('admin.fields.name'))
                                ->required()
                                ->maxLength(255),
                            TextInput::make("position.{$locale}")
                                ->label(__('admin.fields.position'))
                                ->maxLength(255),
                            Textarea::make("bio.{$locale}")
                                ->label(__('admin.fields.bio'))
                                ->rows(5),
                        ]),
                    ])
                    ->columnSpan(['lg' => 2]),
                Section::make(__('admin.sections.publishing'))
                    ->schema([
                        FileUpload::make('photo')
                            ->label(__('admin.fields.photo'))
                            ->image()
                            ->avatar()
                            ->imageEditor()
                            ->circleCropper()
                            ->disk('public')
                            ->directory('team'),
                        TextInput::make('linkedin_url')
                            ->label(__('admin.fields.linkedin_url'))
                            ->url()
                            ->maxLength(255)
                            ->extraInputAttributes(['dir' => 'ltr']),
                        Toggle::make('is_leadership')
                            ->label(__('admin.fields.is_leadership')),
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
