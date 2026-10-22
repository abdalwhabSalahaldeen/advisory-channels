<?php

namespace App\Filament\Resources\JobApplications\Tables;

use App\Enums\ApplicationStatus;
use App\Filament\Resources\JobApplications\Actions\DownloadCvAction;
use App\Models\JobApplication;
use App\Models\JobOpening;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JobApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('jobOpening'))
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('admin.fields.created_at'))
                    ->since()
                    ->dateTimeTooltip()
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label(__('admin.fields.full_name'))
                    ->searchable(),
                TextColumn::make('job_opening')
                    ->label(__('admin.fields.job_opening'))
                    ->state(fn (JobApplication $record): string => $record->jobOpening?->tr('title') ?? __('admin.general_application'))
                    ->wrap(),
                TextColumn::make('email')
                    ->label(__('admin.fields.email'))
                    ->searchable()
                    ->copyable(),
                TextColumn::make('phone')
                    ->label(__('admin.fields.phone'))
                    ->toggleable(),
                TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.fields.status'))
                    ->options(ApplicationStatus::class),
                SelectFilter::make('job_opening_id')
                    ->label(__('admin.fields.job_opening'))
                    ->options(fn (): array => JobOpening::query()->orderBy('sort_order')->get()
                        ->mapWithKeys(fn (JobOpening $job): array => [$job->getKey() => $job->tr('title')])
                        ->all()),
            ])
            ->recordActions([
                ViewAction::make(),
                ActionGroup::make([
                    DownloadCvAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
