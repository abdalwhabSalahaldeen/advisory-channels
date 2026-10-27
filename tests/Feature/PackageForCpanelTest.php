<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class PackageForCpanelTest extends TestCase
{
    protected function tearDown(): void
    {
        File::deleteDirectory(storage_path('framework/testing/releases'));

        parent::tearDown();
    }

    public function test_refuses_to_package_uncommitted_changes(): void
    {
        Process::fake(['git status*' => Process::result(' M app/Models/Lead.php')]);

        $this->artisan('app:package-for-cpanel')->assertFailed();

        Process::assertDidntRun(fn ($process): bool => str_contains($process->command, 'composer install'));
    }

    public function test_builds_production_dependencies_assets_and_the_zip(): void
    {
        Process::fake();
        $output = storage_path('framework/testing/releases/site.zip');

        $this->artisan('app:package-for-cpanel', ['--output' => $output])->assertSuccessful();

        Process::assertRan(fn ($process): bool => str_starts_with($process->command, 'git archive HEAD'));
        Process::assertRan('composer install --no-dev --optimize-autoloader --no-interaction --no-progress');
        Process::assertRan('npm run build');
        Process::assertRan(fn ($process): bool => str_contains($process->command, 'zip -r -q -y')
            && str_contains($process->command, 'advisory-channels public_html'));
    }
}
