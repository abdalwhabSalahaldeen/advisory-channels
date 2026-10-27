<?php

namespace App\Support;

use App\Models\User;
use Database\Seeders\SiteContentSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;

/**
 * Sets the site up from the browser on hosting without a terminal (cPanel):
 * database tables, launch content, the public storage link and the first admin.
 */
class Installer
{
    private const MINIMUM_PHP = '8.3.0';

    private const EXTENSIONS = ['ctype', 'curl', 'dom', 'fileinfo', 'intl', 'mbstring', 'openssl', 'tokenizer', 'xml'];

    /**
     * Open only with a configured token, and only until the first admin exists.
     */
    public function isAvailable(): bool
    {
        return filled(config('site.install_token')) && ! $this->isInstalled();
    }

    public function acceptsToken(?string $token): bool
    {
        return $this->isAvailable() && is_string($token) && hash_equals((string) config('site.install_token'), $token);
    }

    public function isInstalled(): bool
    {
        try {
            return Schema::hasTable('users') && User::query()->where('is_admin', true)->exists();
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @return list<array{label: string, passes: bool, detail: string}>
     */
    public function requirements(): array
    {
        $checks = [
            [
                'label' => __('install.checks.php'),
                'passes' => version_compare(PHP_VERSION, self::MINIMUM_PHP, '>='),
                'detail' => PHP_VERSION,
            ],
        ];

        // The PDO driver of whichever database .env points at (pdo_mysql on cPanel).
        $databaseDriver = 'pdo_'.config('database.connections.'.config('database.default').'.driver');

        foreach ([...self::EXTENSIONS, $databaseDriver] as $extension) {
            $checks[] = [
                'label' => __('install.checks.extension', ['name' => $extension]),
                'passes' => extension_loaded($extension),
                'detail' => extension_loaded($extension) ? '✓' : __('install.checks.missing'),
            ];
        }

        foreach (['storage' => storage_path(), 'bootstrap/cache' => base_path('bootstrap/cache')] as $label => $path) {
            $checks[] = [
                'label' => __('install.checks.writable', ['path' => $label]),
                'passes' => is_writable($path),
                'detail' => $path,
            ];
        }

        $checks[] = [
            'label' => __('install.checks.app_key'),
            'passes' => filled(config('app.key')),
            'detail' => filled(config('app.key')) ? '✓' : 'APP_KEY',
        ];

        $checks[] = $this->databaseCheck();

        return $checks;
    }

    public function meetsRequirements(): bool
    {
        return collect($this->requirements())->every(fn (array $check): bool => $check['passes']);
    }

    /**
     * Runs every step and reports each one; a failing step stops the ones after it.
     *
     * @return list<array{label: string, passes: bool, detail: string}>
     */
    public function install(string $name, string $email, string $password): array
    {
        $steps = [];

        $run = function (string $label, callable $step) use (&$steps): bool {
            try {
                $detail = (string) $step();
                $steps[] = ['label' => $label, 'passes' => true, 'detail' => $detail];

                return true;
            } catch (Throwable $exception) {
                report($exception);
                $steps[] = ['label' => $label, 'passes' => false, 'detail' => $exception->getMessage()];

                return false;
            }
        };

        $run(__('install.steps.migrate'), fn (): string => $this->artisan('migrate', ['--force' => true]))
            && $run(__('install.steps.seed'), fn (): string => $this->artisan('db:seed', ['--class' => SiteContentSeeder::class, '--force' => true]))
            && $run(__('install.steps.admin'), fn (): string => User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'is_admin' => true,
            ])->email);

        // The storage link is helpful but not essential: some hosts forbid symlinks.
        if (collect($steps)->every(fn (array $step): bool => $step['passes'])) {
            $run(__('install.steps.storage_link'), fn (): string => $this->artisan('storage:link', ['--force' => true]));
        }

        return $steps;
    }

    /**
     * @return array{label: string, passes: bool, detail: string}
     */
    private function databaseCheck(): array
    {
        try {
            DB::connection()->getPdo();

            return ['label' => __('install.checks.database'), 'passes' => true, 'detail' => DB::connection()->getDatabaseName()];
        } catch (Throwable $exception) {
            return ['label' => __('install.checks.database'), 'passes' => false, 'detail' => $exception->getMessage()];
        }
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    private function artisan(string $command, array $parameters = []): string
    {
        if (Artisan::call($command, $parameters) !== 0) {
            throw new RuntimeException(trim(Artisan::output()));
        }

        return trim(Artisan::output());
    }
}
