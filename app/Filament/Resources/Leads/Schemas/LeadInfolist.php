<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\GatePackage;
use App\Models\Lead;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.contact'))
                    ->schema([
                        TextEntry::make('full_name')
                            ->label(__('admin.fields.full_name')),
                        TextEntry::make('company')
                            ->label(__('admin.fields.company'))
                            ->placeholder('—'),
                        TextEntry::make('job_title')
                            ->label(__('admin.fields.job_title'))
                            ->placeholder('—'),
                        TextEntry::make('email')
                            ->label(__('admin.fields.email'))
                            ->url(fn (Lead $record): string => "mailto:{$record->email}")
                            ->copyable(),
                        TextEntry::make('phone')
                            ->label(__('admin.fields.phone'))
                            ->url(fn (Lead $record): ?string => $record->phone ? "tel:{$record->phone}" : null)
                            ->placeholder('—')
                            ->copyable(),
                        TextEntry::make('country')
                            ->label(__('admin.fields.country'))
                            ->placeholder('—'),
                    ])
                    ->columns(['md' => 2])
                    ->columnSpan(['lg' => 2]),
                Section::make(__('admin.sections.follow_up'))
                    ->schema([
                        TextEntry::make('status')
                            ->label(__('admin.fields.status'))
                            ->badge(),
                        TextEntry::make('notes')
                            ->label(__('admin.fields.notes'))
                            ->placeholder('—'),
                    ])
                    ->columnSpan(['lg' => 1]),
                Section::make(__('admin.sections.request'))
                    ->schema([
                        TextEntry::make('type')
                            ->label(__('admin.fields.type'))
                            ->badge(),
                        TextEntry::make('service')
                            ->label(__('admin.fields.service'))
                            ->state(fn (Lead $record): ?string => $record->service?->tr('title'))
                            ->placeholder('—'),
                        TextEntry::make('packages')
                            ->label(__('admin.fields.packages'))
                            ->state(fn (Lead $record): array => $record->selectedPackages()
                                ->map(fn (GatePackage $package): ?string => $package->tr('title'))
                                ->all())
                            ->badge()
                            ->placeholder('—'),
                        TextEntry::make('timeline')
                            ->label(__('admin.fields.timeline'))
                            ->placeholder('—'),
                        TextEntry::make('message')
                            ->label(__('admin.fields.message'))
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(['md' => 2])
                    ->columnSpan(['lg' => 2]),
                Section::make(__('admin.sections.meta'))
                    ->schema([
                        TextEntry::make('created_at')
                            ->label(__('admin.fields.created_at'))
                            ->dateTime(),
                        TextEntry::make('locale')
                            ->label(__('admin.fields.locale'))
                            ->formatStateUsing(fn (string $state): string => config("site.locales.{$state}.name", $state)),
                        TextEntry::make('source_url')
                            ->label(__('admin.fields.source_url'))
                            ->url(fn (Lead $record): ?string => $record->source_url, shouldOpenInNewTab: true)
                            ->placeholder('—'),
                        TextEntry::make('consented_at')
                            ->label(__('admin.fields.consented_at'))
                            ->dateTime(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(['lg' => 3]);
    }
}
