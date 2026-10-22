<?php

namespace App\Filament\Resources\JobOpenings\Tables;

use App\Enums\JobType;
use App\Filament\Support\Translations;
use App\Models\JobOpening;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JobOpeningsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->withCount('applications'))
            ->columns([
                TextColumn::make('title')
                    ->label(__('admin.fields.title'))
                    ->state(fn (JobOpening $record): ?string => $record->tr('title'))
                    ->description(fn (JobOpening $record): ?string => $record->tr('location'))
                    ->searchable(query: Translations::search('title'))
                    ->wrap(),
                TextColumn::make('type')
                    ->label(__('admin.fields.type'))
                    ->badge(),
                TextColumn::make('applications_count')
                    ->label(__('admin.fields.applications_count'))
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                TextColumn::make('closes_at')
                    ->label(__('admin.fields.closes_at'))
                    ->date()
                    ->placeholder('—')
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label(__('admin.fields.is_active')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('type')
                    ->label(__('admin.fields.type'))
                    ->options(JobType::class),
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
