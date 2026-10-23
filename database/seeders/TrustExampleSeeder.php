<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use App\Models\Partner;
use App\Models\Service;
use App\Models\TeamMember;
use App\Support\SiteSettings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Two worked examples each for the trust content (case studies, partners, team,
 * licences). Every one is saved hidden, so the site shows nothing until real
 * entries are added or an example is edited and published from the admin.
 */
class TrustExampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(SiteSettings $settings): void
    {
        $this->caseStudies();
        $this->partners();
        $this->teamMembers();

        if (blank($settings->get('credentials'))) {
            $settings->set(['credentials' => [
                [
                    'name' => ['ar' => 'السجل التجاري (مثال)', 'en' => 'Commercial Registration (example)'],
                    'number' => '1010000000',
                    'url' => null,
                    'logo' => null,
                    'visible' => false,
                ],
                [
                    'name' => ['ar' => 'عضوية غرفة الرياض (مثال)', 'en' => 'Riyadh Chamber membership (example)'],
                    'number' => null,
                    'url' => null,
                    'logo' => null,
                    'visible' => false,
                ],
            ]]);
        }
    }

    private function caseStudies(): void
    {
        $examples = [
            [
                'slug' => 'example-operating-model-government-entity',
                'service' => 'organizational-development',
                'title' => ['ar' => 'مثال: إعادة تصميم النموذج التشغيلي لجهة حكومية', 'en' => 'Example: Redesigning a government entity\'s operating model'],
                'client' => ['ar' => 'جهة حكومية (سري)', 'en' => 'Government entity (confidential)'],
                'sector' => ['ar' => 'القطاع الحكومي', 'en' => 'Public sector'],
                'summary' => [
                    'ar' => 'هيكل تنظيمي جديد وأدلة إجراءات موحدة رفعت سرعة إنجاز المعاملات.',
                    'en' => 'A new structure and unified procedures that sped up service delivery.',
                ],
                'challenge' => [
                    'ar' => 'تداخل في الصلاحيات بين الإدارات وبطء في إنجاز المعاملات.',
                    'en' => 'Overlapping mandates between departments and slow processing times.',
                ],
                'approach' => [
                    'ar' => 'تشخيص الوضع الراهن، ثم تصميم هيكل جديد ومصفوفة صلاحيات وأدلة إجراءات، ومرافقة التطبيق.',
                    'en' => 'Diagnosed the current state, then designed a new structure, authority matrix and procedures, and supported roll-out.',
                ],
                'outcome' => [
                    'ar' => 'انخفاض زمن إنجاز المعاملات ووضوح المسؤوليات بين الإدارات.',
                    'en' => 'Shorter processing times and clear accountability between departments.',
                ],
                'metrics' => [
                    ['value' => '35%', 'label' => ['ar' => 'أسرع في إنجاز المعاملات', 'en' => 'Faster processing']],
                    ['value' => '12', 'label' => ['ar' => 'دليل إجراءات', 'en' => 'Procedure manuals']],
                ],
            ],
            [
                'slug' => 'example-market-entry-foreign-company',
                'service' => 'strategy-growth',
                'title' => ['ar' => 'مثال: دخول شركة أجنبية إلى السوق السعودي', 'en' => 'Example: A foreign company entering the Saudi market'],
                'client' => ['ar' => 'شركة تقنية أوروبية', 'en' => 'European technology company'],
                'sector' => ['ar' => 'التقنية', 'en' => 'Technology'],
                'summary' => [
                    'ar' => 'من دراسة السوق إلى الترخيص والتشغيل خلال أشهر قليلة عبر البوابة السعودية.',
                    'en' => 'From market study to licensing and operations in a few months through Saudi Gate.',
                ],
                'challenge' => [
                    'ar' => 'رغبة في دخول السوق بسرعة دون معرفة كافية بالأنظمة والتراخيص المحلية.',
                    'en' => 'Wanted to enter quickly without enough knowledge of local regulations and licensing.',
                ],
                'approach' => [
                    'ar' => 'دراسة سوق وخطة عمل، ثم إصدار التراخيص وتجهيز الكيان والتوظيف والعلاقات الحكومية.',
                    'en' => 'Market study and business plan, then licensing, entity set-up, hiring and government relations.',
                ],
                'outcome' => [
                    'ar' => 'كيان جاهز للعمل وأول عقد محلي خلال السنة الأولى.',
                    'en' => 'A ready-to-operate entity and a first local contract within year one.',
                ],
                'metrics' => [
                    ['value' => '4', 'label' => ['ar' => 'أشهر حتى التشغيل', 'en' => 'Months to operations']],
                    ['value' => '100%', 'label' => ['ar' => 'ملكية أجنبية', 'en' => 'Foreign ownership']],
                ],
            ],
        ];

        foreach ($examples as $index => $example) {
            $service = Service::query()->where('slug', $example['service'])->value('id');
            unset($example['service']);

            CaseStudy::query()->firstOrCreate(
                ['slug' => $example['slug']],
                [...$example, 'service_id' => $service, 'sort_order' => $index, 'is_active' => false],
            );
        }
    }

    private function partners(): void
    {
        foreach (['Example Partner One', 'Example Partner Two'] as $index => $name) {
            $logo = 'partners/example-'.($index + 1).'.svg';

            if (! Storage::disk('public')->exists($logo)) {
                Storage::disk('public')->put($logo, '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 240 60"><rect width="240" height="60" rx="6" fill="#e8ece9"/><text x="120" y="37" font-family="sans-serif" font-size="18" text-anchor="middle" fill="#231f20">'.e($name).'</text></svg>');
            }

            Partner::query()->firstOrCreate(
                ['name' => $name],
                ['logo' => $logo, 'url' => null, 'sort_order' => $index, 'is_active' => false],
            );
        }
    }

    private function teamMembers(): void
    {
        $examples = [
            [
                'name' => ['ar' => 'اسم المسؤول (مثال)', 'en' => 'Executive name (example)'],
                'position' => ['ar' => 'الرئيس التنفيذي', 'en' => 'Chief Executive Officer'],
                'bio' => [
                    'ar' => 'نبذة مختصرة: سنوات الخبرة، وأبرز الجهات التي عمل معها، ومجال التخصص.',
                    'en' => 'A short bio: years of experience, notable organizations and area of expertise.',
                ],
            ],
            [
                'name' => ['ar' => 'اسم الشريك (مثال)', 'en' => 'Partner name (example)'],
                'position' => ['ar' => 'شريك، الاستراتيجية والنمو', 'en' => 'Partner, Strategy & Growth'],
                'bio' => [
                    'ar' => 'نبذة مختصرة: الخلفية المهنية والشهادات والقطاعات التي يقودها.',
                    'en' => 'A short bio: professional background, certifications and the sectors they lead.',
                ],
            ],
        ];

        foreach ($examples as $index => $example) {
            TeamMember::query()->firstOrCreate(
                ['name->en' => $example['name']['en']],
                [...$example, 'linkedin_url' => null, 'is_leadership' => true, 'sort_order' => $index, 'is_active' => false],
            );
        }
    }
}
