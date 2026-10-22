<?php

namespace App\Filament\Resources\GatePackages\Tables;

use App\Filament\Support\Translations;
use App\Models\GatePackage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class GatePackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('admin.fields.title'))
                    ->state(fn (GatePackage $record): ?string => $record->tr('title'))
                    ->description(fn (GatePackage $record): ?string => $record->tr('tagline'))
                    ->searchable(query: Translations::search('title'))
                    ->wrap(),
                TextColumn::make('deliverables')
                    ->label(__('admin.fields.deliverables'))
                    ->state(fn (GatePackage $record): int => count($record->deliverables ?? []))
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_premium')
                    ->label(__('admin.fields.is_premium'))
                    ->boolean(),
                ToggleColumn::make('is_active')
                    ->label(__('admin.fields.is_active')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                //
            ])
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
