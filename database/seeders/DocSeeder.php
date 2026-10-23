<?php

namespace Database\Seeders;

use App\Models\Doc;
use Illuminate\Database\Seeder;

/**
 * Draft legal pages. Both need a legal review before going live.
 */
class DocSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Doc::query()->updateOrCreate(['slug' => Doc::PRIVACY_POLICY], [
            'title' => ['ar' => 'سياسة الخصوصية', 'en' => 'Privacy Policy'],
            'body' => [
                'ar' => '<p><em>مسودة يجب مراجعتها قانونياً قبل النشر.</em></p><h2>مقدمة</h2><p>توضح هذه السياسة كيف تجمع قنوات الاستشارات بياناتك الشخصية وتستخدمها وتحميها عند زيارتك لموقعنا، وفقاً لنظام حماية البيانات الشخصية في المملكة العربية السعودية.</p><h2>البيانات التي نجمعها</h2><ul><li>البيانات التي تقدمها طوعاً عبر نماذج الموقع: الاسم والبريد الإلكتروني ورقم الجوال واسم الجهة وتفاصيل طلبك.</li><li>السيرة الذاتية والبيانات المهنية عند التقديم على الوظائف.</li><li>بيانات الاستخدام التقنية الأساسية اللازمة لتشغيل الموقع وحمايته.</li></ul><h2>كيف نستخدم بياناتك</h2><ul><li>الرد على طلباتك واستفساراتك.</li><li>دراسة طلبات التوظيف.</li><li>تحسين خدماتنا وموقعنا.</li></ul><h2>مشاركة البيانات</h2><p>لا نبيع بياناتك. قد نشاركها مع مزودي خدمات يعملون نيابةً عنا وفق التزامات تعاقدية بالسرية، أو عندما يتطلب النظام ذلك.</p><h2>حقوقك</h2><p>يحق لك طلب الاطلاع على بياناتك أو تصحيحها أو إتلافها أو سحب موافقتك في أي وقت عبر التواصل معنا على info@advisory.com.sa.</p>',
                'en' => '<p><em>Draft: to be reviewed by legal counsel before publishing.</em></p><h2>Introduction</h2><p>This policy explains how Advisory Channels collects, uses and protects your personal data when you visit our website, in line with the Saudi Personal Data Protection Law (PDPL).</p><h2>Data we collect</h2><ul><li>Data you voluntarily provide through our forms: name, email, phone number, organization and the details of your request.</li><li>Your CV and professional details when you apply for a job.</li><li>Basic technical usage data needed to run and secure the website.</li></ul><h2>How we use your data</h2><ul><li>To respond to your requests and enquiries.</li><li>To assess job applications.</li><li>To improve our services and website.</li></ul><h2>Data sharing</h2><p>We never sell your data. We may share it with service providers acting on our behalf under contractual confidentiality obligations, or where required by law.</p><h2>Your rights</h2><p>You may request access to, correction or destruction of your data, or withdraw your consent at any time, by contacting us at info@advisory.com.sa.</p>',
            ],
        ]);

        Doc::query()->updateOrCreate(['slug' => Doc::TERMS], [
            'title' => ['ar' => 'الشروط والأحكام', 'en' => 'Terms & Conditions'],
            'body' => [
                'ar' => '<p><em>مسودة يجب مراجعتها قانونياً قبل النشر.</em></p><h2>القبول</h2><p>باستخدامك لهذا الموقع فإنك توافق على هذه الشروط. إن لم توافق عليها فيرجى عدم استخدام الموقع.</p><h2>المحتوى</h2><p>المحتوى المنشور في الموقع لأغراض تعريفية عامة، ولا يُعد استشارة مهنية أو قانونية أو مالية. تُقدَّم خدماتنا الاستشارية بموجب عقود مستقلة.</p><h2>الملكية الفكرية</h2><p>جميع النصوص والتصاميم والشعارات في الموقع مملوكة لقنوات الاستشارات، ولا يجوز نسخها أو إعادة استخدامها دون إذن كتابي مسبق.</p><h2>النماذج والبيانات</h2><p>تلتزم عند تعبئة نماذج الموقع بتقديم بيانات صحيحة، وتخضع معالجة بياناتك لسياسة الخصوصية.</p><h2>القانون الواجب التطبيق</h2><p>تخضع هذه الشروط لأنظمة المملكة العربية السعودية.</p>',
                'en' => '<p><em>Draft: to be reviewed by legal counsel before publishing.</em></p><h2>Acceptance</h2><p>By using this website you agree to these terms. If you do not agree, please do not use the website.</p><h2>Content</h2><p>Content on this website is provided for general information only and does not constitute professional, legal or financial advice. Our consulting services are provided under separate agreements.</p><h2>Intellectual property</h2><p>All text, designs and logos on this website are owned by Advisory Channels and may not be copied or reused without prior written permission.</p><h2>Forms and data</h2><p>When filling in our forms you agree to provide accurate information. Your data is processed in accordance with our Privacy Policy.</p><h2>Governing law</h2><p>These terms are governed by the laws of the Kingdom of Saudi Arabia.</p>',
            ],
        ]);
    }
}
