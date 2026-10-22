<?php

namespace App\Filament\Resources\JobApplications\Schemas;

use App\Models\JobApplication;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class JobApplicationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('admin.sections.applicant'))
                    ->schema([
                        TextEntry::make('full_name')
                            ->label(__('admin.fields.full_name')),
                        TextEntry::make('job_opening')
                            ->label(__('admin.fields.job_opening'))
                            ->state(fn (JobApplication $record): string => $record->jobOpening?->tr('title') ?? __('admin.general_application')),
                        TextEntry::make('email')
                            ->label(__('admin.fields.email'))
                            ->url(fn (JobApplication $record): string => "mailto:{$record->email}")
                            ->copyable(),
                        TextEntry::make('phone')
                            ->label(__('admin.fields.phone'))
                            ->url(fn (JobApplication $record): string => "tel:{$record->phone}")
                            ->copyable(),
                        TextEntry::make('linkedin_url')
                            ->label(__('admin.fields.linkedin_url'))
                            ->url(fn (JobApplication $record): ?string => $record->linkedin_url, shouldOpenInNewTab: true)
                            ->placeholder('—'),
                        TextEntry::make('cover_letter')
                            ->label(__('admin.fields.cover_letter'))
                            ->placeholder('—')
                            ->columnSpanFull(),
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
                        TextEntry::make('created_at')
                            ->label(__('admin.fields.created_at'))
                            ->dateTime(),
                        TextEntry::make('locale')
                            ->label(__('admin.fields.locale'))
                            ->formatStateUsing(fn (string $state): string => config("site.locales.{$state}.name", $state)),
                        TextEntry::make('consented_at')
                            ->label(__('admin.fields.consented_at'))
                            ->dateTime(),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(['lg' => 3]);
    }
}
