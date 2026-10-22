<?php

namespace Database\Seeders;

use App\Enums\JobType;
use App\Models\JobOpening;
use Illuminate\Database\Seeder;

class JobOpeningSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        JobOpening::query()->updateOrCreate(['slug' => 'path-to-consultancy'], [
            'type' => JobType::Program,
            'sort_order' => 1,
            'is_active' => true,
            'title' => [
                'ar' => 'برنامج «الطريق إلى الاستشارات»',
                'en' => 'Path to Consultancy Program',
            ],
            'location' => [
                'ar' => 'الرياض، المملكة العربية السعودية',
                'en' => 'Riyadh, Saudi Arabia',
            ],
            'summary' => [
                'ar' => 'برنامج مصمم لحديثي التخرج مدته 6 أشهر، ويضمن التخرج الناجح منه وظيفة «مستشار مشارك».',
                'en' => 'A 6-month program designed for fresh graduates. Successful graduation guarantees a job as an Associate Consultant.',
            ],
            'description' => [
                'ar' => '<p>نؤمن بأن الكفاءات الوطنية الشابة هي مستقبل قطاع الاستشارات في المملكة. لذلك صممنا برنامج «الطريق إلى الاستشارات» ليكون بوابة حديثي التخرج إلى عالم الاستشارات الإدارية.</p><p>على مدى 6 أشهر يعمل المشاركون على مشاريع حقيقية مع فرقنا، ويتلقون تدريباً منهجياً في حل المشكلات وتحليل البيانات وإعداد العروض والتواصل مع العملاء.</p><p>التخرج الناجح من البرنامج يضمن وظيفة <strong>مستشار مشارك</strong> في الشركة.</p>',
                'en' => '<p>We believe young Saudi talent is the future of the consulting sector in the Kingdom. That is why we designed the Path to Consultancy Program as a gateway for fresh graduates into management consulting.</p><p>Over 6 months, participants work on real engagements alongside our teams and receive structured training in problem solving, data analysis, storytelling and client communication.</p><p>Successful graduation guarantees a job as an <strong>Associate Consultant</strong> with the firm.</p>',
            ],
            'requirements' => [
                ['ar' => 'بكالوريوس حديث التخرج في الأعمال أو الهندسة أو تخصص ذي صلة', 'en' => 'Recent bachelor\'s degree in business, engineering or a related field'],
                ['ar' => 'مهارات تحليلية قوية وشغف بحل المشكلات', 'en' => 'Strong analytical skills and a passion for problem solving'],
                ['ar' => 'إتقان اللغتين العربية والإنجليزية', 'en' => 'Fluency in Arabic and English'],
                ['ar' => 'التفرغ الكامل لمدة 6 أشهر', 'en' => 'Full-time availability for 6 months'],
            ],
        ]);
    }
}
