<?php

namespace App\Filament\Resources\Testimonials\Tables;

use App\Models\Testimonial;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label(__('admin.fields.avatar'))
                    ->disk('public')
                    ->circular(),
                TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->description(fn (Testimonial $record): ?string => $record->tr('position'))
                    ->searchable(),
                TextColumn::make('quote')
                    ->label(__('admin.fields.quote'))
                    ->state(fn (Testimonial $record): ?string => $record->tr('quote'))
                    ->limit(80)
                    ->wrap(),
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
