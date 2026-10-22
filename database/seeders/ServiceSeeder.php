<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * The four service channels from advisory-channels.com, with Arabic drafts
 * and expanded detail copy for the new per-service pages.
 */
class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'slug' => 'strategy-growth',
                'icon' => 'compass',
                'title' => [
                    'ar' => 'قناة الاستراتيجية والنمو',
                    'en' => 'Strategy & Growth Channel',
                ],
                'excerpt' => [
                    'ar' => 'نساعد المؤسسات على صياغة خطط النمو الاستراتيجي وتطويرها وتنفيذها.',
                    'en' => 'Help organizations define, refine, and implement strategic growth plans.',
                ],
                'body' => [
                    'ar' => '<p>تبدأ كل رحلة نمو ناجحة باستراتيجية واضحة وقابلة للتنفيذ. نعمل جنباً إلى جنب مع القيادات لفهم موقع المؤسسة الحالي وفرصها، ثم نصمم معها مساراً واقعياً للنمو يتسق مع مستهدفات رؤية المملكة 2030.</p><p>لا نكتفي بتسليم وثيقة استراتيجية؛ بل نحوّلها إلى مبادرات ومؤشرات أداء وخطط تنفيذ، ونرافق فرق العمل حتى تتحقق النتائج على أرض الواقع.</p>',
                    'en' => '<p>Every successful growth journey starts with a clear, executable strategy. We work side by side with leadership to understand where the organization stands today and where its opportunities lie, then co-design a realistic growth path aligned with Saudi Vision 2030.</p><p>We don\'t stop at a strategy document. We translate it into initiatives, KPIs and implementation plans, and stay with your teams until results are delivered on the ground.</p>',
                ],
                'highlights' => [
                    ['ar' => 'الاستراتيجية المؤسسية واستراتيجيات وحدات الأعمال', 'en' => 'Corporate & business-unit strategy'],
                    ['ar' => 'دراسات السوق والجدوى', 'en' => 'Market & feasibility studies'],
                    ['ar' => 'خرائط طريق النمو والتوسع', 'en' => 'Growth & expansion roadmaps'],
                    ['ar' => 'إدارة الأداء ومؤشرات الأداء الرئيسية', 'en' => 'Performance management & KPIs'],
                ],
            ],
            [
                'slug' => 'organizational-development',
                'icon' => 'network',
                'title' => [
                    'ar' => 'التطوير المؤسسي',
                    'en' => 'Organizational Development',
                ],
                'excerpt' => [
                    'ar' => 'نصمم الهياكل والإجراءات والحوافز لرفع كفاءة الأداء المؤسسي.',
                    'en' => 'Design structures, processes, and incentives to optimize organizational performance.',
                ],
                'body' => [
                    'ar' => '<p>المؤسسة القوية تقوم على هيكل واضح وأدوار محددة وإجراءات تخدم أهدافها. نساعد الجهات على إعادة تصميم بنيتها التنظيمية بما يتناسب مع طبيعة عملها وحجمها وطموحاتها.</p><p>ومن خلال منهجية «التفصيل حسب المقاس» نبني حلولاً تراعي ثقافة كل جهة وواقعها، بدلاً من نماذج جاهزة لا تناسب الجميع.</p>',
                    'en' => '<p>Strong organizations are built on clear structures, well-defined roles, and processes that serve their goals. We help entities redesign their organizational architecture to fit their nature, size and ambitions.</p><p>Through our "Customize to Fit" methodology we build solutions that respect each organization\'s culture and reality, rather than one-size-fits-all templates.</p>',
                ],
                'highlights' => [
                    ['ar' => 'تصميم الهياكل التنظيمية', 'en' => 'Organizational structure design'],
                    ['ar' => 'السياسات والإجراءات', 'en' => 'Policies & procedures'],
                    ['ar' => 'بطاقات الوصف الوظيفي وسلالم الدرجات', 'en' => 'Job architecture & grading'],
                    ['ar' => 'أنظمة الحوافز والمكافآت', 'en' => 'Incentive & reward schemes'],
                ],
            ],
            [
                'slug' => 'transformation-management',
                'icon' => 'refresh',
                'title' => [
                    'ar' => 'إدارة التحول',
                    'en' => 'Transformation Management',
                ],
                'excerpt' => [
                    'ar' => 'نقود التحول في الأعمال عبر التغيير التشغيلي والتقني والثقافي.',
                    'en' => 'Lead business transformation through operational, technological, and cultural change.',
                ],
                'body' => [
                    'ar' => '<p>التحول ليس مشروعاً تقنياً فحسب، بل رحلة تغيير تمس العمليات والأنظمة والأشخاص. نساعد الجهات الحكومية وشبه الحكومية والشركات على إدارة برامج التحول من التخطيط إلى التنفيذ والمتابعة.</p><p>نؤسس مكاتب إدارة التحول، ونضع أطر الحوكمة، ونقود إدارة التغيير لضمان تبنّي الفرق للأساليب الجديدة واستدامة أثرها.</p>',
                    'en' => '<p>Transformation is not just a technology project; it is a change journey that touches processes, systems and people. We help government, semi-government and private entities run transformation programs from planning through execution and follow-up.</p><p>We set up transformation offices, establish governance frameworks, and lead change management so teams adopt new ways of working and the impact lasts.</p>',
                ],
                'highlights' => [
                    ['ar' => 'مكاتب إدارة التحول والمشاريع', 'en' => 'Transformation & project management offices'],
                    ['ar' => 'التحول الرقمي', 'en' => 'Digital transformation'],
                    ['ar' => 'إدارة التغيير والثقافة المؤسسية', 'en' => 'Change management & culture'],
                    ['ar' => 'التميز التشغيلي', 'en' => 'Operational excellence'],
                ],
            ],
            [
                'slug' => 'business-process-outsourcing',
                'icon' => 'layers',
                'title' => [
                    'ar' => 'إسناد العمليات',
                    'en' => 'Business Process Outsourcing',
                ],
                'excerpt' => [
                    'ar' => 'ندير الوظائف الأساسية نيابةً عنك لرفع الكفاءة وخفض التكاليف وضمان الامتثال.',
                    'en' => 'Manage core functions to boost efficiency, cut costs, and ensure compliance.',
                ],
                'body' => [
                    'ar' => '<p>ركّز على أعمالك الأساسية ودع العمليات المساندة لنا. نتولى إدارة وظائف حيوية بمعايير عالية من الجودة والامتثال للأنظمة المحلية، مع تقارير دورية وشفافية كاملة.</p><p>يناسب هذا الحل الشركات الناشئة والشركات الأجنبية الجديدة في المملكة، كما يناسب المؤسسات القائمة الراغبة في خفض التكاليف ورفع الكفاءة.</p>',
                    'en' => '<p>Focus on your core business and leave the supporting operations to us. We run critical functions to high standards of quality and local regulatory compliance, with regular reporting and full transparency.</p><p>It suits startups and foreign companies new to the Kingdom, as well as established organizations looking to cut costs and raise efficiency.</p>',
                ],
                'highlights' => [
                    ['ar' => 'المالية والمحاسبة', 'en' => 'Finance & accounting'],
                    ['ar' => 'الموارد البشرية والرواتب', 'en' => 'HR & payroll'],
                    ['ar' => 'العلاقات الحكومية', 'en' => 'Government relations'],
                    ['ar' => 'المشتريات والتعاقدات', 'en' => 'Procurement & contracting'],
                ],
            ],
        ];

        foreach ($services as $order => $service) {
            Service::query()->updateOrCreate(
                ['slug' => $service['slug']],
                [...$service, 'sort_order' => $order + 1, 'is_active' => true],
            );
        }
    }
}
