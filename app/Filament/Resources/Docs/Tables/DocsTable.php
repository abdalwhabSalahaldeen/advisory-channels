<?php

namespace App\Filament\Resources\Docs\Tables;

use App\Models\Doc;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DocsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('admin.fields.title'))
                    ->state(fn (Doc $record): ?string => $record->tr('title')),
                TextColumn::make('slug')
                    ->label(__('admin.fields.slug'))
                    ->color('gray'),
                TextColumn::make('updated_at')
                    ->label(__('admin.fields.updated_at'))
                    ->since()
                    ->dateTimeTooltip(),
            ])
            ->paginated(false)
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
