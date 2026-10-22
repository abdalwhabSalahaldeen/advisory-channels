<?php

namespace Tests\Feature\Admin;

use App\Enums\JobType;
use App\Enums\LeadType;
use App\Models\GatePackage;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Lead;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class AdminTestCase extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['is_admin' => true]);
    }

    protected function createService(array $attributes = []): Service
    {
        return Service::query()->create([
            'slug' => 'strategy-growth',
            'title' => ['ar' => 'قناة الاستراتيجية والنمو', 'en' => 'Strategy & Growth Channel'],
            'excerpt' => ['ar' => 'نبذة', 'en' => 'Excerpt'],
            'highlights' => [['ar' => 'دراسات السوق', 'en' => 'Market studies']],
            'sort_order' => 1,
            ...$attributes,
        ]);
    }

    protected function createGatePackage(): GatePackage
    {
        return GatePackage::query()->create([
            'slug' => 'assess',
            'title' => ['ar' => 'التقييم', 'en' => 'Assess'],
            'tagline' => ['ar' => 'الوضوح أولاً', 'en' => 'Clarity first'],
            'deliverables' => [['ar' => 'تحليل السوق', 'en' => 'Market analysis']],
            'sort_order' => 1,
        ]);
    }

    protected function createJobOpening(): JobOpening
    {
        return JobOpening::query()->create([
            'slug' => 'path-to-consultancy',
            'type' => JobType::Program,
            'title' => ['ar' => 'برنامج الطريق إلى الاستشارات', 'en' => 'Path to Consultancy Program'],
            'location' => ['ar' => 'الرياض', 'en' => 'Riyadh'],
        ]);
    }

    protected function createLead(array $attributes = []): Lead
    {
        return Lead::query()->create([
            'type' => LeadType::SaudiGate,
            'full_name' => 'Sara Ahmed',
            'company' => 'Acme',
            'email' => 'sara@example.com',
            'packages' => ['assess'],
            'locale' => 'en',
            'consented_at' => now(),
            ...$attributes,
        ]);
    }

    protected function createJobApplication(array $attributes = []): JobApplication
    {
        return JobApplication::query()->create([
            'full_name' => 'Omar Ali',
            'email' => 'omar@example.com',
            'phone' => '+966500000000',
            'cv_path' => 'cvs/omar.pdf',
            'locale' => 'ar',
            'consented_at' => now(),
            ...$attributes,
        ]);
    }
}
