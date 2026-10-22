<?php

namespace App\Filament\Resources\Faqs\Tables;

use App\Filament\Resources\Faqs\Schemas\FaqForm;
use App\Filament\Support\Translations;
use App\Models\Faq;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class FaqsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('category'))
            ->columns([
                TextColumn::make('question')
                    ->label(__('admin.fields.question'))
                    ->state(fn (Faq $record): ?string => $record->tr('question'))
                    ->searchable(query: Translations::search('question'))
                    ->wrap(),
                TextColumn::make('category')
                    ->label(__('admin.fields.category'))
                    ->state(fn (Faq $record): ?string => $record->category?->tr('name'))
                    ->placeholder(__('admin.no_category'))
                    ->badge()
                    ->color('gray'),
                ToggleColumn::make('is_active')
                    ->label(__('admin.fields.is_active')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('faq_category_id')
                    ->label(__('admin.fields.category'))
                    ->options(fn (): array => FaqForm::categoryOptions()),
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
