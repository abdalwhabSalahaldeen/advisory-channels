<?php

namespace Database\Seeders;

use App\Support\SiteSettings;
use Illuminate\Database\Seeder;

/**
 * Default site copy. Everything here is editable from Admin > Site settings.
 */
class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(SiteSettings $settings): void
    {
        $settings->set([
            // The Arabic trade name is a placeholder until the official one is confirmed.
            'site_name' => ['ar' => 'قنوات الاستشارات', 'en' => 'Advisory Channels'],
            'contact_email' => 'info@advisory-channels.com',
            'contact_phone' => null,
            'whatsapp' => null,
            'address' => ['ar' => 'الرياض، المملكة العربية السعودية', 'en' => 'Riyadh, Saudi Arabia'],
            'map_url' => null,
            'linkedin_url' => 'https://www.linkedin.com/company/advisory-channels/',
            'x_url' => 'https://x.com/advisory_en',
            'notification_email' => null,
            'request_service_url' => null,

            'meta_description' => [
                'ar' => 'قنوات الاستشارات شركة سعودية للاستشارات والخدمات المهنية، تقدم حلولاً مصممة حسب احتياج كل عميل في الاستراتيجية والتطوير المؤسسي والتحول وإسناد العمليات، وبوابة متكاملة لدخول السوق السعودي.',
                'en' => 'Advisory Channels is a Saudi consulting and professional services firm delivering tailor-made solutions in strategy, organizational development, transformation and outsourcing, plus a 360° gateway into the Saudi market.',
            ],

            'hero_title' => [
                'ar' => 'إرثٌ سعودي، حلولٌ مصممة، طموحاتٌ جريئة',
                'en' => 'Saudi Heritage, Tailored Solutions, Bold Ambitions',
            ],
            'hero_subtitle' => [
                'ar' => 'شركة سعودية متجذرة في أسس وطنية راسخة، تتطلع إلى التوسع إقليمياً وعالمياً. النخلة، رمز هويتنا، تجسد انتماءنا الوطني العميق وقيم الأصالة والاستدامة التي نؤمن بها.',
                'en' => 'We are a Saudi company rooted in strong national foundations, aspiring to expand both regionally and globally. The palm tree, the core symbol of our identity, represents our deep national belonging and the values of authenticity and sustainability we uphold.',
            ],
            'home_intro' => [
                'ar' => 'نجمع بين المعرفة المحلية وأفضل الممارسات العالمية لتقديم حلول استشارية عالمية المستوى ومصممة حسب احتياج كل عميل. نؤمن بأن لكل تحدٍّ طبيعته الخاصة، لذلك نرفض الحلول الجاهزة ونصمم استراتيجيات تناسب احتياجات عملائنا وتطلعاتهم.',
                'en' => 'We combine local insight with international best practices to deliver world-class, tailor-made consulting solutions. We believe every challenge is unique, so we reject one-size-fits-all approaches and craft strategies that fit each client\'s specific needs and aspirations.',
            ],
            'home_stats' => [
                ['value' => '2019', 'label' => ['ar' => 'سنة التأسيس', 'en' => 'Founded']],
                ['value' => '+20', 'label' => ['ar' => 'عاماً من خبرة فريقنا في السوق المحلي', 'en' => 'Years of team experience in the local market']],
                ['value' => '4', 'label' => ['ar' => 'قنوات خدمات متخصصة', 'en' => 'Specialized service channels']],
                ['value' => '7', 'label' => ['ar' => 'مراحل في رحلة البوابة السعودية', 'en' => 'Stages in the Saudi Gate journey']],
            ],

            'about_story' => [
                'ar' => 'تأسست قنوات الاستشارات عام 2019 شركةً سعودية تعمل في قطاع الاستشارات، وتقدم خدمات تطوير الأعمال والحلول المهنية والتشغيلية والرقمية بأسلوب فريد ومبتكر. تتميز الشركة بتطبيق أفضل الممارسات واعتماد منهجية «التفصيل حسب المقاس» لعملائها، لتقودهم نحو التميز التشغيلي واستدامة الأعمال والموارد.',
                'en' => 'Advisory Channels is a Saudi company founded in 2019. It operates in the consulting sector, offering business development, professional, operational and digital solutions in a unique and innovative manner. The company distinguishes itself by applying best practices and adopting a "Customize to Fit" methodology for its clients, leading them to operational excellence and the sustainability of businesses and resources.',
            ],
            'about_team' => [
                'ar' => 'يضم فريقنا نخبة من المحترفين ذوي الخبرة في كبرى الشركات العالمية والشغف بالابتكار. نعمل مع الشركات والجهات الحكومية وشبه الحكومية في مختلف القطاعات لمساعدتها على تجاوز التعقيدات وقيادة التحول وتحقيق نمو مستدام.',
                'en' => 'Our team of seasoned professionals brings experience from leading global firms and a passion for innovation. We partner with corporate enterprises and government and semi-government institutions across sectors, helping them navigate complexity, drive transformation and achieve sustainable growth.',
            ],
            'mission' => [
                'ar' => 'دعم الجهات المحلية والإقليمية والخاصة والمؤسسات الحكومية وشبه الحكومية لتحسين أدائها وتحقيق أهدافها، من خلال المنافسة بنزاهة ومصداقية واحترافية، وتطبيق أفضل الممارسات العالمية واتباع منهجية «التفصيل حسب المقاس».',
                'en' => 'Supporting local, regional and private entities, as well as government and semi-government institutions, to enhance their performance and achieve their objectives, by competing with integrity, credibility and professionalism through the application of global best practices and the "Customize to Fit" methodology.',
            ],
            'vision' => [
                'ar' => 'توظيف الخبرات وأفضل الممارسات لنكون شركة استشارات سعودية المنشأ عالمية الانتشار، تنافس بشرف أفضل 10 شركات استشارية في العالم.',
                'en' => 'To harness expertise and best practices to be a Saudi-origin consultancy firm with global reach, competing honorably with the top 10 consulting firms in the world.',
            ],

            'privacy_policy' => [
                'ar' => '<p><em>مسودة يجب مراجعتها قانونياً قبل النشر.</em></p><h2>مقدمة</h2><p>توضح هذه السياسة كيف تجمع قنوات الاستشارات بياناتك الشخصية وتستخدمها وتحميها عند زيارتك لموقعنا، وفقاً لنظام حماية البيانات الشخصية في المملكة العربية السعودية.</p><h2>البيانات التي نجمعها</h2><ul><li>البيانات التي تقدمها طوعاً عبر نماذج الموقع: الاسم والبريد الإلكتروني ورقم الجوال واسم الجهة وتفاصيل طلبك.</li><li>السيرة الذاتية والبيانات المهنية عند التقديم على الوظائف.</li><li>بيانات الاستخدام التقنية الأساسية اللازمة لتشغيل الموقع وحمايته.</li></ul><h2>كيف نستخدم بياناتك</h2><ul><li>الرد على طلباتك واستفساراتك.</li><li>دراسة طلبات التوظيف.</li><li>تحسين خدماتنا وموقعنا.</li></ul><h2>مشاركة البيانات</h2><p>لا نبيع بياناتك. قد نشاركها مع مزودي خدمات يعملون نيابةً عنا وفق التزامات تعاقدية بالسرية، أو عندما يتطلب النظام ذلك.</p><h2>حقوقك</h2><p>يحق لك طلب الاطلاع على بياناتك أو تصحيحها أو إتلافها أو سحب موافقتك في أي وقت عبر التواصل معنا على info@advisory-channels.com.</p>',
                'en' => '<p><em>Draft: to be reviewed by legal counsel before publishing.</em></p><h2>Introduction</h2><p>This policy explains how Advisory Channels collects, uses and protects your personal data when you visit our website, in line with the Saudi Personal Data Protection Law (PDPL).</p><h2>Data we collect</h2><ul><li>Data you voluntarily provide through our forms: name, email, phone number, organization and the details of your request.</li><li>Your CV and professional details when you apply for a job.</li><li>Basic technical usage data needed to run and secure the website.</li></ul><h2>How we use your data</h2><ul><li>To respond to your requests and enquiries.</li><li>To assess job applications.</li><li>To improve our services and website.</li></ul><h2>Data sharing</h2><p>We never sell your data. We may share it with service providers acting on our behalf under contractual confidentiality obligations, or where required by law.</p><h2>Your rights</h2><p>You may request access to, correction or destruction of your data, or withdraw your consent at any time, by contacting us at info@advisory-channels.com.</p>',
            ],
        ]);
    }
}
