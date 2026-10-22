<?php

namespace App\Filament\Resources\JobApplications\Actions;

use App\Models\JobApplication;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadCvAction
{
    public static function make(): Action
    {
        return Action::make('downloadCv')
            ->label(__('admin.download_cv'))
            ->icon(Heroicon::OutlinedArrowDownTray)
            ->color('gray')
            ->visible(fn (JobApplication $record): bool => filled($record->cv_path)
                && Storage::disk(JobApplication::CV_DISK)->exists($record->cv_path))
            ->action(fn (JobApplication $record): StreamedResponse => Storage::disk(JobApplication::CV_DISK)->download(
                $record->cv_path,
                Str::slug($record->full_name ?: 'applicant').'-cv.'.pathinfo($record->cv_path, PATHINFO_EXTENSION),
            ));
    }
}
