<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Builds the release for cPanel hosting without a terminal: the application with
 * its production dependencies and built assets, laid out as
 *
 *   advisory-channels/   (outside the web root; name set with --app-folder)
 *   public_html/         (the web root, pointing at the folder above)
 *
 * By default it is zipped to upload and extract by hand; with --directory the
 * folders are left in place for the GitHub deploy workflow to upload over FTP.
 */
#[Signature('app:package-for-cpanel
    {--update : Leave out .env so an existing installation keeps its settings}
    {--domain=https://advisory.com.sa : The address the site will run on}
    {--output= : Where to write the zip (defaults to storage/framework/releases)}
    {--app-folder=advisory-channels : The application folder\'s name, next to public_html on the server}
    {--directory= : Leave the built folders in this directory instead of zipping them}')]
#[Description('Build a zip to upload and extract on cPanel hosting without a terminal')]
class PackageForCpanel extends Command
{
    private const WEB_ROOT = 'public_html';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $appFolder = (string) $this->option('app-folder');

        if (! preg_match('/^[A-Za-z0-9._-]+$/', $appFolder) || in_array($appFolder, ['.', '..', self::WEB_ROOT], true)) {
            $this->components->error("[{$appFolder}] is not a valid folder name.");

            return self::FAILURE;
        }

        if (trim(Process::path(base_path())->run('git status --porcelain --untracked-files=no')->output()) !== '') {
            $this->components->error('Commit your changes first: the package is built from the last commit.');

            return self::FAILURE;
        }

        $workspace = storage_path('framework/releases/build-'.now()->format('YmdHis'));
        $app = "{$workspace}/{$appFolder}";
        $webRoot = "{$workspace}/".self::WEB_ROOT;

        File::ensureDirectoryExists($app);

        try {
            $this->build($app, $webRoot, $appFolder);
        } catch (RuntimeException $exception) {
            File::deleteDirectory($workspace);
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        if ($directory = $this->option('directory')) {
            File::deleteDirectory($directory);
            File::ensureDirectoryExists(dirname($directory));

            if (! File::moveDirectory($workspace, $directory)) {
                $this->components->error("Could not move the release to [{$directory}].");

                return self::FAILURE;
            }

            $this->components->info("Release ready in: {$directory}");

            return self::SUCCESS;
        }

        $output = $this->option('output') ?: storage_path('framework/releases/advisory-channels-cpanel'.($this->option('update') ? '-update' : '').'-'.now()->format('Y-m-d-His').'.zip');
        File::ensureDirectoryExists(dirname($output));

        $this->components->task('Zipping', fn (): bool => $this->runProcess(
            sprintf('zip -r -q -y %s %s %s', escapeshellarg($output), $appFolder, self::WEB_ROOT),
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

    /**
     * Exports the last commit and turns it into the two server folders.
     *
     * @throws RuntimeException when a step fails
     */
    private function build(string $app, string $webRoot, string $appFolder): void
    {
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

        $this->components->task('Preparing public_html', function () use ($app, $webRoot, $appFolder): bool {
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

            File::put("{$webRoot}/index.php", $this->frontController($appFolder));

            return true;
        });

        if (! $this->option('update')) {
            $this->components->task('Writing the production .env', fn (): bool => (bool) File::put("{$app}/.env", $this->productionEnvironment()));
        }
    }

    /**
     * @throws RuntimeException when the command fails, so a broken release is never shipped
     */
    private function runProcess(string $command, ?string $path = null, int $timeout = 300): bool
    {
        $result = Process::path($path ?? base_path())->timeout($timeout)->run($command);

        if ($result->failed()) {
            throw new RuntimeException("[{$command}] failed: ".trim($result->errorOutput() ?: $result->output()));
        }

        return true;
    }

    /**
     * public_html/index.php: loads the application from the folder next to the web root.
     */
    private function frontController(string $folder): string
    {
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
