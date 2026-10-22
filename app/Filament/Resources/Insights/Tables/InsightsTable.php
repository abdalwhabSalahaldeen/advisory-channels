<?php

namespace App\Filament\Resources\Insights\Tables;

use App\Enums\InsightType;
use App\Filament\Support\Translations;
use App\Models\Insight;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class InsightsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover')
                    ->label(__('admin.fields.cover'))
                    ->disk('public')
                    ->imageHeight(40),
                TextColumn::make('title')
                    ->label(__('admin.fields.title'))
                    ->state(fn (Insight $record): ?string => $record->tr('title'))
                    ->searchable(query: Translations::search('title'))
                    ->wrap(),
                TextColumn::make('type')
                    ->label(__('admin.fields.type'))
                    ->badge(),
                TextColumn::make('publish_status')
                    ->label(__('admin.fields.publish_status'))
                    ->state(fn (Insight $record): string => static::status($record))
                    ->formatStateUsing(fn (string $state): string => __("admin.insight_status.{$state}"))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'scheduled' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('published_at')
                    ->label(__('admin.fields.published_at'))
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable(),
                ToggleColumn::make('is_featured')
                    ->label(__('admin.fields.is_featured')),
            ])
            ->defaultSort('published_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label(__('admin.fields.type'))
                    ->options(InsightType::class),
                SelectFilter::make('publish_status')
                    ->label(__('admin.fields.publish_status'))
                    ->options(collect(['published', 'scheduled', 'draft'])
                        ->mapWithKeys(fn (string $status): array => [$status => __("admin.insight_status.{$status}")])
                        ->all())
                    ->query(fn (Builder $query, array $data): Builder => match ($data['value'] ?? null) {
                        'published' => $query->whereNotNull('published_at')->where('published_at', '<=', now()),
                        'scheduled' => $query->where('published_at', '>', now()),
                        'draft' => $query->whereNull('published_at'),
                        default => $query,
                    }),
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

    public static function status(Insight $insight): string
    {
        return match (true) {
            $insight->published_at === null => 'draft',
            $insight->published_at->isFuture() => 'scheduled',
            default => 'published',
        };
    }
}
