<?php

namespace Tests\Feature\Site;

use App\Enums\LeadType;
use App\Livewire\ContactForm;
use App\Livewire\JobApplicationForm;
use App\Livewire\RequestServiceForm;
use App\Livewire\SaudiGateForm;
use App\Livewire\SubscribeForm;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Lead;
use App\Models\Subscriber;
use App\Notifications\NewJobApplicationReceived;
use App\Notifications\NewLeadReceived;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FormsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed inside the test transaction: the in-memory database is migrated once and shared by every test.
        $this->seed(DatabaseSeeder::class);
        Notification::fake();
        RateLimiter::clear(RequestServiceForm::class.':127.0.0.1');
    }

    #[Test]
    public function a_service_request_goes_through_three_steps_and_alerts_the_inbox(): void
    {
        Livewire::test(RequestServiceForm::class, ['preselect' => 'grow'])
            ->assertSet('service', '')
            ->call('next')
            ->assertHasErrors(['service' => 'required'])
            ->set('service', 'transformation-management')
            ->call('next')
            ->assertSet('step', 2)
            ->call('next')
            ->assertHasErrors(['company', 'message'])
            ->set('company', 'وزارة تجريبية')
            ->set('message', 'نحتاج مكتب إدارة تحول')
            ->set('timeline', '1-3_months')
            ->call('next')
            ->assertSet('step', 3)
            ->set('full_name', 'سارة أحمد')
            ->set('email', 'sara@example.com')
            ->set('phone', '+966 50 000 0000')
            ->call('submit')
            ->assertHasErrors(['consent' => 'accepted'])
            ->set('consent', true)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $lead = Lead::query()->sole();
        $this->assertSame(LeadType::ServiceRequest, $lead->type);
        $this->assertSame('transformation-management', $lead->service->slug);
        $this->assertSame('1-3_months', $lead->timeline);
        $this->assertNull($lead->job_title);
        $this->assertNotNull($lead->consented_at);

        Notification::assertSentOnDemand(
            NewLeadReceived::class,
            fn ($notification, $channels, AnonymousNotifiable $notifiable) => $notifiable->routes['mail'] === 'info@advisory-channels.com'
                && $notification->lead->is($lead),
        );
    }

    #[Test]
    public function a_service_can_be_preselected_from_the_service_page(): void
    {
        Livewire::test(RequestServiceForm::class, ['preselect' => 'strategy-growth'])
            ->assertSet('service', 'strategy-growth');
    }

    #[Test]
    public function not_sure_requests_are_saved_without_a_service(): void
    {
        $this->fillRequest(Livewire::test(RequestServiceForm::class)->set('service', RequestServiceForm::NOT_SURE))
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertNull(Lead::query()->sole()->service_id);
    }

    #[Test]
    public function bots_filling_the_honeypot_see_success_but_nothing_is_saved(): void
    {
        $this->fillRequest(Livewire::test(RequestServiceForm::class)->set('service', 'grow-not-a-service'))
            ->set('service', 'strategy-growth')
            ->set('website', 'http://spam.example')
            ->call('submit')
            ->assertSet('submitted', true);

        $this->assertSame(0, Lead::query()->count());
        Notification::assertNothingSent();
    }

    #[Test]
    public function submissions_are_rate_limited_per_visitor(): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->fillRequest(Livewire::test(RequestServiceForm::class)->set('service', 'strategy-growth'))->call('submit');
        }

        $this->fillRequest(Livewire::test(RequestServiceForm::class)->set('service', 'strategy-growth'))
            ->call('submit')
            ->assertHasErrors('form');

        $this->assertSame(5, Lead::query()->count());
    }

    #[Test]
    public function the_page_language_is_kept_across_livewire_requests(): void
    {
        app()->setLocale('en');
        $component = Livewire::test(ContactForm::class);
        app()->setLocale('ar');

        $component->call('submit')->assertSee('The full name field is required.');
    }

    #[Test]
    public function a_saudi_gate_enquiry_stores_the_chosen_stages(): void
    {
        Livewire::test(SaudiGateForm::class)
            ->call('submit')
            ->assertHasErrors(['packages' => 'required'])
            ->set('packages', ['assess', 'start-plus', 'not-a-stage'])
            ->set('full_name', 'John Smith')
            ->set('company', 'Acme Ltd')
            ->set('email', 'john@example.com')
            ->set('phone', '+44 20 7946 0000')
            ->set('consent', true)
            ->call('submit')
            ->assertHasErrors('packages.2')
            ->set('packages', ['assess', 'start-plus'])
            ->call('submit')
            ->assertHasNoErrors();

        $lead = Lead::query()->sole();
        $this->assertSame(LeadType::SaudiGate, $lead->type);
        $this->assertSame(['assess', 'start-plus'], $lead->selectedPackages()->pluck('slug')->all());
        Notification::assertSentOnDemandTimes(NewLeadReceived::class, 1);
    }

    #[Test]
    public function a_contact_message_is_saved(): void
    {
        Livewire::test(ContactForm::class)
            ->set('full_name', 'Nora')
            ->set('email', 'nora@example.com')
            ->set('message', 'Hello')
            ->set('consent', true)
            ->call('submit')
            ->assertHasNoErrors();

        $this->assertSame(LeadType::Contact, Lead::query()->sole()->type);
    }

    #[Test]
    public function a_job_application_stores_the_cv_privately(): void
    {
        Storage::fake(JobApplication::CV_DISK);
        $opening = JobOpening::query()->where('slug', 'path-to-consultancy')->sole();

        Livewire::test(JobApplicationForm::class, ['jobOpening' => $opening])
            ->set('full_name', 'خالد')
            ->set('email', 'khaled@example.com')
            ->set('phone', '0500000000')
            ->set('cv', UploadedFile::fake()->create('cv.exe', 100))
            ->assertHasErrors(['cv' => 'mimes'])
            ->set('cv', UploadedFile::fake()->create('cv.pdf', 300, 'application/pdf'))
            ->set('consent', true)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitted', true);

        $application = JobApplication::query()->sole();
        $this->assertTrue($application->jobOpening->is($opening));
        Storage::disk(JobApplication::CV_DISK)->assertExists($application->cv_path);
        Notification::assertSentOnDemand(NewJobApplicationReceived::class);

        $application->delete();
        Storage::disk(JobApplication::CV_DISK)->assertMissing($application->cv_path);
    }

    #[Test]
    public function closed_openings_reject_applications(): void
    {
        Storage::fake(JobApplication::CV_DISK);
        $opening = JobOpening::query()->where('slug', 'path-to-consultancy')->sole();
        $opening->update(['closes_at' => now()->subDay()]);

        $this->get('/ar/careers/path-to-consultancy')->assertSee(__('careers.closed', [], 'ar'));

        Livewire::test(JobApplicationForm::class, ['jobOpening' => $opening])
            ->set('full_name', 'خالد')
            ->set('email', 'khaled@example.com')
            ->set('phone', '0500000000')
            ->set('cv', UploadedFile::fake()->create('cv.pdf', 300, 'application/pdf'))
            ->set('consent', true)
            ->call('submit')
            ->assertForbidden();

        $this->assertSame(0, JobApplication::query()->count());
    }

    #[Test]
    public function subscribing_twice_keeps_one_subscriber(): void
    {
        Livewire::test(SubscribeForm::class)->set('email', 'Reader@Example.com')->call('submit')->assertSet('submitted', true);
        Livewire::test(SubscribeForm::class)->set('email', 'reader@example.com')->call('submit')->assertHasNoErrors();

        $this->assertSame('reader@example.com', Subscriber::query()->sole()->email);
    }

    private function fillRequest($component)
    {
        return $component
            ->set('company', 'Acme')
            ->set('message', 'Need help')
            ->set('full_name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('phone', '0500000000')
            ->set('consent', true);
    }
}
