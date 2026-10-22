<?php

namespace Database\Seeders;

use App\Enums\InsightType;
use App\Models\Insight;
use App\Models\Service;
use Illuminate\Database\Seeder;

/**
 * Three launch insights written from the firm's own site copy (Saudi Gate,
 * Vision 2030, the "Customize to Fit" method). Review them before going live.
 */
class InsightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $insights = [
            [
                'slug' => 'entering-the-saudi-market-in-seven-stages',
                'type' => InsightType::Guide,
                'is_featured' => true,
                'published_at' => '2026-10-01 09:00:00',
                'title' => [
                    'ar' => 'دخول السوق السعودي في سبع مراحل: من التقييم إلى التخارج',
                    'en' => 'Entering the Saudi market in seven stages: from assessment to exit',
                ],
                'excerpt' => [
                    'ar' => 'خارطة عملية للشركات العالمية والناشئة التي تخطط لتأسيس أعمالها في المملكة وتشغيلها وتوسيعها بثقة.',
                    'en' => 'A practical roadmap for global firms and startups planning to establish, operate and scale in the Kingdom with confidence.',
                ],
                'body' => [
                    'ar' => '<p>تفتح المملكة العربية السعودية أبوابها للمستثمرين أكثر من أي وقت مضى؛ ملكية أجنبية بنسبة 100% في كثير من الأنشطة، وتراخيص سريعة، وحوافز ضريبية، ومنظومة حكومية رقمية. لكن الفرصة الكبيرة تحتاج طريقاً واضحاً.</p><h2>1. التقييم</h2><p>قبل دخول السوق، الوضوح أولاً: تحليل السوق، وخطة العمل، والنموذج المالي، وخارطة طريق لخمس سنوات.</p><h2>2. الانطلاق و3. الانطلاق +</h2><p>التراخيص والتصاريح والموافقات الحكومية لكيان جاهز للعمل، ثم خدمات الهبوط السلس للتنفيذيين وفرقهم من الإقامة والسكن إلى الخدمات اللوجستية.</p><h2>4. التشغيل</h2><p>عمليات متكاملة جاهزة: التمثيل المحلي، والإقرارات النظامية، والمحاسبة، والموارد البشرية، والعلاقات الحكومية.</p><h2>5. النمو و6. التوسع</h2><p>استراتيجية دخول السوق والجاهزية للمستثمرين وجمع رأس المال، ثم دخول قطاعات ومناطق جديدة داخل المملكة وفي دول الخليج.</p><h2>7. التخارج</h2><p>كل رحلة ناجحة تحتاج خطة تخارج: الاندماج أو البيع أو الطرح العام أو التصفية النظامية بأعلى قيمة ممكنة.</p><p>لا نكتفي بفتح الأبواب، بل نرسم معك طريقاً ممهداً نحو النجاح.</p>',
                    'en' => '<p>Saudi Arabia is more open to investors than ever: 100% foreign ownership in many activities, fast-track licensing, tax incentives and a digital government ecosystem. But a big opportunity needs a clear path.</p><h2>1. Assess</h2><p>Before stepping into the market, clarity is critical: market analysis, business planning, financial modeling and a five-year roadmap.</p><h2>2. Start and 3. Start +</h2><p>Licences, permits and governmental approvals for a ready-to-operate entity, then a soft landing for executives and teams, from residency and housing to logistics.</p><h2>4. Operate</h2><p>Operations in a box: local representation, statutory filings, accounting, HR and government relations.</p><h2>5. Grow and 6. Expand</h2><p>Go-to-market strategy, investor readiness and capital raising, then new sectors and territories in the Kingdom and across the GCC.</p><h2>7. Exit</h2><p>Every successful journey needs an exit plan: merger, sale, IPO or orderly dissolution, with maximum value realization.</p><p>We do not just open doors; we help you define a paved path to success.</p>',
                ],
            ],
            [
                'slug' => 'vision-2030-opportunities-for-international-investors',
                'type' => InsightType::Article,
                'published_at' => '2026-09-15 09:00:00',
                'title' => [
                    'ar' => 'رؤية 2030: لماذا أصبحت المملكة وجهة المستثمرين الدوليين؟',
                    'en' => 'Vision 2030: why the Kingdom has become a destination for international investors',
                ],
                'excerpt' => [
                    'ar' => 'الموقع والقوة والفرص: قراءة في التحول الاقتصادي للمملكة وما يعنيه للشركات الراغبة في دخول السوق.',
                    'en' => 'Position, power, possibility: a look at the Kingdom\'s economic transformation and what it means for companies entering the market.',
                ],
                'body' => [
                    'ar' => '<p>تربط المملكة العربية السعودية ثلاث قارات، وتحتضن أكبر اقتصاد في المنطقة بناتج محلي يتجاوز تريليون دولار. ولم تعد رائدة في الطاقة فحسب، بل أصبحت مركزاً عالمياً للتقنية والاستدامة والاستثمار.</p><h2>رؤية قابلة للقياس</h2><p>تعيد رؤية 2030 تشكيل المشهد الاجتماعي والاقتصادي، وتدعم التنويع وتمكين القطاع الخاص والنمو المستدام في قطاعات مثل التقنية المالية والصناعة والسياحة والطاقة المتجددة والتنقل الذكي.</p><h2>منظومة مصممة للعمل</h2><p>من خلال وزارة الاستثمار و«منشآت» و«مدن»، يجد المستثمر الأجنبي مساراً واضحاً ومبسطاً ورقمياً لدخول السوق.</p><h2>من الفرصة إلى التنفيذ</h2><p>الفرص كبيرة، لكن النجاح يتطلب فهماً عميقاً للسوق المحلي وشريكاً يعرف الطريق. هذا ما صُممت البوابة السعودية من أجله.</p>',
                    'en' => '<p>Saudi Arabia connects three continents and anchors the region\'s largest economy, with a GDP exceeding USD 1 trillion. It is no longer just an energy leader; it is fast becoming a global hub for technology, sustainability and investment.</p><h2>A measurable vision</h2><p>Vision 2030 is reshaping the social and economic landscape, championing diversification, private-sector empowerment and sustainable growth across fintech, manufacturing, tourism, renewable energy and smart mobility.</p><h2>An ecosystem built for action</h2><p>Through the Ministry of Investment, Monsha\'at and MODON, foreign investors find a clear, streamlined and digital pathway into the market.</p><h2>From opportunity to execution</h2><p>The opportunities are large, but success requires deep local insight and a partner who knows the way. That is what Saudi Gate was designed for.</p>',
                ],
            ],
            [
                'slug' => 'why-one-size-fits-all-consulting-fails',
                'type' => InsightType::Article,
                'service' => 'strategy-growth',
                'published_at' => '2026-08-20 09:00:00',
                'title' => [
                    'ar' => '«التفصيل حسب المقاس»: لماذا تفشل الحلول الاستشارية الجاهزة؟',
                    'en' => '"Customize to Fit": why one-size-fits-all consulting fails',
                ],
                'excerpt' => [
                    'ar' => 'لكل مؤسسة ثقافتها وواقعها وطموحاتها. لماذا نبدأ من الواقع قبل أن نوصي بأي حل؟',
                    'en' => 'Every organization has its own culture, reality and ambitions. Why we start from reality before recommending anything.',
                ],
                'body' => [
                    'ar' => '<p>كثير من مشاريع التحول تتعثر لأن الحل صُمم لمؤسسة أخرى. النماذج الجاهزة مغرية لأنها سريعة، لكنها نادراً ما تصمد أمام واقع المؤسسة وثقافتها.</p><h2>نبدأ من الفهم</h2><p>نتعمق في واقع المؤسسة وأهدافها وتحدياتها قبل أي توصية، ونستمع إلى القيادات والفرق التي ستعيش التغيير.</p><h2>نصمم مع العميل لا له</h2><p>نمزج المعرفة المحلية بأفضل الممارسات العالمية، ونصمم الحل مع فرق العميل ليكون قابلاً للتنفيذ ومملوكاً لهم.</p><h2>ننفذ ونستدام</h2><p>لا نكتفي بتسليم وثيقة؛ بل نرافق التنفيذ ونبني القدرات ونضع أدوات القياس لضمان استدامة الأثر.</p>',
                    'en' => '<p>Many transformation programs stall because the solution was designed for a different organization. Ready-made models are tempting because they are fast, but they rarely survive an organization\'s reality and culture.</p><h2>We start by understanding</h2><p>We dig into the organization\'s reality, goals and challenges before recommending anything, and we listen to the leaders and teams who will live the change.</p><h2>We design with the client, not for them</h2><p>We blend local insight with global best practices and co-design the solution with the client\'s teams so it is executable and owned by them.</p><h2>We implement and sustain</h2><p>We don\'t stop at a document; we support implementation, build capabilities and put measurement in place so the impact lasts.</p>',
                ],
            ],
        ];

        foreach ($insights as $insight) {
            $serviceSlug = $insight['service'] ?? null;
            unset($insight['service']);

            Insight::query()->updateOrCreate(['slug' => $insight['slug']], [
                'is_featured' => false,
                ...$insight,
                'service_id' => $serviceSlug ? Service::query()->where('slug', $serviceSlug)->value('id') : null,
            ]);
        }
    }
}
