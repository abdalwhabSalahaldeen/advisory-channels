<?php

namespace App\Filament\Resources\TeamMembers\Tables;

use App\Filament\Support\Translations;
use App\Models\TeamMember;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TeamMembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label(__('admin.fields.photo'))
                    ->disk('public')
                    ->circular(),
                TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->state(fn (TeamMember $record): ?string => $record->tr('name'))
                    ->description(fn (TeamMember $record): ?string => $record->tr('position'))
                    ->searchable(query: Translations::search('name')),
                ToggleColumn::make('is_leadership')
                    ->label(__('admin.fields.is_leadership')),
                ToggleColumn::make('is_active')
                    ->label(__('admin.fields.is_active')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                TernaryFilter::make('is_leadership')
                    ->label(__('admin.fields.is_leadership')),
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
