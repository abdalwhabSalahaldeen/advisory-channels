<?php

namespace App\Filament\Pages;

use App\Exceptions\FileManagerException;
use App\Filament\NavigationGroup;
use App\Support\FileManager;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Livewire\Attributes\Url;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use UnitEnum;

/**
 * Browse, upload, edit, rename and delete the files stored on the server, and
 * review or change their permissions (the Drovox dashboard's file manager).
 */
class ManageFiles extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::Settings;

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'files';

    #[Url]
    public string $root = 'public';

    #[Url]
    public string $path = '';

    public static function canAccess(): bool
    {
        return config('site.file_manager.enabled') && parent::canAccess();
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.files.title');
    }

    public function getTitle(): string
    {
        return __('admin.files.title');
    }

    public function getSubheading(): string
    {
        return __("admin.files.roots.{$this->root}.description");
    }

    public function mount(FileManager $files): void
    {
        if (! $files->hasRoot($this->root)) {
            $this->root = array_key_first($files->roots());
        }

        try {
            if (! is_dir($files->absolute($this->root, $this->path))) {
                $this->path = '';
            }
        } catch (FileManagerException) {
            $this->path = '';
        }
    }

    /**
     * @return array<string, string>
     */
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [$this->folderUrl('') => __("admin.files.roots.{$this->root}.label")];
        $trail = '';

        foreach (array_filter(explode('/', $this->path)) as $segment) {
            $trail = ltrim("{$trail}/{$segment}", '/');
            $breadcrumbs[$this->folderUrl($trail)] = $segment;
        }

        return $breadcrumbs;
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([EmbeddedTable::make()]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ...collect(app(FileManager::class)->roots())->keys()->map(fn (string $root): Action => Action::make("root_{$root}")
                ->label(__("admin.files.roots.{$root}.label"))
                ->icon($root === 'public' ? Heroicon::OutlinedGlobeAlt : Heroicon::OutlinedLockClosed)
                ->color($root === $this->root ? 'primary' : 'gray')
                ->outlined($root !== $this->root)
                ->url(static::getUrl(['root' => $root])))->all(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->records(fn (?string $search, ?string $sortColumn, ?string $sortDirection, int $page, int $recordsPerPage): LengthAwarePaginator => $this->records($search, $sortColumn, $sortDirection, $page, $recordsPerPage))
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.files.columns.name'))
                    ->icon(fn (array $record): Heroicon => match (true) {
                        $record['type'] === 'directory' => Heroicon::Folder,
                        $record['image'] => Heroicon::OutlinedPhoto,
                        default => Heroicon::OutlinedDocument,
                    })
                    ->iconColor(fn (array $record): string => $record['type'] === 'directory' ? 'warning' : 'gray')
                    ->weight(fn (array $record): ?FontWeight => $record['type'] === 'directory' ? FontWeight::Medium : null)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('size')
                    ->label(__('admin.files.columns.size'))
                    ->formatStateUsing(fn (?int $state): string => Number::fileSize($state ?? 0, precision: 1))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('modified')
                    ->label(__('admin.files.columns.modified'))
                    ->formatStateUsing(fn (int $state): string => Carbon::createFromTimestamp($state)->timezone(config('app.timezone'))->translatedFormat('j M Y, H:i'))
                    ->sortable(),
                TextColumn::make('permissions')
                    ->label(__('admin.files.columns.permissions'))
                    ->formatStateUsing(fn (string $state, array $record): string => "{$state}  {$record['symbolic']}")
                    ->fontFamily(FontFamily::Mono)
                    ->badge()
                    ->color(fn (array $record): string => $record['writable'] ? 'success' : 'danger')
                    ->tooltip(fn (array $record): string => $record['writable'] ? __('admin.files.writable') : __('admin.files.read_only')),
                TextColumn::make('owner')
                    ->label(__('admin.files.columns.owner'))
                    ->formatStateUsing(fn (string $state, array $record): string => "{$state}:{$record['group']}")
                    ->fontFamily(FontFamily::Mono)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordUrl(fn (array $record): ?string => $record['type'] === 'directory' ? $this->folderUrl($record['path']) : null)
            ->headerActions([
                $this->uploadAction(),
                $this->newFolderAction(),
                $this->newFileAction(),
                Action::make('up')
                    ->label(__('admin.files.actions.up'))
                    ->icon(Heroicon::OutlinedArrowUturnLeft)
                    ->color('gray')
                    ->visible(fn (): bool => $this->path !== '')
                    ->url(fn (): string => $this->folderUrl(dirname($this->path) === '.' ? '' : dirname($this->path))),
            ])
            ->recordActions([
                Action::make('view')
                    ->label(__('admin.files.actions.view'))
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->visible(fn (array $record): bool => filled($record['url']))
                    ->url(fn (array $record): ?string => $record['url'], shouldOpenInNewTab: true),
                ActionGroup::make([
                    Action::make('download')
                        ->label(__('admin.files.actions.download'))
                        ->icon(Heroicon::OutlinedArrowDownTray)
                        ->visible(fn (array $record): bool => $record['type'] === 'file')
                        ->action(fn (array $record): BinaryFileResponse => response()->download(app(FileManager::class)->absolute($this->root, $record['path']))),
                    $this->editAction(),
                    $this->renameAction(),
                    $this->permissionsAction(),
                    Action::make('delete')
                        ->label(__('admin.files.actions.delete'))
                        ->icon(Heroicon::OutlinedTrash)
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(fn (array $record): string => __('admin.files.delete_heading', ['name' => $record['name']]))
                        ->modalDescription(fn (array $record): string => $record['type'] === 'directory' ? __('admin.files.delete_folder_warning') : __('admin.files.delete_file_warning'))
                        ->action(fn (array $record) => $this->attempt(
                            fn (FileManager $files) => $files->delete($this->root, $record['path']),
                            __('admin.files.notifications.deleted'),
                        )),
                ]),
            ])
            ->toolbarActions([
                BulkAction::make('deleteSelected')
                    ->label(__('admin.files.actions.delete_selected'))
                    ->icon(Heroicon::OutlinedTrash)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $this->attempt(
                        fn (FileManager $files) => $records->each(fn (array $record) => $files->delete($this->root, $record['path'])),
                        __('admin.files.notifications.deleted'),
                    )),
            ])
            ->paginated([25, 50, 100])
            ->defaultPaginationPageOption(50)
            ->emptyStateIcon(Heroicon::OutlinedFolderOpen)
            ->emptyStateHeading(__('admin.files.empty'));
    }

    private function uploadAction(): Action
    {
        return Action::make('upload')
            ->label(__('admin.files.actions.upload'))
            ->icon(Heroicon::OutlinedArrowUpTray)
            ->modalWidth(Width::TwoExtraLarge)
            ->schema([
                FileUpload::make('files')
                    ->label(__('admin.files.fields.files'))
                    ->helperText(__('admin.files.upload_help', [
                        'max' => config('site.file_manager.max_upload_kb') / 1024,
                        'extensions' => implode(', ', config('site.file_manager.allowed_extensions')),
                    ]))
                    ->multiple()
                    ->storeFiles(false)
                    ->maxSize(config('site.file_manager.max_upload_kb'))
                    ->required(),
            ])
            ->action(function (array $data, FileManager $files): void {
                $uploaded = 0;

                foreach ($data['files'] as $file) {
                    try {
                        $files->upload($this->root, $this->path, $file);
                        $uploaded++;
                    } catch (FileManagerException $exception) {
                        Notification::make()->danger()->title($file->getClientOriginalName())->body($exception->getMessage())->send();
                    }
                }

                if ($uploaded > 0) {
                    Notification::make()->success()->title(__('admin.files.notifications.uploaded', ['count' => $uploaded]))->send();
                }

                $this->flushCachedTableRecords();
            });
    }

    private function newFolderAction(): Action
    {
        return Action::make('newFolder')
            ->label(__('admin.files.actions.new_folder'))
            ->icon(Heroicon::OutlinedFolderPlus)
            ->color('gray')
            ->schema([
                TextInput::make('name')->label(__('admin.files.fields.folder_name'))->required()->maxLength(150),
            ])
            ->action(fn (array $data, Action $action) => $this->attempt(
                fn (FileManager $files) => $files->createDirectory($this->root, $this->path, $data['name']),
                __('admin.files.notifications.folder_created'),
                $action,
            ));
    }

    private function newFileAction(): Action
    {
        return Action::make('newFile')
            ->label(__('admin.files.actions.new_file'))
            ->icon(Heroicon::OutlinedDocumentPlus)
            ->color('gray')
            ->modalWidth(Width::FourExtraLarge)
            ->schema([
                TextInput::make('name')
                    ->label(__('admin.files.fields.file_name'))
                    ->helperText(__('admin.files.editable_help', ['extensions' => implode(', ', config('site.file_manager.editable_extensions'))]))
                    ->placeholder('notes.txt')
                    ->required()
                    ->maxLength(150),
                CodeEditor::make('contents')->label(__('admin.files.fields.contents')),
            ])
            ->action(fn (array $data, Action $action) => $this->attempt(
                fn (FileManager $files) => $files->createFile($this->root, $this->path, $data['name'], $data['contents'] ?? ''),
                __('admin.files.notifications.file_created'),
                $action,
            ));
    }

    private function editAction(): Action
    {
        return Action::make('edit')
            ->label(__('admin.files.actions.edit'))
            ->icon(Heroicon::OutlinedPencilSquare)
            ->visible(fn (array $record): bool => $record['editable'])
            ->modalHeading(fn (array $record): string => $record['name'])
            ->modalWidth(Width::SixExtraLarge)
            ->fillForm(fn (array $record, FileManager $files): array => ['contents' => $files->read($this->root, $record['path'])])
            ->schema(fn (array $record): array => [
                CodeEditor::make('contents')
                    ->hiddenLabel()
                    ->language(match ($record['extension']) {
                        'json' => Language::Json,
                        'md' => Language::Markdown,
                        'xml', 'svg' => Language::Xml,
                        default => null,
                    }),
            ])
            ->action(fn (array $data, array $record, Action $action) => $this->attempt(
                fn (FileManager $files) => $files->write($this->root, $record['path'], $data['contents'] ?? ''),
                __('admin.files.notifications.saved'),
                $action,
            ));
    }

    private function renameAction(): Action
    {
        return Action::make('rename')
            ->label(__('admin.files.actions.rename'))
            ->icon(Heroicon::OutlinedPencil)
            ->fillForm(fn (array $record): array => ['name' => $record['name']])
            ->schema([
                TextInput::make('name')->label(__('admin.files.fields.new_name'))->required()->maxLength(150),
            ])
            ->action(fn (array $data, array $record, Action $action) => $this->attempt(
                fn (FileManager $files) => $files->rename($this->root, $record['path'], $data['name']),
                __('admin.files.notifications.renamed'),
                $action,
            ));
    }

    private function permissionsAction(): Action
    {
        return Action::make('permissions')
            ->label(__('admin.files.actions.permissions'))
            ->icon(Heroicon::OutlinedShieldCheck)
            ->modalHeading(fn (array $record): string => __('admin.files.permissions_heading', ['name' => $record['name']]))
            ->modalWidth(Width::Large)
            ->fillForm(fn (array $record): array => ['mode' => $record['permissions']])
            ->schema(fn (array $record): array => [
                Grid::make(2)->schema([
                    Text::make(__('admin.files.columns.permissions')),
                    Text::make("{$record['permissions']}  {$record['symbolic']}")->fontFamily(FontFamily::Mono),
                    Text::make(__('admin.files.columns.owner')),
                    Text::make("{$record['owner']}:{$record['group']}")->fontFamily(FontFamily::Mono),
                    Text::make(__('admin.files.access')),
                    Text::make(collect([
                        $record['readable'] ? __('admin.files.readable') : null,
                        $record['writable'] ? __('admin.files.writable') : __('admin.files.read_only'),
                    ])->filter()->implode(' · ')),
                ]),
                Select::make('mode')
                    ->label(__('admin.files.fields.mode'))
                    ->options(collect(config($record['type'] === 'directory' ? 'site.file_manager.directory_modes' : 'site.file_manager.file_modes'))
                        ->mapWithKeys(fn (string $mode): array => [$mode => $mode.' — '.__("admin.files.modes.{$mode}")]))
                    ->helperText(__('admin.files.mode_help'))
                    ->required(),
            ])
            ->action(fn (array $data, array $record, Action $action) => $this->attempt(
                fn (FileManager $files) => $files->changePermissions($this->root, $record['path'], $data['mode']),
                __('admin.files.notifications.permissions_changed'),
                $action,
            ));
    }

    /**
     * Runs a file operation and reports the outcome; a refused operation keeps its modal open.
     */
    private function attempt(Closure $operation, string $success, ?Action $action = null): void
    {
        try {
            app()->call($operation);
        } catch (FileManagerException $exception) {
            Notification::make()->danger()->title($exception->getMessage())->send();
            $action?->halt();

            return;
        }

        Notification::make()->success()->title($success)->send();
        $this->flushCachedTableRecords();
    }

    private function records(?string $search, ?string $sortColumn, ?string $sortDirection, int $page, int $recordsPerPage): LengthAwarePaginator
    {
        $entries = collect(app(FileManager::class)->list($this->root, $this->path))
            ->when(filled($search), fn (Collection $entries) => $entries->filter(fn (array $entry): bool => str_contains(mb_strtolower($entry['name']), mb_strtolower($search))))
            ->when(in_array($sortColumn, ['name', 'size', 'modified'], true), fn (Collection $entries) => $entries->sort(function (array $a, array $b) use ($sortColumn, $sortDirection): int {
                // Folders stay above files whichever way the column is sorted.
                $byType = ($a['type'] !== 'directory') <=> ($b['type'] !== 'directory');

                if ($byType !== 0) {
                    return $byType;
                }

                $order = $sortColumn === 'name' ? strnatcasecmp($a['name'], $b['name']) : ($a[$sortColumn] ?? 0) <=> ($b[$sortColumn] ?? 0);

                return $sortDirection === 'desc' ? -$order : $order;
            }))
            ->map(fn (array $entry): array => ['__key' => $entry['path'], ...$entry])
            ->values();

        return new LengthAwarePaginator(
            $entries->forPage($page, $recordsPerPage)->keyBy('__key'),
            $entries->count(),
            $recordsPerPage,
            $page,
        );
    }

    private function folderUrl(string $path): string
    {
        return static::getUrl(array_filter(['root' => $this->root, 'path' => $path]));
    }
}
