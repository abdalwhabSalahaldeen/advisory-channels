<?php

namespace Tests\Feature\Admin;

use App\Enums\InsightType;
use App\Filament\Resources\CaseStudies\Pages\CreateCaseStudy;
use App\Filament\Resources\Insights\Pages\CreateInsight;
use App\Filament\Resources\Insights\Pages\ListInsights;
use App\Filament\Resources\TeamMembers\Pages\CreateTeamMember;
use App\Models\CaseStudy;
use App\Models\Insight;
use App\Models\TeamMember;
use Livewire\Livewire;

class InsightsAndCaseStudiesTest extends AdminTestCase
{
    public function test_insight_is_created_as_a_draft_with_both_translations(): void
    {
        $service = $this->createService();
        $author = $this->createTeamMember();

        $this->actingAs($this->admin);

        Livewire::test(CreateInsight::class)
            ->fillForm([
                'title.ar' => 'مستقبل التحول الرقمي في المملكة',
                'title.en' => 'The Future of Digital Transformation',
                'excerpt.ar' => 'قراءة في أبرز الاتجاهات.',
                'excerpt.en' => 'A look at the key trends.',
                'body.en' => '<p>Full article.</p>',
                'type' => InsightType::Guide,
                'service_id' => $service->id,
                'author_id' => $author->id,
                'is_featured' => true,
            ])
            ->assertSchemaStateSet(['slug' => 'the-future-of-digital-transformation'])
            ->call('create')
            ->assertHasNoFormErrors();

        $insight = Insight::query()->sole();

        $this->assertSame('مستقبل التحول الرقمي في المملكة', $insight->tr('title', 'ar'));
        $this->assertSame('A look at the key trends.', $insight->tr('excerpt', 'en'));
        $this->assertSame(InsightType::Guide, $insight->type);
        $this->assertTrue($insight->service->is($service));
        $this->assertTrue($insight->author->is($author));
        $this->assertTrue($insight->is_featured);
        $this->assertNull($insight->published_at);
        $this->assertFalse($insight->isPublished());
    }

    public function test_insights_can_be_filtered_by_publish_status(): void
    {
        $published = $this->createInsight(['slug' => 'published']);
        $scheduled = $this->createInsight(['slug' => 'scheduled', 'published_at' => now()->addWeek()]);
        $draft = $this->createInsight(['slug' => 'draft', 'published_at' => null]);

        $this->actingAs($this->admin);

        Livewire::test(ListInsights::class)
            ->assertCanSeeTableRecords([$published, $scheduled, $draft])
            ->filterTable('publish_status', 'draft')
            ->assertCanSeeTableRecords([$draft])
            ->assertCanNotSeeTableRecords([$published, $scheduled])
            ->filterTable('publish_status', 'scheduled')
            ->assertCanSeeTableRecords([$scheduled])
            ->assertCanNotSeeTableRecords([$published, $draft])
            ->filterTable('publish_status', 'published')
            ->assertCanSeeTableRecords([$published])
            ->assertCanNotSeeTableRecords([$scheduled, $draft]);
    }

    public function test_team_member_is_created(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateTeamMember::class)
            ->fillForm([
                'name.ar' => 'خالد الحربي',
                'name.en' => 'Khalid Alharbi',
                'position.ar' => 'شريك',
                'position.en' => 'Partner',
                'is_leadership' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $member = TeamMember::query()->sole();

        $this->assertSame('Khalid Alharbi', $member->tr('name', 'en'));
        $this->assertTrue($member->is_leadership);
        $this->assertTrue($member->is_active);
    }

    public function test_case_study_is_created_with_metrics(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateCaseStudy::class)
            ->fillForm([
                'title.ar' => 'إعادة هيكلة جهة حكومية',
                'title.en' => 'Restructuring a Government Entity',
                'client.ar' => 'جهة حكومية',
                'client.en' => 'Government entity',
                'outcome.en' => 'A leaner structure.',
                'metrics' => [
                    ['value' => '40%', 'label' => ['ar' => 'خفض التكاليف', 'en' => 'Lower costs']],
                    ['value' => '6', 'label' => ['ar' => 'أشهر للتنفيذ', 'en' => 'Months to deliver']],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $caseStudy = CaseStudy::query()->sole();

        $this->assertSame('restructuring-a-government-entity', $caseStudy->slug);
        $this->assertSame('A leaner structure.', $caseStudy->tr('outcome', 'en'));
        $this->assertSame([
            ['value' => '40%', 'label' => ['ar' => 'خفض التكاليف', 'en' => 'Lower costs']],
            ['value' => '6', 'label' => ['ar' => 'أشهر للتنفيذ', 'en' => 'Months to deliver']],
        ], $caseStudy->metrics);
    }

    public function test_case_study_accepts_at_most_four_metrics(): void
    {
        $this->actingAs($this->admin);

        $metric = ['value' => '1', 'label' => ['ar' => 'مؤشر', 'en' => 'Metric']];

        Livewire::test(CreateCaseStudy::class)
            ->fillForm([
                'title.ar' => 'قصة',
                'title.en' => 'Story',
                'metrics' => array_fill(0, 5, $metric),
            ])
            ->call('create')
            ->assertHasFormErrors(['metrics' => 'max']);
    }
}
