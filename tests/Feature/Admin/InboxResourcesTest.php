<?php

namespace Tests\Feature\Admin;

use App\Enums\ApplicationStatus;
use App\Enums\LeadStatus;
use App\Filament\Resources\JobApplications\Pages\ViewJobApplication;
use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Resources\Leads\Pages\EditLead;
use App\Filament\Resources\Leads\Pages\ListLeads;
use App\Models\JobApplication;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

class InboxResourcesTest extends AdminTestCase
{
    public function test_lead_view_shows_selected_packages_and_service(): void
    {
        $this->createGatePackage();
        $service = $this->createService();
        $lead = $this->createLead(['service_id' => $service->id, 'message' => 'We plan to open a Riyadh office.']);

        app()->setLocale('en');

        $this->actingAs($this->admin)
            ->get(LeadResource::getUrl('view', ['record' => $lead]))
            ->assertOk()
            ->assertSee('Sara Ahmed')
            ->assertSee('Assess')
            ->assertSee('Strategy &amp; Growth Channel', escape: false)
            ->assertSee('We plan to open a Riyadh office.');
    }

    public function test_lead_status_and_notes_can_be_updated(): void
    {
        $lead = $this->createLead();

        $this->actingAs($this->admin);

        Livewire::test(EditLead::class, ['record' => $lead->getKey()])
            ->fillForm(['status' => LeadStatus::Contacted, 'notes' => 'Call scheduled for Sunday.'])
            ->call('save')
            ->assertHasNoFormErrors();

        $lead->refresh();

        $this->assertSame(LeadStatus::Contacted, $lead->status);
        $this->assertSame('Call scheduled for Sunday.', $lead->notes);
        $this->assertSame('Sara Ahmed', $lead->full_name);
    }

    public function test_leads_table_filters_by_status(): void
    {
        $new = $this->createLead(['full_name' => 'New Lead']);
        $won = $this->createLead(['full_name' => 'Won Lead', 'status' => LeadStatus::Won]);

        $this->actingAs($this->admin);

        Livewire::test(ListLeads::class)
            ->assertCanSeeTableRecords([$new, $won])
            ->filterTable('status', LeadStatus::Won->value)
            ->assertCanSeeTableRecords([$won])
            ->assertCanNotSeeTableRecords([$new]);
    }

    public function test_navigation_badge_counts_new_leads(): void
    {
        $this->createLead();
        $this->createLead(['status' => LeadStatus::Lost]);

        $this->assertSame('1', LeadResource::getNavigationBadge());
    }

    public function test_cv_can_be_downloaded_from_the_application_page(): void
    {
        Storage::fake(JobApplication::CV_DISK);
        $path = UploadedFile::fake()->create('cv.pdf', 120, 'application/pdf')
            ->store('cvs', JobApplication::CV_DISK);

        $application = $this->createJobApplication(['cv_path' => $path, 'status' => ApplicationStatus::New]);

        $this->actingAs($this->admin);

        Livewire::test(ViewJobApplication::class, ['record' => $application->getKey()])
            ->assertActionVisible('downloadCv')
            ->callAction('downloadCv')
            ->assertFileDownloaded('omar-ali-cv.pdf');
    }

    public function test_download_is_hidden_when_the_cv_file_is_missing(): void
    {
        Storage::fake(JobApplication::CV_DISK);
        $application = $this->createJobApplication(['cv_path' => 'cvs/missing.pdf']);

        $this->actingAs($this->admin);

        Livewire::test(ViewJobApplication::class, ['record' => $application->getKey()])
            ->assertActionHidden('downloadCv');
    }
}
