<?php

namespace Database\Seeders;

use App\Models\GatePackage;
use Illuminate\Database\Seeder;

/**
 * The seven Saudi Gate stages from advisory-channels.com/saudi-gate.
 */
class GatePackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'slug' => 'assess',
                'title' => ['ar' => 'التقييم', 'en' => 'Assess'],
                'tagline' => [
                    'ar' => 'قبل دخول السوق، الوضوح أولاً.',
                    'en' => 'Before stepping into the market, clarity is critical.',
                ],
                'description' => [
                    'ar' => 'تمنح باقة «التقييم» المستثمرين ورواد الأعمال فهماً تفصيلياً لفرصهم في السوق السعودي، وتضع الأساس لقرارات مدروسة ونجاح مستدام في السوق.',
                    'en' => 'Our Assess Package provides investors and entrepreneurs with a detailed understanding of their potential in the Saudi market. This stage sets the foundation for informed decisions and sustainable market success.',
                ],
                'deliverables' => [
                    ['ar' => 'تحليل السوق', 'en' => 'Market analysis'],
                    ['ar' => 'خطط الأعمال', 'en' => 'Business planning'],
                    ['ar' => 'النمذجة المالية', 'en' => 'Financial modeling'],
                    ['ar' => 'خارطة طريق استراتيجية لخمس سنوات', 'en' => 'Five-year strategic roadmap'],
                ],
            ],
            [
                'slug' => 'start',
                'title' => ['ar' => 'الانطلاق', 'en' => 'Start'],
                'tagline' => [
                    'ar' => 'امتثال كامل وحرية في العمل.',
                    'en' => 'Full compliance and freedom to operate.',
                ],
                'description' => [
                    'ar' => 'للشركات الجاهزة لتأسيس حضورها في المملكة، تقدّم باقة «الانطلاق» كياناً جاهزاً للعمل دون أي تعقيدات، من التراخيص إلى الموافقات الحكومية.',
                    'en' => 'For businesses ready to establish a footprint, our Start Package delivers a ready-to-operate business presence with zero friction, from licensing to governmental approvals.',
                ],
                'deliverables' => [
                    ['ar' => 'التراخيص التجارية', 'en' => 'Business licensing'],
                    ['ar' => 'التصاريح النظامية', 'en' => 'Regulatory permits'],
                    ['ar' => 'الموافقات الحكومية الأساسية', 'en' => 'Essential governmental approvals'],
                ],
            ],
            [
                'slug' => 'start-plus',
                'is_premium' => true,
                'title' => ['ar' => 'الانطلاق +', 'en' => 'Start +'],
                'tagline' => [
                    'ar' => 'هبوط سلس في المملكة.',
                    'en' => 'A soft landing in Saudi Arabia.',
                ],
                'description' => [
                    'ar' => 'النسخة المميزة من باقة «الانطلاق»؛ تتجاوز تأسيس الكيان لتمنح التنفيذيين وفرقهم انتقالاً سلساً إلى الحياة والعمل في المملكة.',
                    'en' => 'A premium version of our Start package that goes beyond business setup, offering executives and teams a seamless transition into life and work in Saudi Arabia.',
                ],
                'deliverables' => [
                    ['ar' => 'تسهيل إجراءات الإقامة', 'en' => 'Residency facilitation'],
                    ['ar' => 'السكن', 'en' => 'Accommodation'],
                    ['ar' => 'جولات تعريفية بالمدن', 'en' => 'City tours'],
                    ['ar' => 'شراكات فندقية', 'en' => 'Hotel partnerships'],
                    ['ar' => 'خدمات لوجستية لكبار الشخصيات', 'en' => 'VIP logistics'],
                ],
            ],
            [
                'slug' => 'operate',
                'title' => ['ar' => 'التشغيل', 'en' => 'Operate'],
                'tagline' => [
                    'ar' => 'عمليات متكاملة جاهزة.',
                    'en' => 'Operations in a box.',
                ],
                'description' => [
                    'ar' => 'بعد التأسيس يصبح التميز التشغيلي هو الفارق. باقة «التشغيل» مثالية للشركات التي تبحث عن عمليات فعّالة وممتثلة ومندمجة محلياً.',
                    'en' => 'Once established, operational excellence becomes the differentiator. The Operate Package is ideal for companies seeking efficient, compliant, and locally integrated operations.',
                ],
                'deliverables' => [
                    ['ar' => 'التمثيل المحلي', 'en' => 'Local representation'],
                    ['ar' => 'الإقرارات النظامية', 'en' => 'Statutory filings'],
                    ['ar' => 'المحاسبة ومسك الدفاتر', 'en' => 'Accounting & bookkeeping'],
                    ['ar' => 'الموارد البشرية', 'en' => 'Human resources'],
                    ['ar' => 'أعمال السكرتارية', 'en' => 'Secretarial services'],
                    ['ar' => 'العلاقات الحكومية', 'en' => 'Government relations'],
                    ['ar' => 'خدمات تقنية المعلومات', 'en' => 'IT services'],
                    ['ar' => 'المشتريات والتعاقدات', 'en' => 'Procurement & contracting'],
                ],
            ],
            [
                'slug' => 'grow',
                'title' => ['ar' => 'النمو', 'en' => 'Grow'],
                'tagline' => [
                    'ar' => 'وسّع حضورك وعزّز تنافسيتك.',
                    'en' => 'Scale your presence and competitiveness.',
                ],
                'description' => [
                    'ar' => 'تدعم باقة «النمو» العملاء في توسيع حضورهم وتعزيز تنافسيتهم في السوق السعودي، بهدف تعظيم فرص النمو والحصة السوقية.',
                    'en' => 'Our Grow Package supports clients in scaling their presence and competitiveness within the Saudi market, aimed at maximizing growth potential and market share.',
                ],
                'deliverables' => [
                    ['ar' => 'استراتيجية دخول السوق', 'en' => 'Go-to-market strategy'],
                    ['ar' => 'الجاهزية للمستثمرين', 'en' => 'Investor readiness'],
                    ['ar' => 'جمع رأس المال', 'en' => 'Capital raising'],
                    ['ar' => 'تطوير منتجات وخدمات جديدة', 'en' => 'New product & service development'],
                ],
            ],
            [
                'slug' => 'expand',
                'title' => ['ar' => 'التوسع', 'en' => 'Expand'],
                'tagline' => [
                    'ar' => 'ادخل قطاعات وأسواقاً جديدة.',
                    'en' => 'Enter new sectors, industries, or territories.',
                ],
                'description' => [
                    'ar' => 'بعد استقرار النمو، تمكّن باقة «التوسع» الشركات من دخول قطاعات وصناعات ومناطق جديدة داخل المملكة أو في دول الخليج، لتكون الخطوة الأهم نحو الريادة الإقليمية.',
                    'en' => 'Once growth is stabilized, our Expand Package empowers businesses to enter new sectors, industries, or territories within the Kingdom or across the GCC. It is the ultimate step toward becoming a regional leader.',
                ],
                'deliverables' => [
                    ['ar' => 'دخول قطاعات وصناعات جديدة', 'en' => 'New sectors & industries'],
                    ['ar' => 'التوسع في مناطق جديدة داخل المملكة', 'en' => 'New territories within the Kingdom'],
                    ['ar' => 'دخول أسواق دول الخليج', 'en' => 'GCC market entry'],
                ],
            ],
            [
                'slug' => 'exit',
                'title' => ['ar' => 'التخارج', 'en' => 'Exit'],
                'tagline' => [
                    'ar' => 'كل رحلة ناجحة تحتاج خطة تخارج استراتيجية.',
                    'en' => 'Every successful journey requires a strategic exit plan.',
                ],
                'description' => [
                    'ar' => 'تدعم باقة «التخارج» العملاء الراغبين في الاندماج أو البيع أو الطرح العام أو تصفية أعمالهم، بما يحقق أعلى قيمة ويضمن الامتثال.',
                    'en' => 'Our Exit Package supports clients looking to merge, sell, go public, or dissolve operations with maximum value realization and compliance assurance.',
                ],
                'deliverables' => [
                    ['ar' => 'الاندماج', 'en' => 'Mergers'],
                    ['ar' => 'البيع والاستحواذ', 'en' => 'Sale & acquisition'],
                    ['ar' => 'الطرح العام الأولي', 'en' => 'Initial public offering (IPO)'],
                    ['ar' => 'التصفية النظامية', 'en' => 'Orderly dissolution'],
                ],
            ],
        ];

        foreach ($packages as $order => $package) {
            GatePackage::query()->updateOrCreate(
                ['slug' => $package['slug']],
                ['is_premium' => false, ...$package, 'sort_order' => $order + 1, 'is_active' => true],
            );
        }
    }
}
