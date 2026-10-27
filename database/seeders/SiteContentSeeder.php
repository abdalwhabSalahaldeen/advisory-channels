<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * The website's launch content (settings, services, Saudi Gate, careers, legal
 * pages, FAQs, insights). Used by DatabaseSeeder and by the web installer.
 */
class SiteContentSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            ServiceSeeder::class,
            GatePackageSeeder::class,
            JobOpeningSeeder::class,
            DocSeeder::class,
            FaqSeeder::class,
            InsightSeeder::class,
            TrustExampleSeeder::class,
        ]);
    }
}
