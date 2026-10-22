<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;

class AdminAccessTest extends AdminTestCase
{
    /**
     * @return array<string, array{string}>
     */
    public static function adminPages(): array
    {
        return [
            'dashboard' => ['/management'],
            'services' => ['/management/services'],
            'services create' => ['/management/services/create'],
            'services edit' => ['/management/services/strategy-growth/edit'],
            'gate packages' => ['/management/gate-packages'],
            'gate packages edit' => ['/management/gate-packages/1/edit'],
            'job openings' => ['/management/job-openings'],
            'job openings edit' => ['/management/job-openings/path-to-consultancy/edit'],
            'leads' => ['/management/leads'],
            'lead view' => ['/management/leads/1'],
            'lead edit' => ['/management/leads/1/edit'],
            'job applications' => ['/management/job-applications'],
            'job application view' => ['/management/job-applications/1'],
            'docs' => ['/management/docs'],
            'doc edit' => ['/management/docs/1/edit'],
            'faqs' => ['/management/faqs'],
            'faqs create' => ['/management/faqs/create'],
            'faq edit' => ['/management/faqs/1/edit'],
            'faq categories' => ['/management/faq-categories'],
            'testimonials' => ['/management/testimonials'],
            'testimonial edit' => ['/management/testimonials/1/edit'],
            'partners' => ['/management/partners'],
            'partner edit' => ['/management/partners/1/edit'],
            'subscribers' => ['/management/subscribers'],
            'users' => ['/management/users'],
            'profile' => ['/management/profile'],
            'site settings' => ['/management/site-settings'],
        ];
    }

    #[DataProvider('adminPages')]
    public function test_admin_can_open_page(string $url): void
    {
        $this->seedRecords();

        $this->actingAs($this->admin)->get($url)->assertOk();
    }

    #[DataProvider('adminPages')]
    public function test_admin_can_open_page_in_english(string $url): void
    {
        $this->seedRecords();
        app()->setLocale('en');

        $this->actingAs($this->admin)->get($url)->assertOk();
    }

    public function test_non_admin_users_are_forbidden(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get('/management')->assertForbidden();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/management')->assertRedirect('/management/login');
    }

    /**
     * @return array<string, array{string}>
     */
    public static function pagesWithoutCreate(): array
    {
        return [
            'leads' => ['/management/leads/create'],
            'job applications' => ['/management/job-applications/create'],
            'docs' => ['/management/docs/create'],
        ];
    }

    #[DataProvider('pagesWithoutCreate')]
    public function test_records_that_come_from_the_site_cannot_be_created_from_the_panel(string $url): void
    {
        $this->actingAs($this->admin)->get($url)->assertNotFound();
    }

    protected function seedRecords(): void
    {
        $service = $this->createService();
        $this->createGatePackage();
        $job = $this->createJobOpening();
        $this->createLead(['service_id' => $service->id]);
        $this->createJobApplication(['job_opening_id' => $job->id]);
        $this->createDoc();
        $this->createFaq();
        $this->createTestimonial();
        $this->createPartner();
        $this->createSubscriber();
    }
}
