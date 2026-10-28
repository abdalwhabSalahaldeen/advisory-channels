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

    public function test_names_the_application_folder_after_the_server(): void
    {
        Process::fake();
        $output = storage_path('framework/testing/releases/site.zip');

        $this->artisan('app:package-for-cpanel', ['--output' => $output, '--app-folder' => 'advisory'])->assertSuccessful();

        Process::assertRan(fn ($process): bool => str_contains($process->command, 'advisory public_html'));
    }

    public function test_rejects_a_folder_name_that_could_escape_the_home_folder(): void
    {
        Process::fake();

        $this->artisan('app:package-for-cpanel', ['--app-folder' => '../etc'])->assertFailed();

        Process::assertNothingRan();
    }

    public function test_leaves_the_folders_unzipped_for_the_github_deploy(): void
    {
        Process::fake();
        $directory = storage_path('framework/testing/releases/github');

        $this->artisan('app:package-for-cpanel', ['--update' => true, '--app-folder' => 'advisory', '--directory' => $directory])->assertSuccessful();

        Process::assertDidntRun(fn ($process): bool => str_starts_with($process->command, 'zip'));
        $this->assertFileExists("{$directory}/public_html/index.php");
        $this->assertStringContainsString("__DIR__.'/../advisory'", File::get("{$directory}/public_html/index.php"));
    }
}
