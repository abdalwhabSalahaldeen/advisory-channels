<?php

namespace App\Filament\Resources\Subscribers\Pages;

use App\Filament\Resources\Subscribers\SubscriberResource;
use App\Models\Subscriber;
use Filament\Actions\Action;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ManageSubscribers extends ManageRecords
{
    protected static string $resource = SubscriberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportCsv')
                ->label(__('admin.export_csv'))
                ->icon(Heroicon::OutlinedArrowDownTray)
                ->color('gray')
                ->action(fn (): StreamedResponse => $this->exportCsv()),
        ];
    }

    protected function exportCsv(): StreamedResponse
    {
        return response()->streamDownload(function (): void {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel shows Arabic correctly.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['email', 'locale', 'consented_at', 'created_at']);

            Subscriber::query()->orderBy('id')->lazy()->each(function (Subscriber $subscriber) use ($handle): void {
                fputcsv($handle, [
                    $subscriber->email,
                    $subscriber->locale,
                    $subscriber->consented_at?->toIso8601String(),
                    $subscriber->created_at?->toIso8601String(),
                ]);
            });

            fclose($handle);
        }, 'subscribers-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
