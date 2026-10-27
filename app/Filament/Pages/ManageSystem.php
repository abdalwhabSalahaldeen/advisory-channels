<?php

namespace App\Filament\Pages;

use App\Filament\NavigationGroup;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Html;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use SplFileObject;
use Throwable;
use UnitEnum;

/**
 * Server maintenance without a terminal: database updates, cache clearing,
 * the storage link, environment details and the latest error log lines.
 */
class ManageSystem extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Settings;

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'system';

    private const LOG_LINES = 80;

    public static function getNavigationLabel(): string
    {
        return __('admin.system.title');
    }

    public function getTitle(): string
    {
        return __('admin.system.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->artisanAction('migrate', 'migrate', ['--force' => true], Heroicon::OutlinedCircleStack)
                ->requiresConfirmation()
                ->modalDescription(__('admin.system.migrate_help')),
            $this->artisanAction('clearCache', 'optimize:clear', [], Heroicon::OutlinedArrowPath),
            $this->artisanAction('storageLink', 'storage:link', ['--force' => true], Heroicon::OutlinedLink),
        ];
    }

    public function content(Schema $schema): Schema
    {
        $pendingMigrations = $this->pendingMigrations();
        $isProduction = app()->isProduction();

        return $schema->components([
            Section::make(__('admin.system.environment'))
                ->schema([
                    Grid::make(['default' => 1, 'md' => 2])->schema(collect([
                        'laravel' => app()->version(),
                        'php' => PHP_VERSION,
                        'environment' => app()->environment(),
                        'debug' => config('app.debug') ? __('admin.system.on') : __('admin.system.off'),
                        'database' => config('database.default'),
                        'pending_migrations' => (string) $pendingMigrations,
                        'queue' => config('queue.default'),
                        'mail' => config('mail.default'),
                        'two_factor' => config('site.admin_two_factor') ? __('admin.system.on') : __('admin.system.off'),
                        'storage_link' => is_link(public_path('storage')) ? __('admin.system.linked') : __('admin.system.not_linked'),
                    ])->flatMap(fn (string $value, string $key): array => [
                        Text::make(__("admin.system.info.{$key}")),
                        Text::make($value)
                            ->fontFamily(FontFamily::Mono)
                            ->color(match (true) {
                                $key === 'debug' && $isProduction && config('app.debug') => 'danger',
                                $key === 'pending_migrations' && $pendingMigrations > 0 => 'warning',
                                $key === 'storage_link' && ! is_link(public_path('storage')) => 'warning',
                                default => null,
                            }),
                    ])->all()),
                ]),
            Section::make(__('admin.system.log'))
                ->description(__('admin.system.log_help', ['lines' => self::LOG_LINES]))
                ->collapsible()
                ->schema([
                    Html::make(fn (): HtmlString => new HtmlString(
                        '<pre dir="ltr" style="max-height:28rem;overflow:auto;white-space:pre-wrap;font-size:12px;line-height:1.6">'.e($this->logTail() ?: __('admin.system.log_empty')).'</pre>',
                    )),
                ]),
        ]);
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    private function artisanAction(string $name, string $command, array $parameters, Heroicon $icon): Action
    {
        return Action::make($name)
            ->label(__("admin.system.actions.{$name}"))
            ->icon($icon)
            ->color('gray')
            ->action(function () use ($name, $command, $parameters): void {
                try {
                    $exitCode = Artisan::call($command, $parameters);
                    $output = trim(Artisan::output());
                } catch (Throwable $exception) {
                    report($exception);
                    $exitCode = 1;
                    $output = $exception->getMessage();
                }

                Log::info("system.{$name}", ['user' => auth()->user()?->email, 'exit_code' => $exitCode]);

                Notification::make()
                    ->title(__("admin.system.actions.{$name}"))
                    ->body(mb_strimwidth($output ?: __('admin.system.done'), 0, 600, '…'))
                    ->status($exitCode === 0 ? 'success' : 'danger')
                    ->send();
            });
    }

    private function pendingMigrations(): int
    {
        try {
            $migrator = app('migrator');

            if (! $migrator->repositoryExists()) {
                return count($migrator->getMigrationFiles(database_path('migrations')));
            }

            return count(array_diff(
                array_keys($migrator->getMigrationFiles(database_path('migrations'))),
                $migrator->getRepository()->getRan(),
            ));
        } catch (Throwable) {
            return 0;
        }
    }

    private function logTail(): string
    {
        $path = storage_path('logs/laravel.log');

        if (! is_readable($path)) {
            return '';
        }

        $lines = [];
        $file = new SplFileObject($path);
        $file->seek(PHP_INT_MAX);
        $last = $file->key();

        for ($line = max(0, $last - self::LOG_LINES); $line <= $last; $line++) {
            $file->seek($line);
            $lines[] = rtrim((string) $file->current());
        }

        return trim(implode("\n", $lines));
    }
}
