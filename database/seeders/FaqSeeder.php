<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Database\Seeder;

/**
 * Starter FAQs drawn from what the current site already says. Review with the
 * team before launch and add more from the admin panel.
 */
class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'general' => [
                'name' => ['ar' => 'عن الشركة', 'en' => 'About the firm'],
                'faqs' => [
                    [
                        'question' => ['ar' => 'من هي قنوات الاستشارات؟', 'en' => 'Who is Advisory Channels?'],
                        'answer' => [
                            'ar' => 'شركة سعودية تأسست عام 2019 تعمل في قطاع الاستشارات، وتقدم خدمات تطوير الأعمال والحلول المهنية والتشغيلية والرقمية وفق منهجية «التفصيل حسب المقاس».',
                            'en' => 'A Saudi company founded in 2019 in the consulting sector, offering business development, professional, operational and digital solutions through its "Customize to Fit" methodology.',
                        ],
                    ],
                    [
                        'question' => ['ar' => 'من هم عملاؤكم؟', 'en' => 'Who do you work with?'],
                        'answer' => [
                            'ar' => 'نعمل مع الشركات والجهات الحكومية وشبه الحكومية في مختلف القطاعات، ومع الشركات الدولية والناشئة الراغبة في دخول السوق السعودي.',
                            'en' => 'We partner with corporate enterprises and government and semi-government institutions across sectors, as well as international firms and startups entering the Saudi market.',
                        ],
                    ],
                    [
                        'question' => ['ar' => 'ماذا تعني منهجية «التفصيل حسب المقاس»؟', 'en' => 'What does "Customize to Fit" mean?'],
                        'answer' => [
                            'ar' => 'نؤمن بأن لكل تحدٍّ طبيعته الخاصة، لذلك لا نقدم حلولاً جاهزة، بل نصمم لكل عميل استراتيجية تناسب احتياجاته وتطلعاته.',
                            'en' => 'We believe every challenge is unique, so we reject one-size-fits-all approaches and craft strategies that fit each client\'s specific needs and aspirations.',
                        ],
                    ],
                ],
            ],
            'saudi-gate' => [
                'name' => ['ar' => 'البوابة السعودية', 'en' => 'Saudi Gate'],
                'faqs' => [
                    [
                        'question' => ['ar' => 'ما هي البوابة السعودية؟', 'en' => 'What is Saudi Gate?'],
                        'answer' => [
                            'ar' => 'حل متكامل 360° من قنوات الاستشارات لدخول السوق السعودي والتوسع فيه، يغطي سبع مراحل: التقييم، الانطلاق، الانطلاق +، التشغيل، النمو، التوسع، والتخارج.',
                            'en' => 'A turnkey 360° market-entry and expansion solution by Advisory Channels covering seven stages: Assess, Start, Start +, Operate, Grow, Expand and Exit.',
                        ],
                    ],
                    [
                        'question' => ['ar' => 'هل يجب أن أشترك في جميع المراحل؟', 'en' => 'Do I need to sign up for every stage?'],
                        'answer' => [
                            'ar' => 'لا. يمكنك اختيار المرحلة أو المراحل التي تناسب وضع شركتك، أو أن نرافقك في الرحلة كاملة.',
                            'en' => 'No. You can pick the stage or stages that fit where your business is today, or let us walk the whole journey with you.',
                        ],
                    ],
                    [
                        'question' => ['ar' => 'هل يمكن للمستثمر الأجنبي تملك شركته بالكامل في المملكة؟', 'en' => 'Can a foreign investor fully own a company in Saudi Arabia?'],
                        'answer' => [
                            'ar' => 'تتيح المملكة الملكية الأجنبية بنسبة 100% في كثير من الأنشطة. وتساعدك باقة «التقييم» على التحقق من متطلبات نشاطك تحديداً قبل البدء.',
                            'en' => 'The Kingdom allows 100% foreign ownership in many activities. Our Assess package helps you confirm the specific requirements for your activity before you start.',
                        ],
                    ],
                ],
            ],
            'careers' => [
                'name' => ['ar' => 'الوظائف', 'en' => 'Careers'],
                'faqs' => [
                    [
                        'question' => ['ar' => 'لمن صُمم برنامج «الطريق إلى الاستشارات»؟', 'en' => 'Who is the Path to Consultancy Program for?'],
                        'answer' => [
                            'ar' => 'لحديثي التخرج. مدة البرنامج 6 أشهر، والتخرج الناجح منه يضمن وظيفة مستشار مشارك في الشركة.',
                            'en' => 'Fresh graduates. The program runs for 6 months, and successful graduation guarantees a job as an Associate Consultant.',
                        ],
                    ],
                ],
            ],
        ];

        $categoryOrder = 0;

        foreach ($categories as $slug => $data) {
            $category = FaqCategory::query()->updateOrCreate(
                ['slug' => $slug],
                ['name' => $data['name'], 'sort_order' => ++$categoryOrder],
            );

            foreach ($data['faqs'] as $order => $faq) {
                Faq::query()->updateOrCreate(
                    ['faq_category_id' => $category->getKey(), 'sort_order' => $order + 1],
                    [...$faq, 'is_active' => true],
                );
            }
        }
    }
}
