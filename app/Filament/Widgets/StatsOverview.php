<?php

namespace App\Filament\Widgets;

use App\Enums\ApplicationStatus;
use App\Enums\LeadStatus;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Lead;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $awaiting = Lead::query()->where('status', LeadStatus::New)->count();

        return [
            Stat::make(
                __('admin.widgets.new_leads_this_month'),
                Lead::query()->where('created_at', '>=', now()->startOfMonth())->count(),
            )
                ->icon(Heroicon::OutlinedInboxArrowDown)
                ->color('primary'),
            Stat::make(__('admin.widgets.total_leads'), Lead::query()->count())
                ->description(__('admin.widgets.leads_awaiting', ['count' => $awaiting]))
                ->descriptionColor($awaiting > 0 ? 'warning' : 'gray')
                ->icon(Heroicon::OutlinedUserGroup),
            Stat::make(
                __('admin.widgets.new_applications'),
                JobApplication::query()->where('status', ApplicationStatus::New)->count(),
            )
                ->icon(Heroicon::OutlinedDocumentText),
            Stat::make(__('admin.widgets.open_jobs'), JobOpening::query()->open()->count())
                ->icon(Heroicon::OutlinedAcademicCap),
        ];
    }
}
