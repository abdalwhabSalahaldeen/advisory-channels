<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestLeads extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('admin.widgets.latest_leads'))
            ->query(fn (): Builder => Lead::query()->with('service')->latest()->limit(5))
            ->paginated(false)
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('admin.fields.created_at'))
                    ->since()
                    ->dateTimeTooltip(),
                TextColumn::make('type')
                    ->label(__('admin.fields.type'))
                    ->badge(),
                TextColumn::make('full_name')
                    ->label(__('admin.fields.full_name'))
                    ->description(fn (Lead $record): ?string => $record->company),
                TextColumn::make('email')
                    ->label(__('admin.fields.email')),
                TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Lead $record): string => LeadResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
