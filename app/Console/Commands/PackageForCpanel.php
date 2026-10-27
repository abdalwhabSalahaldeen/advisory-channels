<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

/**
 * Builds a zip for cPanel hosting without a terminal: the application with its
 * production dependencies and built assets, laid out as
 *
 *   advisory-channels/   (outside the web root)
 *   public_html/         (the web root, pointing at the folder above)
 *
 * Upload it to the hosting account's home folder and extract it there.
 */
#[Signature('app:package-for-cpanel
    {--update : Leave out .env so an existing installation keeps its settings}
    {--domain=https://advisory.com.sa : The address the site will run on}
    {--output= : Where to write the zip (defaults to storage/framework/releases)}')]
#[Description('Build a zip to upload and extract on cPanel hosting without a terminal')]
class PackageForCpanel extends Command
{
    private const APP_FOLDER = 'advisory-channels';

    private const WEB_ROOT = 'public_html';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (trim(Process::path(base_path())->run('git status --porcelain --untracked-files=no')->output()) !== '') {
            $this->components->error('Commit your changes first: the package is built from the last commit.');

            return self::FAILURE;
        }

        $workspace = storage_path('framework/releases/build-'.now()->format('YmdHis'));
        $app = "{$workspace}/".self::APP_FOLDER;
        $webRoot = "{$workspace}/".self::WEB_ROOT;

        File::ensureDirectoryExists($app);

        $this->components->task('Exporting the last commit', fn (): bool => $this->runProcess(
            sprintf('git archive HEAD | tar -x -C %s', escapeshellarg($app)),
        ));

        $this->components->task('Removing development-only files', function () use ($app): bool {
            foreach (['tests', '.claude', '.ai', '.github'] as $folder) {
                File::deleteDirectory("{$app}/{$folder}");
            }

            File::delete(array_map(fn (string $file): string => "{$app}/{$file}", ['.mcp.json', 'boost.json', 'CLAUDE.md', 'AGENTS.md', 'phpunit.xml']));

            return true;
        });

        $this->components->task('Installing production PHP dependencies', fn (): bool => $this->runProcess(
            'composer install --no-dev --optimize-autoloader --no-interaction --no-progress',
            $app,
            timeout: 900,
        ));

        $this->components->task('Building the front-end assets', fn (): bool => $this->runProcess('npm run build', base_path(), timeout: 600));

        $this->components->task('Preparing public_html', function () use ($app, $webRoot): bool {
            // The web root gets the committed public files plus the built and published assets.
            if (is_dir("{$app}/public")) {
                File::moveDirectory("{$app}/public", $webRoot);
            }

            File::ensureDirectoryExists($webRoot);

            foreach (['build', 'css', 'fonts', 'js'] as $folder) {
                if (is_dir(public_path($folder))) {
                    File::copyDirectory(public_path($folder), "{$webRoot}/{$folder}");
                }
            }

            File::put("{$webRoot}/index.php", $this->frontController());

            return true;
        });

        if (! $this->option('update')) {
            $this->components->task('Writing the production .env', fn (): bool => (bool) File::put("{$app}/.env", $this->productionEnvironment()));
        }

        $output = $this->option('output') ?: storage_path('framework/releases/advisory-channels-cpanel'.($this->option('update') ? '-update' : '').'-'.now()->format('Y-m-d-His').'.zip');
        File::ensureDirectoryExists(dirname($output));

        $this->components->task('Zipping', fn (): bool => $this->runProcess(
            sprintf('zip -r -q -y %s %s %s', escapeshellarg($output), self::APP_FOLDER, self::WEB_ROOT),
            $workspace,
        ));

        File::deleteDirectory($workspace);

        $this->newLine();
        $this->components->info("Package ready: {$output}");

        if (! $this->option('update')) {
            $this->components->warn('The zip contains a fresh APP_KEY and INSTALL_TOKEN in .env. Do not commit or share it.');
        }

        return self::SUCCESS;
    }

    private function runProcess(string $command, ?string $path = null, int $timeout = 300): bool
    {
        $result = Process::path($path ?? base_path())->timeout($timeout)->run($command);

        if ($result->failed()) {
            $this->components->error(trim($result->errorOutput() ?: $result->output()));
        }

        return $result->successful();
    }

    /**
     * public_html/index.php: loads the application from the folder next to the web root.
     */
    private function frontController(): string
    {
        $folder = self::APP_FOLDER;

        return <<<PHP
        <?php

        use Illuminate\\Foundation\\Application;
        use Illuminate\\Http\\Request;

        define('LARAVEL_START', microtime(true));

        // The application lives next to public_html, outside the web root.
        \$appPath = __DIR__.'/../{$folder}';

        if (file_exists(\$maintenance = \$appPath.'/storage/framework/maintenance.php')) {
            require \$maintenance;
        }

        require \$appPath.'/vendor/autoload.php';

        /** @var Application \$app */
        \$app = require_once \$appPath.'/bootstrap/app.php';

        \$app->usePublicPath(__DIR__);

        \$app->handleRequest(Request::capture());

        PHP;
    }

    /**
     * Production settings for shared hosting: file sessions and cache (no extra
     * services), synchronous queue (no worker process) and mail written to the log
     * until SMTP is configured.
     */
    private function productionEnvironment(): string
    {
        $values = [
            'APP_NAME' => '"Advisory Channels"',
            'APP_ENV' => 'production',
            'APP_KEY' => 'base64:'.base64_encode(random_bytes(32)),
            'APP_DEBUG' => 'false',
            'APP_URL' => rtrim($this->option('domain'), '/'),
            'APP_LOCALE' => 'ar',
            'APP_FALLBACK_LOCALE' => 'en',
            'APP_FAKER_LOCALE' => 'ar_SA',
            'LOG_CHANNEL' => 'stack',
            'LOG_STACK' => 'single',
            'LOG_LEVEL' => 'warning',
            'DB_CONNECTION' => 'mysql',
            'DB_HOST' => 'localhost',
            'DB_PORT' => '3306',
            'DB_DATABASE' => '',
            'DB_USERNAME' => '',
            'DB_PASSWORD' => '',
            'SESSION_DRIVER' => 'file',
            'SESSION_LIFETIME' => '120',
            'SESSION_SECURE_COOKIE' => 'true',
            'CACHE_STORE' => 'file',
            'QUEUE_CONNECTION' => 'sync',
            'FILESYSTEM_DISK' => 'public',
            'MAIL_MAILER' => 'log',
            'MAIL_HOST' => '',
            'MAIL_PORT' => '465',
            'MAIL_USERNAME' => '',
            'MAIL_PASSWORD' => '',
            'MAIL_FROM_ADDRESS' => '"no-reply@advisory.com.sa"',
            'MAIL_FROM_NAME' => '"${APP_NAME}"',
            'LEADS_NOTIFY_EMAIL' => 'info@advisory.com.sa',
            'ADMIN_TWO_FACTOR' => 'false',
            'FILE_MANAGER_ENABLED' => 'true',
            'INSTALL_TOKEN' => Str::random(40),
        ];

        return collect($values)->map(fn (string $value, string $key): string => "{$key}={$value}")->implode("\n")."\n";
    }
}
