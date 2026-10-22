<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadAttachment')
                ->label(__('admin.download_attachment'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->visible(fn (Lead $record): bool => filled($record->attachment_path)
                    && Storage::disk(Lead::ATTACHMENT_DISK)->exists($record->attachment_path))
                ->action(fn (Lead $record): StreamedResponse => Storage::disk(Lead::ATTACHMENT_DISK)->download($record->attachment_path)),
            EditAction::make(),
        ];
    }
}
