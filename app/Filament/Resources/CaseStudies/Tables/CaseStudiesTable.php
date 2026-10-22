<?php

namespace App\Filament\Resources\CaseStudies\Tables;

use App\Filament\Support\Translations;
use App\Models\CaseStudy;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CaseStudiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('service'))
            ->columns([
                ImageColumn::make('cover')
                    ->label(__('admin.fields.cover'))
                    ->disk('public')
                    ->imageHeight(40),
                TextColumn::make('title')
                    ->label(__('admin.fields.title'))
                    ->state(fn (CaseStudy $record): ?string => $record->tr('title'))
                    ->description(fn (CaseStudy $record): ?string => $record->tr('client'))
                    ->searchable(query: Translations::search('title'))
                    ->wrap(),
                TextColumn::make('sector')
                    ->label(__('admin.fields.sector'))
                    ->state(fn (CaseStudy $record): ?string => $record->tr('sector'))
                    ->placeholder('—')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('service')
                    ->label(__('admin.fields.service'))
                    ->state(fn (CaseStudy $record): ?string => $record->service?->tr('title'))
                    ->placeholder('—')
                    ->toggleable(),
                ToggleColumn::make('is_active')
                    ->label(__('admin.fields.is_active')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
