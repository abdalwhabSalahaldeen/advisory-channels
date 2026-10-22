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
            'dashboard' => ['/admin'],
            'services' => ['/admin/services'],
            'services create' => ['/admin/services/create'],
            'services edit' => ['/admin/services/strategy-growth/edit'],
            'gate packages' => ['/admin/gate-packages'],
            'gate packages edit' => ['/admin/gate-packages/1/edit'],
            'job openings' => ['/admin/job-openings'],
            'job openings edit' => ['/admin/job-openings/path-to-consultancy/edit'],
            'leads' => ['/admin/leads'],
            'lead view' => ['/admin/leads/1'],
            'lead edit' => ['/admin/leads/1/edit'],
            'job applications' => ['/admin/job-applications'],
            'job application view' => ['/admin/job-applications/1'],
            'users' => ['/admin/users'],
            'site settings' => ['/admin/site-settings'],
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

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_leads_cannot_be_created_from_the_panel(): void
    {
        $this->actingAs($this->admin)->get('/admin/leads/create')->assertNotFound();
    }

    protected function seedRecords(): void
    {
        $service = $this->createService();
        $this->createGatePackage();
        $job = $this->createJobOpening();
        $this->createLead(['service_id' => $service->id]);
        $this->createJobApplication(['job_opening_id' => $job->id]);
    }
}
