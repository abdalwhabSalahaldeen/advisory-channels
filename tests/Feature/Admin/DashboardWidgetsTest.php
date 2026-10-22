<?php

namespace Tests\Feature\Admin;

use App\Enums\LeadStatus;
use App\Filament\Widgets\LatestLeads;
use App\Filament\Widgets\StatsOverview;
use Livewire\Livewire;

class DashboardWidgetsTest extends AdminTestCase
{
    public function test_stats_overview_counts_leads_and_openings(): void
    {
        $this->createLead();
        $this->createLead(['status' => LeadStatus::Won])->forceFill(['created_at' => now()->subMonths(2)])->save();
        $this->createLead(['status' => LeadStatus::Lost])->forceFill(['created_at' => now()->subMonths(3)])->save();
        $this->createJobOpening();
        $this->createJobApplication();

        app()->setLocale('en');
        $this->actingAs($this->admin);

        $stats = collect((fn () => $this->getStats())->call(new StatsOverview))
            ->mapWithKeys(fn ($stat) => [$stat->getLabel() => $stat->getValue()]);

        $this->assertEquals([
            'Leads this month' => 1,
            'Total leads' => 3,
            'New job applications' => 1,
            'Open job openings' => 1,
        ], $stats->all());

        Livewire::test(StatsOverview::class)
            ->assertSee('1 awaiting contact');
    }

    public function test_latest_leads_lists_the_five_newest(): void
    {
        $leads = collect(range(1, 6))->map(function (int $i) {
            $lead = $this->createLead(['full_name' => "Lead {$i}"]);
            $lead->forceFill(['created_at' => now()->subMinutes(10 - $i)])->save();

            return $lead;
        });

        $this->actingAs($this->admin);

        Livewire::test(LatestLeads::class)
            ->assertCanSeeTableRecords($leads->slice(1))
            ->assertCanNotSeeTableRecords([$leads->first()]);
    }
}
