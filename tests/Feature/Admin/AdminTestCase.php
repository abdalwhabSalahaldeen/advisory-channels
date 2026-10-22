<?php

namespace Tests\Feature\Admin;

use App\Enums\InsightType;
use App\Enums\JobType;
use App\Enums\LeadType;
use App\Models\CaseStudy;
use App\Models\Doc;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\GatePackage;
use App\Models\Insight;
use App\Models\JobApplication;
use App\Models\JobOpening;
use App\Models\Lead;
use App\Models\Partner;
use App\Models\Service;
use App\Models\Subscriber;
use App\Models\TeamMember;
use App\Models\Testimonial;
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

        // Two-factor authentication is required for panel users; tests act as an admin who has set it up.
        $this->admin = User::factory()->create(['is_admin' => true, 'has_email_authentication' => true]);
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

    protected function createDoc(): Doc
    {
        return Doc::query()->create([
            'slug' => Doc::PRIVACY_POLICY,
            'title' => ['ar' => 'سياسة الخصوصية', 'en' => 'Privacy Policy'],
            'body' => ['ar' => '<p>نص</p>', 'en' => '<p>Text</p>'],
        ]);
    }

    protected function createFaq(): Faq
    {
        $category = FaqCategory::query()->create([
            'slug' => 'general',
            'name' => ['ar' => 'عام', 'en' => 'General'],
        ]);

        return Faq::query()->create([
            'faq_category_id' => $category->id,
            'question' => ['ar' => 'ما هي البوابة السعودية؟', 'en' => 'What is Saudi Gate?'],
            'answer' => ['ar' => 'حل متكامل لدخول السوق.', 'en' => 'A 360° market-entry solution.'],
        ]);
    }

    protected function createTestimonial(): Testimonial
    {
        return Testimonial::query()->create([
            'name' => 'Khalid',
            'position' => ['ar' => 'مدير تنفيذي', 'en' => 'CEO'],
            'quote' => ['ar' => 'تجربة رائعة', 'en' => 'Great experience'],
        ]);
    }

    protected function createPartner(): Partner
    {
        return Partner::query()->create(['name' => 'Acme', 'logo' => 'partners/acme.svg', 'url' => 'https://acme.test']);
    }

    protected function createSubscriber(array $attributes = []): Subscriber
    {
        return Subscriber::query()->create([
            'email' => 'reader@example.com',
            'locale' => 'ar',
            'consented_at' => now(),
            ...$attributes,
        ]);
    }

    protected function createTeamMember(array $attributes = []): TeamMember
    {
        return TeamMember::query()->create([
            'name' => ['ar' => 'سارة العتيبي', 'en' => 'Sara Alotaibi'],
            'position' => ['ar' => 'الشريك الإداري', 'en' => 'Managing Partner'],
            'is_leadership' => true,
            ...$attributes,
        ]);
    }

    protected function createInsight(array $attributes = []): Insight
    {
        return Insight::query()->create([
            'slug' => 'vision-2030-market-entry',
            'type' => InsightType::Report,
            'title' => ['ar' => 'دليل دخول السوق السعودي', 'en' => 'Saudi market entry guide'],
            'excerpt' => ['ar' => 'ملخص', 'en' => 'Summary'],
            'published_at' => now()->subDay(),
            ...$attributes,
        ]);
    }

    protected function createCaseStudy(array $attributes = []): CaseStudy
    {
        return CaseStudy::query()->create([
            'slug' => 'semi-government-transformation',
            'title' => ['ar' => 'تحول مؤسسي لجهة شبه حكومية', 'en' => 'Transforming a semi-government entity'],
            'client' => ['ar' => 'جهة شبه حكومية', 'en' => 'Semi-government entity'],
            'sector' => ['ar' => 'القطاع العام', 'en' => 'Public sector'],
            'metrics' => [['value' => '35%', 'label' => ['ar' => 'خفض زمن الإجراءات', 'en' => 'Faster processes']]],
            ...$attributes,
        ]);
    }
}
